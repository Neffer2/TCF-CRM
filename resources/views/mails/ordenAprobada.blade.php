<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Orden de compra aprobada</title>
</head> 
<body>
    <p>
        Hola <b>{{ $orden->presupuesto->productor_info->name }}</b>,<br><br>

        De acuerdo con tu solicitud se gener&oacute; la <b>Orden de compra: {{ $orden->cod_oc }}</b> para el proveedor <b>{{ $orden->proveedor }}</b>.<br>
        Por favor validar y una vez est&eacute; recibido a satisfacci&oacute;n, enviar al &aacute;rea de compras firmada para radicar a proveedor <br>
        con el correspondiente GR que se genera desde el &aacute;rea de compras.<br><br>

        Cordialmente,
        Saludos.
    </p>
    <p style="font-size: 12px">
        Este correo fue enviado desde <a href="https://bullcrm.com.co" target="_blank"><b>BullCRM</b></a>. <a href="https://www.bullmarketing.com.co/" target="_blank"><b>BullMarketing</b></a> <b>Siempre se Puede</b>.
    </p>
</body>
</html>