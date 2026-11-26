<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Certificado de Solvencia</title>
    <style>
        @page {
            margin: 28px 34px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #1f2937;
        }

        .header {
            border-bottom: 3px solid #b45309;
            padding-bottom: 10px;
            margin-bottom: 18px;
        }

        .header h1 {
            font-size: 15px;
            margin: 0;
            color: #7c2d12;
            letter-spacing: .5px;
        }

        .header p {
            margin: 2px 0 0;
            font-size: 10px;
            color: #57534e;
        }

        .title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 2px;
            margin: 10px 0 4px;
        }

        .subtitle {
            text-align: center;
            font-size: 10px;
            color: #57534e;
            margin-bottom: 18px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .datos td {
            padding: 4px 6px;
            vertical-align: top;
        }

        .datos .label {
            width: 32%;
            color: #57534e;
            font-weight: bold;
        }

        .predios th {
            background: #fef3c7;
            border: 1px solid #d6d3d1;
            padding: 6px;
            text-align: left;
            font-size: 10px;
        }

        .predios td {
            border: 1px solid #d6d3d1;
            padding: 6px;
            font-size: 10px;
        }

        .leyenda {
            margin-top: 18px;
            padding: 12px 14px;
            background: #fffbeb;
            border: 1px solid #fcd34d;
            line-height: 1.5;
        }

        .valor {
            margin-top: 14px;
            text-align: right;
            font-size: 13px;
            font-weight: bold;
        }

        .firmas {
            margin-top: 60px;
            width: 100%;
        }

        .firmas td {
            width: 50%;
            text-align: center;
            padding-top: 40px;
        }

        .linea {
            border-top: 1px solid #57534e;
            margin: 0 30px;
            padding-top: 6px;
            font-size: 10px;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8px;
            color: #78716c;
            border-top: 1px solid #e7e5e4;
            padding-top: 6px;
        }
    </style>
</head>
<body>
    @php
        $comprobante = $certificado->comprobante;
        $contribuyente = $comprobante?->contribuyente;
        $punto = $comprobante?->puntoEmision;
        $numero = $comprobante
            ? sprintf(
                '%s-%s-%s',
                str_pad((string) $punto?->establecimiento_codigo, 3, '0', STR_PAD_LEFT),
                str_pad((string) $punto?->punto_codigo, 3, '0', STR_PAD_LEFT),
                $comprobante->secuencial,
            )
            : '—';
    @endphp

    <div class="header">
        <h1>BENEMÉRITO CUERPO DE BOMBEROS DE MONTECRISTI</h1>
        <p>GESADMEN · Sistema de Gestión y Recaudación</p>
    </div>

    <div class="title">CERTIFICADO DE SOLVENCIA</div>
    <div class="subtitle">
        No. {{ $numero }} · Fecha de emisión: {{ $comprobante?->fecha_emision?->format('d/m/Y') ?? '—' }}
    </div>

    <table class="datos">
        <tr>
            <td class="label">Solicitante</td>
            <td>{{ $contribuyente?->razon_social ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Identificación</td>
            <td>{{ $contribuyente?->identificacion ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Tipo de trámite</td>
            <td>{{ $certificado->tipoTramite?->nombre ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Descripción del trámite</td>
            <td>{{ $certificado->descripcion_tramite }}</td>
        </tr>
        <tr>
            <td class="label">A favor de</td>
            <td>{{ $certificado->a_favor_de }}</td>
        </tr>
    </table>

    <p style="margin: 18px 0 6px; font-weight: bold;">Predios incluidos en la certificación</p>
    <table class="predios">
        <thead>
            <tr>
                <th style="width: 8%;">#</th>
                <th>Código catastral</th>
                <th>Matrícula</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($certificado->predios as $indice => $predio)
                <tr>
                    <td>{{ $indice + 1 }}</td>
                    <td>{{ $predio->codigo_catastral }}</td>
                    <td>{{ $predio->matricula ?? '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">Sin predios registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="leyenda">{{ $certificado->leyenda }}</div>

    <div class="valor">Valor: ${{ number_format((float) $certificado->valor, 2) }}</div>

    <table class="firmas">
        <tr>
            <td><div class="linea">Firma del solicitante</div></td>
            <td><div class="linea">Tesorería / Recaudación</div></td>
        </tr>
    </table>

    <div class="footer">
        Documento generado electrónicamente por GESADMEN. Válido sin enmiendas ni tachaduras.
    </div>
</body>
</html>
