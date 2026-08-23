<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Agenda;
use App\Models\Medico;
use App\Models\Especialidad;
use App\Models\Cita;
use App\Models\Notificacion;
use App\Models\Auditoria;
use Carbon\Carbon;

class AgendaController extends Controller
{
    public function index(Request $request)
    {
        $medicoId = $request->get('medico_id');
        $especialidadId = $request->get('especialidad_id');
        $fechaInicio = $request->get('fecha_inicio', Carbon::today()->format('Y-m-d'));

        $especialidades = Especialidad::where('estado', 'ACTIVO')->get();

        $medicosQuery = Medico::with(['usuario', 'especialidad'])->where('estado', 'ACTIVO');
        if ($especialidadId) {
            $medicosQuery->where('id_especialidad', $especialidadId);
        }
        $medicos = $medicosQuery->get();

        $agendas = collect();
        if ($medicoId) {
            $fechaFin = Carbon::parse($fechaInicio)->addDays(6)->format('Y-m-d');
            $agendas = Agenda::with(['medico.usuario', 'citas.paciente.usuario'])
                ->where('id_medico', $medicoId)
                ->whereBetween('fecha', [$fechaInicio, $fechaFin])
                ->orderBy('fecha', 'asc')
                ->orderBy('hora_inicio', 'asc')
                ->get();
        }

        return view('agendas.index', compact('especialidades', 'medicos', 'agendas', 'medicoId', 'especialidadId', 'fechaInicio'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_medico' => 'required|exists:medicos,id_medico',
            'fecha' => 'required|date|after_or_equal:today',
            'hora_inicio' => 'required',
            'hora_fin' => 'required|after:hora_inicio',
            'capacidad' => 'required|integer|min:1|max:10',
        ]);

        // Verificar si ya existe bloque en esa fecha y horario para el mismo médico
        $existe = Agenda::where('id_medico', $data['id_medico'])
            ->where('fecha', $data['fecha'])
            ->where('hora_inicio', $data['hora_inicio'])
            ->exists();

        if ($existe) {
            return back()->with('error', 'Ya existe un bloque de agenda registrado en esa fecha y hora para el médico.');
        }

        Agenda::create([
            'id_medico' => $data['id_medico'],
            'fecha' => $data['fecha'],
            'hora_inicio' => $data['hora_inicio'],
            'hora_fin' => $data['hora_fin'],
            'capacidad' => $data['capacidad'],
            'disponibles' => $data['capacidad'],
            'estado' => 'DISPONIBLE',
        ]);

        Auditoria::create([
            'id_usuario' => Auth::id(),
            'accion' => 'CREAR_AGENDA',
            'tabla_afectada' => 'agendas',
            'registro_afectado' => $data['id_medico'],
            'detalle' => json_encode(['fecha' => $data['fecha'], 'hora' => $data['hora_inicio']]),
            'ip_origen' => $request->ip(),
        ]);

        return back()->with('success', 'Nuevo bloque de agenda creado exitosamente.');
    }

    public function bloquear(Request $request, $id)
    {
        $agenda = Agenda::with(['medico.usuario', 'citas.paciente.usuario'])->findOrFail($id);

        $motivo = $request->input('motivo_bloqueo', 'Suspensión o paro médico programado');

        $agenda->update([
            'estado' => 'BLOQUEADO',
            'motivo_bloqueo' => $motivo,
        ]);

        // Si existen citas asociadas en ese bloque, cancelarlas y notificar a los pacientes
        foreach ($agenda->citas as $cita) {
            if (in_array($cita->estado, ['SOLICITADA', 'CONFIRMADA'])) {
                $cita->update([
                    'estado' => 'CANCELADA',
                    'motivo_cancelacion' => 'SUSPENSIÓN: ' . $motivo,
                ]);

                Notificacion::create([
                    'id_cita' => $cita->id_cita,
                    'id_paciente' => $cita->id_paciente,
                    'tipo' => 'SUSPENSION',
                    'canal' => 'WHATSAPP',
                    'mensaje' => "ALERTA HOSPITAL PLAN 3000: Su cita de fecha {$cita->fecha_cita} a las {$cita->hora_cita} fue suspendida debido a: {$motivo}. Le solicitamos ingresar al portal para reprogramar su turno.",
                    'estado' => 'ENVIADO',
                ]);
            }
        }

        Auditoria::create([
            'id_usuario' => Auth::id(),
            'accion' => 'BLOQUEAR_AGENDA',
            'tabla_afectada' => 'agendas',
            'registro_afectado' => $agenda->id_agenda,
            'detalle' => json_encode(['motivo' => $motivo, 'citas_afectadas' => $agenda->citas->count()]),
            'ip_origen' => $request->ip(),
        ]);

        return back()->with('success', 'El bloque de agenda fue BLOQUEADO y se notificó a todos los pacientes afectados.');
    }
}
