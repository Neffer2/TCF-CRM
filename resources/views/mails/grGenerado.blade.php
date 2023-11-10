<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Gr generado</title>
</head> 
<body>
    <p> 
        Sr. Proveedor <br>
        A continuaci&oacute;n, se relaciona el GR correspondiente para el proceso de radicaci&oacute;n de factura. <br>
        <ul>
            <li>OC: {{ $orden->cod_oc }}</li>
            <li>GR: {{ $orden->gr }}</li> 
        </ul>
        Por favor radicar en <a href="mailto:Facturacion.proveedores@bullmarketing.com.co">Facturacion.proveedores@bullmarketing.com.co</a>
        con copia a <a href="mailto:compras@bullmarketing.com.co">compras@bullmarketing.com.co</a>. <br>        
    </p>
    @if ($orden->observacion_remision)
    <p>
        <b>Observaciones:</b> {{ $orden->observacion_remision }}
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