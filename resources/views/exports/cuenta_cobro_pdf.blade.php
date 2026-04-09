<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cuenta de Cobro</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Agdasima:wght@400;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: "Agdasima", sans-serif;
            font-size: 14px;
            color: #000;
        }

        .sign {
            font-family: 'DejaVu Serif';
            font-style: italic;
            font-size: 18px;
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
            font-weight: bold;
            text-align: center;
            background-color: lightgray;
        }

        .table-cols {
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
        }

        /* agregado */
        .table-cols td, .table-cols th {
            border-left: 1px solid #000;
            border-bottom: 1px solid #000;
        }

        .table-cols td:last-child, .table-cols th:last-child {
            border-right: 1px solid #000;
            border-bottom: 1px solid #000;
        }

        .table-cols td {
            padding: 0.1rem 0.2rem;
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
            font-size: 9rem;
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
            <td width="60%">
                <table>
                    <tr>
                        <td style="text-align: center">
                            <img src="https://www.bullmarketing.com.co/wp-content/uploads/2022/02/Logo-bull-negro_2-e1664286411369.png"
                                 alt="BUllmarketing logo" height="60">
                        </td>
                        <td style="text-align: center">
                            <p style="font-size: 15px; margin-bottom: 0; line-height: 5px">
                                <strong>BULL MARKETING S A S</strong>
                            </p>
                            <p style="font-size: 15px; margin-bottom: 0; line-height: 5px">
                                <strong>NIT. 900.298.176-1</strong>
                            </p>
                            <p style="font-size: 15px; margin-bottom: 0; line-height: 5px">
                                <strong>CRA 53C No. 127D - 23</strong>
                            </p>
                            <p style="font-size: 15px; margin-bottom: 0; line-height: 5px">
                                <strong>TEL. 432 27 00</strong>
                            </p>
                            <p style="font-size: 15px; margin-bottom: 0; line-height: 5px">
                                <strong>RESPONSABLE DE IVA REGIMEN COMUN</strong>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <p style="font-size: 15px; margin-bottom: 0; line-height: 15px">
                                Documento equivalente a la factura en adquisiciones efectuadas por responsables del régimen
                                común a personas naturales no comerciantes o inscritas como no responsables de IVA en el RUT.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
            <td width="30%">
                <table>
                    <tr>
                        <td style="text-align: center">
                            <p style="font-size: 15px; margin-bottom: 0; line-height: 5px">
                                <strong>DOCUMENTO SOPORTE EN ADQUISICIONES</strong>
                            </p>
                            <p style="font-size: 15px; margin-bottom: 0; line-height: 5px">
                                <strong>EFECTUADAS A NO OBLIGADOS A FACTURAR</strong>
                            </p>
                            <p style="font-size: 15px; margin-bottom: 0; line-height: 5px">
                                Art 3 Dec. 522 DE MARZO 7 DE 2003
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: center">
                            <p style="font-size: 15px; margin-top: 2rem; margin-bottom: 2rem; line-height: 5px">
                                <strong>No.</strong>
                            </p>
                            <p style="font-size: 18px; margin-bottom: 0; line-height: 15px; border-bottom: 1px solid #000000; margin: 0 2rem">
                                <strong>
                                    {{ $cod_cuenta_cobro }}
                                </strong>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <td colspan="2"><br></td>
        <tr>
            <td width="60%">
                <table class="table-cols" style="border-top: 0px solid #000;">
                    <tr>
                        <td colspan="4" style="border-left: 0px solid #000; border-right: 0px solid #000; border-bottom: 1px solid #000;">&nbsp;</td>
                    </tr>
                    <tr class="">
                        <th class="table-title" colspan="4">
                            Persona natural de quien se adquieren los bienes y/o servicios
                        </th>
                    </tr>
                    <tr>
                        <td style="font-weight: 700">NOMBRE O RAZÓN SOCIAL</td>
                        <td colspan="3">{{ $orden->naturalInfo->tercero->nombre }} {{ $orden->naturalInfo->tercero->apellido }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: 700">DIRECCIÓN</td>
                        <td>{{ $orden->naturalInfo->tercero->ciudad }}</td>
                        <td style="font-weight: 700">TELEFONO.</td>
                        <td>{{ $orden->naturalInfo->tercero->telefono }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: 700">C.C. O NIT.</td>
                        <td>{{ $orden->naturalInfo->tercero->cedula }}</td>
                        <td style="font-weight: 700">CORREO</td>
                        <td>{{ $orden->naturalInfo->tercero->correo }}</td>
                    </tr>
                </table>
            </td>
            <td width="30%" style="text-align: center">
                <table class="table-cols" style="width: 80%; margin: 0 auto">
                    <tr>
                        <th class="table-title" colspan="3">Fecha del documento</th>
                    </tr>
                    <tr>
                        <td style="text-align: center">{{ date_format(date_create( $orden->fecha_aprobacion ), "Y") }}</td>
                        <td style="text-align: center">{{ date_format(date_create( $orden->fecha_aprobacion ), "m") }}</td>
                        <td style="text-align: center">{{ date_format(date_create( $orden->fecha_aprobacion ), "d") }}</td>
                    </tr>
                    <tr>
                        <td colspan="3">&nbsp;</td>
                    </tr>
                    <tr>
                        <th class="table-title" colspan="3">Ciudad</th>
                    </tr>
                    <tr>
                        <td colspan="3" style="text-align: center">{{ $orden->naturalInfo->tercero->ciudad }}</td>
                    </tr>
                </table>
            </td>
        </tr>
        <td colspan="2"><br></td>
    </table>

    <table class="table-cols">
        <tr class="">
            <th class="table-title">CANTIDAD</th>
            <th class="table-title">CONCEPTO</th>
            <th class="table-title">FECHA DEL SERVICIO</th>
            <th class="table-title">CENTRO COSTOS</th>
            <th class="table-title">VALOR UNITARIO</th>
            <th class="table-title">TOTALES</th>
        </tr>
        @foreach ( $orden->ordenItems as $item )
            <tr>
                <td style="text-align: center">{{ $item->cant_oc }}</td>
                <td>{{ $item->desc_oc }}</td>
                <td></td>
                <td>{{ $item->itemPresupuesto->presto->cod_cc }}</td>
                <td style="text-align: right">{{ number_format( $item->vunit_oc ) }}</td>
                <td style="text-align: right">{{ number_format( $item->vtotal_oc ) }}</td>
            </tr>
        @endforeach
    </table>

    <table width="100%">
        <tr>
            <td colspan="2">
                <p style="margin-bottom: 1rem;">
                    Se firma en señal de aceptacion del contenido de este docuemnto
                </p>
            </td>
        </tr>
        <tr>
            <td style="text-align: center; padding: 0 2rem">
                <table>
                    <tr>
                        <td style="text-align: center">
                            <p style="font-size: 15px; margin-bottom: 0; line-height: 5px">
                                <strong>FIRMA</strong>
                            </p>
                        </td>
                        <td>
                            @if ( ! $borrador && $orden->naturalInfo->tercero->firma )
                                <img
                                    class="firma-tercero"
                                    src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path( str_replace('public', 'storage', $orden->naturalInfo->tercero->firma) ))) }}"
                                    alt="Firma tercero">
                            @endif
                            <p class="bold">__________________________</p>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="text-align: center; padding: 0 2rem">
                <table>
                    <tr>
                        <td style="text-align: center">
                            <p style="font-size: 15px; margin-bottom: 0; line-height: 5px">
                                <strong>IDENTIFICACION No.</strong>
                            </p>
                        </td>
                        <td>
                            <p>
                                {{ $orden->naturalInfo->tercero->cedula }}
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center">
                <p style="margin-bottom: 1rem;">
                    Cra 53C no. 127D - 23 Prado Veraniego PBX (+57)(1) 4 32 2700 E-mail contadores@bullmarketing.com.co Bogotá Colombia
                </p>
                <p style="margin-bottom: 1rem;">
                    RESOLUSION N° 18764081739178 Fecha: 2024/10/18 AUTORIZADO DESDE 20.001 HASTA EL 35.000 ; DOCUMENTO SOPORTE EN LAS ADQUISICIONES DE BIENES Y
                    SERVICIOS CON LOS NO OBLIGADOS A FACTURAR SEG ART 772-1 INICSO 3 Y ART 1.6.1.4.12 DEL DUT INTRODUCIDO POR EL DECRETO 358 DE 2020
                </p>
                <p style="margin-bottom: 1rem;">
                    la tabla de retención en la fuente establecida en elarticulo 383 del E.T, la cual se aplica a los pagos o abonos en cuenta por concepto de honorarios y por
                    compensación por servicios personales , SI( ) No ( ) He contratado o vinculado más de un trabajados asociado a mi actividad económica por al menos noventa (90)
                    días continuos (Parágrafo 2 art 383 E.T.)
                </p>
                <p style="margin-bottom: 1rem;">
                    En la misma manera, en el momento en que contrate o vincule más de un trabajador asociado a mi actividad económica, me comprometo a informar
                </p>
            </td>
        </tr>
        <tr>
            <td colspan="2"><br><br></td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center; padding: 0 2rem">
                <table>
                    <tr>
                        <td style="text-align: center">
                            <p class="bold">__________________________</p>
                            <p style="font-size: 15px; margin-bottom: 0; line-height: 5px">
                                <strong>FIRMA PRODUCTOR</strong>
                            </p>
                        </td>
                        <td style="text-align: center">
                            <p class="bold">__________________________</p>
                            <p style="font-size: 15px; margin-bottom: 0; line-height: 5px">
                                <strong>FIRMA APROBACIÓN</strong>
                            </p>
                        </td>
                        <td style="text-align: center">
                            <p class="bold">__________________________</p>
                            <p style="font-size: 15px; margin-bottom: 0; line-height: 5px">
                                <strong>FIRMA CONTABILIDAD</strong>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
