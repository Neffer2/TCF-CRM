<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">  
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Orden de compra Anulada</title>
</head> 
<body>
    <p>
        Hola <b>{{ $orden->presupuesto->productor_info->name }}</b>,<br><br>

        Debido a inconsitencias en la <b>Orden de compra: {{ $orden->cod_oc }}</b> para el proveedor <b>{{ $orden->proveedor->tercero }}</b>,<br>
        El equipo de compras ha decidido anularla con las siguientes obervaciones: <br><br>
        
        @if ($orden->observaciones_anulacion)
            <b>Observaciones:</b> {{ $orden->observaciones_anulacion }} <br><br>
        @endif 
        
        Cordialmente,<br>
        Saludos.
    </p>  
    <p style="font-size: 12px">
        Enviado desde <b>BullCRM</b>. <a href="https://www.bullmarketing.com.co/" target="_blank"><b>BullMarketing</b></a> <b>Siempre se Puede</b>.
    </p>
</body>
</html>