<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $documento->titulo }} - Hospital Plan 3000</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 2rem;
            color: #1e293b;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px solid #0f4c81;
            padding-bottom: 1rem;
            margin-bottom: 1.5rem;
        }
        .hospital-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: #0f4c81;
        }
        .subtitle {
            font-size: 0.9rem;
            color: #64748b;
        }
        .doc-box {
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 1.5rem;
            background: #fff;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        }
        .footer {
            margin-top: 3rem;
            border-top: 1px solid #cbd5e1;
            padding-top: 1rem;
            display: flex;
            justify-content: space-between;
            font-size: 0.8rem;
            color: #64748b;
        }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="margin-bottom: 1rem; text-align: right;">
        <button onclick="window.print();" style="padding: 0.6rem 1.2rem; background-color: #0f4c81; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold;">
            🖨️ Imprimir / Guardar en PDF
        </button>
    </div>

    <div class="header">
        <div>
            <div class="hospital-title">HOSPITAL MUNICIPAL PLAN 3000</div>
            <div class="subtitle">Distrito Municipal 8 — Santa Cruz de la Sierra, Bolivia</div>
            <div class="subtitle">Red de Salud del Gobierno Autónomo Municipal</div>
        </div>
        <div style="text-align: right;">
            <div style="font-weight: bold; color: #0f4c81;">{{ $documento->tipo_documento }}</div>
            <div style="font-size: 0.85rem; color: #64748b;">Código: {{ $documento->codigo_verificacion }}</div>
        </div>
    </div>

    <div class="doc-box">
        {!! $documento->contenido_html !!}
    </div>

    <div class="footer">
        <div>Firma y Sello Médico Autorizado</div>
        <div>Fecha de Emisión: {{ $documento->created_at->format('d/m/Y H:i') }}</div>
        <div>Verificación Digital SUIS: VÁLIDA</div>
    </div>

</body>
</html>
