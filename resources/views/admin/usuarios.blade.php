@extends('layouts.app')

@section('title', 'Gestión de Usuarios - Hospital Plan 3000')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fa-solid fa-users-gear"></i> Gestión de Usuarios y Roles</h3>
        <button onclick="toggleModal('modalNuevoUsuario')" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-user-plus"></i> + Agregar Usuario
        </button>
    </div>

    <!-- Search & Filters -->
    <form action="{{ route('admin.usuarios') }}" method="GET" style="display: flex; gap: 1rem; margin-bottom: 1.25rem;">
        <input type="text" name="search" class="form-control" placeholder="Buscar por Nombre, Email o Cédula (CI)..." value="{{ $search }}" style="max-width: 400px;">
        <select name="role_id" class="form-select" style="max-width: 220px;" onchange="this.form.submit()">
            <option value="">-- Todos los Roles --</option>
            @foreach($roles as $r)
                <option value="{{ $r->id_rol }}" {{ $roleId == $r->id_rol ? 'selected' : '' }}>{{ $r->nombre_rol }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-secondary"><i class="fa-solid fa-magnifying-glass"></i> Buscar</button>
    </form>

    <!-- Users Table -->
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Usuario</th>
                    <th>CI / Ref</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($usuarios as $u)
                <tr>
                    <td><strong>{{ $u->id_usuario }}</strong></td>
                    <td>
                        <strong>{{ $u->nombre_completo }}</strong>
                        @if($u->medico)
                            <div style="font-size: 0.78rem; color: var(--accent);">{{ $u->medico->especialidad->nombre }} ({{ $u->medico->numero_colegiatura }})</div>
                        @endif
                    </td>
                    <td>{{ optional($u->paciente)->ci ?? '-' }}</td>
                    <td>{{ $u->email }}</td>
                    <td>{{ $u->telefono }}</td>
                    <td>
                        <span class="badge-role badge-{{ strtolower($u->role->nombre_rol) }}">
                            {{ $u->role->nombre_rol }}
                        </span>
                    </td>
                    <td>
                        <span class="status-badge status-{{ $u->estado }}">{{ $u->estado }}</span>
                    </td>
                    <td>
                        <form action="{{ route('admin.usuarios.estado', $u->id_usuario) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-sm {{ $u->estado == 'ACTIVO' ? 'btn-danger' : 'btn-primary' }}" title="Cambiar Estado">
                                <i class="fa-solid {{ $u->estado == 'ACTIVO' ? 'fa-user-slash' : 'fa-user-check' }}"></i>
                                {{ $u->estado == 'ACTIVO' ? 'Deshabilitar' : 'Habilitar' }}
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="margin-top: 1rem;">
        {{ $usuarios->links() }}
    </div>
</div>

<!-- Modal para Crear Usuario -->
<div id="modalNuevoUsuario" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div class="card" style="max-width: 600px; width: 90%; max-height: 90vh; overflow-y: auto; margin: 0;">
        <div class="card-header">
            <h3 class="card-title"><i class="fa-solid fa-user-plus"></i> Crear Nuevo Usuario</h3>
            <button onclick="toggleModal('modalNuevoUsuario')" style="background: none; border: none; font-size: 1.2rem; cursor: pointer;">&times;</button>
        </div>

        <form action="{{ route('admin.usuarios.crear') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Rol del Usuario *</label>
                <select name="id_rol" id="id_rol_select" class="form-select" onchange="toggleRolFields()" required>
                    @foreach($roles as $r)
                        <option value="{{ $r->id_rol }}" data-role="{{ $r->nombre_rol }}" {{ old('id_rol') == $r->id_rol ? 'selected' : '' }}>
                            {{ $r->nombre_rol }} - {{ $r->descripcion }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">Nombre *</label>
                    <input type="text" name="nombre" class="form-control" value="{{ old('nombre') }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Apellido *</label>
                    <input type="text" name="apellido" class="form-control" value="{{ old('apellido') }}" required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">Email *</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Teléfono *</label>
                    <input type="text" name="telefono" class="form-control" value="{{ old('telefono') }}" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Contraseña Inicial *</label>
                <input type="password" name="password" class="form-control" required minlength="6">
            </div>

            <!-- Campos Condicionales según Rol -->
            <div id="fieldsPaciente" style="display: none; border-top: 1px solid #e2e8f0; padding-top: 1rem; margin-top: 1rem;">
                <h4 style="font-size: 0.9rem; color: var(--primary); margin-bottom: 0.5rem;">Datos de Paciente</h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label">Cédula de Identidad (CI)</label>
                        <input type="text" name="ci" class="form-control" value="{{ old('ci') }}" placeholder="Opcional (Auto-generado si está vacío)">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Fecha Nacimiento</label>
                        <input type="date" name="fecha_nacimiento" class="form-control" value="{{ old('fecha_nacimiento') }}">
                    </div>
                </div>
            </div>

            <div id="fieldsMedico" style="display: none; border-top: 1px solid #e2e8f0; padding-top: 1rem; margin-top: 1rem;">
                <h4 style="font-size: 0.9rem; color: var(--primary); margin-bottom: 0.5rem;">Datos de Médico</h4>
                <div class="form-group">
                    <label class="form-label">Especialidad *</label>
                    <select name="id_especialidad" id="id_especialidad_select" class="form-select">
                        <option value="">-- Seleccionar Especialidad --</option>
                        @foreach($especialidades as $esp)
                            <option value="{{ $esp->id_especialidad }}" {{ old('id_especialidad') == $esp->id_especialidad ? 'selected' : '' }}>
                                {{ $esp->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label">Título Profesional</label>
                        <input type="text" name="titulo" class="form-control" value="{{ old('titulo') }}" placeholder="Ej: Cirujano Pediatra">
                    </div>
                    <div class="form-group">
                        <label class="form-label">N° Colegiatura</label>
                        <input type="text" name="numero_colegiatura" class="form-control" value="{{ old('numero_colegiatura') }}" placeholder="Ej: MP-99887 (Auto-generado si está vacío)">
                    </div>
                </div>
            </div>

            <div style="margin-top: 1.5rem; display: flex; gap: 0.75rem; justify-content: flex-end;">
                <button type="button" onclick="toggleModal('modalNuevoUsuario')" class="btn btn-secondary">Cancelar</button>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Guardar Usuario</button>
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

    function toggleRolFields() {
        const select = document.getElementById('id_rol_select');
        if (!select) return;
        const selectedOption = select.options[select.selectedIndex];
        const role = selectedOption ? selectedOption.getAttribute('data-role') : '';

        const fieldsPaciente = document.getElementById('fieldsPaciente');
        const fieldsMedico = document.getElementById('fieldsMedico');
        const espSelect = document.getElementById('id_especialidad_select');

        if (fieldsPaciente) fieldsPaciente.style.display = (role === 'PACIENTE') ? 'block' : 'none';
        if (fieldsMedico) fieldsMedico.style.display = (role === 'MEDICO') ? 'block' : 'none';
        if (espSelect) {
            if (role === 'MEDICO') {
                espSelect.setAttribute('required', 'required');
            } else {
                espSelect.removeAttribute('required');
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        toggleRolFields();
        @if($errors->any())
            toggleModal('modalNuevoUsuario');
        @endif
    });
</script>
@endsection
