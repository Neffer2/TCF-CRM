<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cuenta de Cobro</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
            color: #000;
        }

        .sign {
            font-family: 'DejaVu Serif';
            font-style: italic;
            font-size: 18px;
            font-style: italic
        }

        .bold {
            font-weight: bold;
        }

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

        .table-cols {
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
        }

        /* agregado */
        .table-cols td, .table-cols th {
            border-left: 1px solid #000;
        }

        .table-cols td:last-child, .table-cols th:last-child {
            border-right: 1px solid #000;
        }

        .table-cols td {
            padding: 0.5rem;
            word-wrap: break-word;
            white-space: pre-wrap
        }

        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }

        .totals-table td {
            border: 1px solid #000;
            padding: 4px;
        }

        .marca-borrador {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 30%;
            left: 0;
            z-index: 2;
            opacity: 0.3;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .marca-borrador h1 {
            font-size: 7rem;
            color: #fd7e14;
            text-align: center;
        }

        .firma-tercero {
            width: 135px;
        }

        /* .totals-table tr:last-child td { background:#d3d3d3; } */
    </style>
</head>
<body>
@if ( $borrador )
    <div class="marca-borrador">
        <h1>BORRADOR</h1>
    </div>
@endif

<table>
    <tr>
        <td style="vertical-align: top;">
            <p style="font-size: 17px; margin-bottom: 0"><strong>BULL MARKETING S A S</strong></p>
            <p style="margin-bottom: 0; line-height: 5px">N.I.T: 900298176</p>
            <p style="margin-bottom: 0; line-height: 5px">DIRECCIÓN: CRA 53C No. 127D 23</p>
            <p style="margin-bottom: 0; line-height: 5px">TELEFONOS: 4322700</p>
        </td>
        <td style="text-align: right;">
            <img src="https://www.bullmarketing.com.co/wp-content/uploads/2022/02/Logo-bull-negro_2-e1664286411369.png"
                 alt="BUllmarketing logo" height="70">
        </td>
    </tr>
    <td colspan="2"><br></td>
    <tr>
        <td colspan="2" style="text-align: center; vertical-align: top">
            <p style="font-size: 17px; font-weight: bold;">
                CUENTA DE COBRO <br>
                NRO {{ $cod_cuenta_cobro }}
            </p>
            <p>FECHA DE EMISIÓN: {{ date_format(date_create( $orden->fecha_aprobacion ), "Y-m-d") }}</p>
        </td>
    </tr>
    <td colspan="2"><br></td>
</table>

<table>
    <tr>
        <td>
            <p style="font-size: 14px; margin-bottom: 1rem;">
                Debe a:
            </p>
            <table>
                <tr>
                    <td width="60"><strong>Nombre:</strong></td>
                    <td>{{ $orden->naturalInfo->tercero->nombre }} {{ $orden->naturalInfo->tercero->apellido }}</td>
                </tr>
                <tr>
                    <td width="60"><strong>NIT:</strong></td>
                    <td>{{ $orden->naturalInfo->tercero->cedula }}</td>
                </tr>
                <tr>
                    <td width="60"><strong>Dirección:</strong></td>
                    <td></td>
                </tr>
                <tr>
                    <td width="60"><strong>Ciudad:</strong></td>
                    <td>{{ $orden->naturalInfo->tercero->ciudad }}</td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td>
            <p style="font-size: 14px; margin-bottom: 1rem;">
                La suma de: <strong>$ {{ number_format($subtotal) }}</strong>
            </p>
        </td>
    </tr>
    <tr>
        <td>
            <p style="font-size: 14px; margin-bottom: 1rem;">
                Por concepto de:
            </p>
        </td>
    </tr>
</table>

<table class="table-cols">
    <tr class="">
        <th class="table-title">DESCRIPCIÓN</th>
        <th class="table-title">CANT</th>
        <th class="table-title">VR UNITARIO</th>
        <th class="table-title">VR TOTAL</th>
    </tr>
    @foreach( $orden->ordenItems as $item )
        <tr>
            <td width="50%" style="font-size: 10px">{{ $item->desc_oc }}</td>
            <td width="10%" style="text-align: center; font-size: 10px">{{ $item->cant_oc }}</td>
            <td width="10%" style="text-align: right; font-size: 10px">{{ number_format($item->vunit_oc) }}</td>
            <td width="10%" style="text-align: right; font-size: 10px">{{ number_format($item->vtotal_oc) }}</td>
        </tr>
    @endforeach
</table>
<br><br>

<table width="100%">
    <tr>
        <td>
            <p style="font-size: 14px; margin-bottom: 1rem;">
                Forma de pago:
            </p>

            <table>
                <tr>
                    <td width="20%"><strong>Tipo de cuenta:</strong></td>
                    <td>{{ $orden->naturalInfo->tercero->tipo_cuenta }}</td>
                </tr>
                <tr>
                    <td width="20%"><strong>Número de cuenta:</strong></td>
                    <td>{{ $orden->naturalInfo->tercero->num_cuenta }}</td>
                </tr>
                <tr>
                    <td width="20%"><strong>Banco:</strong></td>
                    <td>{{ $orden->naturalInfo->tercero->banco }}</td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td><br><br></td>
    </tr>
    <tr>
        <td style="text-align: center">
            <table>
                <tr>
                    <td style="text-align: center;">
                        <p class="bold">LA SOCIEDAD CONTRATANTE. <br><br><br></p>
                        <img
                            src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('storage/signs/sign__.png'))) }}"
                            alt="Firma">
                        <p class="bold">__________________________</p>
                        <p class="bold" style="font-size: 10px; margin-bottom: 0; line-height: 5px">
                            BULL MARKETING S.A.S.
                        </p>
                        <p style="font-size: 10px; margin-bottom: 0; line-height: 5px">
                            Representante Legal
                        </p>
                        <p style="font-size: 10px; margin-bottom: 0; line-height: 5px">
                            NIT. 900.298.176-1
                        </p>
                    </td>
                    <td style="text-align: center;">
                        <p class="bold">EL CONTRATISTA. <br><br><br></p>
                        @if ( ! $borrador && $orden->naturalInfo->tercero->firma )
                            <img
                                class="firma-tercero"
                                src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path( str_replace('public', 'storage', $orden->naturalInfo->tercero->firma) ))) }}"
                                alt="Firma tercero">
                        @endif
                        <p class="bold">__________________________</p>
                        <p class="bold" style="font-size: 10px; margin-bottom: 0; line-height: 5px">
                            Nombre: {{ $orden->naturalInfo->tercero->nombre }} {{ $orden->naturalInfo->tercero->apellido }}
                        </p>
                        <p style="font-size: 10px; margin-bottom: 0; line-height: 5px">
                            CEDULA. {{ $orden->naturalInfo->tercero->cedula }}
                        </p>
                        <p style="font-size: 10px; margin-bottom: 0; line-height: 5px">
                            <br>
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
