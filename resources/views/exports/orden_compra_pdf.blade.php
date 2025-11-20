<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orden de Compra</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; color: #000; } 

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .table-title {
            padding: 8px;
            font-size: 12px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
            background-color: lightgray;
        }

        .table-cols { border-top:1px solid #000; border-bottom:1px solid #000; } /* agregado */
    .table-cols td, .table-cols th { border-left: 1px solid #000; }
    .table-cols td:last-child, .table-cols th:last-child { border-right: 1px solid #000; }

        .totals-table { width:100%; border-collapse:collapse; }
        .totals-table td { border:1px solid #000; padding:4px; }
        /* .totals-table tr:last-child td { background:#d3d3d3; } */
    </style>
</head>
<body>
    <table>
        <tr>
            <td>
                <div>
                    <p><strong>BULL MARKETING S A S</strong></p>
                    <p>NIT: 900298176</p>
                    <p>DIRECCIÓN: CRA 53C No. 127D 23</p>
                    <p>TELEFONOS: 4322700</p>
                </div>
            </td>
            <td style="text-align: right;">
                <img src="https://www.bullmarketing.com.co/wp-content/uploads/2022/02/Logo-bull-negro_2-e1664286411369.png" alt="BUllmarketing logo" height="80">
                <p style="font-weight: bold;">
                    ORDEN DE COMPRA NRO 555
                </p>
                <p>FECHA: Octubre 30 DE 2025</p>
            </td>
        </tr>
        <tr>
            <td>
                <div>
                    <p style="text-decoration: underline;"><strong>DATOS DEL PROVEEDOR</strong></p>
                    <p>Nombre: EJEMPLO</p>
                    <p>NIT: EJEMPLO</p>
                    <p>DIRECCIÓN: EJEMPLO</p>
                    <p>CIUDAD: BOGOTÁ</p>
                </div>
            </td>
        </tr>
    </table>
    <p style="font-weight: bold;">
        Solicitamos sean ejecutados los siguientes servicios:
    </p>

    <table class="table-cols"> 
        <tr class="">
            <th class="table-title">DESCRIPCIÓN</th>
            <th class="table-title">COSTO</th>
            <th class="table-title">CANT</th>
            <th class="table-title">VR UNITARIO</th>
            <th class="table-title">VR TOTAL</th>
        </tr>
        <tr>
            <td>Servicio de ejemplo</td>
            <td>Servicio</td>
            <td style="text-align: center;">2</td>
            <td style="text-align: right;">1,000.00</td>
            <td style="text-align: right;">2,000.00</td>
        </tr>
        <tr>
            <td>Servicio de ejemplo</td>
            <td>Servicio</td>
            <td style="text-align: center;">2</td>
            <td style="text-align: right;">1,000.00</td>
            <td style="text-align: right;">2,000.00</td>
        </tr>
    </table>
    <br><br>
    <table class="totals-table"> 
        <tr>
            <td>
                <p>
                    <b>Observaciones:</b> Ejemplo de observaciones de la orden de compra.  
                </p>
            </td>
            <td>
                <table class="totals-table">
                    <tr>
                        <td style="font-weight: bold;">SUBTOTAL:</td>
                        <td style="text-align: right;">4,000.00</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">IVA:</td>
                        <td style="text-align: right;">760.00</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">RETEFUENTE:</td>
                        <td style="text-align: right;">4,760.00</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">RETEIVA:</td>
                        <td style="text-align: right;">4,760.00</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">RETEICA:</td>
                        <td style="text-align: right;">4,760.00</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold; background-color: lightgray">TOTALES:</td>
                        <td style="text-align: right;">4,760.00</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>