@extends('layouts.app')

@section('title', 'Panel de Control - Farmacia Hospital Plan 3000')

@section('content')
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
        <div class="card-title" style="margin: 0;">
            <i class="fa-solid fa-pills text-primary"></i>
            <span>Módulo de Farmacia e Inventario Hospitalario</span>
        </div>
        <div>
            <a href="{{ route('farmacia.inventario') }}" class="btn btn-sm btn-primary">
                <i class="fa-solid fa-plus-circle"></i> Gestionar Catálogo
            </a>
            <a href="{{ route('farmacia.recetas') }}" class="btn btn-sm btn-success">
                <i class="fa-solid fa-prescription"></i> Ver Recetas Médicas
            </a>
        </div>
    </div>

    <!-- Estadísticas Clave de Farmacia -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
        <div style="background: #eff6ff; padding: 1.25rem; border-radius: 10px; border-left: 4px solid #2563eb;">
            <div style="font-size: 0.8rem; color: #1e40af; font-weight: 600; text-transform: uppercase;">Total Productos en Catálogo</div>
            <div style="font-size: 1.8rem; font-weight: 700; color: #1e3a8a;">{{ $totalMedicamentos }}</div>
            <span style="font-size: 0.78rem; color: #64748b;">Medicamentos, jarabes e insumos</span>
        </div>
        <div style="background: #fef2f2; padding: 1.25rem; border-radius: 10px; border-left: 4px solid #ef4444;">
            <div style="font-size: 0.8rem; color: #991b1b; font-weight: 600; text-transform: uppercase;">Alerta: Stock Crítico / Bajo</div>
            <div style="font-size: 1.8rem; font-weight: 700; color: #b91c1c;">{{ $stockBajoCount }}</div>
            <span style="font-size: 0.78rem; color: #64748b;">Con &le; 10 unidades</span>
        </div>
        <div style="background: #fffbeb; padding: 1.25rem; border-radius: 10px; border-left: 4px solid #f59e0b;">
            <div style="font-size: 0.8rem; color: #92400e; font-weight: 600; text-transform: uppercase;">Recetas Pendientes de Despacho</div>
            <div style="font-size: 1.8rem; font-weight: 700; color: #b45309;">{{ $recetasPendientes }}</div>
            <span style="font-size: 0.78rem; color: #64748b;">Emitidas por médicos en consulta</span>
        </div>
        <div style="background: #f0fdf4; padding: 1.25rem; border-radius: 10px; border-left: 4px solid #10b981;">
            <div style="font-size: 0.8rem; color: #065f46; font-weight: 600; text-transform: uppercase;">Recetas Despachadas Hoy</div>
            <div style="font-size: 1.8rem; font-weight: 700; color: #047857;">{{ $recetasDespachadasHoy }}</div>
            <span style="font-size: 0.78rem; color: #64748b;">Entregadas a pacientes</span>
        </div>
    </div>

    <!-- Tableros Divididos: Recetas Recientes vs Stock Bajo -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 1.5rem;">
        
        <!-- Recetas Recientes -->
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 1rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.85rem;">
                <h4 style="margin: 0; color: var(--primary-dark); font-size: 1rem;">
                    <i class="fa-solid fa-receipt text-primary"></i> Últimas Recetas Médicas Emitidas
                </h4>
                <a href="{{ route('farmacia.recetas') }}" style="font-size: 0.82rem; color: var(--accent); font-weight: 600; text-decoration: none;">Ver Todas &rarr;</a>
            </div>

            @if($ultimasRecetas->count() > 0)
                <div class="table-responsive">
                    <table class="table" style="font-size: 0.85rem;">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Paciente</th>
                                <th>Médico / Esp.</th>
                                <th>Estado</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ultimasRecetas as $r)
                            <tr>
                                <td><strong>{{ $r->codigo_receta }}</strong></td>
                                <td>
                                    <strong>{{ $r->paciente->usuario->nombre_completo }}</strong>
                                    <div style="font-size: 0.75rem; color: #64748b;">CI: {{ $r->paciente->ci }}</div>
                                </td>
                                <td>
                                    <div>Dr(a). {{ $r->medico->usuario->apellido }}</div>
                                    <div style="font-size: 0.75rem; color: #64748b;">{{ $r->medico->especialidad->nombre }}</div>
                                </td>
                                <td>
                                    @if($r->estado === 'PENDIENTE')
                                        <span class="status-badge" style="background: #fef3c7; color: #b45309; font-size: 0.75rem;">PENDIENTE</span>
                                    @elseif($r->estado === 'DISPENSADA')
                                        <span class="status-badge" style="background: #dcfce7; color: #15803d; font-size: 0.75rem;">DESPACHADA</span>
                                    @else
                                        <span class="status-badge" style="background: #fee2e2; color: #b91c1c; font-size: 0.75rem;">{{ $r->estado }}</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('farmacia.recetas.despachar_form', $r->id_receta) }}" class="btn btn-sm btn-outline-primary" style="padding: 0.2rem 0.5rem; font-size: 0.78rem;">
                                        <i class="fa-solid fa-truck-ramp-box"></i> {{ $r->estado === 'PENDIENTE' ? 'Despachar' : 'Ver' }}
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p style="text-align: center; padding: 2rem; color: #94a3b8; margin: 0;">No hay recetas médicas registradas recientemente.</p>
            @endif
        </div>

        <!-- Productos con Stock Crítico -->
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 1rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.85rem;">
                <h4 style="margin: 0; color: #b91c1c; font-size: 1rem;">
                    <i class="fa-solid fa-triangle-exclamation"></i> Productos con Stock Crítico
                </h4>
                <a href="{{ route('farmacia.inventario') }}" style="font-size: 0.82rem; color: var(--accent); font-weight: 600; text-decoration: none;">Ver Inventario &rarr;</a>
            </div>

            @if($productosBajoStock->count() > 0)
                <div class="table-responsive">
                    <table class="table" style="font-size: 0.85rem;">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Categoría</th>
                                <th>Stock Actual</th>
                                <th>Precio (Bs.)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($productosBajoStock as $p)
                            <tr>
                                <td>
                                    <strong>{{ $p->nombre_comercial }}</strong>
                                    <div style="font-size: 0.75rem; color: #64748b;">{{ $p->concentracion }} ({{ $p->presentacion }})</div>
                                </td>
                                <td><span style="font-size: 0.75rem; color: #475569;">{{ $p->categoria }}</span></td>
                                <td>
                                    <strong style="color: {{ $p->stock_actual == 0 ? '#b91c1c' : '#b45309' }};">
                                        {{ $p->stock_actual }} unidades
                                    </strong>
                                </td>
                                <td>Bs. {{ number_format($p->precio_unitario, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p style="text-align: center; padding: 2rem; color: #16a34a; margin: 0;"><i class="fa-solid fa-circle-check"></i> Todos los productos cuentan con stock suficiente.</p>
            @endif
        </div>

    </div>
</div>
@endsection
