@extends('layouts.app')

@section('title', 'Agenda Médica - Hospital Plan 3000')

@section('content')
<div class="card">
    <div class="card-header">
        <div>
            <h3 class="card-title"><i class="fa-solid fa-user-doctor"></i> Agenda Personal del Dr(a). {{ $medico->usuario->nombre_completo }}</h3>
            <span style="font-size: 0.85rem; color: #64748b;">Especialidad: <strong>{{ $medico->especialidad->nombre }}</strong> | Colegiatura: {{ $medico->numero_colegiatura }}</span>
        </div>

        <form action="{{ route('medico.agenda') }}" method="GET" style="display: flex; gap: 0.5rem; align-items: center;">
            <label class="form-label" style="margin: 0; font-size: 0.85rem;">Fecha:</label>
            <input type="date" name="fecha" class="form-control" value="{{ $fechaSeleccionada }}" onchange="this.form.submit()" style="padding: 0.4rem 0.6rem; font-size: 0.88rem;">
        </form>
    </div>

    <!-- Quick Stats -->
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.25rem;">
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 0.75rem 1rem; border-radius: 8px;">
            <div style="font-size: 0.8rem; color: #64748b;">Total Pacientes Agendados</div>
            <div style="font-size: 1.4rem; font-weight: 700; color: var(--primary-dark);">{{ $statsHoy['total'] }}</div>
        </div>
        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; padding: 0.75rem 1rem; border-radius: 8px;">
            <div style="font-size: 0.8rem; color: #166534;">Atendidos</div>
            <div style="font-size: 1.4rem; font-weight: 700; color: #166534;">{{ $statsHoy['atendidas'] }}</div>
        </div>
        <div style="background: #e0f2fe; border: 1px solid #bae6fd; padding: 0.75rem 1rem; border-radius: 8px;">
            <div style="font-size: 0.8rem; color: #0369a1;">Pendientes</div>
            <div style="font-size: 1.4rem; font-weight: 700; color: #0369a1;">{{ $statsHoy['pendientes'] }}</div>
        </div>
        <div style="background: #fee2e2; border: 1px solid #fca5a5; padding: 0.75rem 1rem; border-radius: 8px;">
            <div style="font-size: 0.8rem; color: #991b1b;">Cancelados / Ausentes</div>
            <div style="font-size: 1.4rem; font-weight: 700; color: #991b1b;">{{ $statsHoy['canceladas'] }}</div>
        </div>
    </div>

    @if($citas->count() > 0)
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Hora</th>
                        <th>Paciente</th>
                        <th>Cédula (CI)</th>
                        <th>Teléfono Contacto</th>
                        <th>Observaciones / Motivo</th>
                        <th>Estado Actual</th>
                        <th>Acción Médica</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($citas as $c)
                    <tr>
                        <td><strong><i class="fa-regular fa-clock"></i> {{ substr($c->hora_cita, 0, 5) }}</strong></td>
                        <td>
                            <strong>{{ $c->paciente->usuario->nombre_completo }}</strong>
                            <div style="font-size: 0.78rem; color: #64748b;">Género: {{ $c->paciente->genero }} | F. Nac: {{ $c->paciente->fecha_nacimiento }}</div>
                        </td>
                        <td>{{ $c->paciente->ci }}</td>
                        <td>{{ $c->paciente->usuario->telefono }}</td>
                        <td style="font-size: 0.85rem; color: #475569;">{{ $c->observaciones ?? 'Consulta programada' }}</td>
                        <td><span class="status-badge status-{{ $c->estado }}">{{ $c->estado }}</span></td>
                        <td>
                            @if(in_array($c->estado, ['SOLICITADA', 'CONFIRMADA', 'EN_ESPERA', 'EN_CONSULTA']))
                                <div style="display: flex; gap: 0.35rem; flex-wrap: wrap;">
                                    <a href="{{ route('medico.cita.atender', $c->id_cita) }}" class="btn btn-sm btn-primary">
                                        <i class="fa-solid fa-stethoscope"></i> Atender ECE
                                    </a>

                                    <a href="{{ route('medico.paciente.historial', $c->id_paciente) }}" class="btn btn-sm btn-outline-info" title="Ver Historial Clínico Completo">
                                        <i class="fa-solid fa-file-medical"></i> Historial
                                    </a>

                                    <form action="{{ route('medico.cita.estado', $c->id_cita) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="estado" value="NO_ASISTIO">
                                        <button type="submit" class="btn btn-sm btn-secondary" title="Marcar Inasistencia">
                                            <i class="fa-solid fa-user-xmark"></i> Ausente
                                        </button>
                                    </form>
                                </div>
                            @else
                                <div style="display: flex; gap: 0.35rem; align-items: center;">
                                    <span style="font-size: 0.8rem; color: #166534; font-weight: bold;"><i class="fa-solid fa-check"></i> Atendida</span>
                                    <a href="{{ route('medico.paciente.historial', $c->id_paciente) }}" class="btn btn-sm btn-outline-info" title="Ver Historial Clínico">
                                        <i class="fa-solid fa-file-medical"></i>
                                    </a>
                                </div>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div style="text-align: center; padding: 2.5rem; color: #64748b;">
            <i class="fa-regular fa-calendar-check" style="font-size: 2.5rem; color: #cbd5e1; margin-bottom: 0.5rem;"></i>
            <p style="font-size: 1rem; font-weight: 500;">No hay pacientes agendados para el {{ \Carbon\Carbon::parse($fechaSeleccionada)->format('d/m/Y') }}.</p>
        </div>
    @endif
</div>
@endsection
