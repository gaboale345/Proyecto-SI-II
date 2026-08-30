@extends('layouts.app')

@section('title', 'Despacho de Receta Médica - Farmacia')

@section('content')
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h3 class="card-title" style="margin: 0; color: var(--primary-dark);">
                <i class="fa-solid fa-file-prescription"></i> Despacho de Receta Médica Ref: {{ $receta->codigo_receta }}
            </h3>
            <span style="font-size: 0.85rem; color: #64748b;">Verificación de stock y entrega de fármacos al paciente</span>
        </div>
        <a href="{{ route('farmacia.recetas') }}" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Volver a la Bandeja
        </a>
    </div>

    <!-- Ficha de la Consulta y Paciente -->
    <div style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 1.25rem;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; font-size: 0.9rem;">
            <div>
                <span style="font-size: 0.78rem; color: #64748b; font-weight: 600; text-transform: uppercase;">Paciente:</span>
                <div style="font-weight: 700; color: #0f172a;">{{ $receta->paciente->usuario->nombre_completo }}</div>
                <div style="font-size: 0.8rem; color: #64748b;">CI: {{ $receta->paciente->ci }} | Tel: {{ $receta->paciente->usuario->telefono }}</div>
            </div>
            <div>
                <span style="font-size: 0.78rem; color: #64748b; font-weight: 600; text-transform: uppercase;">Médico Prescriptor:</span>
                <div style="font-weight: 700; color: #0f172a;">Dr(a). {{ $receta->medico->usuario->nombre_completo }}</div>
                <div style="font-size: 0.8rem; color: #64748b;">Especialidad: {{ $receta->medico->especialidad->nombre }}</div>
            </div>
            <div>
                <span style="font-size: 0.78rem; color: #64748b; font-weight: 600; text-transform: uppercase;">Fecha y Hora de Emisión:</span>
                <div style="font-weight: 700; color: #0f172a;">{{ \Carbon\Carbon::parse($receta->fecha_emision)->format('d/m/Y H:i') }}</div>
            </div>
            <div>
                <span style="font-size: 0.78rem; color: #64748b; font-weight: 600; text-transform: uppercase;">Estado de la Receta:</span>
                <div>
                    @if($receta->estado === 'PENDIENTE')
                        <span class="status-badge" style="background: #fef3c7; color: #b45309; font-weight: 700;">PENDIENTE DE ENTREGA</span>
                    @else
                        <span class="status-badge" style="background: #dcfce7; color: #15803d; font-weight: 700;">DESPACHADA</span>
                    @endif
                </div>
            </div>
        </div>

        @if($receta->consulta && $receta->consulta->diagnostico_principal)
            <div style="margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px dashed #cbd5e1; font-size: 0.85rem;">
                <strong>Diagnóstico Médico:</strong> {{ $receta->consulta->diagnostico_principal }}
                @if($receta->indicaciones_generales)
                    <br><strong>Indicaciones Generales:</strong> <em>{{ $receta->indicaciones_generales }}</em>
                @endif
            </div>
        @endif
    </div>

    <!-- Formulario / Detalle de Dispensación -->
    <div style="padding: 1.25rem;">
        <h4 style="color: var(--primary); margin-bottom: 1rem;">
            <i class="fa-solid fa-pills"></i> Medicamentos a Dispensar
        </h4>

        @if($receta->estado === 'PENDIENTE')
            <form action="{{ route('farmacia.recetas.despachar', $receta->id_receta) }}" method="POST">
                @csrf

                <div class="table-responsive" style="margin-bottom: 1.5rem;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Medicamento Prescrito por Médico</th>
                                <th>Posología e Instrucciones</th>
                                <th>Producto Asignado en Farmacia</th>
                                <th>Stock en Farmacia</th>
                                <th style="width: 140px;">Cantidad a Entregar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($receta->items as $item)
                            <tr>
                                <td>
                                    <strong style="font-size: 0.95rem; color: #0f172a;">{{ $item->nombre_medicamento }}</strong>
                                </td>
                                <td style="font-size: 0.85rem; color: #475569;">
                                    <div><strong>Dosis:</strong> {{ $item->dosis ?? 'N/A' }}</div>
                                    <div><strong>Frecuencia:</strong> {{ $item->frecuencia ?? 'N/A' }}</div>
                                    <div><strong>Duración:</strong> {{ $item->duracion ?? 'N/A' }}</div>
                                </td>
                                <td>
                                    <select name="items[{{ $item->id_item }}][id_medicamento]" class="form-select select-farmacia" style="font-size: 0.85rem;">
                                        <option value="">-- Seleccionar producto del catálogo --</option>
                                        @foreach($medicamentosDisponibles as $m)
                                            <option value="{{ $m->id_medicamento }}" 
                                                data-stock="{{ $m->stock_actual }}"
                                                data-precio="{{ $m->precio_unitario }}"
                                                {{ ($item->id_medicamento == $m->id_medicamento || stripos($m->nombre_comercial, $item->nombre_medicamento) !== false) ? 'selected' : '' }}>
                                                {{ $m->nombre_comercial }} {{ $m->concentracion }} (Stock: {{ $m->stock_actual }} - Bs. {{ number_format($m->precio_unitario, 2) }})
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    @if($item->medicamento)
                                        <span style="font-weight: 700; color: {{ $item->medicamento->stock_actual > 0 ? '#166534' : '#b91c1c' }};">
                                            {{ $item->medicamento->stock_actual }} Unid.
                                        </span>
                                    @else
                                        <span style="color: #64748b; font-size: 0.8rem;">Seleccione producto</span>
                                    @endif
                                </td>
                                <td>
                                    <input type="number" name="items[{{ $item->id_item }}][cantidad_despachada]" class="form-control" value="{{ $item->cantidad_solicitada }}" min="1" required style="font-weight: bold; text-align: center;">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label class="form-label">Observaciones de Farmacia / Entrega</label>
                    <textarea name="observaciones_farmacia" class="form-control" rows="2" placeholder="Observaciones sobre la entrega, lote de medicamentos o recomendaciones al paciente..."></textarea>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #e2e8f0; padding-top: 1.25rem;">
                    <a href="{{ route('farmacia.recetas') }}" class="btn btn-secondary">
                        <i class="fa-solid fa-arrow-left"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="fa-solid fa-check-double"></i> Confirmar Despacho y Descontar Stock
                    </button>
                </div>
            </form>
        @else
            <!-- Receta Ya Despachada (Solo Lectura) -->
            <div class="table-responsive" style="margin-bottom: 1.5rem;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Medicamento Prescrito</th>
                            <th>Posología</th>
                            <th>Producto Entregado</th>
                            <th>Cantidad Entregada</th>
                            <th>Estado Ítem</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($receta->items as $item)
                        <tr>
                            <td><strong>{{ $item->nombre_medicamento }}</strong></td>
                            <td style="font-size: 0.85rem;">{{ $item->dosis }} - {{ $item->frecuencia }} por {{ $item->duracion }}</td>
                            <td>{{ $item->medicamento->nombre_comercial ?? 'Producto Despachado' }} ({{ $item->medicamento->concentracion ?? '' }})</td>
                            <td><strong style="color: #166534;">{{ $item->cantidad_despachada }} Unidades</strong></td>
                            <td><span class="status-badge status-ATENDIDA">{{ $item->estado_item }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="background: #f0fdf4; border: 1px solid #bbf7d0; padding: 1rem; border-radius: 8px; font-size: 0.9rem;">
                <div style="font-weight: 700; color: #166534; margin-bottom: 0.25rem;">
                    <i class="fa-solid fa-circle-check"></i> Receta Despachada el {{ \Carbon\Carbon::parse($receta->fecha_dispensacion)->format('d/m/Y H:i') }}
                </div>
                <div style="color: #334155;">{{ $receta->observaciones_farmacia ?? 'Despachado en farmacia hospitalaria' }}</div>
            </div>
        @endif
    </div>
</div>
@endsection
