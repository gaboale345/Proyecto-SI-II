@extends('layouts.app')

@section('title', 'Reprogramar Cita - Hospital Plan 3000')

@section('content')
<div class="card" style="max-width: 700px; margin: 0 auto;">
    <div class="card-header">
        <div class="card-title">
            <i class="fa-solid fa-calendar-week text-primary"></i>
            <span>Reprogramar Cita Médica #{{ $cita->id_cita }}</span>
        </div>
    </div>

    <div style="background-color: var(--primary-light); padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border-left: 4px solid var(--primary);">
        <p style="font-size: 0.95rem; margin-bottom: 0.3rem;">
            <strong>Cita Actual:</strong> {{ $cita->medico->especialidad->nombre }} con Dr(a). {{ $cita->medico->usuario->nombre }} {{ $cita->medico->usuario->apellido }}
        </p>
        <p style="font-size: 0.88rem; color: #475569;">
            <strong>Fecha/Hora Anterior:</strong> {{ $cita->fecha_cita->format('d/m/Y') }} a las {{ $cita->hora_cita }}
        </p>
    </div>

    <form action="{{ route('paciente.citas.reprogramar', $cita->id_cita) }}" method="POST">
        @csrf

        <div class="form-group">
            <label class="form-label">Seleccionar Nuevo Horario Disponible</label>
            <select name="id_nueva_agenda" class="form-select" required size="6">
                @forelse($agendas as $ag)
                    <option value="{{ $ag->id_agenda }}">
                        📅 {{ \Carbon\Carbon::parse($ag->fecha)->format('d/m/Y') }} — ⏰ {{ $ag->hora_inicio }} a {{ $ag->hora_fin }} (Dr. {{ $cita->medico->usuario->nombre }} {{ $cita->medico->usuario->apellido }})
                    </option>
                @empty
                    <option value="" disabled>No hay nuevos cupos disponibles para este médico en los próximos días.</option>
                @endforelse
            </select>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1.5rem;">
            <a href="{{ route('paciente.citas.historial') }}" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Cancelar
            </a>
            @if($agendas->count() > 0)
                <button type="submit" class="btn btn-accent">
                    <i class="fa-solid fa-clock-rotate-left"></i> Confirmar Reprogramación
                </button>
            @endif
        </div>
    </form>
</div>
@endsection
