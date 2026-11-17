@php
    use Carbon\Carbon;

    $oc = $oc ?? $orden;
    $grNumero = $grNumero ?? $oc->id;
    
    $oc->loadMissing(['ordenItems', 'proveedor', 'presupuesto', 'anticipos']);

    $meses = [
        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio',
        7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
    ];

    $fecha      = Carbon::parse($grFecha ?? now());
    $fechaTexto = $meses[$fecha->month] . ' ' . $fecha->day . ' DE ' . $fecha->year;

    // ── Empresa (fija) ───────────────────────────────────────────
    $empresa = [
        'nombre'    => 'BULL MARKETING S A S',
        'nit'       => '900298176',
        'direccion' => 'CRA 53C No. 127D 23',
        'telefono'  => '4322700',
        'logo'      => public_path('images/bull-marketing.png'),
    ];

    // ── Proveedor ──────────────────────────────────────────
    $prov = $oc->proveedor;
    $proveedor = [
        'nombre'    => $prov->tercero ?? $prov->nombre ?? '',
        'nit'       => $prov->nit ?? $prov->documento ?? '',
        'direccion' => $prov->direccion ?? '',
        'ciudad'    => $prov->ciudad ?? '',
    ];

    // ── Centro de costo ────────────────────────────────────────
    $presupuesto = $oc->presupuesto_resuelto;
    $cCosto      = $presupuesto->cod_cc ?? '';

    // ── Ítems de la OC ───────────────────────────────────────────
    $items = $oc->ordenItems->map(fn ($i) => [
        'descripcion' => $i->desc_oc ?: $i->display_item,
        'c_costo'     => $cCosto,
        'cantidad'    => $i->cant_oc,
        'vr_unitario' => $i->vunit_oc,
        'vr_total'    => $i->vtotal_oc,
    ])->all();

    $gr = [
        'numero'         => str_pad($grNumero, 8, '0', STR_PAD_LEFT),
        'observaciones'    => '',
    ];

    // ── Totales ──────────────────────────────────────────────────
    $subtotal   = collect($items)->sum('vr_total');
    $anticipo   = $oc->anticipos
        ->where('estado_id', '!=', 6)
        ->filter(fn ($anticipo) => filled($anticipo->comprobante_pago))
        ->sum('total_anticipo');
    $total      = $subtotal - $anticipo;

    $fmt  = fn ($n) => number_format((float) $n, 2, '.', ',');
    $logo = $empresa['logo'] ?? null;
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Good Received {{ $gr['numero'] }}</title>
    <style>
        @page { margin: 30px 35px; }
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 10px; color: #000; }

        table { border-collapse: collapse; }
        .w100 { width: 100%; }
        .right { text-align: right; }
        .center { text-align: center; }
        .bold { font-weight: bold; }

        .empresa-nombre { font-size: 15px; font-weight: bold; margin-bottom: 4px; }
        .logo-box { border: 1px solid #555; padding: 4px; text-align: center; }
        .logo-box img { max-width: 230px; max-height: 70px; }
        .logo-text { font-size: 28px; font-weight: bold; color: #ff5a1f; letter-spacing: 1px; }
        .logo-sub { font-size: 9px; letter-spacing: 8px; color: #333; }

        .gr-title { font-weight: bold; font-size: 11px; line-height: 1.3; }
        .fecha { font-size: 9px; }

        /* Estilo para la validez de facturación */
        .valido-facturacion {
            font-size: 8px;
            font-weight: bold;
            border: 1px solid #000;
            background-color: #f2f2f2;
            padding: 4px;
            margin-top: 6px;
            text-align: center;
            letter-spacing: 0.5px;
        }

        .seccion { font-weight: bold; font-size: 12px; text-decoration: underline; margin: 22px 0 6px 0; }
        .datos td { padding: 2px 4px; font-size: 11px; }
        .datos .lbl { width: 55px; }
        .datos .sep { width: 10px; text-align: center; }

        .solicitamos { font-weight: bold; font-size: 12px; margin: 22px 0 8px 0; }
        .pagina { font-size: 8px; text-align: right; margin-bottom: 22px; padding-right: 40px; }

        /* Tabla de detalle */
        table.detalle { width: 100%; border: 1px solid #000; }
        table.detalle th {
            background: #c0c0c0; border: 1px solid #000;
            font-size: 9px; font-weight: bold; padding: 3px; text-align: center;
        }
        table.detalle td { font-size: 9px; padding: 2px 3px; vertical-align: top; }
        table.detalle td.col { border-left: 1px solid #000; border-right: 1px solid #000; }
        table.detalle tr.relleno td { height: 470px; border-left: 1px solid #000; border-right: 1px solid #000; }

        /* Totales */
        table.pie { width: 100%; margin-top: 2px; }
        table.pie td { vertical-align: top; }
        .obs { border: 1px solid #000; height: 105px; padding: 2px 3px; font-size: 9px; }

        table.totales { width: 100%; border: 1px solid #000; }
        table.totales td { border: 1px solid #000; padding: 2px 4px; font-size: 11px; }
        table.totales td.lbl { font-weight: bold; width: 55%; }
        table.totales td.val { text-align: right; width: 45%; }
        table.totales tr.total td { background: #d9d9d9; font-weight: bold; }
        table.totales tr.total td.lbl { letter-spacing: 3px; }
    </style>
</head>
<body>

    {{-- Encabezado --}}
    <table class="w100">
        <tr>
            <td style="width: 55%; vertical-align: top;">
                <div class="empresa-nombre">{{ $empresa['nombre'] }}</div>
                N.I.T. {{ $empresa['nit'] }}<br>
                DIRECCION: {{ $empresa['direccion'] }}<br>
                TELEFONOS: {{ $empresa['telefono'] }}
            </td>
            <td style="width: 45%; vertical-align: top;">
                <div class="logo-box">
                    @if ($logo && file_exists($logo))
                        <img src="{{ $logo }}" alt="{{ $empresa['nombre'] }}">
                    @else
                        <div class="logo-text">BULL</div>
                        <div class="logo-sub">MARKETING</div>
                    @endif
                </div>
                <div class="center gr-title" style="margin-top: 4px;">
                    GOOD RECEIVED<br>
                    Nro. &nbsp;&nbsp;&nbsp;&nbsp;{{ $gr['numero'] }}
                </div>
                {{-- Aviso de validez para facturación --}}
                <div class="valido-facturacion">
                    DOCUMENTO VÁLIDO PARA FACTURACIÓN
                </div>
            </td>
        </tr>
    </table>

    {{-- Proveedor --}}
    <table class="w100" style="margin-top: 6px;">
        <tr>
            <td style="vertical-align: bottom;">
                <div class="seccion" style="margin-top: 14px;">Datos del Proveedor</div>
            </td>
            <td class="right fecha" style="vertical-align: bottom; padding-bottom: 8px;">
                FECHA: {{ $fechaTexto }}
            </td>
        </tr>
    </table>

    <table class="w100 datos">
        <tr>
            <td class="lbl">Nombre</td><td class="sep">:</td>
            <td colspan="2">{{ $proveedor['nombre'] }}</td>
        </tr>
        <tr>
            <td class="lbl">Nit</td><td class="sep">:</td>
            <td colspan="2">{{ $proveedor['nit'] }}</td>
        </tr>
        <tr>
            <td class="lbl">Dirección</td><td class="sep">:</td>
            <td colspan="2">{{ $proveedor['direccion'] }}</td>
        </tr>
        <tr>
            <td class="lbl">Ciudad</td><td class="sep">:</td>
            <td style="width: 45%;">{{ $proveedor['ciudad'] }}</td>
        </tr>
    </table>

    <div class="solicitamos">Solicitamos sean ejecutados los siguientes servicios:</div>
    <div class="pagina">Página &nbsp; 1 &nbsp; de &nbsp; 1</div>

    {{-- Detalle --}}
    <table class="detalle">
        <thead>
            <tr>
                <th style="width: 46%;">DESCRIPCION</th>
                <th style="width: 13%;">C COSTO</th>
                <th style="width: 7%;">CANT</th>
                <th style="width: 15%;">VR UNITARIO</th>
                <th style="width: 19%;">VR TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $item)
                <tr>
                    <td class="col">{{ $item['descripcion'] }}</td>
                    <td class="col center">{{ $item['c_costo'] }}</td>
                    <td class="col right">{{ $fmt($item['cantidad']) }}</td>
                    <td class="col right">{{ $fmt($item['vr_unitario']) }}</td>
                    <td class="col right">{{ $fmt($item['vr_total']) }}</td>
                </tr>
            @endforeach
            {{-- Fila de relleno para mantener el alto de la tabla --}}
            <tr class="relleno">
                <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
            </tr>
        </tbody>
    </table>

    {{-- Observaciones y totales --}}
    <table class="pie">
        <tr>
            <td style="width: 55%; padding-right: 4px;">
                <div class="obs">OBSERVACIONES: {{ $gr['observaciones'] ?? '' }}</div>
            </td>
            <td style="width: 45%;">
                <table class="totales">
                    <tr><td class="lbl">SUBTOTAL</td><td class="val">{{ $fmt($subtotal) }}</td></tr>
                    <tr><td class="lbl">ANTICIPO PAGADO</td><td class="val">{{ $anticipo > 0 ? $fmt($anticipo) : 'No aplica' }}</td></tr>
                    <tr class="total"><td class="lbl">TOTAL A FACTURAR</td><td class="val">{{ $fmt($total) }}</td></tr>
                </table>
            </td>
        </tr>
    </table>

</body>
</html>
