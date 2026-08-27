@extends('layouts.app')

@section('title', 'Sala de Espera y Recepción de Pacientes - Hospital Plan 3000')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <i class="fa-solid fa-users-rectangle text-primary"></i>
            <span>Monitor de Sala de Espera y Cola de Atención Presencial</span>
        </div>
        <div>
            <a href="{{ route('ventanilla.walkin') }}" class="btn btn-accent btn-sm">
                <i class="fa-solid fa-user-plus"></i> Registrar Paciente Presencial (Walk-in)
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
        <div style="background: #e0f2fe; padding: 1rem; border-radius: 10px; border-left: 4px solid #0284c7;">
            <div style="font-size: 0.82rem; color: #0369a1; font-weight: 600;">CITAS HOY</div>
            <div style="font-size: 1.75rem; font-weight: 700; color: #075985;">{{ $citasHoy->count() }}</div>
        </div>
        <div style="background: #fef3c7; padding: 1rem; border-radius: 10px; border-left: 4px solid #d97706;">
            <div style="font-size: 0.82rem; color: #b45309; font-weight: 600;">EN SALA DE ESPERA</div>
            <div style="font-size: 1.75rem; font-weight: 700; color: #92400e;">{{ $enEspera->count() }}</div>
        </div>
        <div style="background: #dcfce7; padding: 1rem; border-radius: 10px; border-left: 4px solid #16a34a;">
            <div style="font-size: 0.82rem; color: #15803d; font-weight: 600;">CONFIRMADAS PENDIENTES</div>
            <div style="font-size: 1.75rem; font-weight: 700; color: #166534;">{{ $confirmadas->count() }}</div>
        </div>
        <div style="background: #f0fdf4; padding: 1rem; border-radius: 10px; border-left: 4px solid #22c55e;">
            <div style="font-size: 0.82rem; color: #166534; font-weight: 600;">ATENDIDAS HOY</div>
            <div style="font-size: 1.75rem; font-weight: 700; color: #14532d;">{{ $atendidas->count() }}</div>
        </div>
    </div>

    <h4 style="color: var(--primary); margin-bottom: 1rem;">
        <i class="fa-solid fa-list-check"></i> Cola de Citas del Día ({{ date('d/m/Y') }})
    </h4>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Hora</th>
                    <th>Paciente</th>
                    <th>CI</th>
                    <th>Especialidad</th>
                    <th>Médico Asignado</th>
                    <th>Consultorio</th>
                    <th>Estado Cita</th>
                    <th>Pago</th>
                    <th>Acción Recepción</th>
                </tr>
            </thead>
            <tbody>
                @forelse($citasHoy as $c)
                    <tr style="{{ $c->estado == 'EN_ESPERA' ? 'background-color: #fffbe6;' : '' }}">
                        <td><strong>{{ $c->hora_cita }}</strong></td>
                        <td>
                            <strong>{{ $c->paciente->usuario->nombre }} {{ $c->paciente->usuario->apellido }}</strong>
                        </td>
                        <td><code>{{ $c->paciente->ci }}</code></td>
                        <td>{{ $c->medico->especialidad->nombre }}</td>
                        <td>Dr(a). {{ $c->medico->usuario->nombre }} {{ $c->medico->usuario->apellido }}</td>
                        <td>
                            @if($c->consultorio)
                                <span class="badge-role" style="background: #e2e8f0; color: #1e293b;">
                                    <i class="fa-solid fa-door-open"></i> {{ $c->consultorio->nombre_numero }}
                                </span>
                            @else
                                <span style="color: #94a3b8; font-size: 0.85rem;">Sin asignar</span>
                            @endif
                        </td>
                        <td>
                            <span class="status-badge status-{{ $c->estado }}">
                                {{ $c->estado }}
                            </span>
                        </td>
                        <td>
                            @if($c->pago)
                                <span class="status-badge status-CONFIRMADA">
                                    PAGADO ({{ $c->pago->metodo_pago }})
                                </span>
                            @else
                                <span class="status-badge status-CANCELADA">
                                    PENDIENTE
                                </span>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('ventanilla.llegada', $c->id_cita) }}" method="POST" style="display: flex; gap: 0.3rem;">
                                @csrf
                                @if($c->estado == 'CONFIRMADA' || $c->estado == 'SOLICITADA')
                                    <input type="hidden" name="estado" value="EN_ESPERA">
                                    <select name="id_consultorio" class="form-select" style="padding: 0.2rem 0.4rem; font-size: 0.8rem; width: auto;">
                                        <option value="">Consultorio...</option>
                                        @foreach($consultorios as $cons)
                                            <option value="{{ $cons->id_consultorio }}">{{ $cons->nombre_numero }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-accent" title="Registrar llegada a sala de espera">
                                        <i class="fa-solid fa-chair"></i> En Espera
                                    </button>
                                @elseif($c->estado == 'EN_ESPERA')
                                    <input type="hidden" name="estado" value="EN_CONSULTA">
                                    <button type="submit" class="btn btn-sm btn-success" title="Pasar al consultorio médico">
                                        <i class="fa-solid fa-arrow-right-to-bracket"></i> En Consulta
                                    </button>
                                @else
                                    <span style="font-size: 0.8rem; color: #94a3b8;">Finalizado</span>
                                @endif
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 2rem; color: #94a3b8;">
                            No hay citas programadas para la fecha de hoy.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
