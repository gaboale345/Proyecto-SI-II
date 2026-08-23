<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Agenda;
use App\Models\Cita;
use App\Models\Notificacion;
use App\Models\Auditoria;
use App\Models\Especialidad;
use App\Models\Medico;

class ContingenciaController extends Controller
{
    public function index()
    {
        $especialidades = Especialidad::where('estado', 'ACTIVO')->get();
        $medicos = Medico::with('usuario')->where('estado', 'ACTIVO')->get();

        $suspensionesRecientes = Agenda::with(['medico.usuario', 'medico.especialidad'])
            ->where('estado', 'BLOQUEADO')
            ->orderBy('fecha', 'desc')
            ->take(10)
            ->get();

        return view('agendas.contingencia', compact('especialidades', 'medicos', 'suspensionesRecientes'));
    }

    public function procesarSuspension(Request $request)
    {
        $data = $request->validate([
            'motivo' => 'required|string|max:200',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'id_medico' => 'nullable|exists:medicos,id_medico',
            'id_especialidad' => 'nullable|exists:especialidades,id_especialidad',
        ]);

        $query = Agenda::whereBetween('fecha', [$data['fecha_inicio'], $data['fecha_fin']]);

        if (!empty($data['id_medico'])) {
            $query->where('id_medico', $data['id_medico']);
        } elseif (!empty($data['id_especialidad'])) {
            $query->whereHas('medico', function ($q) use ($data) {
                $q->where('id_especialidad', $data['id_especialidad']);
            });
        }

        $agendasAf = $query->get();
        $citasNotificadas = 0;

        foreach ($agendasAf as $agenda) {
            $agenda->update([
                'estado' => 'BLOQUEADO',
                'motivo_bloqueo' => $data['motivo'],
            ]);

            $citas = Cita::with('paciente.usuario')->where('id_agenda', $agenda->id_agenda)
                ->whereIn('estado', ['SOLICITADA', 'CONFIRMADA'])
                ->get();

            foreach ($citas as $cita) {
                $cita->update([
                    'estado' => 'CANCELADA',
                    'motivo_cancelacion' => 'CONTINGENCIA INSTITUCIONAL: ' . $data['motivo'],
                ]);

                // Envío masivo de notificación
                Notificacion::create([
                    'id_cita' => $cita->id_cita,
                    'id_paciente' => $cita->id_paciente,
                    'tipo' => 'SUSPENSION',
                    'canal' => 'WHATSAPP',
                    'mensaje' => "COMUNICADO URGENTE HOSPITAL PLAN 3000: Estimado(a) {$cita->paciente->usuario->nombre}, su cita del {$cita->fecha_cita} a las {$cita->hora_cita} fue suspendida por: {$data['motivo']}. Puede ingresar al portal digital para reprogramar su turno.",
                    'estado' => 'ENVIADO',
                ]);
                $citasNotificadas++;
            }
        }

        Auditoria::create([
            'id_usuario' => Auth::id(),
            'accion' => 'SUSPENSION_MASIVA_CONTINGENCIA',
            'tabla_afectada' => 'agendas',
            'registro_afectado' => 0,
            'detalle' => json_encode([
                'motivo' => $data['motivo'],
                'bloques_afectados' => $agendasAf->count(),
                'pacientes_notificados' => $citasNotificadas,
            ]),
            'ip_origen' => $request->ip(),
        ]);

        return back()->with('success', "Proceso de contingencia completado: Se bloquearon {$agendasAf->count()} bloques de agenda y se notificó automáticamente a {$citasNotificadas} pacientes afectados.");
    }
}
