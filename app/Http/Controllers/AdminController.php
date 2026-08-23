<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;
use App\Models\Role;
use App\Models\Paciente;
use App\Models\Medico;
use App\Models\Especialidad;
use App\Models\Auditoria;
use App\Models\Cita;

class AdminController extends Controller
{
    public function usuarios(Request $request)
    {
        $search = $request->get('search');
        $roleId = $request->get('role_id');

        $roles = Role::where('estado', 'ACTIVO')->get();

        $query = Usuario::with(['role', 'paciente', 'medico.especialidad']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('apellido', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('paciente', function ($qp) use ($search) {
                      $qp->where('ci', 'like', "%{$search}%");
                  });
            });
        }

        if ($roleId) {
            $query->where('id_rol', $roleId);
        }

        $usuarios = $query->orderBy('id_usuario', 'desc')->paginate(10);
        $especialidades = Especialidad::where('estado', 'ACTIVO')->get();

        return view('admin.usuarios', compact('usuarios', 'roles', 'search', 'roleId', 'especialidades'));
    }

    public function crearUsuario(Request $request)
    {
        $data = $request->validate([
            'id_rol' => 'required|exists:roles,id_rol',
            'nombre' => 'required|string|max:50',
            'apellido' => 'required|string|max:50',
            'email' => 'required|email|max:100|unique:usuarios,email',
            'password' => 'required|string|min:6',
            'telefono' => 'required|string|max:20',
            // Campos según rol
            'ci' => 'nullable|string|max:15|unique:pacientes,ci',
            'fecha_nacimiento' => 'nullable|date',
            'genero' => 'nullable|string|in:MASCULINO,FEMENINO,OTRO',
            'id_especialidad' => 'nullable|exists:especialidades,id_especialidad',
            'titulo' => 'nullable|string|max:100',
            'numero_colegiatura' => 'nullable|string|max:30|unique:medicos,numero_colegiatura',
        ]);

        $rol = Role::findOrFail($data['id_rol']);

        $usuario = Usuario::create([
            'id_rol' => $data['id_rol'],
            'nombre' => $data['nombre'],
            'apellido' => $data['apellido'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'telefono' => $data['telefono'],
            'estado' => 'ACTIVO',
            'fecha_registro' => now(),
        ]);

        if ($rol->nombre_rol === 'PACIENTE') {
            Paciente::create([
                'id_usuario' => $usuario->id_usuario,
                'ci' => $data['ci'] ?? rand(1000000, 9999999),
                'fecha_nacimiento' => $data['fecha_nacimiento'] ?? '1995-01-01',
                'genero' => $data['genero'] ?? 'MASCULINO',
            ]);
        } elseif ($rol->nombre_rol === 'MEDICO') {
            Medico::create([
                'id_usuario' => $usuario->id_usuario,
                'id_especialidad' => $data['id_especialidad'],
                'titulo' => $data['titulo'] ?? 'Médico Especialista',
                'numero_colegiatura' => $data['numero_colegiatura'] ?? 'MP-' . rand(10000, 99999),
                'estado' => 'ACTIVO',
            ]);
        }

        Auditoria::create([
            'id_usuario' => auth()->id(),
            'accion' => 'CREAR_USUARIO',
            'tabla_afectada' => 'usuarios',
            'registro_afectado' => $usuario->id_usuario,
            'detalle' => json_encode(['rol' => $rol->nombre_rol, 'email' => $usuario->email]),
            'ip_origen' => $request->ip(),
        ]);

        return back()->with('success', "Usuario {$usuario->nombre_completo} ({$rol->nombre_rol}) creado exitosamente.");
    }

    public function cambiarEstadoUsuario(Request $request, $id)
    {
        $usuario = Usuario::findOrFail($id);
        $nuevoEstado = $usuario->estado === 'ACTIVO' ? 'BLOQUEADO' : 'ACTIVO';
        
        $usuario->update(['estado' => $nuevoEstado]);

        Auditoria::create([
            'id_usuario' => auth()->id(),
            'accion' => 'CAMBIAR_ESTADO_USUARIO',
            'tabla_afectada' => 'usuarios',
            'registro_afectado' => $usuario->id_usuario,
            'detalle' => json_encode(['nuevo_estado' => $nuevoEstado]),
            'ip_origen' => $request->ip(),
        ]);

        return back()->with('success', "El estado del usuario {$usuario->nombre_completo} cambió a {$nuevoEstado}.");
    }

    public function auditoria()
    {
        $auditorias = Auditoria::with('usuario')
            ->orderBy('fecha_hora', 'desc')
            ->paginate(15);

        return view('admin.auditoria', compact('auditorias'));
    }

    public function reportes()
    {
        $citasPorEstado = Cita::selectRaw('estado, count(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado');

        $citasPorEspecialidad = Cita::join('medicos', 'citas.id_medico', '=', 'medicos.id_medico')
            ->join('especialidades', 'medicos.id_especialidad', '=', 'especialidades.id_especialidad')
            ->selectRaw('especialidades.nombre as especialidad, count(*) as total')
            ->groupBy('especialidades.nombre')
            ->pluck('total', 'especialidad');

        return view('admin.reportes', compact('citasPorEstado', 'citasPorEspecialidad'));
    }
}
