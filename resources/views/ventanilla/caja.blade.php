@extends('layouts.app')

@section('title', 'Módulo de Caja & Cobro de Consultas - Hospital Plan 3000')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <i class="fa-solid fa-cash-register text-primary"></i>
            <span>Caja, Cobro de Consultas y Cierre Diario de Recepción</span>
        </div>
    </div>

    <!-- Resume de Totales Diario -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
        <div style="background: #f0fdf4; padding: 1rem; border-radius: 10px; border-left: 4px solid #16a34a;">
            <div style="font-size: 0.8rem; color: #15803d; font-weight: 600;">TOTAL EFECTIVO</div>
            <div style="font-size: 1.5rem; font-weight: 700; color: #166534;">Bs. {{ number_format($totalEfectivo, 2) }}</div>
        </div>
        <div style="background: #eff6ff; padding: 1rem; border-radius: 10px; border-left: 4px solid #2563eb;">
            <div style="font-size: 0.8rem; color: #1d4ed8; font-weight: 600;">TOTAL TARJETAS</div>
            <div style="font-size: 1.5rem; font-weight: 700; color: #1e40af;">Bs. {{ number_format($totalTarjeta, 2) }}</div>
        </div>
        <div style="background: #faf5ff; padding: 1rem; border-radius: 10px; border-left: 4px solid #9333ea;">
            <div style="font-size: 0.8rem; color: #7e22ce; font-weight: 600;">TRANSFERENCIAS</div>
            <div style="font-size: 1.5rem; font-weight: 700; color: #6b21a8;">Bs. {{ number_format($totalTransferencia, 2) }}</div>
        </div>
        <div style="background: #fff7ed; padding: 1rem; border-radius: 10px; border-left: 4px solid #ea580c;">
            <div style="font-size: 0.8rem; color: #c2410c; font-weight: 600;">PAGOS CON QR</div>
            <div style="font-size: 1.5rem; font-weight: 700; color: #9a3412;">Bs. {{ number_format($totalQR, 2) }}</div>
        </div>
        <div style="background: #0f4c81; color: white; padding: 1rem; border-radius: 10px;">
            <div style="font-size: 0.8rem; opacity: 0.9; font-weight: 600;">TOTAL RECAUDADO</div>
            <div style="font-size: 1.5rem; font-weight: 700;">Bs. {{ number_format($totalGeneral, 2) }}</div>
        </div>
    </div>

    <!-- Formulario de Cobro -->
    <div style="background: #f8fafc; padding: 1.25rem; border-radius: 10px; border: 1px solid #e2e8f0; margin-bottom: 2rem;">
        <h4 style="color: var(--primary-dark); margin-bottom: 1rem;">
            <i class="fa-solid fa-receipt"></i> Registrar Cobro de Consulta
        </h4>

        <form action="{{ route('ventanilla.caja.pagar') }}" method="POST" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; align-items: end;">
            @csrf

            <div class="form-group" style="margin: 0;">
                <label class="form-label">Seleccionar Cita / Paciente *</label>
                <select name="id_cita" class="form-select" required>
                    <option value="" disabled selected>Seleccione cita pendiente de pago...</option>
                    @foreach($citasSinPago as $c)
                        <option value="{{ $c->id_cita }}">
                            Ref #{{ $c->id_cita }} — {{ $c->paciente->usuario->nombre }} {{ $c->paciente->usuario->apellido }} ({{ $c->medico->especialidad->nombre }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group" style="margin: 0;">
                <label class="form-label">Costo Consulta (Bs.) *</label>
                <input type="number" step="0.50" name="monto_total" class="form-control" value="50.00" required>
            </div>

            <div class="form-group" style="margin: 0;">
                <label class="form-label">Monto Cobrado (Bs.) *</label>
                <input type="number" step="0.50" name="monto_pagado" class="form-control" value="50.00" required>
            </div>

            <div class="form-group" style="margin: 0;">
                <label class="form-label">Método de Pago *</label>
                <select name="metodo_pago" class="form-select" required>
                    <option value="EFECTIVO">Efectivo</option>
                    <option value="TARJETA">Tarjeta de Débito/Crédito</option>
                    <option value="TRANSFERENCIA">Transferencia Bancaria</option>
                    <option value="QR">Pago QR Simple</option>
                </select>
            </div>

            <div>
                <button type="submit" class="btn btn-success" style="width: 100%;">
                    <i class="fa-solid fa-print"></i> Generar Comprobante
                </button>
            </div>
        </form>
    </div>

    <!-- Historial de Pagos del Día -->
    <h4 style="color: var(--primary); margin-bottom: 1rem;">
        <i class="fa-solid fa-clock-rotate-left"></i> Registro de Pagos del Día ({{ date('d/m/Y') }})
    </h4>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Comprobante</th>
                    <th>Cita Ref</th>
                    <th>Paciente</th>
                    <th>Especialidad</th>
                    <th>Método</th>
                    <th>Monto Total</th>
                    <th>Monto Pagado</th>
                    <th>Cajero</th>
                    <th>Hora</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pagosHoy as $p)
                    <tr>
                        <td><code>{{ $p->numero_comprobante }}</code></td>
                        <td>#{{ $p->id_cita }}</td>
                        <td>{{ $p->cita->paciente->usuario->nombre }} {{ $p->cita->paciente->usuario->apellido }}</td>
                        <td>{{ $p->cita->medico->especialidad->nombre }}</td>
                        <td>
                            <span class="badge-role" style="background: #e2e8f0; color: #1e293b;">
                                {{ $p->metodo_pago }}
                            </span>
                        </td>
                        <td>Bs. {{ number_format($p->monto_total, 2) }}</td>
                        <td><strong>Bs. {{ number_format($p->monto_pagado, 2) }}</strong></td>
                        <td>{{ $p->cajero->nombre ?? 'Sistema' }}</td>
                        <td>{{ $p->created_at->format('H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 2rem; color: #94a3b8;">
                            No hay cobros registrados aún el día de hoy.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
