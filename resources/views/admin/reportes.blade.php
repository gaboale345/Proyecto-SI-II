@extends('layouts.app')

@section('title', 'Reportes e Indicadores - Hospital Plan 3000')

@section('content')
<div class="card">
    <div class="card-header">
        <div>
            <h3 class="card-title"><i class="fa-solid fa-chart-pie"></i> Reportes e Indicadores de Gestión Hospitalaria</h3>
            <span style="font-size: 0.85rem; color: #64748b;">Hospital Municipal Plan 3000 — Recaudación Total en Período: <strong>Bs. {{ number_format($pagosTotal ?? 0, 2) }}</strong></span>
        </div>

        <div style="display: flex; gap: 0.5rem; align-items: center;">
            <a href="{{ route('admin.reportes.exportar', ['tipo' => 'csv']) }}" class="btn btn-sm btn-success">
                <i class="fa-solid fa-file-csv"></i> Exportar a CSV / Excel
            </a>
            <button onclick="window.print()" class="btn btn-sm btn-secondary">
                <i class="fa-solid fa-print"></i> Imprimir Reporte PDF
            </button>
        </div>
    </div>

    <!-- Filtro por Fechas -->
    <form action="{{ route('admin.reportes') }}" method="GET" style="display: flex; gap: 1rem; align-items: flex-end; background: #f8fafc; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
        <div>
            <label class="form-label" style="margin: 0; font-size: 0.82rem;">Fecha Desde:</label>
            <input type="date" name="fecha_inicio" class="form-control" value="{{ $fechaInicio ?? date('Y-m-01') }}">
        </div>
        <div>
            <label class="form-label" style="margin: 0; font-size: 0.82rem;">Fecha Hasta:</label>
            <input type="date" name="fecha_fin" class="form-control" value="{{ $fechaFin ?? date('Y-m-d') }}">
        </div>
        <div>
            <button type="submit" class="btn btn-sm btn-primary">
                <i class="fa-solid fa-filter"></i> Filtrar Período
            </button>
        </div>
    </form>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
        <!-- Distribution by Status -->
        <div style="border: 1px solid #e2e8f0; border-radius: 10px; padding: 1.25rem; background: #fafafa;">
            <h4 style="font-size: 1rem; color: var(--primary-dark); font-weight: 700; margin-bottom: 1rem;">
                <i class="fa-solid fa-list-check"></i> Distribución de Citas por Estado
            </h4>
            <table class="table">
                <thead>
                    <tr>
                        <th>Estado de la Cita</th>
                        <th>Cantidad</th>
                        <th>Porcentaje</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalCitasSum = array_sum($citasPorEstado->toArray()); @endphp
                    @foreach($citasPorEstado as $estado => $count)
                    <tr>
                        <td><span class="status-badge status-{{ $estado }}">{{ $estado }}</span></td>
                        <td><strong>{{ $count }}</strong></td>
                        <td>{{ $totalCitasSum > 0 ? round(($count / $totalCitasSum) * 100, 1) : 0 }}%</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Demand by Specialty -->
        <div style="border: 1px solid #e2e8f0; border-radius: 10px; padding: 1.25rem; background: #fafafa;">
            <h4 style="font-size: 1rem; color: var(--primary-dark); font-weight: 700; margin-bottom: 1rem;">
                <i class="fa-solid fa-stethoscope"></i> Demanda de Citas por Especialidad
            </h4>
            <table class="table">
                <thead>
                    <tr>
                        <th>Especialidad Médica</th>
                        <th>Citas Reservadas</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($citasPorEspecialidad as $esp => $count)
                    <tr>
                        <td><strong>{{ $esp }}</strong></td>
                        <td><span class="status-badge status-DISPONIBLE">{{ $count }} citas</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
