<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Consumidos</title>
</head>
<body>
    <table>
        <tr>
            <td style="text-align: center; font-weight: bold;">FECHA</td>
            <td style="text-align: center; font-weight: bold;">COD OC</td>
            <td style="text-align: center; font-weight: bold;">C.C</td>
            <td style="text-align: center; font-weight: bold;">NIT PROVEEDOR</td>
            <td style="text-align: center; font-weight: bold;">NUM ITEM</td>
            <td style="text-align: center; font-weight: bold;">CANT</td>
            <td style="text-align: center; font-weight: bold;">VALOR UNITARIO (OC)</td>
            <td style="text-align: center; font-weight: bold;">ARTICULO/SERVICIO</td>
            <td style="text-align: center; font-weight: bold;">FECHA ENVIO (PRODUCCI&Oacute;N)</td>
            <td style="text-align: center; font-weight: bold;">PRODUCTOR</td>
            <td style="text-align: center; font-weight: bold;">CIUDAD</td>

{{--
            <td style="text-align: center; font-weight: bold;">NOMBRE C.C.</td>
            <td style="text-align: center; font-weight: bold;">DESC ITEM</td>
            <td style="text-align: center; font-weight: bold;">PROVEEDOR</td>
            <td style="text-align: center; font-weight: bold;">TIPO</td>
            <td style="text-align: center; font-weight: bold;">HORAS</td>
            <td style="text-align: center; font-weight: bold;">DIAS</td>
            <td style="text-align: center; font-weight: bold;">VALOR TOTAL (OC)</td>
            <td style="text-align: center; font-weight: bold;">VALOR UNITARIO (ITEM)</td>
            <td style="text-align: center; font-weight: bold;">VALOR TOTAL (ITEM)</td>
            <td style="text-align: center; font-weight: bold;">SALDO</td>
            <td style="text-align: center; font-weight: bold;">FECHA APROBACI&Oacute;N (CONTROLLER)</td> --}}
            <td style="text-align: center; font-weight: bold;">ESTADO</td>
        </tr>
        @foreach ($ordenes as $orden)
            @foreach ($orden->ordenItems as $ocItem)
                <tr>
                    <td>{{ $ocItem->created_at }}</td>
                    <td>{{ $ocItem->OrdenCompra->id }}</td>
                    <td>{{ $ocItem->itemPresupuesto->presto->cod_cc }}</td>
                    @if ($ocItem->OrdenCompra->tipo_oc == 1)
                        <td>{{ $ocItem->OrdenCompra->proveedor->documento }}</td>
                    @else
                        <td>{{ $ocItem->OrdenCompra->naturalInfo->tercero->cedula }}</td>
                    @endif
                    <td>{{ $ocItem->itemPresupuesto->displayItem() }}</td>
                    <td>{{ $ocItem->cant_oc }}</td>
                    <td>{{ $ocItem->vunit_oc }}</td>
                    <td>{{ $ocItem->tipo_servicio }} IT {{ $ocItem->itemPresupuesto->displayItem() }} {{ $ocItem->itemPresupuesto->ciudad }} # {{ $ocItem->OrdenCompra->id }} </td>
                    <td>{{ $ocItem->OrdenCompra->fecha_envio_produccion }}</td>
                    <td>
                        @if ($ocItem->OrdenCompra->tipo_oc == 1)
                            @if ($ocItem->OrdenCompra->presupuesto->productor_info)
                                {{ $ocItem->OrdenCompra->presupuesto->productor_info->name }}
                            @endif
                        @else
                            {{ $ocItem->OrdenCompra->naturalInfo->productor->name }}
                        @endif
                    </td>
                    <td>{{ $ocItem->OrdenCompra->naturalInfo->tercero->ciudad }}</td>
                    <td>{{ $ocItem->OrdenCompra->estado_oc->description }}</td>

                    {{--
                    <td>{{ $ocItem->itemPresupuesto->presto->gestion->nom_proyecto_cot }}</td>
                    <td>{{ $ocItem->desc_oc }}</td>
                    <td>{{ $ocItem->OrdenCompra->tipo->description }}</td>
                    <td>{{ $ocItem->dias_oc }}</td>
                    <td>{{ $ocItem->otros_oc }}</td>
                    <td>{{ $ocItem->vtotal_oc }}</td>
                    <td>{{ $ocItem->itemPresupuesto->v_unitario }}</td>
                    <td>{{ $ocItem->itemPresupuesto->v_total }}</td>
                    <td>{{ ($ocItem->itemPresupuesto->v_total - $ocItem->vtotal_oc) }}</td>
                    <td>{{ $ocItem->OrdenCompra->fecha_aprobacion }}</td>
                     --}}
                </tr>
            @endforeach
        @endforeach
    </table>
</body>
</html>
