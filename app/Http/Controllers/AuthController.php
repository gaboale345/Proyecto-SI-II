<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;
use App\Models\Paciente;
use App\Models\Role;
use App\Models\Auditoria;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => 'required|string', // CI o Email
            'password' => 'required|string',
        ]);

        $loginInput = $credentials['login'];

        // Buscar por email o por CI (a través de la relación paciente)
        $user = Usuario::where('email', $loginInput)
            ->orWhereHas('paciente', function ($q) use ($loginInput) {
                $q->where('ci', $loginInput);
            })
            ->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            if ($user->estado !== 'ACTIVO') {
                return back()->withErrors(['login' => 'Su cuenta se encuentra inactiva o bloqueada. Contacte al administrador.']);
            }

            Auth::login($user, $request->has('remember'));
            $user->update(['ultimo_acceso' => now()]);

            // Registrar auditoría
            Auditoria::create([
                'id_usuario' => $user->id_usuario,
                'accion' => 'INICIO_SESION',
                'tabla_afectada' => 'usuarios',
                'registro_afectado' => $user->id_usuario,
                'detalle' => json_encode(['mensaje' => 'Inicio de sesión exitoso']),
                'ip_origen' => $request->ip(),
                'user_agent' => substr($request->userAgent() ?? '', 0, 200),
            ]);

            return redirect()->route('dashboard')->with('success', 'Bienvenido al sistema digital del Hospital Plan 3000');
        }

        return back()->withErrors(['login' => 'Las credenciales ingresadas son incorrectas (Cédula de Identidad/Correo o contraseña).']);
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'ci' => 'required|string|max:15|unique:pacientes,ci',
            'nombre' => 'required|string|max:50',
            'apellido' => 'required|string|max:50',
            'fecha_nacimiento' => 'required|date',
            'genero' => 'required|string|in:MASCULINO,FEMENINO,OTRO',
            'telefono' => 'required|string|max:20',
            'direccion' => 'nullable|string|max:150',
            'email' => 'required|email|max:100|unique:usuarios,email',
            'password' => 'required|string|min:6|confirmed',
            'telefono_emergencia' => 'nullable|string|max:20',
            'contacto_emergencia' => 'nullable|string|max:100',
        ], [
            'email.unique' => 'El correo electrónico ya se encuentra registrado por otro paciente.',
            'ci.unique' => 'La Cédula de Identidad (CI) ya se encuentra registrada por otro paciente.',
        ]);

        $rolPaciente = Role::where('nombre_rol', 'PACIENTE')->first();

        $usuario = Usuario::create([
            'id_rol' => $rolPaciente->id_rol,
            'nombre' => $data['nombre'],
            'apellido' => $data['apellido'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'telefono' => $data['telefono'],
            'estado' => 'ACTIVO',
            'fecha_registro' => now(),
        ]);

        Paciente::create([
            'id_usuario' => $usuario->id_usuario,
            'ci' => $data['ci'],
            'fecha_nacimiento' => $data['fecha_nacimiento'],
            'genero' => $data['genero'],
            'direccion' => $data['direccion'] ?? null,
            'telefono_emergencia' => $data['telefono_emergencia'] ?? null,
            'contacto_emergencia' => $data['contacto_emergencia'] ?? null,
        ]);

        Auth::login($usuario);

        Auditoria::create([
            'id_usuario' => $usuario->id_usuario,
            'accion' => 'REGISTRO_PACIENTE',
            'tabla_afectada' => 'pacientes',
            'registro_afectado' => $usuario->paciente->id_paciente,
            'detalle' => json_encode(['mensaje' => 'Nuevo paciente registrado']),
            'ip_origen' => $request->ip(),
            'user_agent' => substr($request->userAgent() ?? '', 0, 200),
        ]);

        return redirect()->route('paciente.dashboard')->with('success', 'Registro completado con éxito. Ahora puede reservar citas médicas.');
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            Auditoria::create([
                'id_usuario' => Auth::id(),
                'accion' => 'CIERRE_SESION',
                'tabla_afectada' => 'usuarios',
                'registro_afectado' => Auth::id(),
                'ip_origen' => $request->ip(),
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Sesión cerrada correctamente.');
    }
}
