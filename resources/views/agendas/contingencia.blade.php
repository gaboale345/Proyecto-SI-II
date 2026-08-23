@extends('layouts.app')

@section('title', 'Módulo de Contingencias y Notificaciones - Hospital Plan 3000')

@section('content')
<div style="max-width: 950px; margin: 0 auto;">
    <div class="card" style="border-top: 4px solid var(--danger);">
        <div class="card-header">
            <h3 class="card-title" style="color: var(--danger);"><i class="fa-solid fa-triangle-exclamation"></i> Módulo de Gestión de Contingencias y Notificaciones Masivas</h3>
            <span style="font-size: 0.85rem; color: #64748b;">RF14 - Notificación por paros / mantenimientos / contingencias</span>
        </div>

        <p style="font-size: 0.92rem; color: #475569; margin-bottom: 1.25rem;">
            Este módulo permite declarar suspensiones masivas de atención por motivos institucionales (ej: paros médicos, cortes de energía eléctrica o mantenimiento de salas). Al ejecutar la contingencia, el sistema <strong>bloqueará automáticamente las agendas afectadas</strong> y enviará <strong>notificaciones masivas por WhatsApp / SMS</strong> a todos los pacientes con citas programadas para que reprogramen su turno.
        </p>

        <form action="{{ route('contingencia.procesar') }}" method="POST" onsubmit="return confirm('¿CONFIRMA la declaración de suspensión masiva de servicios? Se notificarán de forma inmediata a todos los pacientes afectados.');">
            @csrf
            
            <div class="form-group">
                <label class="form-label" for="motivo">Motivo de la Contingencia / Suspensión *</label>
                <input type="text" id="motivo" name="motivo" class="form-control" placeholder="Ej: Paro médico de 24 hrs acatado por SIRMES / Mantenimiento eléctrico programado" required>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label" for="fecha_inicio">Fecha Inicio *</label>
                    <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="fecha_fin">Fecha Fin *</label>
                    <input type="date" id="fecha_fin" name="fecha_fin" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">Filtrar por Especialidad (Opcional)</label>
                    <select name="id_especialidad" class="form-select">
                        <option value="">-- Todas las Especialidades --</option>
                        @foreach($especialidades as $esp)
                            <option value="{{ $esp->id_especialidad }}">{{ $esp->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Filtrar por Médico Específico (Opcional)</label>
                    <select name="id_medico" class="form-select">
                        <option value="">-- Todos los Médicos --</option>
                        @foreach($medicos as $m)
                            <option value="{{ $m->id_medico }}">Dr(a). {{ $m->usuario->nombre_completo }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <button type="submit" class="btn btn-danger" style="padding: 0.8rem 1.5rem; font-size: 0.95rem; margin-top: 0.5rem;">
                <i class="fa-solid fa-bullhorn"></i> EJECUTAR SUSPENSIÓN Y ENVIAR NOTIFICACIONES MASIVAS
            </button>
        </form>
    </div>

    <!-- Suspensiones Recientes -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fa-solid fa-clock-rotate-left"></i> Historial de Bloqueos por Contingencia</h3>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Fecha Afectada</th>
                        <th>Médico</th>
                        <th>Especialidad</th>
                        <th>Motivo de Bloqueo</th>
                        <th>Estado Agenda</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($suspensionesRecientes as $susp)
                    <tr>
                        <td><strong>{{ \Carbon\Carbon::parse($susp->fecha)->format('d/m/Y') }}</strong> ({{ substr($susp->hora_inicio, 0, 5) }})</td>
                        <td>Dr(a). {{ $susp->medico->usuario->nombre_completo }}</td>
                        <td>{{ $susp->medico->especialidad->nombre }}</td>
                        <td style="color: var(--danger); font-size: 0.88rem;">{{ $susp->motivo_bloqueo ?? 'Sin detalle' }}</td>
                        <td><span class="status-badge status-BLOQUEADO">BLOQUEADO</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
