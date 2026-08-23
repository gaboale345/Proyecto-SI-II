@extends('layouts.app')

@section('title', 'Solicitud de Cita Médica - Hospital Plan 3000')

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fa-solid fa-calendar-check"></i> Solicitud de Cita Médica (Paso a Paso)</h3>
            <span style="font-size: 0.85rem; color: #64748b;">Hospital Municipal Plan 3000</span>
        </div>

        <!-- 3 Steps Visual Progress Bar -->
        <div style="display: flex; justify-content: space-around; margin-bottom: 2rem; border-bottom: 2px solid #e2e8f0; padding-bottom: 1.25rem;">
            <div style="display: flex; align-items: center; gap: 0.5rem; color: var(--primary); font-weight: 700;">
                <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-size: 0.9rem;">1</div>
                <span>1. Especialidad</span>
            </div>
            <div style="display: flex; align-items: center; gap: 0.5rem; color: {{ $selectedEspecialidad ? 'var(--primary)' : '#94a3b8' }}; font-weight: 600;">
                <div style="width: 32px; height: 32px; border-radius: 50%; background: {{ $selectedEspecialidad ? 'var(--primary)' : '#cbd5e1' }}; color: white; display: flex; align-items: center; justify-content: center; font-size: 0.9rem;">2</div>
                <span>2. Fecha y Médico</span>
            </div>
            <div style="display: flex; align-items: center; gap: 0.5rem; color: {{ count($turnosDisponibles) > 0 ? 'var(--primary)' : '#94a3b8' }}; font-weight: 600;">
                <div style="width: 32px; height: 32px; border-radius: 50%; background: {{ count($turnosDisponibles) > 0 ? 'var(--primary)' : '#cbd5e1' }}; color: white; display: flex; align-items: center; justify-content: center; font-size: 0.9rem;">3</div>
                <span>3. Horario y Confirmación</span>
            </div>
        </div>

        <!-- Filter Form for Steps 1 & 2 -->
        <form action="{{ route('paciente.citas.solicitar') }}" method="GET" style="background: #f8fafc; padding: 1.25rem; border-radius: 10px; margin-bottom: 1.5rem; border: 1px solid #e2e8f0;">
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label"><i class="fa-solid fa-stethoscope"></i> Paso 1: Especialidad *</label>
                    <select name="especialidad_id" class="form-select" onchange="this.form.submit()" required>
                        <option value="">-- Seleccionar Especialidad --</option>
                        @foreach($especialidades as $esp)
                            <option value="{{ $esp->id_especialidad }}" {{ $selectedEspecialidad == $esp->id_especialidad ? 'selected' : '' }}>
                                {{ $esp->nombre }} ({{ $esp->duracion_turno }} min)
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label"><i class="fa-regular fa-calendar-days"></i> Paso 2: Fecha de Atención *</label>
                    <input type="date" name="fecha" class="form-control" value="{{ $selectedFecha }}" min="{{ date('Y-m-d') }}" onchange="this.form.submit()" {{ !$selectedEspecialidad ? 'disabled' : '' }}>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label"><i class="fa-solid fa-user-md"></i> Médico (Opcional)</label>
                    <select name="medico_id" class="form-select" onchange="this.form.submit()" {{ !$selectedEspecialidad ? 'disabled' : '' }}>
                        <option value="">-- Todos los Médicos --</option>
                        @foreach($medicos as $med)
                            <option value="{{ $med->id_medico }}" {{ $selectedMedico == $med->id_medico ? 'selected' : '' }}>
                                Dr(a). {{ $med->usuario->nombre_completo }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>

        <!-- Step 3: Available Turnos -->
        @if($selectedEspecialidad)
            <div style="margin-top: 1rem;">
                <h4 style="font-size: 1.05rem; color: var(--primary-dark); font-weight: 700; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fa-solid fa-clock"></i> Turnos Disponibles para el {{ \Carbon\Carbon::parse($selectedFecha)->format('d/m/Y') }}
                </h4>

                @if(count($turnosDisponibles) > 0)
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 1rem;">
                        @foreach($turnosDisponibles as $turno)
                            <div style="border: 1px solid #cbd5e1; border-radius: 10px; padding: 1.1rem; background: white; box-shadow: 0 2px 8px rgba(0,0,0,0.04); transition: transform 0.2s;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                    <span style="font-size: 1.2rem; font-weight: 700; color: var(--primary-dark);">
                                        <i class="fa-regular fa-clock" style="color: var(--accent);"></i> {{ substr($turno->hora_inicio, 0, 5) }} - {{ substr($turno->hora_fin, 0, 5) }}
                                    </span>
                                    <span class="status-badge status-DISPONIBLE">{{ $turno->disponibles }} cupo disponible</span>
                                </div>
                                <p style="font-size: 0.88rem; color: #475569; margin-bottom: 0.85rem;">
                                    <strong>Dr(a). {{ $turno->medico->usuario->nombre_completo }}</strong><br>
                                    <span style="font-size: 0.8rem; color: #64748b;">{{ $turno->medico->titulo }}</span>
                                </p>

                                <form action="{{ route('paciente.citas.reservar') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="id_agenda" value="{{ $turno->id_agenda }}">

                                    @if(!Auth::user()->isPaciente())
                                        <!-- Si es personal de ventanilla, permitir seleccionar el paciente -->
                                        <div class="form-group" style="margin-bottom: 0.75rem;">
                                            <label class="form-label" style="font-size: 0.8rem;">Seleccionar Paciente *</label>
                                            <select name="id_paciente" class="form-select" style="padding: 0.4rem 0.6rem; font-size: 0.85rem;" required>
                                                <option value="">-- Paciente a asignar --</option>
                                                @foreach(\App\Models\Paciente::with('usuario')->get() as $p)
                                                    <option value="{{ $p->id_paciente }}">{{ $p->ci }} - {{ $p->usuario->nombre_completo }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif

                                    <button type="submit" class="btn btn-primary btn-sm" style="width: 100%; justify-content: center;">
                                        <i class="fa-solid fa-check"></i> CONFIRMAR CITA
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="background: #fffbe6; border: 1px solid #ffe58f; padding: 1.5rem; border-radius: 10px; text-align: center; color: #873800;">
                        <i class="fa-solid fa-triangle-exclamation" style="font-size: 2rem; margin-bottom: 0.5rem;"></i>
                        <p style="font-size: 1rem; font-weight: 600;">No existen turnos disponibles para los filtros seleccionados.</p>
                        <p style="font-size: 0.85rem; margin-top: 0.25rem;">Intente seleccionar otra fecha u otro médico de la lista.</p>
                    </div>
                @endif
            </div>
        @else
            <div style="text-align: center; padding: 2.5rem; color: #94a3b8;">
                <i class="fa-solid fa-arrow-up" style="font-size: 2rem; margin-bottom: 0.5rem;"></i>
                <p style="font-size: 0.95rem; font-weight: 500;">Por favor, seleccione una especialidad médica en el Paso 1 para ver la oferta de turnos.</p>
            </div>
        @endif
    </div>
</div>
@endsection
