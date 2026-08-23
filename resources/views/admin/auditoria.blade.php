@extends('layouts.app')

@section('title', 'Auditoría del Sistema - Hospital Plan 3000')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fa-solid fa-shield-halved"></i> Registros de Auditoría y Trazabilidad</h3>
        <span style="font-size: 0.85rem; color: #64748b;">Cumplimiento RNF04 & IEEE 830</span>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha / Hora</th>
                    <th>Usuario Responsable</th>
                    <th>Acción Realizada</th>
                    <th>Tabla Afectada</th>
                    <th>ID Reg.</th>
                    <th>Dirección IP</th>
                    <th>Detalles Adicionales</th>
                </tr>
            </thead>
            <tbody>
                @foreach($auditorias as $aud)
                <tr>
                    <td>#{{ $aud->id_auditoria }}</td>
                    <td style="font-size: 0.82rem; color: #475569;">{{ \Carbon\Carbon::parse($aud->fecha_hora)->format('d/m/Y H:i:s') }}</td>
                    <td>
                        <strong>{{ optional($aud->usuario)->nombre_completo ?? 'Sistema Automático' }}</strong>
                        @if($aud->usuario)
                            <div style="font-size: 0.78rem; color: #64748b;">{{ $aud->usuario->role->nombre_rol }}</div>
                        @endif
                    </td>
                    <td>
                        <span style="font-family: monospace; background: #e0f2fe; color: #0369a1; padding: 0.2rem 0.5rem; border-radius: 4px; font-weight: 600; font-size: 0.8rem;">
                            {{ $aud->accion }}
                        </span>
                    </td>
                    <td><code>{{ $aud->tabla_afectada }}</code></td>
                    <td>{{ $aud->registro_afectado ?? '-' }}</td>
                    <td style="font-size: 0.82rem;">{{ $aud->ip_origen }}</td>
                    <td style="font-size: 0.8rem; color: #64748b; max-width: 250px; word-break: break-all;">
                        {{ $aud->detalle }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="margin-top: 1rem;">
        {{ $auditorias->links() }}
    </div>
</div>
@endsection
