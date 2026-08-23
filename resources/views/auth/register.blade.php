@extends('layouts.app')

@section('title', 'Registro de Paciente - Hospital Plan 3000')

@section('content')
<div style="max-width: 650px; margin: 1.5rem auto;">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fa-solid fa-id-card-clip"></i> Registro de Nuevo Paciente</h3>
            <span style="font-size: 0.85rem; color: #64748b;">Hospital Municipal Plan 3000</span>
        </div>

        <form action="{{ route('register') }}" method="POST">
            @csrf
            <h4 style="font-size: 0.95rem; color: var(--primary); margin-bottom: 0.75rem; font-weight: 700;">Datos Personales</h4>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label" for="ci">Cédula de Identidad (CI) *</label>
                    <input type="text" id="ci" name="ci" class="form-control" placeholder="Ej: 12345678" value="{{ old('ci') }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="fecha_nacimiento">Fecha de Nacimiento *</label>
                    <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" class="form-control" value="{{ old('fecha_nacimiento') }}" required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label" for="nombre">Nombres *</label>
                    <input type="text" id="nombre" name="nombre" class="form-control" placeholder="Ej: Juan Carlos" value="{{ old('nombre') }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="apellido">Apellidos *</label>
                    <input type="text" id="apellido" name="apellido" class="form-control" placeholder="Ej: Pérez García" value="{{ old('apellido') }}" required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label" for="genero">Género *</label>
                    <select id="genero" name="genero" class="form-select" required>
                        <option value="MASCULINO" {{ old('genero') == 'MASCULINO' ? 'selected' : '' }}>Masculino</option>
                        <option value="FEMENINO" {{ old('genero') == 'FEMENINO' ? 'selected' : '' }}>Femenino</option>
                        <option value="OTRO" {{ old('genero') == 'OTRO' ? 'selected' : '' }}>Otro</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="telefono">Teléfono / WhatsApp *</label>
                    <input type="text" id="telefono" name="telefono" class="form-control" placeholder="Ej: 76543210" value="{{ old('telefono') }}" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="direccion">Dirección de Residencia</label>
                <input type="text" id="direccion" name="direccion" class="form-control" placeholder="Ej: Av. San Aurelio #123, Plan 3000" value="{{ old('direccion') }}">
            </div>

            <h4 style="font-size: 0.95rem; color: var(--primary); margin: 1.25rem 0 0.75rem; font-weight: 700;">Contacto de Emergencia (Opcional)</h4>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label" for="contacto_emergencia">Nombre de Contacto</label>
                    <input type="text" id="contacto_emergencia" name="contacto_emergencia" class="form-control" placeholder="Ej: María Pérez (Esposa)" value="{{ old('contacto_emergencia') }}">
                </div>

                <div class="form-group">
                    <label class="form-label" for="telefono_emergencia">Teléfono de Emergencia</label>
                    <input type="text" id="telefono_emergencia" name="telefono_emergencia" class="form-control" placeholder="Ej: 76543211" value="{{ old('telefono_emergencia') }}">
                </div>
            </div>

            <h4 style="font-size: 0.95rem; color: var(--primary); margin: 1.25rem 0 0.75rem; font-weight: 700;">Credenciales de Acceso</h4>

            <div class="form-group">
                <label class="form-label" for="email">Correo Electrónico *</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="paciente@mail.com" value="{{ old('email') }}" required>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label" for="password">Contraseña *</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Mínimo 6 caracteres" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password_confirmation">Confirmar Contraseña *</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Repita contraseña" required>
                </div>
            </div>

            <div style="margin-top: 1.5rem; display: flex; gap: 1rem; align-items: center;">
                <button type="submit" class="btn btn-primary" style="flex: 1; justify-content: center; padding: 0.75rem;">
                    <i class="fa-solid fa-user-check"></i> REGISTRARME E INICIAR SESIÓN
                </button>
                <a href="{{ route('login') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
