@php
    $fmt = fn($v) => number_format((float) $v, 2, ',', '.');

    // Mapeo del número de orden (usando el código o ID real)
    $numeroOrden = $orden->cod_oc ?? $orden->id;
    // Extraer solo los números si el código tiene formato ej: "OC 001" -> 1
    $numeroLimpio = preg_match('/\d+/', $numeroOrden, $m) ? (int)$m[0] : $orden->id;

    // Fecha de emisión o aprobación
    $fechaBase = $orden->fecha_aprobacion ?? $orden->created_at ?? now();
    $fecha = \Carbon\Carbon::parse($fechaBase)->locale('es');
    $fechaTexto = ucfirst($fecha->translatedFormat('F d')) . ' DE ' . $fecha->format('Y');

    // Datos del proveedor real
    $prov = $orden->proveedor;

    // Mapeo de ítems reales desde la relación ordenItems
    $itemsTransformados = $orden->ordenItems->map(function($item) {
        return (object)[
            'descripcion'    => $item->desc_oc,
            'centro_costo'   => $item->num_item ?? '-', // O el campo de centro de costo si aplica
            'cantidad'       => $item->cant_oc,
            'valor_unitario' => $item->vunit_oc,
        0];
    });

    $subtotal = $orden->ordenItems->sum('vtotal_oc');
    $iva        = $orden->iva         ?? 0;
    $retefuente = $orden->retefuente ?? 0;
    $reteiva    = $orden->reteiva    ?? 0;
    $reteica    = $orden->reteica    ?? 0;
    $total      = $subtotal + $iva - $retefuente - $reteiva - $reteica;
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Orden de Compra {{ str_pad($numeroLimpio, 8, '0', STR_PAD_LEFT) }}</title>
    <style>
        @page { margin: 40px 38px 30px 38px; }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10px;
            color: #000;
            margin: 0;
        }

        table { border-collapse: collapse; width: 100%; }
        td, th { vertical-align: top; }

        .empresa-nombre { font-size: 18px; font-weight: bold; padding-bottom: 6px; }
        .empresa-datos td { font-size: 10px; padding: 1px 0; }
        .empresa-datos b { font-weight: bold; }

        .logo { width: 260px; height: auto; }
        .oc-titulo { font-size: 13px; font-weight: bold; text-align: right; line-height: 1.3; }
        .fecha { font-size: 9.5px; text-align: right; }

        .proveedor td { font-size: 9.5px; padding: 3px 0; }
        .proveedor .lbl { font-weight: bold; width: 62px; }

        .sub-header td { font-size: 9.5px; padding: 6px 0 3px 0; }

        /* Tabla de ítems */
        .items { border: 1px solid #000; }
        .items th {
            background: #c8c8c8;
            border: 1px solid #000;
            font-size: 9.5px;
            font-weight: bold;
            text-align: center;
            padding: 3px;
        }
        .items td {
            font-size: 9.5px;
            padding: 2px 3px;
            border-left: 1px solid #000;
            border-right: 1px solid #000;
        }
        .items td.num { text-align: right; }
        .items td.ctr { text-align: center; }
        .items tr.relleno td { height: 260px; }

        /* Observaciones + totales */
        .pie { border: 1px solid #000; border-top: 1px solid #000; }
        .pie td.obs { width: 62%; font-size: 9.5px; padding: 2px 3px; border-right: 1px solid #000; }
        .totales td { font-size: 9.5px; padding: 2px 3px; border-bottom: 1px solid #000; }
        .totales td.lbl { font-weight: bold; width: 40%; border-right: 1px solid #000; background: #fff; }
        .totales td.val { text-align: right; }
        .totales tr.total td { background: #c8c8c8; }
        .totales tr.total td.val { background: #fff; }
        .totales tr:last-child td { border-bottom: none; }

        .aceptada {
            border: 1px solid #000;
            border-top: none;
            font-size: 9px;
            padding: 2px 3px;
        }

        .legal {
            border: 1px solid #000;
            border-top: none;
            font-size: 6.2px;
            line-height: 1.25;
            padding: 2px 3px 10px 3px;
            text-align: justify;
        }

        .firmas td { font-size: 10px; padding-top: 16px; }
        .firmas .rol { font-size: 6.5px; padding-top: 18px; }
    </style>
</head>
<body>

    {{-- ENCABEZADO --}}
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
                {{-- Si usas imagen pública o asset local --}}
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
            <td style="font-size:9.5px;">Datos del Proveedor</td>
            <td class="fecha">FECHA: {{ $fechaTexto }}</td>
        </tr>
    </table>

    {{-- DATOS DEL PROVEEDOR (Adaptados a la tabla proveedor de tu base de datos) --}}
    <table class="proveedor" style="margin-top:6px;">
        <tr>
            <td class="lbl">Nombre :</td>
            <td colspan="3">{{ $prov->tercero ?? $prov->nombre ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="lbl">Nit &nbsp;&nbsp;&nbsp;:</td>
            <td colspan="3">{{ $prov->nit ?? $prov->documento ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="lbl">Dirección :</td>
            <td colspan="3">{{ $prov->direccion ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="lbl">Ciudad &nbsp;:</td>
            <td style="width:40%;">{{ $prov->ciudad ?? 'N/A' }}</td>
            <td colspan="2"><b>PROYECTO / ORIGEN:</b> {{ optional($orden->presupuesto->gestion)->nom_proyecto_cot ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="lbl">Correo:</td>
            <td><span>{{ $prov->correo ?? $prov->email ?? '' }}</span></td>
            <td class="lbl" style="width:60px;">Contacto:</td>
            <td>{{ $prov->contacto ?? '' }}</td>
        </tr>
    </table>

    <table class="sub-header">
        <tr>
            <td>Solicitamos sean ejecutados los siguientes servicios:</td>
            <td style="text-align:right;">Página 1 de 1</td>
        </tr>
    </table>

    {{-- ÍTEMS REALES --}}
    <table class="items">
        <thead>
            <tr>
                <th style="width:50%;">DESCRIPCION</th>
                <th style="width:13%;">ITEM #</th>
                <th style="width:7%;">CANT</th>
                <th style="width:14%;">VR UNITARIO</th>
                <th style="width:16%;">VR TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orden->ordenItems as $item)
                <tr>
                    <td>{{ $item->desc_oc }}</td>
                    <td class="ctr">{{ $item->num_item }}</td>
                    <td class="num">{{ $fmt($item->cant_oc) }}</td>
                    <td class="num">{{ $fmt($item->vunit_oc) }}</td>
                    <td class="num">{{ $fmt($item->vtotal_oc) }}</td>
                </tr>
            @endforeach
            <tr class="relleno">
                <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
            </tr>
        </tbody>
    </table>

    {{-- OBSERVACIONES Y TOTALES --}}
    <table class="pie">
        <tr>
            <td class="obs">OBSERVACIONES: {{ $orden->observaciones_negociacion ?? $orden->observacion_remision ?? '' }}</td>
            <td style="padding:0;">
                <table class="totales">
                    <tr><td class="lbl">SUBTOTAL</td><td class="val">{{ $fmt($subtotal) }}</td></tr>
                    <tr><td class="lbl">IVA</td><td class="val">{{ $fmt($iva) }}</td></tr>
                    <tr><td class="lbl">RETEFUENTE</td><td class="val">{{ $fmt($retefuente) }}</td></tr>
                    <tr><td class="lbl">RETEIVA</td><td class="val">{{ $fmt($reteiva) }}</td></tr>
                    <tr><td class="lbl">RETEICA</td><td class="val">{{ $fmt($reteica) }}</td></tr>
                    <tr class="total"><td class="lbl">T O T A L E S</td><td class="val">{{ $fmt($total) }}</td></tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="aceptada">
        Nos permitimos comunicarles que su oferta comercial ha sido aceptada, razón por la cual les solicitamos el suministro de los siguientes items:
    </div>

    {{-- CONDICIONES --}}
    <div class="legal">
        Su cotización comercial hace parte integral de la presente orden en todo lo relativo a condiciones comerciales, compromisos adquiridos, plazos de entrega, cantidades, características, materiales, elementos y valores.<br>
        BULL MARKETING SAS no aceptara productos y/o servicios que no cumplan con las especificaciones suministradas en las muestras que dieron origen a su oferta comercial, clase de insumo y/o servicios, calidad, aspecto, falta o deficiencia del insumo y/o servicio.<br>
        La presente orden de compra tiene una vigencia de treinta (30) días calendario a partir de su emisión, BULL MARKETING SAS no se hará responsable por el pago de facturación de los productos y/o servicios suministrados una vez vencido el termino antes indicado.<br>
        GARANTIA INCUMPLIMIENTO Y RESPONSABILIDAD<br>
        El Proveedor garantiza que los bienes y/o servicios objeto de la presente Orden de Compra serán aptos, pertinentes y adecuados para el fin al cual están destinados, y que cumplirán con estándares de calidad satisfactorios, conforme a las mejores prácticas del mercado. El Proveedor garantiza igualmente que los bienes y/o servicios se ajustarán estrictamente a las especificaciones técnicas, condiciones comerciales, plazos y alcances establecidos en la Orden de Compra y en los documentos contractuales que la integran. Se considerará incumplimiento, entre otros eventos, la entrega tardía, la entrega incompleta, la entrega defectuosa o dañada, la no conformidad de los bienes y/o servicios con lo pactado, así como cualquier desviación frente a las condiciones acordadas. En caso de incumplimiento, el Proveedor estará obligado, a su exclusivo costo y riesgo, a reparar, subsanar, corregir, sustituir o reemplazar los bienes y/o servicios defectuosos o no conformes, según corresponda, sin costo adicional para la Compañía, dentro del término que esta determine. El Proveedor asumirá la totalidad de los gastos y costos necesarios para la reparación, subsanación, sustitución o reemplazo de los bienes y/o servicios, incluyendo, sin limitarse a ello, costos logísticos, transporte, reprocesos, mano de obra, materiales y cualquier otro rubro asociado. Así mismo, el Proveedor será plenamente responsable por todos los daños directos e indirectos, incluyendo lucro cesante, daño emergente y cualquier perjuicio de naturaleza consecuencial, que se generen como resultado del incumplimiento, la entrega tardía, la entrega dañada o cualquier otra falla en la ejecución de la presente Orden de Compra, sin perjuicio de las demás acciones legales a que haya lugar. La garantía tendrá una vigencia de treinta (30) días calendario, contados a partir de la recepción y aceptación de los bienes y/o servicios por parte de la Compañía. El Proveedor deberá atender la garantía dentro de un plazo no superior a diez (10) días hábiles, contados a partir de la recepción de la reclamación formal.
    </div>

    {{-- FIRMAS (Personalizables según tu lógica o nombres estáticos/dinámicos) --}}
    <table class="firmas">
        <tr>
            <td style="width:33%;">{{ $orden->elaboro ?? 'Equipo Producción' }}</td>
            <td style="width:33%;">{{ $orden->solicito ?? 'Jurídica / Compras' }}</td>
            <td style="width:34%;">{{ $orden->aprobo ?? 'Gerencia' }}</td>
        </tr>
        <tr>
            <td class="rol">ELABORO</td>
            <td class="rol">SOLICITÓ</td>
            <td class="rol">APROBO</td>
        </tr>
    </table>

</body>
</html>