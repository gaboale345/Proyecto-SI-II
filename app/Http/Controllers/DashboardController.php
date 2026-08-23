<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cita;
use App\Models\Notificacion;
use App\Models\Usuario;
use App\Models\Paciente;
use App\Models\Medico;
use App\Models\Especialidad;
use App\Models\Auditoria;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isMedico()) {
            return redirect()->route('medico.agenda');
        } elseif ($user->isCallCenter()) {
            return redirect()->route('ventanilla.dashboard');
        } else {
            return redirect()->route('paciente.dashboard');
        }
    }

    public function pacienteDashboard()
    {
        $user = Auth::user();
        $paciente = $user->paciente;

        if (!$paciente) {
            return redirect()->route('login')->with('error', 'No se encontró el perfil de paciente asociado.');
        }

        $proximasCitas = Cita::with(['medico.usuario', 'medico.especialidad', 'agenda'])
            ->where('id_paciente', $paciente->id_paciente)
            ->whereIn('estado', ['SOLICITADA', 'CONFIRMADA', 'REPROGRAMADA'])
            ->where('fecha_cita', '>=', Carbon::today()->format('Y-m-d'))
            ->orderBy('fecha_cita', 'asc')
            ->orderBy('hora_cita', 'asc')
            ->get();

        $notificaciones = Notificacion::where('id_paciente', $paciente->id_paciente)
            ->orderBy('fecha_envio', 'desc')
            ->take(5)
            ->get();

        $totalCitasRealizadas = Cita::where('id_paciente', $paciente->id_paciente)
            ->where('estado', 'ATENDIDA')
            ->count();

        return view('paciente.dashboard', compact('user', 'paciente', 'proximasCitas', 'notificaciones', 'totalCitasRealizadas'));
    }

    public function ventanillaDashboard()
    {
        $today = Carbon::today()->format('Y-m-d');

        $citasHoy = Cita::with(['paciente.usuario', 'medico.usuario', 'medico.especialidad'])
            ->where('fecha_cita', $today)
            ->orderBy('hora_cita', 'asc')
            ->get();

        $totalPacientes = Paciente::count();
        $especialidades = Especialidad::where('estado', 'ACTIVO')->get();

        return view('ventanilla.dashboard', compact('citasHoy', 'totalPacientes', 'especialidades'));
    }

    public function adminDashboard()
    {
        $today = Carbon::today()->format('Y-m-d');

        $stats = [
            'total_usuarios' => Usuario::count(),
            'total_pacientes' => Paciente::count(),
            'total_medicos' => Medico::where('estado', 'ACTIVO')->count(),
            'citas_hoy' => Cita::where('fecha_cita', $today)->count(),
            'citas_atendidas_hoy' => Cita::where('fecha_cita', $today)->where('estado', 'ATENDIDA')->count(),
            'citas_canceladas_hoy' => Cita::where('fecha_cita', $today)->where('estado', 'CANCELADA')->count(),
        ];

        $ultimasAuditorias = Auditoria::with('usuario')
            ->orderBy('fecha_hora', 'desc')
            ->take(10)
            ->get();

        $especialidadesPopulares = Especialidad::withCount('medicos')->get();

        return view('admin.dashboard', compact('stats', 'ultimasAuditorias', 'especialidadesPopulares'));
    }
}
