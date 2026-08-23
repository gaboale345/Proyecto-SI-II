@extends('layouts.app')

@section('title', 'Módulo Ventanilla y Call Center - Hospital Plan 3000')

@section('content')
<div style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; background: white; padding: 1.25rem 1.5rem; border-radius: 12px; box-shadow: var(--card-shadow);">
    <div>
        <h2 style="color: var(--primary-dark); font-weight: 700;"><i class="fa-solid fa-headset"></i> Ventanilla de Atención Presencial y Call Center</h2>
        <p style="color: #64748b; font-size: 0.9rem;">Gestión asistida de citas para pacientes sin acceso digital y atención telefónica (Teléfono 3494008)</p>
    </div>
    <div style="display: flex; gap: 0.75rem;">
        <a href="{{ route('paciente.citas.solicitar') }}" class="btn btn-accent">
            <i class="fa-solid fa-calendar-plus"></i> Asignar Cita Presencial
        </a>
        <button onclick="toggleModal('modalRegistroPresencial')" class="btn btn-primary">
            <i class="fa-solid fa-user-plus"></i> Registrar Nuevo Paciente
        </button>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fa-solid fa-calendar-day"></i> Citas Programadas para Hoy ({{ date('d/m/Y') }})</h3>
        <span class="status-badge status-CONFIRMADA">{{ $citasHoy->count() }} turnos asignados</span>
    </div>

    @if($citasHoy->count() > 0)
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Hora</th>
                        <th>Paciente</th>
                        <th>Cédula (CI)</th>
                        <th>Especialidad</th>
                        <th>Médico</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($citasHoy as $cita)
                    <tr>
                        <td><strong><i class="fa-regular fa-clock"></i> {{ substr($cita->hora_cita, 0, 5) }}</strong></td>
                        <td>
                            <strong>{{ $cita->paciente->usuario->nombre_completo }}</strong>
                            <div style="font-size: 0.78rem; color: #64748b;">Tel: {{ $cita->paciente->usuario->telefono }}</div>
                        </td>
                        <td><strong>{{ $cita->paciente->ci }}</strong></td>
                        <td>{{ $cita->medico->especialidad->nombre }}</td>
                        <td>Dr(a). {{ $cita->medico->usuario->nombre_completo }}</td>
                        <td><span class="status-badge status-{{ $cita->estado }}">{{ $cita->estado }}</span></td>
                        <td>
                            @if(in_array($cita->estado, ['SOLICITADA', 'CONFIRMADA']))
                                <form action="{{ route('paciente.citas.cancelar', $cita->id_cita) }}" method="POST" onsubmit="return confirm('¿Cancelar esta cita a solicitud del paciente en ventanilla/teléfono?');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fa-solid fa-ban"></i> Cancelar
                                    </button>
                                </form>
                            @else
                                <span style="font-size: 0.8rem; color: #94a3b8;">Finalizado</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div style="text-align: center; padding: 2.5rem; color: #64748b;">
            <p>No hay citas programadas para el día de hoy.</p>
        </div>
    @endif
</div>

<!-- Modal Registro Presencial de Pacientes (HU08) -->
<div id="modalRegistroPresencial" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div class="card" style="max-width: 600px; width: 90%; margin: 0;">
        <div class="card-header">
            <h3 class="card-title"><i class="fa-solid fa-user-plus"></i> Registro Presencial de Paciente (Ventanilla / Call Center)</h3>
            <button onclick="toggleModal('modalRegistroPresencial')" style="background: none; border: none; font-size: 1.2rem; cursor: pointer;">&times;</button>
        </div>

        <form action="{{ route('register') }}" method="POST">
            @csrf
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">Cédula de Identidad (CI) *</label>
                    <input type="text" name="ci" class="form-control" placeholder="Ej: 12345678" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Fecha Nacimiento *</label>
                    <input type="date" name="fecha_nacimiento" class="form-control" required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">Nombres *</label>
                    <input type="text" name="nombre" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Apellidos *</label>
                    <input type="text" name="apellido" class="form-control" required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">Género *</label>
                    <select name="genero" class="form-select" required>
                        <option value="MASCULINO">Masculino</option>
                        <option value="FEMENINO">Femenino</option>
                        <option value="OTRO">Otro</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Teléfono de Contacto *</label>
                    <input type="text" name="telefono" class="form-control" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Correo Electrónico (o generado automáticamente)</label>
                <input type="email" name="email" class="form-control" placeholder="paciente.ci@plan3000.gob.bo">
            </div>

            <input type="hidden" name="password" value="paciente123">
            <input type="hidden" name="password_confirmation" value="paciente123">

            <div style="margin-top: 1.5rem; display: flex; gap: 0.75rem; justify-content: flex-end;">
                <button type="button" onclick="toggleModal('modalRegistroPresencial')" class="btn btn-secondary">Cancelar</button>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-check"></i> Registrar Paciente</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function toggleModal(id) {
        const modal = document.getElementById(id);
        modal.style.display = modal.style.display === 'flex' ? 'none' : 'flex';
    }
</script>
@endsection
