<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Orden de anticipo {{ $a->cod_orden_anticipo ?? '#'.$a->id }}</title>
    <style>
        @page { margin: 34px 38px 62px; }

        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1E1915; line-height: 1.4; }
        table { width: 100%; border-collapse: collapse; }

        /* ---------- Encabezado ---------- */
        .empresa b { font-size: 13px; letter-spacing: .3px; }
        .empresa span { display: block; color: #6A6159; font-size: 9.5px; line-height: 1.5; }
        .titulo { text-align: right; vertical-align: top; }
        .titulo h1 { font-size: 17px; margin: 0 0 4px; letter-spacing: 1px; color: #1E1915; }
        .titulo .nro {
            display: inline-block; background: #EF5F17; color: #FFFFFF;
            font-size: 12px; font-weight: bold; padding: 4px 12px; margin-bottom: 4px;
        }
        .titulo span { display: block; color: #6A6159; font-size: 9.5px; margin-top: 2px; }
        .raya { border-top: 3px solid #EF5F17; margin: 12px 0 4px; }

        /* ---------- Títulos de sección ---------- */
        h2 {
            font-size: 9.5px; letter-spacing: 1.2px; text-transform: uppercase;
            color: #C94A0E; margin: 16px 0 6px; padding: 2px 0 2px 8px;
            border-left: 3px solid #EF5F17;
        }

        /* ---------- Datos (proveedor / proyecto) ---------- */
        .col { vertical-align: top; width: 49%; }
        .gap { width: 2%; }
        .datos td { padding: 5px 7px; border-bottom: 1px solid #E9E2DA; vertical-align: top; }
        .datos td.k { width: 34%; color: #6A6159; font-size: 9px; text-transform: uppercase; letter-spacing: .4px; }
        .datos td.v { font-weight: bold; }
        .datos tr:last-child td { border-bottom: 1px solid #1E1915; }
        .datos.top { border-top: 1px solid #1E1915; }

        /* ---------- Tablas con encabezado (ítems / aprobaciones) ---------- */
        .grid th {
            background: #1E1915; color: #FFFFFF; text-align: left; padding: 6px 7px;
            font-size: 9px; text-transform: uppercase; letter-spacing: .5px;
        }
        .grid td { padding: 6px 7px; border-bottom: 1px solid #E9E2DA; vertical-align: top; }
        .grid tr.par td { background: #F8F5F2; }
        .grid .c { text-align: center; }
        .grid .r { text-align: right; }
        .grid .vacio { text-align: center; color: #6A6159; padding: 12px; }

        /* ---------- Valor del anticipo ---------- */
        .valores td { padding: 7px 10px; border-bottom: 1px solid #E9E2DA; }
        .valores .k { color: #6A6159; }
        .valores .v { text-align: right; font-size: 11px; font-weight: bold; width: 35%; }
        .valores .total td {
            background: #FDECE2; border-top: 2px solid #EF5F17; border-bottom: 2px solid #EF5F17;
            font-size: 13px; font-weight: bold; color: #1E1915;
        }
        .valores .total .v { color: #C94A0E; font-size: 14px; }

        /* ---------- Aprobaciones ---------- */
        .firmas td.paso { font-weight: bold; }
        .firmas td.sub { color: #6A6159; }

        /* ---------- Pie ---------- */
        .pie {
            position: fixed; bottom: -42px; left: 0; right: 0;
            border-top: 1px solid #E9E2DA; padding-top: 5px;
            font-size: 8px; color: #9A9088; text-align: center;
        }
        .pagenum:before { content: counter(page); }
    </style>
</head>
<body>
    @php
        $esEmpleado = (int) $orden?->tipo_oc === 4;
    @endphp
    <div class="pie">
        Generada por BULLCRM el {{ $fecha($a->fecha_solicitud) }} · Anticipo #{{ $a->id }} ·
        Válida con las aprobaciones registradas en el CRM · Página <span class="pagenum"></span>
    </div>

    {{-- ENCABEZADO --}}
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

    {{-- PROVEEDOR / PROYECTO (lado a lado) --}}
    <table>
        <tr>
            <td class="col">
                <h2>{{ $esEmpleado ? 'Empleado' : ($esNatural ? 'Tercero (persona natural)' : 'Proveedor') }}</h2>
                <table class="datos top">
                    <tr>
                        <td class="k">{{ $contraparte['etiqueta'] }}</td>
                        <td class="v">{{ $contraparte['nombre'] ?: 'No Registra' }}</td>
                    </tr>
                    <tr>
                        <td class="k">{{ ($esEmpleado || $esNatural) ? 'Cédula' : 'NIT' }}</td>
                        <td class="v">
                            {{ $contraparte['documento'] ?: 'No Registra' }}{{ !$esEmpleado && !$esNatural && filled($contraparte['dv']) ? ''.$contraparte['dv'] : '' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="k">Dirección</td>
                        <td>{{ trim(($contraparte['direccion'] ?: '').' '.($contraparte['ciudad'] ?: '')) ?: 'No Registra' }}</td>
                    </tr>
                    <tr>
                        <td class="k">Contacto</td>
                        <td>{{ $contraparte['contacto'] ?: 'No Registra' }}</td>
                    </tr>
                </table>
            </td>
            <td class="gap"></td>
            <td class="col">
                <h2>Proyecto</h2>
                <table class="datos top">
                    <tr><td class="k">Proyecto</td><td class="v">{{ optional($gestion)->nom_proyecto_cot ?: 'No aplica' }}</td></tr>
                    <tr><td class="k">Cliente</td><td>{{ optional(optional($gestion)->contacto)->empresa ?: 'No aplica' }}</td></tr>
                    <tr><td class="k">Centro de costos</td><td>{{ optional($presupuesto)->cod_cc ?: 'No aplica' }}</td></tr>
                    <tr><td class="k">Productor</td><td>{{ optional($productor)->name ?: '—' }}</td></tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- ÍTEMS --}}
    <h2>Ítems de la orden de compra</h2>
    <table class="grid">
        <thead>
            <tr>
                <th class="c" style="width: 7%">No.</th>
                <th class="c" style="width: 7%">Cant</th>
                <th class="c" style="width: 7%">Días</th>
                <th class="c" style="width: 7%">Otros</th>
                <th style="width: 14%">Centro de costos</th>
                <th>Descripción</th>
                <th class="r" style="width: 14%">V. Uni</th>
                <th class="r" style="width: 14%">V. Total</th>
            </tr>
        </thead>
        <tbody>
            @if ($orden && $orden->ordenItems && $orden->ordenItems->count() > 0)
                @foreach ($orden->ordenItems as $item)
                    <tr class="{{ $loop->even ? 'par' : '' }}">
                        <td class="c">{{ $item->itemPresupuesto->num_item }}</td>
                        <td class="c">{{ $item->cant_oc }}</td>
                        <td class="c">{{ $item->dias_oc }}</td>
                        <td class="c">{{ $item->otros_oc }}</td>
                        <td>{{ $item->itemPresupuesto->presto->cod_cc ?? '-' }}</td>
                        <td>{{ $item->desc_oc }}</td>
                        <td class="r">$ {{ number_format($item->vunit_oc, 2, ',', '.') }}</td>
                        <td class="r" style="font-weight: bold;">$ {{ number_format($item->vtotal_oc, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="8" class="vacio">Esta orden no contiene ítems registrados o no aplica.</td>
                </tr>
            @endif
        </tbody>
    </table>

    {{-- VALOR DEL ANTICIPO (alineado a la derecha) --}}
    <h2>Valor del anticipo</h2>
    <table>
        <tr>
            <td style="width: 45%"></td>
            <td>
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
                        <td class="k" style="color: #1E1915;">Valor a anticipar</td>
                        <td class="v">{{ $pesos($a->total_anticipo) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- APROBACIONES --}}
    <h2>Aprobaciones</h2>
    <table class="grid firmas">
        <thead>
            <tr>
                <th style="width: 22%">Paso</th>
                <th style="width: 28%">Nombre</th>
                <th style="width: 15%">Fecha</th>
                <th>Observación</th>
            </tr>
        </thead>
        <tbody>
            @if ($solicitud)
                <tr>
                    <td class="paso">Solicitó (productor)</td>
                    <td>{{ optional($solicitud->usuario)->name ?: '—' }}</td>
                    <td>{{ $fecha($solicitud->created_at) }}</td>
                    <td class="sub">{{ $solicitud->observacion ?: '—' }}</td>
                </tr>
            @else
                <tr>
                    <td class="paso">Solicitó (productor)</td>
                    <td>{{ optional($productor)->name ?: '—' }}</td>
                    <td>{{ $fecha($a->fecha_solicitud) }}</td>
                    <td class="sub">—</td>
                </tr>
            @endif

            @foreach ($aprobaciones as $ap)
                <tr class="{{ $loop->odd ? 'par' : '' }}">
                    <td class="paso">{{ $puesto[$ap->accion] ?? $ap->accion }}</td>
                    <td>{{ optional($ap->usuario)->name ?: '—' }}</td>
                    <td>{{ $fecha($ap->created_at) }}</td>
                    <td class="sub">{{ $ap->observacion ?: '—' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
