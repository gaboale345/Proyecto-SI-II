@extends('layouts.app')

@section('title', 'Panel Administrativo - Hospital Plan 3000')

@section('content')
<div style="margin-bottom: 1.5rem;">
    <h2 style="color: var(--primary-dark); font-weight: 700;">Panel General de Administración</h2>
    <p style="color: #64748b; font-size: 0.9rem;">Control de usuarios, monitoreo de turnos e indicadores de gestión hospitalaria</p>
</div>

<!-- Admin KPI Cards Grid -->
<div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.25rem; margin-bottom: 1.5rem;">
    <div class="card" style="margin-bottom: 0; border-top: 4px solid var(--primary);">
        <div style="font-size: 0.85rem; color: #64748b; font-weight: 600;">TOTAL USUARIOS</div>
        <div style="font-size: 2rem; font-weight: 700; color: var(--primary-dark); margin-top: 0.2rem;">{{ $stats['total_usuarios'] }}</div>
        <div style="font-size: 0.8rem; color: var(--primary); margin-top: 0.25rem;"><i class="fa-solid fa-users"></i> Registrados en sistema</div>
    </div>

    <div class="card" style="margin-bottom: 0; border-top: 4px solid var(--accent);">
        <div style="font-size: 0.85rem; color: #64748b; font-weight: 600;">CITAS HOY</div>
        <div style="font-size: 2rem; font-weight: 700; color: var(--primary-dark); margin-top: 0.2rem;">{{ $stats['citas_hoy'] }}</div>
        <div style="font-size: 0.8rem; color: var(--accent); margin-top: 0.25rem;"><i class="fa-solid fa-calendar-day"></i> Programadas para la jornada</div>
    </div>

    <div class="card" style="margin-bottom: 0; border-top: 4px solid var(--success);">
        <div style="font-size: 0.85rem; color: #64748b; font-weight: 600;">ATENDIDAS HOY</div>
        <div style="font-size: 2rem; font-weight: 700; color: var(--success); margin-top: 0.2rem;">{{ $stats['citas_atendidas_hoy'] }}</div>
        <div style="font-size: 0.8rem; color: #065f46; margin-top: 0.25rem;"><i class="fa-solid fa-circle-check"></i> Consultas completadas</div>
    </div>

    <div class="card" style="margin-bottom: 0; border-top: 4px solid var(--danger);">
        <div style="font-size: 0.85rem; color: #64748b; font-weight: 600;">CANCELADAS HOY</div>
        <div style="font-size: 2rem; font-weight: 700; color: var(--danger); margin-top: 0.2rem;">{{ $stats['citas_canceladas_hoy'] }}</div>
        <div style="font-size: 0.8rem; color: #991b1b; margin-top: 0.25rem;"><i class="fa-solid fa-ban"></i> Liberadas / Suspendidas</div>
    </div>
</div>

<!-- Admin Action Buttons -->
<div class="card" style="background: linear-gradient(135deg, #f8fafc 0%, #edf2f7 100%);">
    <h3 class="card-title" style="margin-bottom: 1rem;"><i class="fa-solid fa-wand-magic-sparkles"></i> Accesos Rápidos de Gestión</h3>
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem;">
        <a href="{{ route('admin.usuarios') }}" class="btn btn-primary" style="justify-content: center; padding: 0.85rem;">
            <i class="fa-solid fa-user-gear"></i> Gestión de Usuarios
        </a>
        <a href="{{ route('agendas.index') }}" class="btn btn-accent" style="justify-content: center; padding: 0.85rem;">
            <i class="fa-solid fa-calendar-days"></i> Agendas Médicas
        </a>
        <a href="{{ route('contingencia.index') }}" class="btn btn-danger" style="justify-content: center; padding: 0.85rem;">
            <i class="fa-solid fa-triangle-exclamation"></i> Módulo Contingencias
        </a>
        <a href="{{ route('admin.auditoria') }}" class="btn btn-secondary" style="justify-content: center; padding: 0.85rem;">
            <i class="fa-solid fa-shield-halved"></i> Registros de Auditoría
        </a>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
    <!-- Auditoría Reciente -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fa-solid fa-shield-cat"></i> Auditoría Reciente del Sistema</h3>
            <a href="{{ route('admin.auditoria') }}" style="font-size: 0.85rem; color: var(--primary); text-decoration: none; font-weight: 600;">Ver todo &rarr;</a>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Fecha / Hora</th>
                        <th>Usuario</th>
                        <th>Acción</th>
                        <th>Tabla</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ultimasAuditorias as $aud)
                    <tr>
                        <td style="font-size: 0.82rem; color: #64748b;">{{ \Carbon\Carbon::parse($aud->fecha_hora)->format('d/m/Y H:i:s') }}</td>
                        <td><strong>{{ optional($aud->usuario)->nombre_completo ?? 'Sistema' }}</strong></td>
                        <td><span style="font-family: monospace; background: #e2e8f0; padding: 0.2rem 0.4rem; border-radius: 4px; font-size: 0.78rem;">{{ $aud->accion }}</span></td>
                        <td>{{ $aud->tabla_afectada }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Especialidades Populares -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fa-solid fa-stethoscope"></i> Especialidades y Médicos</h3>
        </div>
        <ul style="list-style: none;">
            @foreach($especialidadesPopulares as $esp)
            <li style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 0; border-bottom: 1px solid #f1f5f9;">
                <div>
                    <strong style="color: var(--primary-dark);">{{ $esp->nombre }}</strong>
                    <div style="font-size: 0.8rem; color: #64748b;">{{ $esp->duracion_turno }} min por turno</div>
                </div>
                <span class="status-badge status-CONFIRMADA">{{ $esp->medicos_count }} médicos</span>
            </li>
            @endforeach
        </ul>
    </div>
</div>
@endsection
