<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title></title>  
</head>
<body>  
    <p>
        <b>Sr. Proveedor</b><br>
        Se relaciona el comprobante de pago del {{ $orden->proveedor->anticipo }}% correspondiente al anticipo acordado en la negociaci&oacute;n realizada.
        <ul>
            <li>OC: {{ $orden->cod_oc }}</li>
        </ul>
    </p>
    @if ($observaciones)
        <p>
            <b>Observaciones:</b> {{ $observaciones }}
        </p>
    @endif
    <p>
        Cordialmente,<br>
        Saludos.
    </p>
    <p style="font-size: 12px">
        Enviado desde <b>BullCRM</b>. <a href="https://www.bullmarketing.com.co/" target="_blank"><b>BullMarketing</b></a> <b>Siempre se Puede</b>.
    </p>
</body>
</html>