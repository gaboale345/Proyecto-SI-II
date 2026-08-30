@extends('layouts.app')

@section('title', 'Bandeja de Recetas Médicas - Farmacia')

@section('content')
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h3 class="card-title" style="margin: 0; color: var(--primary-dark);">
                <i class="fa-solid fa-prescription"></i> Bandeja de Recetas Médicas Hospitalarias
            </h3>
            <span style="font-size: 0.85rem; color: #64748b;">Recetas emitidas en tiempo real desde el Expediente Clínico de cada consultorio</span>
        </div>
        <a href="{{ route('farmacia.dashboard') }}" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Volver al Panel
        </a>
    </div>

    <!-- Filtros de Estado y Búsqueda -->
    <div style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 1rem;">
        <form action="{{ route('farmacia.recetas') }}" method="GET" style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center;">
            <div style="flex: 2; min-width: 220px;">
                <input type="text" name="q" class="form-control" placeholder="Buscar por código de receta (REC-...), paciente o CI..." value="{{ $busqueda ?? '' }}">
            </div>
            <div style="flex: 1; min-width: 160px;">
                <select name="estado" class="form-select" onchange="this.form.submit()">
                    <option value="PENDIENTE" {{ ($estado ?? '') === 'PENDIENTE' ? 'selected' : '' }}>Solo Pendientes</option>
                    <option value="DISPENSADA" {{ ($estado ?? '') === 'DISPENSADA' ? 'selected' : '' }}>Solo Despachadas</option>
                    <option value="TODAS" {{ ($estado ?? '') === 'TODAS' ? 'selected' : '' }}>Todas las Recetas</option>
                </select>
            </div>
            <button type="submit" class="btn btn-secondary">
                <i class="fa-solid fa-magnifying-glass"></i> Filtrar
            </button>
            @if(!empty($busqueda) || (!empty($estado) && $estado !== 'PENDIENTE'))
                <a href="{{ route('farmacia.recetas') }}" class="btn btn-outline-secondary">Limpiar</a>
            @endif
        </form>
    </div>

    <!-- Listado de Recetas -->
    <div style="padding: 1rem;">
        @if($recetas->count() > 0)
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Código Receta</th>
                            <th>Fecha Emisión</th>
                            <th>Paciente</th>
                            <th>Médico Tratante</th>
                            <th>Medicamentos Recetados</th>
                            <th>Estado</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recetas as $r)
                        <tr>
                            <td>
                                <strong style="color: var(--primary); font-size: 0.95rem;">
                                    <i class="fa-solid fa-file-prescription"></i> {{ $r->codigo_receta }}
                                </strong>
                            </td>
                            <td style="font-size: 0.85rem; color: #475569;">
                                {{ \Carbon\Carbon::parse($r->fecha_emision)->format('d/m/Y H:i') }}
                            </td>
                            <td>
                                <strong>{{ $r->paciente->usuario->nombre_completo }}</strong>
                                <div style="font-size: 0.78rem; color: #64748b;">CI: {{ $r->paciente->ci }} | Tel: {{ $r->paciente->usuario->telefono }}</div>
                            </td>
                            <td>
                                <div>Dr(a). {{ $r->medico->usuario->nombre_completo }}</div>
                                <div style="font-size: 0.78rem; color: #64748b;">{{ $r->medico->especialidad->nombre }}</div>
                            </td>
                            <td>
                                <ul style="margin: 0; padding-left: 1rem; font-size: 0.82rem;">
                                    @foreach($r->items as $item)
                                        <li>
                                            <strong>{{ $item->nombre_medicamento }}</strong> 
                                            <span style="color: #64748b;">({{ $item->dosis }} - {{ $item->frecuencia }})</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td>
                                @if($r->estado === 'PENDIENTE')
                                    <span class="status-badge" style="background: #fef3c7; color: #b45309; font-weight: 700;">
                                        <i class="fa-solid fa-clock"></i> PENDIENTE
                                    </span>
                                @elseif($r->estado === 'DISPENSADA')
                                    <span class="status-badge" style="background: #dcfce7; color: #15803d; font-weight: 700;">
                                        <i class="fa-solid fa-check-double"></i> DESPACHADA
                                    </span>
                                @else
                                    <span class="status-badge">{{ $r->estado }}</span>
                                @endif
                            </td>
                            <td>
                                @if($r->estado === 'PENDIENTE')
                                    <a href="{{ route('farmacia.recetas.despachar_form', $r->id_receta) }}" class="btn btn-sm btn-success">
                                        <i class="fa-solid fa-truck-ramp-box"></i> Despachar
                                    </a>
                                @else
                                    <a href="{{ route('farmacia.recetas.despachar_form', $r->id_receta) }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="fa-solid fa-eye"></i> Ver Detalle
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div style="text-align: center; padding: 3rem; color: #64748b;">
                <i class="fa-solid fa-clipboard-check" style="font-size: 2.5rem; color: #cbd5e1; margin-bottom: 0.5rem;"></i>
                <p>No hay recetas médicas en la bandeja con los filtros seleccionados.</p>
            </div>
        @endif
    </div>
</div>
@endsection
