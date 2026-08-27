@extends('layouts.app')

@section('title', 'Gestión de Consultorios y Horarios Masivos - Hospital Plan 3000')

@section('content')
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem;">
    <!-- Consultorios Físicos -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <i class="fa-solid fa-door-open text-primary"></i>
                <span>Gestión de Consultorios Físicos</span>
            </div>
        </div>

        <form action="{{ route('admin.consultorios.store') }}" method="POST" style="margin-bottom: 1.5rem;">
            @csrf
            <div class="form-group">
                <label class="form-label">Nombre / Número de Consultorio *</label>
                <input type="text" name="nombre_numero" class="form-control" required placeholder="Ej: Consultorio 104 - Bloque C">
            </div>

            <div class="form-group">
                <label class="form-label">Especialidad Asignada</label>
                <select name="id_especialidad" class="form-select">
                    <option value="">Ninguna / Compartido</option>
                    @foreach($especialidades as $esp)
                        <option value="{{ $esp->id_especialidad }}">{{ $esp->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Médico Responsable</label>
                <select name="id_medico" class="form-select">
                    <option value="">Sin médico fijo</option>
                    @foreach($medicos as $m)
                        <option value="{{ $m->id_medico }}">Dr(a). {{ $m->usuario->nombre }} {{ $m->usuario->apellido }} ({{ $m->especialidad->nombre }})</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Equipamiento Médico Disponible</label>
                <textarea name="equipamiento" class="form-control" rows="2" placeholder="Camilla, ecógrafo, estetoscopio, etc."></textarea>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">
                <i class="fa-solid fa-plus"></i> Registrar Consultorio
            </button>
        </form>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Especialidad</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($consultorios as $c)
                        <tr>
                            <td><strong>{{ $c->nombre_numero }}</strong></td>
                            <td>{{ $c->especialidad->nombre ?? 'General' }}</td>
                            <td><span class="status-badge status-CONFIRMADA">{{ $c->estado }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Generación Masiva de Horarios -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <i class="fa-solid fa-wand-magic-sparkles text-primary"></i>
                <span>Generador Automático de Horarios para Médicos</span>
            </div>
        </div>

        <form action="{{ route('admin.horarios.generar') }}" method="POST">
            @csrf

            <div class="form-group">
                <label class="form-label">Seleccionar Médico *</label>
                <select name="id_medico" class="form-select" required>
                    @foreach($medicos as $m)
                        <option value="{{ $m->id_medico }}">Dr(a). {{ $m->usuario->nombre }} {{ $m->usuario->apellido }} — {{ $m->especialidad->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                <div class="form-group">
                    <label class="form-label">Fecha Desde *</label>
                    <input type="date" name="fecha_inicio" class="form-control" required value="{{ date('Y-m-d') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Fecha Hasta *</label>
                    <input type="date" name="fecha_fin" class="form-control" required value="{{ date('Y-m-d', strtotime('+7 days')) }}">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                <div class="form-group">
                    <label class="form-label">Hora Inicio *</label>
                    <input type="time" name="hora_inicio" class="form-control" required value="08:00">
                </div>
                <div class="form-group">
                    <label class="form-label">Hora Fin *</label>
                    <input type="time" name="hora_fin" class="form-control" required value="12:00">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Duración por Consulta (Minutos) *</label>
                <select name="duracion_minutos" class="form-select" required>
                    <option value="15">15 minutos (Medicina General)</option>
                    <option value="20" selected>20 minutos (Estándar)</option>
                    <option value="30">30 minutos (Especialidades completas)</option>
                </select>
            </div>

            <button type="submit" class="btn btn-accent" style="width: 100%; margin-top: 1rem;">
                <i class="fa-solid fa-calendar-plus"></i> Generar Cupos de Atención Automáticos
            </button>
        </form>
    </div>
</div>
@endsection
