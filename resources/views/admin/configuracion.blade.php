@extends('layouts.app')

@section('title', 'Configuración General de la Clínica - Hospital Plan 3000')

@section('content')
<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header">
        <div class="card-title">
            <i class="fa-solid fa-gears text-primary"></i>
            <span>Configuración General e Institucional del Hospital</span>
        </div>
    </div>

    <form action="{{ route('admin.configuracion.update') }}" method="POST">
        @csrf

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
            @foreach($configuraciones as $cfg)
                <div class="form-group" style="grid-column: span 2;">
                    <label class="form-label">{{ str_replace('_', ' ', $cfg->clave) }}</label>
                    <input type="text" name="{{ $cfg->clave }}" class="form-control" value="{{ $cfg->valor }}">
                    <small style="color: #64748b; font-size: 0.8rem;">{{ $cfg->descripcion }}</small>
                </div>
            @endforeach
        </div>

        <div style="margin-top: 1.5rem; text-align: right;">
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-floppy-disk"></i> Guardar Ajustes Institucionales
            </button>
        </div>
    </form>
</div>
@endsection
