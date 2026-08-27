@extends('layouts.app')

@section('title', 'Historial Médico Cronológico - Hospital Plan 3000')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <i class="fa-solid fa-notes-medical text-primary"></i>
            <span>Historial Clínico Electrónico (ECE)</span>
        </div>
        <span class="badge-role badge-paciente">Paciente: {{ $paciente->usuario->nombre }} {{ $paciente->usuario->apellido }}</span>
    </div>

    @if($consultas->count() == 0)
        <div style="text-align: center; padding: 3rem 1rem; color: #64748b;">
            <i class="fa-solid fa-folder-open" style="font-size: 3rem; margin-bottom: 1rem; color: #cbd5e1;"></i>
            <h3>Aún no registra consultas médicas atendidas</h3>
            <p>Una vez que sea atendido por nuestros profesionales, sus diagnósticos, indicaciones y recetas se mostrarán aquí de forma cronológica.</p>
        </div>
    @else
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            @foreach($consultas as $c)
                <div style="border: 1px solid #cbd5e1; border-radius: 10px; padding: 1.25rem; background-color: #fafafa; position: relative;">
                    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px dashed #cbd5e1; padding-bottom: 0.75rem; margin-bottom: 1rem;">
                        <div>
                            <span style="font-size: 1.1rem; font-weight: 700; color: var(--primary-dark);">
                                <i class="fa-solid fa-stethoscope"></i> {{ $c->especialidad->nombre }}
                            </span>
                            <div style="font-size: 0.85rem; color: #64748b;">
                                <i class="fa-solid fa-user-doctor"></i> Dr(a). {{ $c->medico->usuario->nombre }} {{ $c->medico->usuario->apellido }} (Reg: {{ $c->medico->numero_colegiatura }})
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <span class="badge-role" style="background-color: var(--primary); color: white;">
                                <i class="fa-regular fa-calendar-check"></i> {{ $c->fecha_hora->format('d/m/Y H:i') }}
                            </span>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1rem;">
                        <div>
                            <p style="font-size: 0.88rem; margin-bottom: 0.4rem;">
                                <strong>Motivo de Consulta:</strong> {{ $c->motivo_consulta }}
                            </p>
                            <p style="font-size: 0.88rem; margin-bottom: 0.4rem;">
                                <strong style="color: var(--primary);">Diagnóstico Principal:</strong> {{ $c->diagnostico_principal }}
                            </p>
                            @if($c->diagnostico_secundario)
                                <p style="font-size: 0.85rem; color: #475569; margin-bottom: 0.4rem;">
                                    <strong>Diagnóstico Secundario:</strong> {{ $c->diagnostico_secundario }}
                                </p>
                            @endif
                            @if($c->plan_tratamiento)
                                <p style="font-size: 0.88rem; margin-bottom: 0.4rem;">
                                    <strong>Plan de Tratamiento:</strong> {{ $c->plan_tratamiento }}
                                </p>
                            @endif
                        </div>

                        <div>
                            @if($c->medicamentos_recetados && count($c->medicamentos_recetados) > 0)
                                <div style="background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 0.75rem; margin-bottom: 0.5rem;">
                                    <strong style="color: #166534;"><i class="fa-solid fa-pills"></i> Medicamentos Recetados:</strong>
                                    <ul style="font-size: 0.85rem; margin-left: 1.25rem; margin-top: 0.3rem;">
                                        @foreach($c->medicamentos_recetados as $m)
                                            <li><strong>{{ $m['nombre'] }}</strong> ({{ $m['dosis'] }}) - {{ $m['frecuencia'] }} por {{ $m['duracion'] }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if($c->indicaciones)
                                <p style="font-size: 0.85rem; background: #fffbe6; padding: 0.5rem; border-radius: 6px; border: 1px solid #ffe58f;">
                                    <i class="fa-solid fa-circle-info text-warning"></i> <strong>Indicaciones:</strong> {{ $c->indicaciones }}
                                </p>
                            @endif

                            @if($c->proximo_control)
                                <div style="margin-top: 0.5rem; font-size: 0.85rem; color: var(--primary);">
                                    <i class="fa-solid fa-clock"></i> <strong>Próximo Control:</strong> {{ \Carbon\Carbon::parse($c->proximo_control)->format('d/m/Y') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
