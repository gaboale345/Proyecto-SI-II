@extends('layouts.app')

@section('title', 'Panel del Paciente - Hospital Plan 3000')

@section('content')
<div style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; background: white; padding: 1.25rem 1.5rem; border-radius: 12px; box-shadow: var(--card-shadow);">
    <div>
        <h2 style="color: var(--primary-dark); font-weight: 700;">Bienvenido, {{ $user->nombre_completo }}</h2>
        <p style="color: #64748b; font-size: 0.9rem;">Cédula de Identidad: <strong>{{ $paciente->ci }}</strong> | Teléfono: {{ $user->telefono }}</p>
    </div>
    <a href="{{ route('paciente.citas.solicitar') }}" class="btn btn-accent" style="padding: 0.75rem 1.25rem; font-size: 0.95rem;">
        <i class="fa-solid fa-calendar-plus"></i> SOLICITAR NUEVA CITA
    </a>
</div>

<!-- Summary Cards -->
<div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.25rem; margin-bottom: 1.5rem;">
    <div class="card" style="margin-bottom: 0; display: flex; align-items: center; gap: 1rem; border-left: 4px solid var(--primary);">
        <div style="width: 50px; height: 50px; border-radius: 10px; background: var(--primary-light); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
            <i class="fa-solid fa-calendar-check"></i>
        </div>
        <div>
            <div style="font-size: 1.8rem; font-weight: 700; color: var(--primary-dark);">{{ $proximasCitas->count() }}</div>
            <div style="font-size: 0.85rem; color: #64748b;">Próximas Citas Programadas</div>
        </div>
    </div>

    <div class="card" style="margin-bottom: 0; display: flex; align-items: center; gap: 1rem; border-left: 4px solid var(--success);">
        <div style="width: 50px; height: 50px; border-radius: 10px; background: #ecfdf5; color: var(--success); display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
            <i class="fa-solid fa-user-check"></i>
        </div>
        <div>
            <div style="font-size: 1.8rem; font-weight: 700; color: var(--primary-dark);">{{ $totalCitasRealizadas }}</div>
            <div style="font-size: 0.85rem; color: #64748b;">Consultas Atendidas</div>
        </div>
    </div>

    <div class="card" style="margin-bottom: 0; display: flex; align-items: center; gap: 1rem; border-left: 4px solid var(--warning);">
        <div style="width: 50px; height: 50px; border-radius: 10px; background: #fffbe6; color: var(--warning); display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
            <i class="fa-solid fa-bell"></i>
        </div>
        <div>
            <div style="font-size: 1.8rem; font-weight: 700; color: var(--primary-dark);">{{ $notificaciones->count() }}</div>
            <div style="font-size: 0.85rem; color: #64748b;">Notificaciones Recientes</div>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
    <!-- Próximas Citas -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fa-solid fa-clock"></i> Próximas Citas Médicas</h3>
            <a href="{{ route('paciente.citas.historial') }}" style="color: var(--primary); font-size: 0.88rem; text-decoration: none; font-weight: 600;">Ver Historial Completo &rarr;</a>
        </div>

        @if($proximasCitas->count() > 0)
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                @foreach($proximasCitas as $cita)
                <div style="border: 1px solid #e2e8f0; border-radius: 10px; padding: 1.1rem; display: flex; justify-content: space-between; align-items: center; background: #fafafa;">
                    <div>
                        <div style="display: flex; align-items: center; gap: 0.6rem; margin-bottom: 0.4rem;">
                            <span class="status-badge status-{{ $cita->estado }}">{{ $cita->estado }}</span>
                            <span style="font-size: 0.82rem; color: #64748b;">Ref: #{{ $cita->id_cita }}</span>
                        </div>
                        <h4 style="font-size: 1.05rem; color: var(--primary-dark); font-weight: 700;">
                            {{ $cita->medico->especialidad->nombre }}
                        </h4>
                        <p style="font-size: 0.88rem; color: #475569; margin-top: 0.2rem;">
                            <i class="fa-solid fa-user-md" style="color: var(--accent);"></i> Dr(a). {{ $cita->medico->usuario->nombre_completo }}
                        </p>
                        <p style="font-size: 0.85rem; color: #64748b; margin-top: 0.2rem;">
                            <i class="fa-regular fa-calendar" style="color: var(--primary);"></i> <strong>{{ \Carbon\Carbon::parse($cita->fecha_cita)->format('d/m/Y') }}</strong> a las <strong>{{ substr($cita->hora_cita, 0, 5) }} hrs</strong>
                        </p>
                    </div>

                    <div style="display: flex; gap: 0.5rem; flex-direction: column; align-items: flex-end;">
                        <form action="{{ route('paciente.citas.cancelar', $cita->id_cita) }}" method="POST" onsubmit="return confirm('¿Está seguro que desea cancelar esta cita médica?');">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="fa-solid fa-xmark"></i> Cancelar Cita
                            </button>
                        </form>
                        <a href="{{ route('paciente.citas.solicitar') }}" class="btn btn-sm btn-secondary">
                            <i class="fa-solid fa-arrows-rotate"></i> Reprogramar
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div style="text-align: center; padding: 2.5rem 1rem; color: #64748b;">
                <i class="fa-regular fa-calendar-xmark" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 0.75rem;"></i>
                <p style="font-size: 1rem; font-weight: 500;">No tiene citas médicas pendientes agendadas.</p>
                <p style="font-size: 0.85rem; margin-top: 0.25rem;">Haga clic en el botón de abajo para consultar disponibilidad de turnos.</p>
                <a href="{{ route('paciente.citas.solicitar') }}" class="btn btn-primary" style="margin-top: 1rem;">
                    <i class="fa-solid fa-calendar-plus"></i> Reservar Turno Ahora
                </a>
            </div>
        @endif
    </div>

    <!-- Notificaciones Recientes -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fa-solid fa-bell"></i> Notificaciones</h3>
        </div>

        @if($notificaciones->count() > 0)
            <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                @foreach($notificaciones as $noti)
                <div style="padding: 0.85rem; border-radius: 8px; background: #f8fafc; border-left: 3px solid {{ $noti->tipo == 'SUSPENSION' ? 'var(--danger)' : ($noti->tipo == 'CONFIRMACION' ? 'var(--success)' : 'var(--accent)') }};">
                    <div style="display: flex; justify-content: space-between; font-size: 0.78rem; color: #64748b; margin-bottom: 0.25rem;">
                        <span style="font-weight: 700; text-transform: uppercase;">{{ $noti->tipo }} ({{ $noti->canal }})</span>
                        <span>{{ \Carbon\Carbon::parse($noti->fecha_envio)->diffForHumans() }}</span>
                    </div>
                    <p style="font-size: 0.84rem; color: #334155; line-height: 1.35;">{{ $noti->mensaje }}</p>
                </div>
                @endforeach
            </div>
        @else
            <p style="font-size: 0.88rem; color: #94a3b8; text-align: center; padding: 1.5rem;">No hay notificaciones recientes.</p>
        @endif
    </div>
</div>
@endsection
