<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Paciente;
use App\Models\ExpedienteMedico;
use App\Models\Cita;
use App\Models\Consulta;
use App\Models\Documento;
use App\Models\Agenda;
use Carbon\Carbon;

class PacienteController extends Controller
{
    public function perfil()
    {
        $user = Auth::user();
        $paciente = Paciente::where('id_usuario', $user->id_usuario)->firstOrFail();
        $expediente = ExpedienteMedico::firstOrCreate(['id_paciente' => $paciente->id_paciente]);

        return view('paciente.perfil', compact('paciente', 'expediente', 'user'));
    }

    public function updatePerfil(Request $request)
    {
        $user = Auth::user();
        $paciente = Paciente::where('id_usuario', $user->id_usuario)->firstOrFail();

        $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'fecha_nacimiento' => 'required|date',
            'genero' => 'required|string',
            'sexo' => 'required|string',
            'nacionalidad' => 'required|string',
            'estado_civil' => 'required|string',
            'ocupacion' => 'nullable|string',
            'direccion' => 'nullable|string',
            'ciudad' => 'required|string',
            'telefono_emergencia' => 'nullable|string',
            'whatsapp' => 'nullable|string',
            'contacto_emergencia' => 'nullable|string',
            'relacion_contacto' => 'nullable|string',
            'tipo_sangre' => 'nullable|string',
            'alergias' => 'nullable|string',
            'alergias_medicamentosas' => 'nullable|string',
            'enfermedades_cronicas' => 'nullable|string',
            'antecedentes_personales' => 'nullable|string',
            'antecedentes_familiares' => 'nullable|string',
            'cirugias_previas' => 'nullable|string',
            'hospitalizaciones' => 'nullable|string',
            'medicamentos_actuales' => 'nullable|string',
            'habitos' => 'nullable|string',
        ]);

        // Actualizar usuario
        $user->update([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'telefono' => $request->telefono,
        ]);

        // Actualizar datos paciente
        $paciente->update($request->only([
            'fecha_nacimiento', 'genero', 'sexo', 'nacionalidad', 'estado_civil',
            'ocupacion', 'direccion', 'ciudad', 'telefono_emergencia', 'whatsapp',
            'contacto_emergencia', 'relacion_contacto'
        ]));

        // Actualizar expediente médico
        $expediente = ExpedienteMedico::firstOrCreate(['id_paciente' => $paciente->id_paciente]);
        $expediente->update($request->only([
            'tipo_sangre', 'alergias', 'alergias_medicamentosas', 'enfermedades_cronicas',
            'antecedentes_personales', 'antecedentes_familiares', 'cirugias_previas',
            'hospitalizaciones', 'medicamentos_actuales', 'habitos'
        ]));

        return redirect()->back()->with('success', 'Perfil y expediente médico actualizados correctamente.');
    }

    public function historialClinico()
    {
        $user = Auth::user();
        $paciente = Paciente::where('id_usuario', $user->id_usuario)->firstOrFail();
        $consultas = Consulta::with(['medico.usuario', 'especialidad', 'cardiologia', 'pediatria', 'medicinaGeneral', 'ginecologia', 'traumatologia'])
            ->where('id_paciente', $paciente->id_paciente)
            ->orderBy('fecha_hora', 'desc')
            ->get();

        return view('paciente.historial_clinico', compact('paciente', 'consultas'));
    }

    public function reprogramarCitaForm($id)
    {
        $user = Auth::user();
        $paciente = Paciente::where('id_usuario', $user->id_usuario)->firstOrFail();
        $cita = Cita::with(['medico.usuario', 'medico.especialidad'])
            ->where('id_paciente', $paciente->id_paciente)
            ->where('id_cita', $id)
            ->firstOrFail();

        $agendas = Agenda::where('id_medico', $cita->id_medico)
            ->where('fecha', '>=', now()->format('Y-m-d'))
            ->where('disponibles', '>', 0)
            ->where('estado', 'DISPONIBLE')
            ->orderBy('fecha')
            ->orderBy('hora_inicio')
            ->get();

        return view('paciente.reprogramar_cita', compact('cita', 'agendas'));
    }

    public function reprogramarCita(Request $request, $id)
    {
        $request->validate([
            'id_nueva_agenda' => 'required|exists:agendas,id_agenda'
        ]);

        $user = Auth::user();
        $paciente = Paciente::where('id_usuario', $user->id_usuario)->firstOrFail();
        $citaOriginal = Cita::where('id_paciente', $paciente->id_paciente)->where('id_cita', $id)->firstOrFail();

        $nuevaAgenda = Agenda::where('id_agenda', $request->id_nueva_agenda)
            ->where('disponibles', '>', 0)
            ->firstOrFail();

        // Liberar agenda vieja
        if ($citaOriginal->id_agenda) {
            $oldAgenda = Agenda::find($citaOriginal->id_agenda);
            if ($oldAgenda) {
                $oldAgenda->increment('disponibles');
                $oldAgenda->update(['estado' => 'DISPONIBLE']);
            }
        }

        // Marcar cita anterior como REPROGRAMADA
        $citaOriginal->update(['estado' => 'REPROGRAMADA', 'motivo_cancelacion' => 'Reprogramada por el paciente']);

        // Ocupar nueva agenda
        $nuevaAgenda->decrement('disponibles');
        if ($nuevaAgenda->disponibles <= 0) {
            $nuevaAgenda->update(['estado' => 'COMPLETO']);
        }

        // Crear nueva cita vinculada
        Cita::create([
            'id_paciente' => $paciente->id_paciente,
            'id_medico' => $nuevaAgenda->id_medico,
            'id_agenda' => $nuevaAgenda->id_agenda,
            'fecha_solicitud' => now(),
            'fecha_cita' => $nuevaAgenda->fecha,
            'hora_cita' => $nuevaAgenda->hora_inicio,
            'estado' => 'SOLICITADA',
            'id_cita_original' => $citaOriginal->id_cita,
            'observaciones' => 'Cita reprogramada a partir de Cita #' . $citaOriginal->id_cita,
        ]);

        return redirect()->route('paciente.citas.historial')->with('success', 'Tu cita ha sido reprogramada exitosamente.');
    }

    public function documentos()
    {
        $user = Auth::user();
        $paciente = Paciente::where('id_usuario', $user->id_usuario)->firstOrFail();
        $documentos = Documento::with(['medico.usuario', 'consulta'])
            ->where('id_paciente', $paciente->id_paciente)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('paciente.documentos', compact('paciente', 'documentos'));
    }

    public function verDocumento($id)
    {
        $user = Auth::user();
        $paciente = Paciente::where('id_usuario', $user->id_usuario)->firstOrFail();
        $documento = Documento::with(['paciente.usuario', 'medico.usuario', 'consulta.especialidad'])
            ->where('id_paciente', $paciente->id_paciente)
            ->where('id_documento', $id)
            ->firstOrFail();

        return view('pdf.documento_vista', compact('documento'));
    }
}
