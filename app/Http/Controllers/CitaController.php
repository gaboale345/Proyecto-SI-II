<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cita;
use App\Models\Agenda;
use App\Models\Especialidad;
use App\Models\Medico;
use App\Models\Paciente;
use App\Models\Notificacion;
use App\Models\Auditoria;
use Carbon\Carbon;

class CitaController extends Controller
{
    // Muestra pantalla para solicitar cita
    public function solicitarForm(Request $request)
    {
        $especialidades = Especialidad::where('estado', 'ACTIVO')->get();

        $selectedEspecialidad = $request->get('especialidad_id');
        $selectedFecha = $request->get('fecha', Carbon::today()->format('Y-m-d'));
        $selectedMedico = $request->get('medico_id');

        $medicos = collect();
        $turnosDisponibles = collect();

        if ($selectedEspecialidad) {
            $medicos = Medico::with('usuario')
                ->where('id_especialidad', $selectedEspecialidad)
                ->where('estado', 'ACTIVO')
                ->get();
        }

        if ($selectedEspecialidad && $selectedFecha) {
            $query = Agenda::with(['medico.usuario', 'medico.especialidad'])
                ->where('fecha', $selectedFecha)
                ->where('estado', 'DISPONIBLE')
                ->where('disponibles', '>', 0)
                ->whereHas('medico', function ($q) use ($selectedEspecialidad) {
                    $q->where('id_especialidad', $selectedEspecialidad)->where('estado', 'ACTIVO');
                });

            if ($selectedMedico) {
                $query->where('id_medico', $selectedMedico);
            }

            $turnosDisponibles = $query->orderBy('hora_inicio', 'asc')->get();
        }

        return view('citas.solicitar', compact('especialidades', 'medicos', 'turnosDisponibles', 'selectedEspecialidad', 'selectedFecha', 'selectedMedico'));
    }

    // Procesa la reserva de cita
    public function solicitarCita(Request $request)
    {
        $data = $request->validate([
            'id_agenda' => 'required|exists:agendas,id_agenda',
            'observaciones' => 'nullable|string|max:300',
            'id_paciente' => 'nullable|exists:pacientes,id_paciente', // Para que personal de ventanilla reserve a otro paciente
        ]);

        $user = Auth::user();
        
        // Determinar el paciente
        if ($user->isPaciente()) {
            $paciente = $user->paciente;
        } else {
            // Admin / Ventanilla reservando
            if (!empty($data['id_paciente'])) {
                $paciente = Paciente::find($data['id_paciente']);
            } else {
                return back()->with('error', 'Debe seleccionar un paciente para reservar la cita.');
            }
        }

        if (!$paciente) {
            return back()->with('error', 'Perfil de paciente no válido.');
        }

        $agenda = Agenda::with('medico.usuario', 'medico.especialidad')->findOrFail($data['id_agenda']);

        if ($agenda->disponibles <= 0 || $agenda->estado !== 'DISPONIBLE') {
            return back()->with('error', 'El turno seleccionado ya no se encuentra disponible. Por favor elija otro.');
        }

        // Verificar si el paciente ya tiene cita con el mismo médico en la misma fecha
        $citaExistente = Cita::where('id_paciente', $paciente->id_paciente)
            ->where('fecha_cita', $agenda->fecha)
            ->whereIn('estado', ['SOLICITADA', 'CONFIRMADA'])
            ->first();

        if ($citaExistente) {
            return back()->with('error', 'Ya tiene una cita médica agendada para la fecha ' . $agenda->fecha);
        }

        // Crear la cita
        $cita = Cita::create([
            'id_paciente' => $paciente->id_paciente,
            'id_medico' => $agenda->id_medico,
            'id_agenda' => $agenda->id_agenda,
            'fecha_solicitud' => now(),
            'fecha_cita' => $agenda->fecha,
            'hora_cita' => $agenda->hora_inicio,
            'estado' => 'CONFIRMADA',
            'observaciones' => $data['observaciones'] ?? 'Reserva en línea',
        ]);

        // Decrementar cupos en agenda
        $agenda->decrement('disponibles');
        if ($agenda->disponibles <= 0) {
            $agenda->update(['estado' => 'COMPLETO']);
        }

        // Generar Notificación automática (Simulación SMS / WhatsApp / Correo)
        $medicoNombre = $agenda->medico->usuario->nombre_completo;
        $especialidadNombre = $agenda->medico->especialidad->nombre;
        $mensaje = "Hospital Municipal Plan 3000: Cita CONFIRMADA para {$paciente->usuario->nombre_completo}. Especialidad: {$especialidadNombre} con Dr(a). {$medicoNombre}. Fecha: {$cita->fecha_cita} a las {$cita->hora_cita}. Ref #{$cita->id_cita}.";

        Notificacion::create([
            'id_cita' => $cita->id_cita,
            'id_paciente' => $paciente->id_paciente,
            'tipo' => 'CONFIRMACION',
            'canal' => 'WHATSAPP',
            'mensaje' => $mensaje,
            'estado' => 'ENVIADO',
        ]);

        Notificacion::create([
            'id_cita' => $cita->id_cita,
            'id_paciente' => $paciente->id_paciente,
            'tipo' => 'RECORDATORIO',
            'canal' => 'SMS',
            'mensaje' => "Recordatorio Plan 3000: Su cita de {$especialidadNombre} es el {$cita->fecha_cita} a las {$cita->hora_cita}. Por favor acuda 15 minutos antes.",
            'estado' => 'ENVIADO',
        ]);

        // Registrar auditoría
        Auditoria::create([
            'id_usuario' => $user->id_usuario,
            'accion' => 'CREAR_CITA',
            'tabla_afectada' => 'citas',
            'registro_afectado' => $cita->id_cita,
            'detalle' => json_encode(['mensaje' => "Cita reservada para paciente {$paciente->ci} en especialidad {$especialidadNombre}"]),
            'ip_origen' => $request->ip(),
        ]);

        $redirectRoute = $user->isPaciente() ? 'paciente.citas.historial' : 'ventanilla.dashboard';
        return redirect()->route($redirectRoute)->with('success', "Cita reservada exitosamente (Ref: #{$cita->id_cita}). Se envió la confirmación por WhatsApp y SMS.");
    }

