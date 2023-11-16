<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">  
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
</head>  
<body>
    <p> 
        Hola 
        @foreach ($recipients as $key => $recipient)
            @if ($key == count($recipients)-1)
                {{ utf8_decode($recipient['name']) }}.
            @else
                {{ utf8_decode($recipient['name']) }},
            @endif
        @endforeach
        <br><br>

        {!! utf8_decode($body) !!} <br><br><br>
    </p> 
    <p style="font-size: 12px">
        Enviado desde <b>BullCRM</b>. <a href="https://www.bullmarketing.com.co/" target="_blank"><b>BullMarketing</b></a> <b>Siempre se Puede</b>.
    </p>
</body>
</html>