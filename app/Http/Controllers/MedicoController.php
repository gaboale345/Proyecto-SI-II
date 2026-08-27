<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cita;
use App\Models\Consulta;
use App\Models\ConsultaCardiologia;
use App\Models\ConsultaPediatria;
use App\Models\ConsultaMedicinaGeneral;
use App\Models\ConsultaGinecologia;
use App\Models\ConsultaTraumatologia;
use App\Models\Documento;
use App\Models\ExpedienteMedico;
use App\Models\Auditoria;
use Carbon\Carbon;

class MedicoController extends Controller
{
    public function agendaPersonal(Request $request)
    {
        $user = Auth::user();
        $medico = $user->medico;

        if (!$medico) {
            return redirect()->route('login')->with('error', 'No se encontró perfil médico asociado.');
        }

        $fechaSeleccionada = $request->get('fecha', Carbon::today()->format('Y-m-d'));

        $citas = Cita::with(['paciente.usuario', 'paciente.expediente', 'agenda', 'consultorio', 'consulta'])
            ->where('id_medico', $medico->id_medico)
            ->where('fecha_cita', $fechaSeleccionada)
            ->orderBy('hora_cita', 'asc')
            ->get();

        $statsHoy = [
            'total' => $citas->count(),
            'en_espera' => $citas->where('estado', 'EN_ESPERA')->count(),
            'atendidas' => $citas->where('estado', 'ATENDIDA')->count(),
            'pendientes' => $citas->whereIn('estado', ['SOLICITADA', 'CONFIRMADA'])->count(),
            'canceladas' => $citas->where('estado', 'CANCELADA')->count(),
        ];

        return view('medico.agenda', compact('medico', 'citas', 'fechaSeleccionada', 'statsHoy'));
    }

    public function cambiarEstadoCita(Request $request, $id)
    {
        $data = $request->validate([
            'estado' => 'required|in:EN_ESPERA,EN_CONSULTA,ATENDIDA,NO_ASISTIO,CONFIRMADA',
            'observaciones' => 'nullable|string|max:300',
        ]);

        $cita = Cita::findOrFail($id);

        $updateData = [
            'estado' => $data['estado'],
            'observaciones' => $data['observaciones'] ?? $cita->observaciones,
        ];

        if ($data['estado'] === 'EN_CONSULTA') {
            $updateData['hora_atencion'] = now();
        }

        $cita->update($updateData);

        Auditoria::create([
            'id_usuario' => Auth::user()->id_usuario,
            'accion' => 'CAMBIAR_ESTADO_CITA',
            'tabla_afectada' => 'citas',
            'registro_afectado' => $cita->id_cita,
            'detalle' => json_encode(['nuevo_estado' => $data['estado']]),
            'ip_origen' => $request->ip(),
        ]);

        return back()->with('success', "El estado de la cita Ref #{$cita->id_cita} se actualizó a '{$data['estado']}'.");
    }

    public function atenderConsultaForm($id)
    {
        $user = Auth::user();
        $medico = $user->medico;

        $cita = Cita::with(['paciente.usuario', 'paciente.expediente', 'medico.especialidad'])
            ->where('id_medico', $medico->id_medico)
            ->where('id_cita', $id)
            ->firstOrFail();

        $expediente = ExpedienteMedico::firstOrCreate(['id_paciente' => $cita->id_paciente]);

        // Determinar disciplina según especialidad
        $especialidadNombre = mb_strtolower($cita->medico->especialidad->nombre ?? 'medicina general');

        return view('medico.atender_consulta', compact('cita', 'expediente', 'especialidadNombre'));
    }

