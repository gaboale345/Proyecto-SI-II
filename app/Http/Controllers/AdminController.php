<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;
use App\Models\Role;
use App\Models\Paciente;
use App\Models\Medico;
use App\Models\Especialidad;
use App\Models\Consultorio;
use App\Models\Agenda;
use App\Models\Pago;
use App\Models\Auditoria;
use App\Models\Cita;
use App\Models\Configuracion;
use Carbon\Carbon;

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
        $rol = Role::findOrFail($request->input('id_rol'));

        $rules = [
            'id_rol' => 'required|exists:roles,id_rol',
            'nombre' => 'required|string|max:50',
            'apellido' => 'required|string|max:50',
            'email' => 'required|email|max:100|unique:usuarios,email',
            'password' => 'required|string|min:6',
            'telefono' => 'required|string|max:20',
            'fecha_nacimiento' => 'nullable|date',
            'genero' => 'nullable|string|in:MASCULINO,FEMENINO,OTRO',
            'titulo' => 'nullable|string|max:100',
        ];

        if ($rol->nombre_rol === 'PACIENTE') {
            if ($request->filled('ci')) {
                $rules['ci'] = 'string|max:15|unique:pacientes,ci';
            }
        } elseif ($rol->nombre_rol === 'MEDICO') {
            $rules['id_especialidad'] = 'required|exists:especialidades,id_especialidad';
            if ($request->filled('numero_colegiatura')) {
                $rules['numero_colegiatura'] = 'string|max:30|unique:medicos,numero_colegiatura';
            }
        }

        $data = $request->validate($rules);

        $usuario = Usuario::create([
            'id_rol' => $data['id_rol'],
            'nombre' => trim($data['nombre']),
            'apellido' => trim($data['apellido']),
            'email' => trim($data['email']),
            'password' => Hash::make($data['password']),
            'telefono' => trim($data['telefono']),
            'estado' => 'ACTIVO',
            'fecha_registro' => now(),
        ]);

        if ($rol->nombre_rol === 'PACIENTE') {
            $ciValue = $request->filled('ci') ? trim($request->input('ci')) : (string)rand(1000000, 9999999);
            Paciente::create([
                'id_usuario' => $usuario->id_usuario,
                'ci' => $ciValue,
                'fecha_nacimiento' => $request->input('fecha_nacimiento') ?: '1995-01-01',
                'genero' => $request->input('genero') ?: 'MASCULINO',
            ]);
        } elseif ($rol->nombre_rol === 'MEDICO') {
            $colegiaturaValue = $request->filled('numero_colegiatura') 
                ? trim($request->input('numero_colegiatura')) 
                : 'MP-' . rand(10000, 99999);

            Medico::create([
                'id_usuario' => $usuario->id_usuario,
                'id_especialidad' => $data['id_especialidad'],
                'titulo' => $request->filled('titulo') ? trim($request->input('titulo')) : 'Médico Especialista',
                'numero_colegiatura' => $colegiaturaValue,
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

    public function consultoriosIndex()
    {
        $consultorios = Consultorio::with(['especialidad', 'medico.usuario'])->get();
        $especialidades = Especialidad::where('estado', 'ACTIVO')->get();
        $medicos = Medico::with('usuario')->where('estado', 'ACTIVO')->get();

        return view('admin.consultorios', compact('consultorios', 'especialidades', 'medicos'));
    }

    public function consultoriosStore(Request $request)
    {
        $request->validate([
            'nombre_numero' => 'required|string|max:50',
            'id_especialidad' => 'nullable|exists:especialidades,id_especialidad',
            'id_medico' => 'nullable|exists:medicos,id_medico',
            'equipamiento' => 'nullable|string',
        ]);

        Consultorio::create([
            'nombre_numero' => $request->nombre_numero,
            'id_especialidad' => $request->id_especialidad,
            'id_medico' => $request->id_medico,
            'estado' => 'DISPONIBLE',
            'equipamiento' => $request->equipamiento,
        ]);

        return back()->with('success', 'Consultorio registrado exitosamente.');
    }

    public function generarHorariosMasivos(Request $request)
    {
        $request->validate([
            'id_medico' => 'required|exists:medicos,id_medico',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'hora_inicio' => 'required',
            'hora_fin' => 'required',
            'duracion_minutos' => 'required|integer|min:5|max:60',
        ]);

        $medico = Medico::findOrFail($request->id_medico);
        $inicio = Carbon::parse($request->fecha_inicio);
        $fin = Carbon::parse($request->fecha_fin);
        $duracion = (int)$request->duracion_minutos;

        $creados = 0;
        for ($date = $inicio->copy(); $date->lte($fin); $date->addDay()) {
            if ($date->isSunday()) continue;

            $timeStart = Carbon::createFromFormat('Y-m-d H:i', $date->format('Y-m-d') . ' ' . $request->hora_inicio);
            $timeEnd = Carbon::createFromFormat('Y-m-d H:i', $date->format('Y-m-d') . ' ' . $request->hora_fin);

            while ($timeStart->copy()->addMinutes($duracion)->lte($timeEnd)) {
                $slotStart = $timeStart->format('H:i');
                $slotEnd = $timeStart->copy()->addMinutes($duracion)->format('H:i');

                $exists = Agenda::where('id_medico', $medico->id_medico)
                    ->where('fecha', $date->format('Y-m-d'))
                    ->where('hora_inicio', $slotStart)
                    ->exists();

                if (!$exists) {
                    Agenda::create([
                        'id_medico' => $medico->id_medico,
                        'fecha' => $date->format('Y-m-d'),
                        'hora_inicio' => $slotStart,
                        'hora_fin' => $slotEnd,
                        'capacidad' => 1,
                        'disponibles' => 1,
                        'estado' => 'DISPONIBLE',
                    ]);
                    $creados++;
                }

                $timeStart->addMinutes($duracion);
            }
        }

        return back()->with('success', "Se generaron automáticamente {$creados} horarios para el médico.");
    }

    public function auditoria()
    {
        $auditorias = Auditoria::with('usuario')
            ->orderBy('fecha_hora', 'desc')
            ->paginate(15);

        return view('admin.auditoria', compact('auditorias'));
    }

    public function reportes(Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio', Carbon::today()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', Carbon::today()->format('Y-m-d'));

        $citasPorEstado = Cita::whereBetween('fecha_cita', [$fechaInicio, $fechaFin])
            ->selectRaw('estado, count(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado');

        $citasPorEspecialidad = Cita::join('medicos', 'citas.id_medico', '=', 'medicos.id_medico')
            ->join('especialidades', 'medicos.id_especialidad', '=', 'especialidades.id_especialidad')
            ->whereBetween('fecha_cita', [$fechaInicio, $fechaFin])
            ->selectRaw('especialidades.nombre as especialidad, count(*) as total')
            ->groupBy('especialidades.nombre')
            ->pluck('total', 'especialidad');

        $pagosTotal = Pago::whereBetween('created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])->sum('monto_pagado');

        return view('admin.reportes', compact('citasPorEstado', 'citasPorEspecialidad', 'pagosTotal', 'fechaInicio', 'fechaFin'));
    }

    public function exportarReporte(Request $request)
    {
        $tipo = $request->get('tipo', 'csv');
        $citas = Cita::with(['paciente.usuario', 'medico.usuario', 'medico.especialidad'])->get();

        if ($tipo === 'csv') {
            $filename = "reporte_citas_" . date('Ymd_His') . ".csv";
            $handle = fopen('php://output', 'w');
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            fputcsv($handle, ['ID Cita', 'Paciente', 'CI', 'Especialidad', 'Medico', 'Fecha Cita', 'Hora', 'Estado']);

            foreach ($citas as $c) {
                fputcsv($handle, [
                    $c->id_cita,
                    $c->paciente->usuario->nombre . ' ' . $c->paciente->usuario->apellido,
                    $c->paciente->ci,
                    $c->medico->especialidad->nombre ?? 'N/A',
                    $c->medico->usuario->nombre . ' ' . $c->medico->usuario->apellido,
                    $c->fecha_cita->format('Y-m-d'),
                    $c->hora_cita,
                    $c->estado
                ]);
            }
            fclose($handle);
            exit;
        }

        return redirect()->back()->with('success', 'Reporte exportado exitosamente.');
    }

    public function configuracionIndex()
    {
        $configuraciones = Configuracion::all();
        return view('admin.configuracion', compact('configuraciones'));
    }

    public function configuracionUpdate(Request $request)
    {
        foreach ($request->except('_token') as $clave => $valor) {
            Configuracion::where('clave', $clave)->update(['valor' => $valor]);
        }

        return back()->with('success', 'Configuraciones de la clínica actualizadas correctamente.');
    }
}
