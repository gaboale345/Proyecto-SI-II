@extends('layouts.app')

@section('title', 'Mis Recetas y Documentos Médicos - Hospital Plan 3000')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <i class="fa-solid fa-file-prescription text-primary"></i>
            <span>Centro de Documentos y Recetas Médicas</span>
        </div>
    </div>

    @if($documentos->count() == 0)
        <div style="text-align: center; padding: 3rem 1rem; color: #64748b;">
            <i class="fa-solid fa-file-pdf" style="font-size: 3rem; margin-bottom: 1rem; color: #cbd5e1;"></i>
            <h3>No posee documentos o recetas autorizadas aún</h3>
            <p>Las recetas médicas, certificados e informes de laboratorio emitidos durante sus consultas aparecerán aquí listos para visualizar e imprimir.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Cód. Verificación</th>
                        <th>Tipo Documento</th>
                        <th>Título</th>
                        <th>Médico Emisor</th>
                        <th>Fecha Emisión</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($documentos as $doc)
                        <tr>
                            <td><code>{{ $doc->codigo_verificacion }}</code></td>
                            <td>
                                <span class="badge-role" style="background-color: #0f4c81; color: white;">
                                    {{ $doc->tipo_documento }}
                                </span>
                            </td>
                            <td><strong>{{ $doc->titulo }}</strong></td>
                            <td>Dr(a). {{ $doc->medico->usuario->nombre ?? '' }} {{ $doc->medico->usuario->apellido ?? '' }}</td>
                            <td>{{ $doc->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <a href="{{ route('paciente.documento.ver', $doc->id_documento) }}" target="_blank" class="btn btn-sm btn-primary">
                                    <i class="fa-solid fa-print"></i> Ver / Imprimir Documento
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
