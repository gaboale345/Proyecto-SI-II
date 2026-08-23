@extends('layouts.app')

@section('title', 'Gestión de Agendas Médicas - Hospital Plan 3000')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fa-solid fa-calendar-days"></i> Gestión de Agendas y Horarios Médicos</h3>
        <button onclick="toggleModal('modalNuevaAgenda')" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-plus"></i> + Crear Bloque de Agenda
        </button>
    </div>

    <!-- Filter by Specialty, Doctor & Date -->
    <form action="{{ route('agendas.index') }}" method="GET" style="background: #f8fafc; padding: 1.25rem; border-radius: 10px; margin-bottom: 1.5rem; border: 1px solid #e2e8f0;">
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;">
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Especialidad</label>
                <select name="especialidad_id" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Todas --</option>
                    @foreach($especialidades as $esp)
                        <option value="{{ $esp->id_especialidad }}" {{ $especialidadId == $esp->id_especialidad ? 'selected' : '' }}>{{ $esp->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Médico *</label>
                <select name="medico_id" class="form-select" onchange="this.form.submit()" required>
                    <option value="">-- Seleccione Médico --</option>
                    @foreach($medicos as $med)
                        <option value="{{ $med->id_medico }}" {{ $medicoId == $med->id_medico ? 'selected' : '' }}>
                            Dr(a). {{ $med->usuario->nombre_completo }} ({{ $med->especialidad->nombre }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Semana desde la Fecha</label>
                <input type="date" name="fecha_inicio" class="form-control" value="{{ $fechaInicio }}" onchange="this.form.submit()">
            </div>
        </div>
    </form>

    <!-- Weekly Agenda Display -->
    @if($medicoId && count($agendas) > 0)
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Horario</th>
                        <th>Capacidad</th>
                        <th>Cupos Disponibles</th>
                        <th>Estado Agenda</th>
                        <th>Citas Asignadas</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($agendas as $ag)
                    <tr>
                        <td><strong>{{ \Carbon\Carbon::parse($ag->fecha)->format('d/m/Y (l)') }}</strong></td>
                        <td><i class="fa-regular fa-clock"></i> {{ substr($ag->hora_inicio, 0, 5) }} - {{ substr($ag->hora_fin, 0, 5) }}</td>
                        <td>{{ $ag->capacidad }} pacientes</td>
                        <td><strong>{{ $ag->disponibles }}</strong> libres</td>
                        <td><span class="status-badge status-{{ $ag->estado }}">{{ $ag->estado }}</span></td>
                        <td>
                            @if($ag->citas->count() > 0)
                                <ul style="list-style: none; font-size: 0.82rem;">
                                    @foreach($ag->citas as $c)
                                        <li>
                                            <i class="fa-solid fa-user" style="color: var(--accent);"></i> {{ $c->paciente->usuario->nombre_completo }} (CI: {{ $c->paciente->ci }}) - <span class="status-badge status-{{ $c->estado }}">{{ $c->estado }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <span style="font-size: 0.8rem; color: #94a3b8;">Sin citas reservadas</span>
                            @endif
                        </td>
                        <td>
                            @if($ag->estado !== 'BLOQUEADO')
                                <form action="{{ route('agendas.bloquear', $ag->id_agenda) }}" method="POST" onsubmit="return confirm('¿Desea bloquear este horario? Si hay citas, se notificarán a los pacientes.');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fa-solid fa-lock"></i> Bloquear
                                    </button>
                                </form>
                            @else
                                <span style="font-size: 0.8rem; color: var(--danger);">Bloqueado</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @elseif($medicoId)
        <div style="text-align: center; padding: 2rem; color: #64748b;">
            <p>No hay bloques de agenda registrados para este médico en la semana seleccionada.</p>
        </div>
    @else
        <div style="text-align: center; padding: 2.5rem; color: #94a3b8;">
            <i class="fa-solid fa-user-doctor" style="font-size: 2.5rem; margin-bottom: 0.5rem;"></i>
            <p style="font-size: 1rem; font-weight: 500;">Seleccione un médico de la lista superior para visualizar y administrar su agenda de citas.</p>
        </div>
    @endif
</div>

<!-- Modal Nueva Agenda -->
<div id="modalNuevaAgenda" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div class="card" style="max-width: 500px; width: 90%; margin: 0;">
        <div class="card-header">
            <h3 class="card-title"><i class="fa-solid fa-calendar-plus"></i> Crear Bloque de Agenda</h3>
            <button onclick="toggleModal('modalNuevaAgenda')" style="background: none; border: none; font-size: 1.2rem; cursor: pointer;">&times;</button>
        </div>

        <form action="{{ route('agendas.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Médico *</label>
                <select name="id_medico" class="form-select" required>
                    @foreach($medicos as $m)
                        <option value="{{ $m->id_medico }}">Dr(a). {{ $m->usuario->nombre_completo }} ({{ $m->especialidad->nombre }})</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Fecha del Turno *</label>
                <input type="date" name="fecha" class="form-control" min="{{ date('Y-m-d') }}" required>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">Hora Inicio *</label>
                    <input type="time" name="hora_inicio" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Hora Fin *</label>
                    <input type="time" name="hora_fin" class="form-control" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Capacidad de Pacientes (Cupos) *</label>
                <input type="number" name="capacidad" class="form-control" value="1" min="1" max="10" required>
            </div>

            <div style="margin-top: 1.5rem; display: flex; gap: 0.75rem; justify-content: flex-end;">
                <button type="button" onclick="toggleModal('modalNuevaAgenda')" class="btn btn-secondary">Cancelar</button>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-check"></i> Guardar Bloque</button>
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
