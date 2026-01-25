<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orden de Compra</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #000; }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .table-title {
            padding: 4px;
            font-size: 12px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
            background-color: lightgray; 
        }

        .table-cols { border-top: 1px solid #000; border-bottom: 1px solid #000; } /* agregado */
        .table-cols td, .table-cols th { border-left: 1px solid #000; }
        .table-cols td:last-child, .table-cols th:last-child { border-right: 1px solid #000; }

        .totals-table { width:100%; border-collapse: collapse; }
        .totals-table td { border:1px solid #000; padding:4px; }
        /* .totals-table tr:last-child td { background:#d3d3d3; } */
    </style>
</head>
<body>
    <table>
        <tr>
            <td style="vertical-align: top;">
                <p style="font-size: 17px; margin-bottom: 0"><strong>BULL MARKETING S A S</strong></p>
                <p style="margin-bottom: 0; line-height: 5px">N.I.T: 900298176</p>
                <p style="margin-bottom: 0; line-height: 5px">DIRECCIÓN: CRA 53C No. 127D 23</p>
                <p style="margin-bottom: 0; line-height: 5px">TELEFONOS: 4322700</p>
            </td>
            <td style="text-align: right;">
                <img src="https://www.bullmarketing.com.co/wp-content/uploads/2022/02/Logo-bull-negro_2-e1664286411369.png" alt="BUllmarketing logo" height="80">
                <p style="font-weight: bold;">
                    ORDEN DE COMPRA <br>
                    NRO {{ "C".$orden->id }}
                </p>
                <p>FECHA: {{ date_format(date_create( $orden->fecha_aprobacion ), "Y-m-d") }}</p>
            </td>
        </tr>
        <tr>
            <td>
                <div>
                    <p style="font-size: 14px; margin-bottom: 0; text-decoration: underline;"><strong>Datos del Proveedor</strong></p>
                    <table>
                        @if ( $orden->tipo_oc === 1 )
                            <tr>
                                <td width="60">Nombre:</td>
                                <td>{{ $orden->proveedor->tercero }}</td>
                            </tr>
                            <tr>
                                <td width="60">NIT:</td>
                                <td>{{ $orden->proveedor->documento }}</td>
                            </tr>
                            <tr>
                                <td width="60">Dirección:</td>
                                <td>{{ $orden->proveedor->direccion }}</td>
                            </tr>
                            <tr>
                                <td width="60">Ciudad:</td>
                                <td>{{ $orden->proveedor->ciudad }}</td>
                            </tr>
                        @else
                            <tr>
                                <td width="60">Nombre:</td>
                                <td>{{ $orden->naturalInfo->tercero->nombre }}</td>
                            </tr>
                            <tr>
                                <td width="60">NIT:</td>
                                <td>{{ $orden->naturalInfo->tercero->cedula }}</td>
                            </tr>
                            <tr>
                                <td width="60">Dirección:</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td width="60">Ciudad:</td>
                                <td>{{ $orden->naturalInfo->tercero->ciudad }}</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <p style="font-size: 14px; font-weight: bold; margin-bottom: 2rem">
        Solicitamos sean ejecutados los siguientes servicios:
    </p>

    <table class="table-cols">
        <tr class="">
            <th class="table-title">DESCRIPCIÓN</th>
            <th class="table-title">CENTRO DE COSTOS</th>
            <th class="table-title">CANT</th>
            <th class="table-title">VR UNITARIO</th>
            <th class="table-title">VR TOTAL</th>
        </tr>
        @foreach( $orden->ordenItems as $item )
            <tr>
                <td style="padding: 2px">{{ $item->desc_oc }}</td>
                @if ( ! empty( $orden->presupuesto->cod_cc ) )
                    <td style="text-align: center">{{  $orden->presupuesto->cod_cc  }}</td>
                @else
                    <td style="text-align: center">{{  $item->itemPresupuesto->presto->cod_cc  }}</td>
                @endif
                <td style="text-align: center;">{{ $item->cant_oc }}</td>
                <td style="text-align: right;">{{ number_format($item->vunit_oc) }}</td>
                <td style="text-align: right;">{{ number_format($item->vtotal_oc) }}</td>
            </tr>
        @endforeach
    </table>
    <br><br>
    <table class="totals-table">
        <tr>
            <td width="60%" style="vertical-align: top">
                <p style="margin-bottom: 0">
                    <b>Observaciones:</b>
                </p>

                {{ $orden->observaciones_negociacion  }}
            </td>
            <td width="40%">
                <table class="totals-table">
                    <tr>
                        <td style="font-weight: bold;">SUBTOTAL:</td>
                        <td style="text-align: right;">{{ number_format($subtotal) }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">IVA:</td>
                        <td style="text-align: right;">{{ number_format(0) }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">RETEFUENTE:</td>
                        <td style="text-align: right;">{{ number_format(0) }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">RETEIVA:</td>
                        <td style="text-align: right;">{{ number_format(0) }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">RETEICA:</td>
                        <td style="text-align: right;">{{ number_format(0) }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold; background-color: lightgray">TOTALES:</td>
                        <td style="text-align: right;">{{ number_format($subtotal) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
