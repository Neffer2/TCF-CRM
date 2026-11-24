@php
    $fmt = fn($v) => number_format((float) $v, 2, ',', '.');

    $numeroOrden = $orden->cod_oc ?? $orden->id;
    $numeroLimpio = preg_match('/\d+/', $numeroOrden, $m) ? (int)$m[0] : $orden->id;

    $fechaBase = $orden->fecha_aprobacion ?? $orden->created_at ?? now();
    $fecha = \Carbon\Carbon::parse($fechaBase)->locale('es');
    $fechaTexto = ucfirst($fecha->translatedFormat('F d')) . ' DE ' . $fecha->format('Y');

    $orden->loadMissing('ordenItems.itemPresupuesto.presupuesto');
    $codCcOrden = $orden->presupuesto?->cod_cc;

    $esNatural = (int) $orden->tipo_oc === 2;
    $orden->loadMissing($esNatural ? 'naturalInfo.tercero' : 'proveedor');
    $prov = $esNatural ? $orden->naturalInfo?->tercero : $orden->proveedor;
    
    $nombreProv = $esNatural
        ? (trim(($prov->nombre ?? '') . ' ' . ($prov->apellido ?? '')) ?: 'N/A')
        : ($prov?->tercero ?: 'N/A');
    $nitProv = $esNatural
        ? ($prov->cedula ?? $prov->documento ?? 'N/A')
        : ($prov->nit ?? $prov->documento ?? 'N/A');
    $dirProv     = $prov->direccion ?? 'N/A';
    $ciudadProv  = $prov->ciudad ?? 'N/A';
    $correoProv  = $prov->correo ?? $prov->email ?? '';
    $contactoProv = $esNatural
        ? ($prov->telefono ?? $prov->celular ?? '')
        : ($prov->contacto ?? $prov->telefono ?? $prov->celular ?? '');

    $subtotal = $orden->ordenItems->sum('vtotal_oc');
    $iva        = $orden->iva         ?? 0;
    $retefuente = $orden->retefuente ?? 0;
    $reteiva    = $orden->reteiva    ?? 0;
    $reteica    = $orden->reteica    ?? 0;
    $total      = $subtotal + $iva - $retefuente - $reteiva - $reteica;
    $esBorrador = $esBorrador ?? ((int) (auth()->user()->rol ?? 0) === 6);
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Orden de Compra {{ str_pad($numeroLimpio, 8, '0', STR_PAD_LEFT) }}</title>
    <style>
        @page { margin: 40px 38px 30px 38px; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 10px; color: #000; margin: 0; }
        .marca-agua {
            position: fixed;
            top: 47%;
            left: 0;
            width: 100%;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            color: #a00000;
            opacity: 0.24;
            transform: rotate(-30deg);
            z-index: 10;
        }
        table { border-collapse: collapse; width: 100%; }
        td, th { vertical-align: top; }
        .empresa-nombre { font-size: 18px; font-weight: bold; padding-bottom: 6px; }
        .empresa-datos td { font-size: 10px; padding: 1px 0; }
        .logo { width: 260px; height: auto; }
        .oc-titulo { font-size: 13px; font-weight: bold; text-align: right; line-height: 1.3; }
        .fecha { font-size: 9.5px; text-align: right; }
        .proveedor td { font-size: 9.5px; padding: 3px 0; }
        .proveedor .lbl { font-weight: bold; width: 62px; }
        .sub-header td { font-size: 9.5px; padding: 6px 0 3px 0; }
        .items { border: 1px solid #000; }
        .items th { background: #c8c8c8; border: 1px solid #000; font-size: 9.5px; font-weight: bold; text-align: center; padding: 3px; }
        .items td { font-size: 9.5px; padding: 2px 3px; border-left: 1px solid #000; border-right: 1px solid #000; }
        .items td.num { text-align: right; }
        .items td.ctr { text-align: center; }
        .items tr.relleno td { height: 260px; }
        .pie { border: 1px solid #000; border-top: 1px solid #000; }
        .pie td.obs { width: 62%; font-size: 9.5px; padding: 2px 3px; border-right: 1px solid #000; }
        .totales td { font-size: 9.5px; padding: 2px 3px; border-bottom: 1px solid #000; }
        .totales td.lbl { font-weight: bold; width: 40%; border-right: 1px solid #000; background: #fff; }
        .totales td.val { text-align: right; }
        .totales tr.total td { background: #c8c8c8; }
        .totales tr.total td.val { background: #fff; }
        .aceptada { border: 1px solid #000; border-top: none; font-size: 9px; padding: 2px 3px; }
        .legal { border: 1px solid #000; border-top: none; font-size: 6.2px; line-height: 1.25; padding: 2px 3px 10px 3px; text-align: justify; }
        .firmas td { font-size: 10px; padding-top: 16px; }
        .firmas .rol { font-size: 6.5px; padding-top: 18px; }
        .aprobaciones { margin-top: 10px; page-break-inside: avoid; }
        .aprobaciones th, .aprobaciones td { border: 1px solid #000; padding: 4px 6px; font-size: 9px; text-align: left; }
        .aprobaciones th { background: #c8c8c8; }
    </style>
</head>
<body>
    @if ($esBorrador)
        <div class="marca-agua">BORRADOR NO VALIDO PARA EJECUTAR</div>
    @endif

    <table>
        <tr>
            <td style="width:58%;">
                <div class="empresa-nombre">BULL MARKETING S A S</div>
                <table class="empresa-datos">
                    <tr><td><b>N.I.T.</b>&nbsp;&nbsp; 900298176</td></tr>
                    <tr><td><b>DIRECCION:</b>&nbsp; CRA 53C No. 127D 23</td></tr>
                    <tr><td><b>TELEFONOS:</b>&nbsp; 4322700</td></tr>
                    <tr><td><b>CORREO:</b> facturacion.proveedores@bullmarketing.com.co</td></tr>
                </table>
            </td>
            <td style="width:42%; text-align:right;">
                <img class="logo" src="{{ public_path('images/bull-marketing-logo.png') }}" alt="BULL Marketing">
                <div class="oc-titulo">
                    ORDEN DE COMPRA<br>
                    Nro. {{ str_pad($numeroLimpio, 8, '0', STR_PAD_LEFT) }}
                </div>
            </td>
        </tr>
    </table>

    <table style="margin-top:6px;">
        <tr>
            <td style="font-size:9.5px;">{{ $esNatural ? 'Datos del Tercero' : 'Datos del Proveedor' }}</td>
            <td class="fecha">FECHA: {{ $fechaTexto }}</td>
        </tr>
    </table>

    <table class="proveedor" style="margin-top:6px;">
        <tr>
            <td class="lbl">Nombre :</td>
            <td colspan="3">{{ $nombreProv }}</td>
        </tr>
        <tr>
            <td class="lbl">Ident. &nbsp;:</td>
            <td colspan="3">{{ $nitProv }}</td>
        </tr>
        <tr>
            <td class="lbl">Dirección :</td>
            <td colspan="3">{{ $dirProv }}</td>
        </tr>
        <tr>
            <td class="lbl">Ciudad &nbsp;:</td>
            <td style="width:40%;">{{ $ciudadProv }}</td>
            <td colspan="2"><b>PROYECTO:</b> {{ optional(optional(optional($orden->presupuesto)->gestion))->nom_proyecto_cot ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="lbl">Correo:</td>
            <td><span>{{ $correoProv }}</span></td>
            <td class="lbl" style="width:60px;">Contacto:</td>
            <td>{{ $contactoProv }}</td>
        </tr>
        <tr>
            <td class="lbl">Productor solicitante:</td>
            <td style="width:40%;">{{ $orden->productor_solicitante?->name ?? 'Sin productor registrado' }}</td>
            <td colspan="2"><b>Negociación:</b> {{ number_format($orden->porcentaje, 0) }}%</td>
        </tr>
    </table>

    <table class="sub-header">
        <tr>
            <td>Solicitamos sean ejecutados los siguientes servicios:</td>
            <td style="text-align:right;">Página 1 de 1</td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th style="width:50%;">DESCRIPCION</th>
                <th style="width:20%;">CENTRO DE COSTOS</th>
                <th style="width:13%;">ITEM #</th>
                <th style="width:7%;">CANT</th>
                <th style="width:14%;">VR UNITARIO</th>
                <th style="width:16%;">VR TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orden->ordenItems as $item)
                @php
                    $itemPresupuesto = $item->itemPresupuesto;
                    $codCcItem = $itemPresupuesto?->presupuesto?->cod_cc;
                @endphp
                <tr>
                    <td>
                        @if (filled($item->desc_oc))
                            <div>{{ $item->desc_oc }}</div>
                        @endif
                        @if (filled($itemPresupuesto?->descripcion))
                            <div>{{ $itemPresupuesto->descripcion }}</div>
                        @endif
                    </td>
                    <td>{{ $codCcItem ?: ($item->cod_cc ?: ($codCcOrden ?: 'N/A')) }}</td>
                    <td class="ctr">{{ $item->num_item ?? $itemPresupuesto?->num_item }}</td>
                    <td class="num">{{ $fmt($item->cant_oc) }}</td>
                    <td class="num">{{ $fmt($item->vunit_oc) }}</td>
                    <td class="num">{{ $fmt($item->vtotal_oc) }}</td>
                </tr>
            @endforeach
            <tr class="relleno">
                <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>web
            </tr>
        </tbody>
    </table>

    <table class="pie">
        <tr>
            <td class="obs">OBSERVACIONES: {{ $orden->observaciones_negociacion ?? $orden->observacion_remision ?? '' }}</td>
            <td style="padding:0;">
                <table class="totales">
                    <tr><td class="lbl">SUBTOTAL</td><td class="val">{{ $fmt($subtotal) }}</td></tr>
                    <tr class="total"><td class="lbl">T O T A L E S</td><td class="val">{{ $fmt($total) }}</td></tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="aceptada">
        Los valores presentados son antes de impuestos y retenciones, los cuales se aplicarán según la normatividad vigente. Esta orden de compra está sujeta a la aceptación de los términos y condiciones establecidos en la cotización comercial correspondiente.
        Este documento no es válido para facturación; el único documento válido para este fin es el GR (Good Receipt).
    </div>

    <div class="legal">
        Su cotización comercial hace parte integral de la presente orden... (condiciones legales estándar).
    </div>

    <table class="aprobaciones">
        <thead>
            <tr><th>Etapa de aprobación</th><th>Usuario que aprobó</th></tr>
        </thead>
        <tbody>
            @foreach ($orden->aprobacionesParaFormato() as $aprobacion)
                <tr>
                    <td>{{ $aprobacion['cargo'] }}</td>
                    <td>{{ $aprobacion['nombre'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
