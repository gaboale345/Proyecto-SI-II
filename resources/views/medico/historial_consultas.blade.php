@extends('layouts.app')

@section('title', 'Historial de Consultas - Dr(a). ' . $medico->usuario->nombre)

@section('content')
<div style="max-width: 1200px; margin: 0 auto;">

    {{-- Encabezado --}}
    <div class="card" style="background: linear-gradient(135deg, #0f4c81 0%, #1a6fb5 100%); color: white; border: none;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h2 style="margin: 0; font-weight: 700;">
                    <i class="fa-solid fa-clock-rotate-left"></i> Historial de Consultas
                </h2>
                <p style="margin: 0.25rem 0 0; opacity: 0.85; font-size: 0.92rem;">
                    Dr(a). {{ $medico->usuario->nombre }} {{ $medico->usuario->apellido }}
                    — {{ $medico->especialidad->nombre ?? 'Medicina General' }}
                </p>
            </div>
            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <div style="background: rgba(255,255,255,0.15); padding: 0.75rem 1.25rem; border-radius: 10px; text-align: center;">
                    <div style="font-size: 1.6rem; font-weight: 700;">{{ $totalPacientesAtendidos }}</div>
                    <div style="font-size: 0.78rem; opacity: 0.85;">Pacientes Atendidos</div>
                </div>
                <div style="background: rgba(255,255,255,0.15); padding: 0.75rem 1.25rem; border-radius: 10px; text-align: center;">
                    <div style="font-size: 1.6rem; font-weight: 700;">{{ $totalConsultas }}</div>
                    <div style="font-size: 0.78rem; opacity: 0.85;">Total Consultas</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtros de búsqueda --}}
    <div class="card">
        <form method="GET" action="{{ route('medico.historial_consultas') }}">
            <div style="display: grid; grid-template-columns: 1fr 160px 160px auto; gap: 0.75rem; align-items: end;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label"><i class="fa-solid fa-magnifying-glass"></i> Buscar paciente</label>
                    <input type="text" name="buscar" class="form-control" placeholder="Nombre, apellido o C.I. del paciente..." value="{{ request('buscar') }}">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Desde</label>
                    <input type="date" name="fecha_desde" class="form-control" value="{{ request('fecha_desde') }}">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Hasta</label>
                    <input type="date" name="fecha_hasta" class="form-control" value="{{ request('fecha_hasta') }}">
                </div>
                <div style="display: flex; gap: 0.5rem;">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fa-solid fa-filter"></i> Filtrar
                    </button>
                    <a href="{{ route('medico.historial_consultas') }}" class="btn btn-secondary btn-sm">
                        <i class="fa-solid fa-rotate-left"></i> Limpiar
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Tabla de consultas --}}
    <div class="card">
        <h3 style="margin-bottom: 1rem; color: var(--primary-dark); font-size: 1.1rem;">
            <i class="fa-solid fa-list"></i> Consultas Realizadas
            <span style="font-weight: 400; font-size: 0.88rem; color: #64748b;">({{ $consultas->total() }} registros)</span>
        </h3>

        @if($consultas->count() > 0)
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.88rem;">
                <thead>
                    <tr style="background: #f1f5f9; border-bottom: 2px solid #e2e8f0;">
                        <th style="padding: 0.7rem; text-align: left; color: #475569;">#</th>
                        <th style="padding: 0.7rem; text-align: left; color: #475569;">Fecha</th>
                        <th style="padding: 0.7rem; text-align: left; color: #475569;">Paciente</th>
                        <th style="padding: 0.7rem; text-align: left; color: #475569;">C.I.</th>
                        <th style="padding: 0.7rem; text-align: left; color: #475569;">Motivo de Consulta</th>
                        <th style="padding: 0.7rem; text-align: left; color: #475569;">Diagnóstico</th>
                        <th style="padding: 0.7rem; text-align: left; color: #475569;">Receta</th>
                        <th style="padding: 0.7rem; text-align: center; color: #475569;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($consultas as $index => $consulta)
                    <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.15s;"
                        onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                        <td style="padding: 0.65rem 0.7rem; color: #94a3b8; font-weight: 600;">
                            {{ $consultas->firstItem() + $index }}
                        </td>
                        <td style="padding: 0.65rem 0.7rem;">
                            <div style="font-weight: 600; color: #334155;">
                                {{ \Carbon\Carbon::parse($consulta->fecha_hora)->format('d/m/Y') }}
                            </div>
                            <div style="font-size: 0.78rem; color: #94a3b8;">
                                {{ \Carbon\Carbon::parse($consulta->fecha_hora)->format('H:i') }}
                            </div>
                        </td>
                        <td style="padding: 0.65rem 0.7rem;">
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <div style="width: 34px; height: 34px; background: var(--primary-light); color: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.8rem;">
                                    {{ substr($consulta->paciente->usuario->nombre ?? '?', 0, 1) }}{{ substr($consulta->paciente->usuario->apellido ?? '?', 0, 1) }}
                                </div>
                                <div>
                                    <div style="font-weight: 600; color: #1e293b;">
                                        {{ $consulta->paciente->usuario->nombre ?? '—' }} {{ $consulta->paciente->usuario->apellido ?? '' }}
                                    </div>
                                    @if($consulta->paciente->usuario->telefono)
                                    <div style="font-size: 0.78rem; color: #94a3b8;">
                                        <i class="fa-solid fa-phone" style="font-size: 0.7rem;"></i> {{ $consulta->paciente->usuario->telefono }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td style="padding: 0.65rem 0.7rem; font-weight: 500; color: #475569;">
                            {{ $consulta->paciente->usuario->ci ?? '—' }}
                        </td>
                        <td style="padding: 0.65rem 0.7rem; max-width: 200px;">
                            <div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $consulta->motivo_consulta }}">
                                {{ Str::limit($consulta->motivo_consulta, 50) }}
                            </div>
                        </td>
                        <td style="padding: 0.65rem 0.7rem; max-width: 200px;">
                            <span style="background: #dcfce7; color: #15803d; padding: 0.2rem 0.5rem; border-radius: 6px; font-size: 0.8rem; font-weight: 600;">
                                {{ Str::limit($consulta->diagnostico_principal, 40) }}
                            </span>
                        </td>
                        <td style="padding: 0.65rem 0.7rem; text-align: center;">
                            @if($consulta->receta)
                                <span style="background: #e0f2fe; color: #0369a1; padding: 0.2rem 0.5rem; border-radius: 6px; font-size: 0.78rem; font-weight: 600;">
                                    <i class="fa-solid fa-prescription"></i> {{ $consulta->receta->items->count() }} med.
                                </span>
                            @else
                                <span style="color: #cbd5e1; font-size: 0.8rem;">Sin receta</span>
                            @endif
                        </td>
                        <td style="padding: 0.65rem 0.7rem; text-align: center;">
                            <a href="{{ route('medico.paciente.historial', $consulta->id_paciente) }}"
                               class="btn btn-primary btn-sm" title="Ver historial completo del paciente"
                               style="padding: 0.3rem 0.6rem; font-size: 0.78rem;">
                                <i class="fa-solid fa-file-medical"></i> Ver Historial
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        <div style="margin-top: 1.25rem; display: flex; justify-content: center;">
            {{ $consultas->links() }}
        </div>

        @else
        <div style="text-align: center; padding: 3rem 1rem; color: #94a3b8;">
            <i class="fa-solid fa-inbox" style="font-size: 3rem; margin-bottom: 1rem; display: block;"></i>
            <p style="font-size: 1.1rem; font-weight: 600;">No se encontraron consultas</p>
            <p style="font-size: 0.9rem;">
                @if(request('buscar') || request('fecha_desde') || request('fecha_hasta'))
                    No hay resultados para los filtros aplicados. Intente con otros criterios.
                @else
                    Aún no ha registrado ninguna consulta médica.
                @endif
            </p>
        </div>
        @endif
    </div>
</div>
@endsection
