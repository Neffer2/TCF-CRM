<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>I need</title>
</head> 
<body>
    <table>
        <thead>
            <tr>
                <th style="text-align: center">FECHA</th>
                <th style="text-align: center">TIPO DOC.</th>
                <th style="text-align: center">IDENTIDAD</th>
                <th style="text-align: center">NOMBRE DEL TERCERO</th>
                <th style="text-align: center">CENTRO DE COSTOS</th>
                <th style="text-align: center">NOMBRE CENTRO DE COSTO</th>
                <th style="text-align: center">D&Eacute;BITO</th>
                <th style="text-align: center">CR&Eacute;DITO</th>
                <th style="text-align: center">COMERCIAL</th>
                <th style="text-align: center">CUENTA</th>
                <th style="text-align: center">PARTICIPACI&Oacute;N</th>
                <th style="text-align: center">BASE FACTURA</th>
                <th style="text-align: center">MES</th>
                <th style="text-align: center">AÑO</th>
                <th style="text-align: center">COMISI&Oacute;N</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($registros_helisa as $registro_helisa)
            <tr> 
                    <td>{{ $registro_helisa->fecha }}</td>   
                    <td>{{ $registro_helisa->tipo_doc }}</td>   
                    <td>{{ $registro_helisa->identidad }}</td>   
                    <td>{{ $registro_helisa->nom_tercero }}</td>   
                    <td>{{ $registro_helisa->centro }}</td>   
                    <td>{{ $registro_helisa->nom_centro_costo }}</td>   
                    <td>{{ $registro_helisa->debito }}</td>   
                    <td>{{ $registro_helisa->credito }}</td>   
                    <td>{{ $registro_helisa->comercial_user->name }}</td>   
                    <td>{{ $registro_helisa->cuenta->description }}</td>   
                    <td>{{ $registro_helisa->porcentaje }}</td>   
                    <td>{{ $registro_helisa->base_factura }}</td>   
                    <td>{{ $registro_helisa->mes }}</td>   
                    <td>{{ $registro_helisa->año }}</td>   
                    <td>{{ $registro_helisa->comision }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>