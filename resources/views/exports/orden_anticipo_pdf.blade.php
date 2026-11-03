<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Orden de anticipo {{ $a->cod_orden_anticipo ?? '#'.$a->id }}</title>
    <style>
        @page { margin: 34px 38px 60px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10.5px; color: #1E1915; }
        table { width: 100%; border-collapse: collapse; }
        .empresa b { font-size: 14px; }
        .empresa span { display: block; color: #6A6159; line-height: 1.5; }
        .titulo { text-align: right; vertical-align: top; }
        .titulo h1 { font-size: 17px; margin: 0 0 2px; letter-spacing: .5px; }
        .titulo .nro { font-size: 13px; font-weight: bold; color: #C94A0E; }
        .titulo span { display: block; color: #6A6159; margin-top: 2px; }
        .raya { border-top: 2px solid #EF5F17; margin: 12px 0 14px; }
        h2 { font-size: 10px; letter-spacing: 1.2px; text-transform: uppercase; color: #6A6159; margin: 16px 0 6px; }
        .datos td { padding: 4px 6px; border-bottom: 1px solid #E9E2DA; vertical-align: top; }
        .datos td.k { width: 26%; color: #6A6159; }
        .valores td { border: 1px solid #1E1915; padding: 7px 8px; }
        .valores .k { background: #F2EEEA; font-weight: bold; width: 55%; }
        .valores .v { text-align: right; font-size: 11.5px; }
        .valores .total td { background: #FDECE2; font-size: 13px; font-weight: bold; }
        .firmas th { background: #F2EEEA; text-align: left; padding: 6px; font-size: 9.5px; border: 1px solid #1E1915; }
        .firmas td { padding: 6px; border: 1px solid #1E1915; vertical-align: top; }
        .pie { position: fixed; bottom: -40px; left: 0; right: 0; font-size: 8.5px; color: #9A9088; text-align: center; }
    </style>
</head>
<body>
    <div class="pie">
        Generada por BULLCRM el {{ $fecha($a->fecha_solicitud) }} · Anticipo #{{ $a->id }} · Válida con las aprobaciones registradas en el CRM.
    </div>

    <table>
        <tr>
            <td class="empresa" style="vertical-align: top">
                @if ($logo)
                    <img src="{{ $logo }}" height="46" alt="Bull Marketing"><br><br>
                @endif
                <b>BULL MARKETING S.A.S.</b>
                <span>NIT 900298176</span>
                <span>Cra 53C No. 127D 23</span>
                <span>Tel. 4322700</span>
            </td>
            <td class="titulo">
                <h1>ORDEN DE ANTICIPO</h1>
                <div class="nro">{{ $a->cod_orden_anticipo ?? '#'.$a->id }}</div>
                <span>Fecha: {{ $a->fecha_solicitud ? \Illuminate\Support\Carbon::parse($a->fecha_solicitud)->format('d/m/Y') : '—' }}</span>
                <span>{{ $orden ? 'Sobre la orden de compra '.($orden->cod_oc ?: '#'.$orden->id) : 'Sin orden de compra previa' }}</span>
            </td>
        </tr>
    </table>
    <div class="raya"></div>

    <h2>{{ $esNatural ? 'Tercero (persona natural)' : 'Proveedor' }}</h2>
    <table class="datos">
        <tr>
            <td class="k">{{ $contraparte['etiqueta'] }}</td>
            <td>{{ $contraparte['nombre'] ?: '—' }}</td>
        </tr>
        <tr>
            <td class="k">{{ $esNatural ? 'cedula' : 'NIT' }}</td>
            <td>
                {{ $contraparte['documento'] ?: '—' }}{{ !$esNatural && filled($contraparte['dv']) ? '-'.$contraparte['dv'] : '' }}
            </td>
        </tr>
        <tr>
            <td class="k">Dirección</td>
            <td>{{ trim(($contraparte['direccion'] ?: '').' '.($contraparte['ciudad'] ?: '')) ?: '—' }}</td>
        </tr>
        <tr>
            <td class="k">Contacto</td>
            <td>{{ $contraparte['contacto'] ?: '—' }}</td>
        </tr>
    </table>

    <h2>Proyecto</h2>
    <table class="datos">
        <tr><td class="k">Proyecto</td><td>{{ optional($gestion)->nom_proyecto_cot ?: '—' }}</td></tr>
        <tr><td class="k">Cliente</td><td>{{ optional(optional($gestion)->contacto)->empresa ?: '—' }}</td></tr>
        <tr><td class="k">Centro de costos</td><td>{{ optional($presupuesto)->cod_cc ?: '—' }}</td></tr>
        <tr><td class="k">Productor</td><td>{{ optional($productor)->name ?: '—' }}</td></tr>
    </table>

    <h2>Valor del anticipo</h2>
    <table class="valores">
        <tr>
            <td class="k">{{ $orden ? 'Valor de la orden de compra' : 'Valor base (según soporte del proveedor)' }}</td>
            <td class="v">{{ $pesos($valorBase) }}</td>
        </tr>
        <tr>
            <td class="k">Porcentaje de anticipo</td>
            <td class="v">{{ rtrim(rtrim(number_format((float) $a->porcentaje_anticipo, 2, ',', '.'), '0'), ',') }} %</td>
        </tr>
        <tr class="total">
            <td class="k">Valor a anticipar</td>
            <td class="v">{{ $pesos($a->total_anticipo) }}</td>
        </tr>
    </table>

    <h2>Aprobaciones</h2>
    <table class="firmas">
        <tr>
            <th style="width: 22%">Paso</th>
            <th style="width: 28%">Nombre</th>
            <th style="width: 20%">Fecha</th>
            <th>Observación</th>
        </tr>
        @if ($solicitud)
            <tr>
                <td>Solicitó (productor)</td>
                <td>{{ optional($solicitud->usuario)->name ?: '—' }}</td>
                <td>{{ $fecha($solicitud->created_at) }}</td>
                <td>{{ $solicitud->observacion ?: '—' }}</td>
            </tr>
        @else
            <tr>
                <td>Solicitó (productor)</td>
                <td class="k">Productor</td><td>{{ optional($productor)->name ?: '—' }}</td>
                <td>{{ $fecha($a->fecha_solicitud) }}</td>
                <td>—</td>
            </tr>
        @endif

        @foreach ($aprobaciones as $ap)
            <tr>
                <td>{{ $puesto[$ap->accion] ?? $ap->accion }}</td>
                <td>{{ optional($ap->usuario)->name ?: '—' }}</td>
                <td>{{ $fecha($ap->created_at) }}</td>
                <td>{{ $ap->observacion ?: '—' }}</td>
            </tr>
        @endforeach
    </table>
</body>
</html>