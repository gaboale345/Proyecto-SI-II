@extends('layouts.app')

@section('title', 'Mi Perfil & Expediente Médico - Hospital Plan 3000')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <i class="fa-solid fa-id-card text-primary"></i>
            <span>Perfil Personal y Expediente Médico Base</span>
        </div>
        <span class="badge-role badge-paciente">CI: {{ $paciente->ci }}</span>
    </div>

    <form action="{{ route('paciente.perfil.update') }}" method="POST">
        @csrf
        
        <h4 style="color: var(--primary); margin-bottom: 1rem; border-bottom: 2px solid var(--primary-light); padding-bottom: 0.5rem;">
            <i class="fa-solid fa-user"></i> 1. Información Personal
        </h4>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
            <div class="form-group">
                <label class="form-label">Nombres</label>
                <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $user->nombre) }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Apellidos</label>
                <input type="text" name="apellido" class="form-control" value="{{ old('apellido', $user->apellido) }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Cédula de Identidad (CI)</label>
                <input type="text" class="form-control" value="{{ $paciente->ci }}" disabled style="background-color: #f1f5f9;">
            </div>
            <div class="form-group">
                <label class="form-label">Fecha de Nacimiento</label>
                <input type="date" name="fecha_nacimiento" class="form-control" value="{{ old('fecha_nacimiento', $paciente->fecha_nacimiento) }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Edad Calculada</label>
                <input type="text" class="form-control" value="{{ $paciente->edad ? $paciente->edad . ' años' : 'No registrada' }}" disabled style="background-color: #f1f5f9;">
            </div>
            <div class="form-group">
                <label class="form-label">Sexo Biológico</label>
                <select name="sexo" class="form-select" required>
                    <option value="MASCULINO" {{ $paciente->sexo == 'MASCULINO' ? 'selected' : '' }}>Masculino</option>
                    <option value="FEMENINO" {{ $paciente->sexo == 'FEMENINO' ? 'selected' : '' }}>Femenino</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Identidad de Género</label>
                <select name="genero" class="form-select" required>
                    <option value="MASCULINO" {{ $paciente->genero == 'MASCULINO' ? 'selected' : '' }}>Masculino</option>
                    <option value="FEMENINO" {{ $paciente->genero == 'FEMENINO' ? 'selected' : '' }}>Femenino</option>
                    <option value="OTRO" {{ $paciente->genero == 'OTRO' ? 'selected' : '' }}>Otro / No especifica</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Nacionalidad</label>
                <input type="text" name="nacionalidad" class="form-control" value="{{ old('nacionalidad', $paciente->nacionalidad) }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Estado Civil</label>
                <select name="estado_civil" class="form-select" required>
                    <option value="SOLTERO/A" {{ $paciente->estado_civil == 'SOLTERO/A' ? 'selected' : '' }}>Soltero/a</option>
                    <option value="CASADO/A" {{ $paciente->estado_civil == 'CASADO/A' ? 'selected' : '' }}>Casado/a</option>
                    <option value="DIVORCIADO/A" {{ $paciente->estado_civil == 'DIVORCIADO/A' ? 'selected' : '' }}>Divorciado/a</option>
                    <option value="VIUDO/A" {{ $paciente->estado_civil == 'VIUDO/A' ? 'selected' : '' }}>Viudo/a</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Ocupación</label>
                <input type="text" name="ocupacion" class="form-control" value="{{ old('ocupacion', $paciente->ocupacion) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Teléfono / Celular</label>
                <input type="text" name="telefono" class="form-control" value="{{ old('telefono', $user->telefono) }}">
            </div>
            <div class="form-group">
                <label class="form-label">WhatsApp</label>
                <input type="text" name="whatsapp" class="form-control" value="{{ old('whatsapp', $paciente->whatsapp) }}" placeholder="Ej: 71000000">
            </div>
            <div class="form-group" style="grid-column: span 2;">
                <label class="form-label">Dirección Domiciliaria</label>
                <input type="text" name="direccion" class="form-control" value="{{ old('direccion', $paciente->direccion) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Ciudad</label>
                <input type="text" name="ciudad" class="form-control" value="{{ old('ciudad', $paciente->ciudad) }}">
            </div>
        </div>

        <h4 style="color: var(--primary); margin: 1.5rem 0 1rem 0; border-bottom: 2px solid var(--primary-light); padding-bottom: 0.5rem;">
            <i class="fa-solid fa-phone-volume"></i> 2. Contacto de Emergencia
        </h4>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
            <div class="form-group">
                <label class="form-label">Nombre del Contacto</label>
                <input type="text" name="contacto_emergencia" class="form-control" value="{{ old('contacto_emergencia', $paciente->contacto_emergencia) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Teléfono de Emergencia</label>
                <input type="text" name="telefono_emergencia" class="form-control" value="{{ old('telefono_emergencia', $paciente->telefono_emergencia) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Relación o Parentesco</label>
                <input type="text" name="relacion_contacto" class="form-control" value="{{ old('relacion_contacto', $paciente->relacion_contacto) }}" placeholder="Ej: Esposa, Padre, Hijo/a">
            </div>
        </div>

        <h4 style="color: var(--primary); margin: 1.5rem 0 1rem 0; border-bottom: 2px solid var(--primary-light); padding-bottom: 0.5rem;">
            <i class="fa-solid fa-heart-pulse"></i> 3. Expediente Médico Base (Antecedentes)
        </h4>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
            <div class="form-group">
                <label class="form-label">Tipo de Sangre</label>
                <select name="tipo_sangre" class="form-select">
                    @foreach(['ORH+', 'ORH-', 'ARH+', 'ARH-', 'BRH+', 'BRH-', 'ABRH+', 'ABRH-'] as $ts)
                        <option value="{{ $ts }}" {{ ($expediente->tipo_sangre ?? 'ORH+') == $ts ? 'selected' : '' }}>{{ $ts }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Alergias Generales</label>
                <input type="text" name="alergias" class="form-control" value="{{ old('alergias', $expediente->alergias ?? '') }}" placeholder="Polen, polvo, alimentos, etc.">
            </div>
            <div class="form-group">
                <label class="form-label">Alergias Medicamentosas</label>
                <input type="text" name="alergias_medicamentosas" class="form-control" value="{{ old('alergias_medicamentosas', $expediente->alergias_medicamentosas ?? '') }}" placeholder="Penicilina, aspirina, etc.">
            </div>
            <div class="form-group">
                <label class="form-label">Enfermedades Crónicas</label>
                <input type="text" name="enfermedades_cronicas" class="form-control" value="{{ old('enfermedades_cronicas', $expediente->enfermedades_cronicas ?? '') }}" placeholder="Hipertensión, Diabetes, Asma, etc.">
            </div>
            <div class="form-group">
                <label class="form-label">Antecedentes Personales</label>
                <input type="text" name="antecedentes_personales" class="form-control" value="{{ old('antecedentes_personales', $expediente->antecedentes_personales ?? '') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Antecedentes Familiares</label>
                <input type="text" name="antecedentes_familiares" class="form-control" value="{{ old('antecedentes_familiares', $expediente->antecedentes_familiares ?? '') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Cirugías Previas</label>
                <input type="text" name="cirugias_previas" class="form-control" value="{{ old('cirugias_previas', $expediente->cirugias_previas ?? '') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Medicamentos Actuales</label>
                <input type="text" name="medicamentos_actuales" class="form-control" value="{{ old('medicamentos_actuales', $expediente->medicamentos_actuales ?? '') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Hábitos Relevantes</label>
                <input type="text" name="habitos" class="form-control" value="{{ old('habitos', $expediente->habitos ?? '') }}" placeholder="Tabaco, alcohol, ejercicio, dieta">
            </div>
        </div>

        <div style="margin-top: 1.5rem; text-align: right;">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="fa-solid fa-floppy-disk"></i> Guardar Cambios de Perfil
            </button>
        </div>
    </form>
</div>
@endsection
