@extends('layouts.app')

@section('title', 'Registro Paciente Presencial (Walk-in) - Hospital Plan 3000')

@section('content')
<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header">
        <div class="card-title">
            <i class="fa-solid fa-user-plus text-primary"></i>
            <span>Registro Rápido de Paciente Presencial en Ventanilla</span>
        </div>
    </div>

    <form action="{{ route('ventanilla.walkin.store') }}" method="POST">
        @csrf

        <div style="background-color: #eff6ff; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border-left: 4px solid #3b82f6;">
            <i class="fa-solid fa-shield-halved text-primary"></i>
            <strong>Prevención de Pacientes Duplicados:</strong> El sistema verifica en tiempo real la Cédula de Identidad (CI) antes de crear la ficha de usuario.
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem;">
            <div class="form-group">
                <label class="form-label">Cédula de Identidad (CI) *</label>
                <input type="text" name="ci" class="form-control" required placeholder="Ej: 12345678" value="{{ old('ci') }}">
            </div>

            <div class="form-group">
                <label class="form-label">Nombres *</label>
                <input type="text" name="nombre" class="form-control" required placeholder="Nombres del paciente" value="{{ old('nombre') }}">
            </div>

            <div class="form-group">
                <label class="form-label">Apellidos *</label>
                <input type="text" name="apellido" class="form-control" required placeholder="Apellidos del paciente" value="{{ old('apellido') }}">
            </div>

            <div class="form-group">
                <label class="form-label">Fecha de Nacimiento *</label>
                <input type="date" name="fecha_nacimiento" class="form-control" required value="{{ old('fecha_nacimiento') }}">
            </div>

            <div class="form-group">
                <label class="form-label">Sexo Biológico *</label>
                <select name="sexo" class="form-select" required>
                    <option value="MASCULINO">Masculino</option>
                    <option value="FEMENINO">Femenino</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Identidad de Género *</label>
                <select name="genero" class="form-select" required>
                    <option value="MASCULINO">Masculino</option>
                    <option value="FEMENINO">Femenino</option>
                    <option value="OTRO">Otro / No especifica</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Teléfono de Contacto</label>
                <input type="text" name="telefono" class="form-control" placeholder="Ej: 71000000" value="{{ old('telefono') }}">
            </div>

            <div class="form-group" style="grid-column: span 2;">
                <label class="form-label">Dirección Domiciliaria</label>
                <input type="text" name="direccion" class="form-control" placeholder="Ej: Barrio 27 de Mayo, Calle 4" value="{{ old('direccion') }}">
            </div>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1.5rem;">
            <a href="{{ route('ventanilla.sala_espera') }}" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Volver a Sala de Espera
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-user-check"></i> Registrar Paciente Walk-in
            </button>
        </div>
    </form>
</div>
@endsection