    // Cancelación de cita
    public function cancelarCita(Request $request, $id)
    {
        $cita = Cita::with(['agenda', 'paciente.usuario', 'medico.usuario', 'medico.especialidad'])->findOrFail($id);
        $user = Auth::user();

        // Validar propiedad si es paciente
        if ($user->isPaciente() && $cita->id_paciente !== optional($user->paciente)->id_paciente) {
            return back()->with('error', 'Acceso no autorizado para esta cita.');
        }

        $motivo = $request->input('motivo_cancelacion', 'Cancelación por el usuario');

        $cita->update([
            'estado' => 'CANCELADA',
            'motivo_cancelacion' => $motivo,
        ]);

        // Restaurar disponible en agenda
        if ($cita->agenda) {
            $cita->agenda->increment('disponibles');
            if ($cita->agenda->estado === 'COMPLETO') {
                $cita->agenda->update(['estado' => 'DISPONIBLE']);
            }
        }

        // Notificación de cancelación
        Notificacion::create([
            'id_cita' => $cita->id_cita,
            'id_paciente' => $cita->id_paciente,
            'tipo' => 'ALERTA',
            'canal' => 'SMS',
            'mensaje' => "Hospital Plan 3000: Su cita Ref #{$cita->id_cita} para {$cita->medico->especialidad->nombre} el {$cita->fecha_cita} ha sido CANCELADA. El turno ha sido liberado.",
            'estado' => 'ENVIADO',
        ]);

        Auditoria::create([
            'id_usuario' => $user->id_usuario,
            'accion' => 'CANCELAR_CITA',
            'tabla_afectada' => 'citas',
            'registro_afectado' => $cita->id_cita,
            'detalle' => json_encode(['motivo' => $motivo]),
            'ip_origen' => $request->ip(),
        ]);

        return back()->with('success', 'La cita fue cancelada correctamente y el turno fue liberado.');
    }

    // Historial de citas del paciente
    public function historial()
    {
        $user = Auth::user();
        $paciente = $user->paciente;

        if (!$paciente) {
            return redirect()->route('login');
        }

        $citas = Cita::with(['medico.usuario', 'medico.especialidad', 'agenda'])
            ->where('id_paciente', $paciente->id_paciente)
            ->orderBy('fecha_cita', 'desc')
            ->orderBy('hora_cita', 'desc')
            ->paginate(10);

        return view('paciente.historial', compact('citas'));
    }
}
