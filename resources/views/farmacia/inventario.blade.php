@extends('layouts.app')

@section('title', 'Catálogo e Inventario de Farmacia - Hospital Plan 3000')

@section('content')
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h3 class="card-title" style="margin: 0; color: var(--primary-dark);">
                <i class="fa-solid fa-boxes-stacked"></i> Catálogo e Inventario de Medicamentos e Insumos
            </h3>
            <span style="font-size: 0.85rem; color: #64748b;">Gestión de stock, jarabes, medicamentos, alcohol, gasas y fechas de caducidad</span>
        </div>
        <button type="button" class="btn btn-primary" onclick="toggleModal('modal-nuevo-producto')">
            <i class="fa-solid fa-plus-circle"></i> Nuevo Producto / Insumo
        </button>
    </div>

    <!-- Filtros de Búsqueda y Categorías -->
    <div style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 1rem;">
        <form action="{{ route('farmacia.inventario') }}" method="GET" style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center;">
            <div style="flex: 2; min-width: 200px;">
                <input type="text" name="q" class="form-control" placeholder="Buscar por nombre comercial, genérico, lote o código..." value="{{ $busqueda ?? '' }}">
            </div>
            <div style="flex: 1; min-width: 180px;">
                <select name="categoria" class="form-select" onchange="this.form.submit()">
                    <option value="TODAS">Todas las Categorías</option>
                    @foreach($categorias as $key => $lbl)
                        <option value="{{ $key }}" {{ ($categoria ?? '') === $key ? 'selected' : '' }}>{{ $lbl }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-secondary">
                <i class="fa-solid fa-magnifying-glass"></i> Filtrar
            </button>
            @if(!empty($busqueda) || (!empty($categoria) && $categoria !== 'TODAS'))
                <a href="{{ route('farmacia.inventario') }}" class="btn btn-outline-secondary">Limpiar</a>
            @endif
        </form>
    </div>

    <!-- Tabla de Productos en Inventario -->
    <div style="padding: 1rem;">
        @if($medicamentos->count() > 0)
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Código / Lote</th>
                            <th>Nombre Comercial & Genérico</th>
                            <th>Categoría</th>
                            <th>Presentación</th>
                            <th>Stock Actual</th>
                            <th>Precio Unitario</th>
                            <th>Vencimiento</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($medicamentos as $m)
                        <tr>
                            <td>
                                <strong>{{ $m->codigo_barras ?? 'S/C' }}</strong>
                                <div style="font-size: 0.75rem; color: #64748b;">Lote: {{ $m->lote ?? 'N/A' }}</div>
                            </td>
                            <td>
                                <strong>{{ $m->nombre_comercial }}</strong>
                                @if($m->nombre_generico)
                                    <div style="font-size: 0.8rem; color: #64748b;">Genérico: {{ $m->nombre_generico }}</div>
                                @endif
                                <div style="font-size: 0.75rem; color: #0284c7;">Ubicación: {{ $m->ubicacion_estante ?? 'Estante Principal' }}</div>
                            </td>
                            <td>
                                <span class="badge" style="background: #e0f2fe; color: #0369a1; font-size: 0.75rem; padding: 0.2rem 0.5rem; border-radius: 4px;">
                                    {{ $m->categoria }}
                                </span>
                            </td>
                            <td style="font-size: 0.88rem;">
                                {{ $m->concentracion }}<br>
                                <span style="font-size: 0.78rem; color: #64748b;">{{ $m->presentacion }}</span>
                            </td>
                            <td>
                                @if($m->stock_actual <= 0)
                                    <span style="color: #b91c1c; font-weight: 700; font-size: 1rem;"><i class="fa-solid fa-circle-xmark"></i> 0 Unid.</span>
                                @elseif($m->stock_actual <= $m->stock_minimo)
                                    <span style="color: #b45309; font-weight: 700; font-size: 1rem;"><i class="fa-solid fa-triangle-exclamation"></i> {{ $m->stock_actual }} Unid.</span>
                                @else
                                    <span style="color: #166534; font-weight: 700; font-size: 1rem;">{{ $m->stock_actual }} Unid.</span>
                                @endif
                                <div style="font-size: 0.72rem; color: #64748b;">Mínimo: {{ $m->stock_minimo }}</div>
                            </td>
                            <td style="font-weight: 700; color: #0f172a;">
                                Bs. {{ number_format($m->precio_unitario, 2) }}
                            </td>
                            <td style="font-size: 0.85rem;">
                                @if($m->fecha_vencimiento)
                                    {{ \Carbon\Carbon::parse($m->fecha_vencimiento)->format('d/m/Y') }}
                                    @if(\Carbon\Carbon::parse($m->fecha_vencimiento)->isPast())
                                        <div style="font-size: 0.72rem; color: #b91c1c; font-weight: bold;">¡VENCIDO!</div>
                                    @endif
                                @else
                                    <span style="color: #94a3b8;">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($m->stock_actual <= 0)
                                    <span class="status-badge" style="background: #fee2e2; color: #b91c1c;">AGOTADO</span>
                                @elseif($m->stock_actual <= $m->stock_minimo)
                                    <span class="status-badge" style="background: #fef3c7; color: #b45309;">BAJO STOCK</span>
                                @else
                                    <span class="status-badge" style="background: #dcfce7; color: #15803d;">DISPONIBLE</span>
                                @endif
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="abrirModalStock({{ $m->id_medicamento }}, '{{ addslashes($m->nombre_comercial) }}', {{ $m->stock_actual }}, {{ $m->precio_unitario }}, '{{ $m->fecha_vencimiento }}', '{{ $m->lote }}')">
                                    <i class="fa-solid fa-pen-to-square"></i> Stock/Precio
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div style="text-align: center; padding: 3rem; color: #64748b;">
                <i class="fa-solid fa-box-open" style="font-size: 2.5rem; color: #cbd5e1; margin-bottom: 0.5rem;"></i>
                <p>No se encontraron productos en el inventario con los criterios seleccionados.</p>
            </div>
        @endif
    </div>
</div>

<!-- Modal: Registrar Nuevo Medicamento / Insumo -->
<div id="modal-nuevo-producto" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 999; justify-content: center; align-items: center; padding: 1rem;">
    <div style="background: white; border-radius: 12px; max-width: 650px; width: 100%; max-height: 90vh; overflow-y: auto; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);">
        <div style="background: var(--primary); color: white; padding: 1rem 1.25rem; display: flex; justify-content: space-between; align-items: center;">
            <h4 style="margin: 0; font-size: 1.1rem;"><i class="fa-solid fa-pills"></i> Registrar Producto en Farmacia</h4>
            <button type="button" onclick="toggleModal('modal-nuevo-producto')" style="background: none; border: none; color: white; font-size: 1.2rem; cursor: pointer;">&times;</button>
        </div>

        <form action="{{ route('farmacia.producto.guardar') }}" method="POST" style="padding: 1.25rem;">
            @csrf
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">Nombre Comercial *</label>
                    <input type="text" name="nombre_comercial" class="form-control" required placeholder="Ej: Paracetamol, Alcohol en Gel, Gasas...">
                </div>
                <div class="form-group">
                    <label class="form-label">Nombre Genérico</label>
                    <input type="text" name="nombre_generico" class="form-control" placeholder="Ej: Acetaminofén, Etanol 70%...">
                </div>
                <div class="form-group">
                    <label class="form-label">Categoría *</label>
                    <select name="categoria" class="form-select" required>
                        <option value="MEDICAMENTO">Medicamentos / Comprimidos</option>
                        <option value="JARABE">Jarabes / Suspensiones</option>
                        <option value="INYECTABLE">Inyectables / Ampollas</option>
                        <option value="INSUMO_MEDICO">Insumos Médicos (Alcohol, Algodón)</option>
                        <option value="MATERIAL_CURACION">Material de Curación (Gasas, Vendas)</option>
                        <option value="OTRO">Otros</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Presentación</label>
                    <input type="text" name="presentacion" class="form-control" placeholder="Ej: Caja x 20 tabletas, Frasco 120ml...">
                </div>
                <div class="form-group">
                    <label class="form-label">Concentración</label>
                    <input type="text" name="concentracion" class="form-control" placeholder="Ej: 500mg, 100mg/5ml, 70%...">
                </div>
                <div class="form-group">
                    <label class="form-label">Código de Barras / SKU</label>
                    <input type="text" name="codigo_barras" class="form-control" placeholder="Ej: 777123456789">
                </div>
                <div class="form-group">
                    <label class="form-label">Stock Inicial *</label>
                    <input type="number" name="stock_actual" class="form-control" min="0" value="50" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Stock Mínimo (Alerta) *</label>
                    <input type="number" name="stock_minimo" class="form-control" min="0" value="10" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Precio Unitario (Bs.) *</label>
                    <input type="number" step="0.10" name="precio_unitario" class="form-control" min="0" value="5.00" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Fecha de Vencimiento</label>
                    <input type="date" name="fecha_vencimiento" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Número de Lote</label>
                    <input type="text" name="lote" class="form-control" placeholder="Ej: LOT-2026-X">
                </div>
                <div class="form-group">
                    <label class="form-label">Ubicación / Estante</label>
                    <input type="text" name="ubicacion_estante" class="form-control" placeholder="Ej: Pasillo A, Estante 2">
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 1.25rem;">
                <button type="button" class="btn btn-secondary" onclick="toggleModal('modal-nuevo-producto')">Cancelar</button>
                <button type="submit" class="btn btn-success"><i class="fa-solid fa-save"></i> Guardar Producto</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Actualizar Stock y Precio -->
<div id="modal-stock" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 999; justify-content: center; align-items: center; padding: 1rem;">
    <div style="background: white; border-radius: 12px; max-width: 480px; width: 100%; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);">
        <div style="background: var(--primary); color: white; padding: 1rem 1.25rem; display: flex; justify-content: space-between; align-items: center;">
            <h4 style="margin: 0; font-size: 1.05rem;" id="modal-stock-titulo"><i class="fa-solid fa-pen-to-square"></i> Actualizar Stock</h4>
            <button type="button" onclick="toggleModal('modal-stock')" style="background: none; border: none; color: white; font-size: 1.2rem; cursor: pointer;">&times;</button>
        </div>

        <form id="form-actualizar-stock" method="POST" style="padding: 1.25rem;">
            @csrf
            <div class="form-group">
                <label class="form-label">Stock Actual (Unidades) *</label>
                <input type="number" name="stock_actual" id="edit-stock" class="form-control" min="0" required>
            </div>
            <div class="form-group">
                <label class="form-label">Precio Unitario (Bs.) *</label>
                <input type="number" step="0.10" name="precio_unitario" id="edit-precio" class="form-control" min="0" required>
            </div>
            <div class="form-group">
                <label class="form-label">Fecha de Vencimiento</label>
                <input type="date" name="fecha_vencimiento" id="edit-vencimiento" class="form-control">
            </div>
            <div class="form-group">
                <label class="form-label">Número de Lote</label>
                <input type="text" name="lote" id="edit-lote" class="form-control">
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 1.25rem;">
                <button type="button" class="btn btn-secondary" onclick="toggleModal('modal-stock')">Cancelar</button>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-check"></i> Actualizar</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
function toggleModal(id) {
    let modal = document.getElementById(id);
    modal.style.display = (modal.style.display === 'none' || modal.style.display === '') ? 'flex' : 'none';
}

function abrirModalStock(id, nombre, stock, precio, vencimiento, lote) {
    document.getElementById('modal-stock-titulo').innerHTML = `<i class="fa-solid fa-pen-to-square"></i> Actualizar: ${nombre}`;
    document.getElementById('edit-stock').value = stock;
    document.getElementById('edit-precio').value = precio;
    document.getElementById('edit-vencimiento').value = vencimiento ? vencimiento.substring(0, 10) : '';
    document.getElementById('edit-lote').value = lote || '';
    
    document.getElementById('form-actualizar-stock').action = `/farmacia/inventario/${id}/actualizar-stock`;
    toggleModal('modal-stock');
}
</script>
@endsection
