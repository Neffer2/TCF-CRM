<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <table>
        <tr>
            <td style="text-align: center; font-weight: bold;">C.C</td>
            <td style="text-align: center; font-weight: bold;">NOMBRE C.C.</td>
            <td style="text-align: center; font-weight: bold;">DESC ITEM</td>
            <td style="text-align: center; font-weight: bold;">NUM ITEM</td>
            <td style="text-align: center; font-weight: bold;">CANT</td>
            <td style="text-align: center; font-weight: bold;">HORAS</td>
            <td style="text-align: center; font-weight: bold;">DIAS</td>
            <td style="text-align: center; font-weight: bold;">VALOR UNITARIO</td>
            <td style="text-align: center; font-weight: bold;">VALOR TOTAL</td>
            <td style="text-align: center; font-weight: bold;">FECHA</td>
        </tr>
        @foreach ($ordenes as $orden)
            @foreach ($orden->presupuesto->presupuestoItems as $item)
                {{-- <tr>
                    <td>{{ $orden->presupuesto->cod_cc }}</td>
                    <td>{{ $orden->presupuesto->gestion->nom_proyecto_cot }}</td>
                    <td>{{ $item->descripcion }}</td>
                    <td>{{ $item->num_item }}</td>
                    <td>{{ $item->cantidad }}</td>
                    <td>{{ $item->dia }}</td>
                    <td>{{ $item->otros }}</td>
                    <td>{{ $item->v_unitario }}</td>
                    <td>{{ $item->v_total }}</td>
                    <td>{{ $orden->created_at->format('d/m/Y H:i') }}</td>
                </tr> --}}
            @endforeach
            @foreach ($orden->presupuesto->presupuestoItems as $item)
                @if ($item->consumidos->count() > 0)
                    @foreach ($item->consumidos as $consumido)
                        <tr>
                            <td>{{ $orden->presupuesto->cod_cc }}</td>
                            <td>{{ $orden->presupuesto->gestion->nom_proyecto_cot }}</td>
                            <td>{{ $consumido->desc_oc }}</td>
                            <td>{{ $consumido->display_item }}</td>
                            <td>{{ $consumido->cant_oc }}</td>
                            <td>{{ $consumido->dias_oc }}</td>
                            <td>{{ $consumido->otros_oc }}</td>
                            <td>{{ $consumido->vunit_oc }}</td>
                            <td>{{ $consumido->vtotal_oc }}</td>
                            <td>{{ $consumido->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    @endforeach
                @endif
            @endforeach
        @endforeach
    </table>
</body>
</html>
