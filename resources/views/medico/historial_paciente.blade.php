@extends('layouts.app')

@section('title', 'Historial Clínico del Paciente - Hospital Plan 3000')

@section('content')
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h3 class="card-title" style="margin: 0; color: var(--primary-dark);">
                <i class="fa-solid fa-folder-open"></i> Expediente e Historial Clínico Integral
            </h3>
            <span style="font-size: 0.88rem; color: #64748b;">
                Paciente: <strong>{{ $paciente->usuario->nombre_completo }}</strong> | C.I.: <strong>{{ $paciente->ci }}</strong>
            </span>
        </div>
        <a href="{{ route('medico.agenda') }}" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Volver a mi Agenda
        </a>
    </div>

    <!-- Ficha Resumen del Paciente y Antecedentes -->
    <div style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 1.25rem;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <div>
                <span style="font-size: 0.78rem; color: #64748b; text-transform: uppercase; font-weight: 600;">Fecha de Nacimiento:</span>
                <div style="font-weight: 600; color: #1e293b;">
                    {{ $paciente->fecha_nacimiento }} 
                    <span style="font-size: 0.8rem; color: #64748b;">({{ \Carbon\Carbon::parse($paciente->fecha_nacimiento)->age }} años)</span>
                </div>
            </div>
            <div>
                <span style="font-size: 0.78rem; color: #64748b; text-transform: uppercase; font-weight: 600;">Sexo / Género:</span>
                <div style="font-weight: 600; color: #1e293b;">{{ $paciente->sexo ?? $paciente->genero }}</div>
            </div>
            <div>
                <span style="font-size: 0.78rem; color: #64748b; text-transform: uppercase; font-weight: 600;">Grupo Sanguíneo:</span>
                <div style="font-weight: 700; color: #b91c1c;">{{ $expediente->grupo_sanguineo ?? 'No registrado' }}</div>
            </div>
            <div>
                <span style="font-size: 0.78rem; color: #64748b; text-transform: uppercase; font-weight: 600;">Teléfono / Contacto:</span>
                <div style="font-weight: 600; color: #1e293b;">{{ $paciente->usuario->telefono }}</div>
            </div>
        </div>

        <hr style="margin: 0.85rem 0; border: 0; border-top: 1px solid #e2e8f0;">

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; font-size: 0.88rem;">
            <div style="background: #fff; padding: 0.75rem; border-radius: 6px; border: 1px solid #e2e8f0;">
                <strong style="color: #b91c1c;"><i class="fa-solid fa-triangle-exclamation"></i> Alergias Conocidas:</strong>
                <p style="margin: 0.25rem 0 0 0; color: #334155;">{{ $expediente->alergias ?? 'Ninguna alergia reportada' }}</p>
            </div>
            <div style="background: #fff; padding: 0.75rem; border-radius: 6px; border: 1px solid #e2e8f0;">
                <strong style="color: #0369a1;"><i class="fa-solid fa-notes-medical"></i> Antecedentes Patológicos:</strong>
                <p style="margin: 0.25rem 0 0 0; color: #334155;">{{ $expediente->antecedentes_patologicos ?? 'Sin antecedentes registrados' }}</p>
            </div>
            <div style="background: #fff; padding: 0.75rem; border-radius: 6px; border: 1px solid #e2e8f0;">
                <strong style="color: #475569;"><i class="fa-solid fa-heart-pulse"></i> Hábitos Tóxicos / FRC:</strong>
                <p style="margin: 0.25rem 0 0 0; color: #334155;">{{ $expediente->habitos_toxicos ?? 'Sin hábitos de riesgo reportados' }}</p>
            </div>
        </div>
    </div>

    <!-- Lista Cronológica de Consultas Médicas -->
    <div style="padding: 1.25rem;">
        <h4 style="margin: 0 0 1rem 0; color: #1e293b; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fa-solid fa-timeline" style="color: var(--primary);"></i> Consultas y Atenciones Médicas Previas ({{ $consultas->count() }})
        </h4>

        @if($consultas->count() > 0)
            <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                @foreach($consultas as $c)
                    <div style="border: 1px solid #cbd5e1; border-radius: 8px; overflow: hidden; background: #ffffff; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                        <!-- Cabecera de la Consulta -->
                        <div style="background: #f1f5f9; padding: 0.75rem 1rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem; border-bottom: 1px solid #e2e8f0;">
                            <div>
                                <span class="badge" style="background: var(--primary); color: #fff; font-size: 0.8rem; padding: 0.3rem 0.6rem; border-radius: 4px;">
                                    <i class="fa-solid fa-stethoscope"></i> {{ $c->especialidad->nombre ?? 'Consulta General' }}
                                </span>
                                <strong style="margin-left: 0.5rem; color: #0f172a; font-size: 0.95rem;">
                                    Dr(a). {{ $c->medico->usuario->nombre_completo ?? 'Médico Asignado' }}
                                </strong>
                            </div>
                            <div style="font-size: 0.85rem; color: #475569; font-weight: 500;">
                                <i class="fa-regular fa-calendar-days"></i> {{ \Carbon\Carbon::parse($c->fecha_hora)->format('d/m/Y H:i') }}
                            </div>
                        </div>

                        <div style="padding: 1rem;">
                            <!-- Diagnósticos y Motivo -->
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem; margin-bottom: 1rem;">
                                <div>
                                    <div style="font-size: 0.78rem; color: #64748b; font-weight: 600;">MOTIVO DE CONSULTA:</div>
                                    <div style="font-size: 0.9rem; color: #1e293b; margin-top: 0.2rem;">{{ $c->motivo_consulta }}</div>
                                </div>
                                <div>
                                    <div style="font-size: 0.78rem; color: #64748b; font-weight: 600;">DIAGNÓSTICO PRINCIPAL:</div>
                                    <div style="font-size: 0.95rem; color: #0369a1; font-weight: 700; margin-top: 0.2rem;">{{ $c->diagnostico_principal }}</div>
                                    @if($c->diagnostico_secundario)
                                        <div style="font-size: 0.82rem; color: #64748b;">Secundario: {{ $c->diagnostico_secundario }}</div>
                                    @endif
                                </div>
                            </div>

                            <!-- DETALLES SEGÚN ESPECIALIDAD -->
                            @if($c->cardiologia)
                                <div style="background: #fdf2f8; border: 1px solid #fbcfe8; padding: 0.75rem; border-radius: 6px; margin-bottom: 1rem;">
                                    <strong style="color: #9d174d; font-size: 0.85rem;"><i class="fa-solid fa-heart-pulse"></i> Evaluación Cardiológica:</strong>
                                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 0.5rem; margin-top: 0.5rem; font-size: 0.85rem;">
                                        <div><strong>PA:</strong> {{ $c->cardiologia->presion_arterial ?? 'N/A' }} mmHg</div>
                                        <div><strong>FC:</strong> {{ $c->cardiologia->frecuencia_cardiaca ?? 'N/A' }} lpm</div>
                                        <div><strong>FR:</strong> {{ $c->cardiologia->frecuencia_respiratoria ?? 'N/A' }} rpm</div>
                                        <div><strong>SatO2:</strong> {{ $c->cardiologia->saturacion_oxigeno ?? 'N/A' }}%</div>
                                        <div><strong>Ritmo:</strong> {{ $c->cardiologia->ritmo ?? 'Regular' }}</div>
                                        <div><strong>Soplos:</strong> {{ $c->cardiologia->soplos ?? 'No' }}</div>
                                        <div><strong>Edemas:</strong> {{ $c->cardiologia->edemas ?? 'No' }}</div>
                                    </div>
                                </div>
                            @endif

                            @if($c->pediatria)
                                <div style="background: #f0fdf4; border: 1px solid #bbf7d0; padding: 0.75rem; border-radius: 6px; margin-bottom: 1rem;">
                                    <strong style="color: #166534; font-size: 0.85rem;"><i class="fa-solid fa-baby"></i> Evaluación Pediátrica:</strong>
                                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 0.5rem; margin-top: 0.5rem; font-size: 0.85rem;">
                                        <div><strong>Peso:</strong> {{ $c->pediatria->peso ?? 'N/A' }} kg</div>
                                        <div><strong>Talla:</strong> {{ $c->pediatria->talla ?? 'N/A' }} cm</div>
                                        <div><strong>P. Cefálico:</strong> {{ $c->pediatria->perimetro_cefalico ?? 'N/A' }} cm</div>
                                        <div><strong>Percentil Peso:</strong> {{ $c->pediatria->percentil_peso ?? 'N/A' }}</div>
                                        <div><strong>Percentil Talla:</strong> {{ $c->pediatria->percentil_talla ?? 'N/A' }}</div>
                                        <div><strong>Acompañante:</strong> {{ $c->pediatria->responsable_nombre ?? 'N/A' }} ({{ $c->pediatria->responsable_relacion ?? 'Tutor' }})</div>
                                    </div>
                                </div>
                            @endif

                            @if($c->traumatologia)
                                <div style="background: #fffbeb; border: 1px solid #fde68a; padding: 0.75rem; border-radius: 6px; margin-bottom: 1rem;">
                                    <strong style="color: #92400e; font-size: 0.85rem;"><i class="fa-solid fa-bone"></i> Evaluación Traumatológica:</strong>
                                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 0.5rem; margin-top: 0.5rem; font-size: 0.85rem;">
                                        <div><strong>Zona:</strong> {{ $c->traumatologia->zona_afectada ?? 'N/A' }}</div>
                                        <div><strong>Mecanismo:</strong> {{ $c->traumatologia->mecanismo_lesion ?? 'N/A' }}</div>
                                        <div><strong>Escala Dolor:</strong> {{ $c->traumatologia->intensidad_dolor ?? '1' }}/10</div>
                                        <div><strong>Movilidad:</strong> {{ $c->traumatologia->movilidad ?? 'Conservada' }}</div>
                                        <div><strong>Inmovilización:</strong> {{ $c->traumatologia->indicacion_inmovilizacion ?? 'No requerida' }}</div>
                                    </div>
                                </div>
                            @endif

                            @if($c->ginecologia)
                                <div style="background: #faf5ff; border: 1px solid #e9d5ff; padding: 0.75rem; border-radius: 6px; margin-bottom: 1rem;">
                                    <strong style="color: #6b21a8; font-size: 0.85rem;"><i class="fa-solid fa-venus"></i> Evaluación Ginecológica / Obstétrica:</strong>
                                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 0.5rem; margin-top: 0.5rem; font-size: 0.85rem;">
                                        <div><strong>FUM:</strong> {{ $c->ginecologia->fum ?? 'N/A' }}</div>
                                        <div><strong>Ciclo:</strong> {{ $c->ginecologia->ciclo_menstrual ?? 'Regular' }}</div>
                                        <div><strong>G/P/C/A:</strong> {{ $c->ginecologia->gestas }}/{{ $c->ginecologia->partos }}/{{ $c->ginecologia->cesareas }}/{{ $c->ginecologia->abortos }}</div>
                                        <div><strong>Método:</strong> {{ $c->ginecologia->metodo_anticonceptivo ?? 'Ninguno' }}</div>
                                    </div>
                                </div>
                            @endif

                            @if($c->medicinaGeneral)
                                <div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 0.75rem; border-radius: 6px; margin-bottom: 1rem;">
                                    <strong style="color: #334155; font-size: 0.85rem;"><i class="fa-solid fa-notes-medical"></i> Signos Vitales & Examen Físico General:</strong>
                                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 0.5rem; margin-top: 0.5rem; font-size: 0.85rem;">
                                        <div><strong>PA:</strong> {{ $c->medicinaGeneral->presion_arterial ?? 'N/A' }}</div>
                                        <div><strong>FC:</strong> {{ $c->medicinaGeneral->frecuencia_cardiaca ?? 'N/A' }} lpm</div>
                                        <div><strong>Temp:</strong> {{ $c->medicinaGeneral->temperatura ?? 'N/A' }} °C</div>
                                        <div><strong>SatO2:</strong> {{ $c->medicinaGeneral->saturacion_oxigeno ?? 'N/A' }}%</div>
                                        <div><strong>IMC:</strong> {{ $c->medicinaGeneral->imc ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            @endif

                            <!-- Tratamiento y Receta -->
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem; font-size: 0.88rem;">
                                <div>
                                    <strong>Plan de Tratamiento / Indicaciones:</strong>
                                    <p style="margin: 0.25rem 0 0 0; color: #475569;">
                                        {{ $c->plan_tratamiento ?? $c->indicaciones ?? 'Sin indicaciones registradas' }}
                                    </p>
                                </div>
                                <div>
                                    <strong>Medicamentos Prescritos:</strong>
                                    @if($c->receta && $c->receta->items->count() > 0)
                                        <ul style="margin: 0.25rem 0 0 1rem; padding: 0; color: #1e293b;">
                                            @foreach($c->receta->items as $item)
                                                <li>
                                                    <strong>{{ $item->nombre_medicamento }}</strong> 
                                                    ({{ $item->dosis }} - {{ $item->frecuencia }} por {{ $item->duracion }})
                                                    <span style="font-size: 0.75rem; color: {{ $item->estado_item == 'ENTREGADO' ? '#166534' : '#b45309' }};">
                                                        [{{ $item->estado_item }}]
                                                    </span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @elseif(!empty($c->medicamentos_recetados) && count($c->medicamentos_recetados) > 0)
                                        <ul style="margin: 0.25rem 0 0 1rem; padding: 0; color: #1e293b;">
                                            @foreach($c->medicamentos_recetados as $med)
                                                <li><strong>{{ $med['nombre'] ?? '' }}</strong> ({{ $med['dosis'] ?? '' }} - {{ $med['frecuencia'] ?? '' }})</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p style="margin: 0.25rem 0 0 0; color: #94a3b8;">No se prescribieron medicamentos en esta consulta.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div style="text-align: center; padding: 3rem; background: #f8fafc; border-radius: 8px; border: 1px dashed #cbd5e1; color: #64748b;">
                <i class="fa-solid fa-file-circle-question" style="font-size: 2.5rem; color: #cbd5e1; margin-bottom: 0.5rem;"></i>
                <p style="margin: 0; font-weight: 500;">Este paciente aún no registra consultas médicas previas en el sistema.</p>
            </div>
        @endif
    </div>
</div>
@endsection
