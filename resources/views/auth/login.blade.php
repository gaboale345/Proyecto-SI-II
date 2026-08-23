@extends('layouts.app')

@section('title', 'Iniciar Sesión - Hospital Municipal Plan 3000')

@section('content')
<div style="max-width: 480px; margin: 2rem auto;">
    <div class="card" style="box-shadow: 0 15px 35px rgba(15, 76, 129, 0.12);">
        <div style="text-align: center; margin-bottom: 1.5rem;">
            <div style="width: 70px; height: 70px; background-color: var(--primary-light); color: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 2rem;">
                <i class="fa-solid fa-hospital"></i>
            </div>
            <h2 style="color: var(--primary-dark); font-weight: 700;">Acceso al Sistema</h2>
            <p style="font-size: 0.9rem; color: #64748b; margin-top: 0.25rem;">Gestión de Citas y Turnos — Plan 3000</p>
        </div>

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label" for="login">Cédula de Identidad (CI) o Correo Electrónico</label>
                <div style="position: relative;">
                    <i class="fa-solid fa-id-card" style="position: absolute; left: 0.85rem; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                    <input type="text" id="login" name="login" class="form-control" style="padding-left: 2.5rem;" placeholder="Ej: 12345678 o usuario@mail.com" value="{{ old('login') }}" required autofocus>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Contraseña</label>
                <div style="position: relative;">
                    <i class="fa-solid fa-lock" style="position: absolute; left: 0.85rem; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                    <input type="password" id="password" name="password" class="form-control" style="padding-left: 2.5rem;" placeholder="••••••••" required>
                </div>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; font-size: 0.88rem;">
                <label style="display: flex; align-items: center; gap: 0.4rem; cursor: pointer;">
                    <input type="checkbox" name="remember"> Recordar sesión
                </label>
                <a href="#" style="color: var(--primary); text-decoration: none; font-weight: 500;">¿Olvidó su contraseña?</a>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 0.8rem; font-size: 1rem;">
                <i class="fa-solid fa-right-to-bracket"></i> INICIAR SESIÓN
            </button>
        </form>

        <div style="text-align: center; margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid #f1f5f9; font-size: 0.9rem;">
            <span>¿Es un paciente nuevo?</span>
            <a href="{{ route('register') }}" style="color: var(--accent); font-weight: 700; text-decoration: none; margin-left: 0.3rem;">Registrarse aquí</a>
        </div>
    </div>

    <!-- Quick Access Demo Shortcuts -->
    <div class="card" style="background: #f8fafc; border: 1px dashed #cbd5e1;">
        <h4 style="font-size: 0.95rem; color: #475569; margin-bottom: 0.75rem; text-align: center;"><i class="fa-solid fa-bolt"></i> Accesos Rápidos de Prueba (Demo)</h4>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; font-size: 0.82rem;">
            <button onclick="fillLogin('12345678', 'paciente123')" class="btn btn-secondary btn-sm" style="justify-content: flex-start;">
                <i class="fa-solid fa-user"></i> Paciente (CI: 12345678)
            </button>
            <button onclick="fillLogin('ventanilla@plan3000.gob.bo', 'ventanilla123')" class="btn btn-secondary btn-sm" style="justify-content: flex-start;">
                <i class="fa-solid fa-headset"></i> Ventanilla / Call Center
            </button>
            <button onclick="fillLogin('jperez@plan3000.gob.bo', 'medico123')" class="btn btn-secondary btn-sm" style="justify-content: flex-start;">
                <i class="fa-solid fa-user-doctor"></i> Médico (Dr. Pérez)
            </button>
            <button onclick="fillLogin('admin@plan3000.gob.bo', 'admin123')" class="btn btn-secondary btn-sm" style="justify-content: flex-start;">
                <i class="fa-solid fa-user-gear"></i> Administrador
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function fillLogin(user, pass) {
        document.getElementById('login').value = user;
        document.getElementById('password').value = pass;
    }
</script>
@endsection
