@extends('layouts.app')

@section('title', 'Historial de Citas - Hospital Plan 3000')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fa-solid fa-clock-rotate-left"></i> Historial de Citas Médicas</h3>
        <a href="{{ route('paciente.citas.solicitar') }}" class="btn btn-accent btn-sm">
            <i class="fa-solid fa-plus"></i> Nueva Cita
        </a>
    </div>

    @if($citas->count() > 0)
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Ref #</th>
                        <th>Especialidad</th>
                        <th>Médico</th>
                        <th>Fecha Cita</th>
                        <th>Hora</th>
                        <th>Estado</th>
                        <th>Observaciones</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($citas as $cita)
                    <tr>
                        <td><strong>#{{ $cita->id_cita }}</strong></td>
                        <td>
                            <strong style="color: var(--primary-dark);">{{ $cita->medico->especialidad->nombre }}</strong>
                        </td>
                        <td>Dr(a). {{ $cita->medico->usuario->nombre_completo }}</td>
                        <td>{{ \Carbon\Carbon::parse($cita->fecha_cita)->format('d/m/Y') }}</td>
                        <td><strong>{{ substr($cita->hora_cita, 0, 5) }}</strong></td>
                        <td>
                            <span class="status-badge status-{{ $cita->estado }}">{{ $cita->estado }}</span>
                        </td>
                        <td style="font-size: 0.85rem; color: #64748b;">
                            {{ $cita->observaciones ?? '-' }}
                            @if($cita->motivo_cancelacion)
                                <br><small style="color: var(--danger);">Motivo: {{ $cita->motivo_cancelacion }}</small>
                            @endif
                        </td>
                        <td>
                            @if(in_array($cita->estado, ['SOLICITADA', 'CONFIRMADA', 'REPROGRAMADA']) && $cita->fecha_cita >= date('Y-m-d'))
                                <form action="{{ route('paciente.citas.cancelar', $cita->id_cita) }}" method="POST" onsubmit="return confirm('¿Confirma que desea cancelar esta cita?');">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="fa-solid fa-ban"></i> Cancelar
                                    </button>
                                </form>
                            @else
                                <span style="font-size: 0.8rem; color: #94a3b8;">Sin acciones</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top: 1rem;">
            {{ $citas->links() }}
        </div>
    @else
        <div style="text-align: center; padding: 2rem; color: #64748b;">
            <p>No cuenta con historial de citas registradas.</p>
        </div>
    @endif
</div>
@endsection