    public function guardarConsulta(Request $request, $id)
    {
        $cita = Cita::with(['paciente.usuario', 'medico.especialidad'])->findOrFail($id);

        $request->validate([
            'motivo_consulta' => 'required|string',
            'diagnostico_principal' => 'required|string',
            'diagnostico_secundario' => 'nullable|string',
            'plan_tratamiento' => 'nullable|string',
            'indicaciones' => 'nullable|string',
            'medicamentos_nombre' => 'nullable|array',
            'medicamentos_dosis' => 'nullable|array',
            'medicamentos_frecuencia' => 'nullable|array',
            'medicamentos_duracion' => 'nullable|array',
            'incapacidad_dias' => 'nullable|integer',
            'certificado_medico' => 'nullable|string',
            'proximo_control' => 'nullable|date',
        ]);

        // Formatear array de medicamentos recetados
        $medicamentos = [];
        if ($request->has('medicamentos_nombre')) {
            foreach ($request->medicamentos_nombre as $index => $nombre) {
                if (!empty($nombre)) {
                    $medicamentos[] = [
                        'nombre' => $nombre,
                        'dosis' => $request->medicamentos_dosis[$index] ?? '',
                        'frecuencia' => $request->medicamentos_frecuencia[$index] ?? '',
                        'duracion' => $request->medicamentos_duracion[$index] ?? '',
                    ];
                }
            }
        }

        // Crear Consulta ECE principal
        $consulta = Consulta::create([
            'id_cita' => $cita->id_cita,
            'id_paciente' => $cita->id_paciente,
            'id_medico' => $cita->id_medico,
            'id_especialidad' => $cita->medico->id_especialidad,
            'fecha_hora' => now(),
            'motivo_consulta' => $request->motivo_consulta,
            'diagnostico_principal' => $request->diagnostico_principal,
            'diagnostico_secundario' => $request->diagnostico_secundario,
            'diagnostico_diferencial' => $request->diagnostico_diferencial,
            'plan_tratamiento' => $request->plan_tratamiento,
            'indicaciones' => $request->indicaciones,
            'medicamentos_recetados' => $medicamentos,
            'incapacidad_dias' => $request->incapacidad_dias ?? 0,
            'certificado_medico' => $request->certificado_medico,
            'proximo_control' => $request->proximo_control,
        ]);

        // Guardar especialidad específica
        $esp = mb_strtolower($cita->medico->especialidad->nombre ?? 'medicina general');

        if (str_contains($esp, 'cardio')) {
            ConsultaCardiologia::create([
                'id_consulta' => $consulta->id_consulta,
                'presion_arterial' => $request->presion_arterial,
                'frecuencia_cardiaca' => $request->frecuencia_cardiaca,
                'frecuencia_respiratoria' => $request->frecuencia_respiratoria,
                'saturacion_oxigeno' => $request->saturacion_oxigeno,
                'peso' => $request->peso,
                'talla' => $request->talla,
                'imc' => $request->imc,
                'temperatura' => $request->temperatura,
                'sintomas' => $request->sintomas_cardio ?? [],
                'ruidos_cardiacos' => $request->ruidos_cardiacos,
                'ritmo' => $request->ritmo,
                'soplos' => $request->soplos,
                'pulsos' => $request->pulsos,
                'edemas' => $request->edemas,
                'estudios_solicitados' => $request->estudios_cardio ?? [],
            ]);
        } elseif (str_contains($esp, 'pedia')) {
            ConsultaPediatria::create([
                'id_consulta' => $consulta->id_consulta,
                'responsable_nombre' => $request->responsable_nombre,
                'responsable_relacion' => $request->responsable_relacion,
                'responsable_contacto' => $request->responsable_contacto,
                'peso' => $request->peso,
                'talla' => $request->talla,
                'perimetro_cefalico' => $request->perimetro_cefalico,
                'percentil_peso' => $request->percentil_peso,
                'percentil_talla' => $request->percentil_talla,
                'antecedentes_perinatales' => $request->antecedentes_perinatales,
                'desarrollo_observaciones' => $request->desarrollo_observaciones,
            ]);
        } elseif (str_contains($esp, 'gineco')) {
            ConsultaGinecologia::create([
                'id_consulta' => $consulta->id_consulta,
                'fum' => $request->fum,
                'ciclo_menstrual' => $request->ciclo_menstrual,
                'gestas' => $request->gestas ?? 0,
                'partos' => $request->partos ?? 0,
                'cesareas' => $request->cesareas ?? 0,
                'abortos' => $request->abortos ?? 0,
                'metodo_anticonceptivo' => $request->metodo_anticonceptivo,
                'resultado_papanicolaou' => $request->resultado_papanicolaou,
                'resultado_ecografia' => $request->resultado_ecografia,
                'exploracion_ginecológica' => $request->exploracion_ginecológica,
            ]);
        } elseif (str_contains($esp, 'trauma')) {
            ConsultaTraumatologia::create([
                'id_consulta' => $consulta->id_consulta,
                'zona_afectada' => $request->zona_afectada,
                'mecanismo_lesion' => $request->mecanismo_lesion,
                'intensidad_dolor' => $request->intensidad_dolor ?? 1,
                'movilidad' => $request->movilidad,
                'fuerza_muscular' => $request->fuerza_muscular,
                'sensibilidad' => $request->sensibilidad,
                'deformidad' => $request->deformidad,
                'estado_neurovascular' => $request->estado_neurovascular,
                'indicacion_inmovilizacion' => $request->indicacion_inmovilizacion,
                'indicacion_fisioterapia' => $request->indicacion_fisioterapia,
            ]);
        } else {
            ConsultaMedicinaGeneral::create([
                'id_consulta' => $consulta->id_consulta,
                'presion_arterial' => $request->presion_arterial,
                'frecuencia_cardiaca' => $request->frecuencia_cardiaca,
                'frecuencia_respiratoria' => $request->frecuencia_respiratoria,
                'saturacion_oxigeno' => $request->saturacion_oxigeno,
                'temperatura' => $request->temperatura,
                'peso' => $request->peso,
                'talla' => $request->talla,
                'imc' => $request->imc,
                'exploracion_cabeza_cuello' => $request->exploracion_cabeza_cuello,
                'exploracion_cardiopulmonar' => $request->exploracion_cardiopulmonar,
                'exploracion_abdomen' => $request->exploracion_abdomen,
                'exploracion_neurologica' => $request->exploracion_neurologica,
                'exploracion_piel_faneras' => $request->exploracion_piel_faneras,
                'exploracion_musculoesqueletica' => $request->exploracion_musculoesqueletica,
            ]);
        }

        // Generar Documento Receta Médica si hay medicamentos
        if (count($medicamentos) > 0) {
            $htmlReceta = '<h3>HOSPITAL MUNICIPAL PLAN 3000 - RECETA MÉDICA</h3>';
            $htmlReceta .= '<p><strong>Paciente:</strong> ' . $cita->paciente->usuario->nombre . ' ' . $cita->paciente->usuario->apellido . '</p>';
            $htmlReceta .= '<p><strong>Médico:</strong> Dr(a). ' . Auth::user()->nombre . ' ' . Auth::user()->apellido . '</p>';
            $htmlReceta .= '<p><strong>Fecha:</strong> ' . now()->format('d/m/Y H:i') . '</p><hr>';
            $htmlReceta .= '<h4>Medicamentos Recetados:</h4><ul>';
            foreach ($medicamentos as $med) {
                $htmlReceta .= '<li><strong>' . e($med['nombre']) . '</strong> - Dosis: ' . e($med['dosis']) . ' | Frecuencia: ' . e($med['frecuencia']) . ' | Duración: ' . e($med['duracion']) . '</li>';
            }
            $htmlReceta .= '</ul><p><strong>Indicaciones:</strong> ' . e($request->indicaciones) . '</p>';

            Documento::create([
                'id_paciente' => $cita->id_paciente,
                'id_consulta' => $consulta->id_consulta,
                'id_medico' => $cita->id_medico,
                'tipo_documento' => 'RECETA',
                'titulo' => 'Receta Médica - ' . $cita->medico->especialidad->nombre,
                'contenido_html' => $htmlReceta,
                'codigo_verificacion' => 'REC-' . strtoupper(uniqid()),
                'autorizado_descarga' => true,
            ]);
        }

        // Actualizar Cita a ATENDIDA
        $cita->update(['estado' => 'ATENDIDA', 'hora_atencion' => now()]);

        return redirect()->route('medico.agenda')->with('success', 'Consulta registrada exitosamente en el Expediente Clínico del Paciente.');
    }
}
