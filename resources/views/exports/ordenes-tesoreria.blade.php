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
            <td style="text-align: center; font-weight: bold;">PRODUCTOR</td>
            <td style="text-align: center; font-weight: bold;">C.C</td>
            <td style="text-align: center; font-weight: bold;">NOMBRE C.C.</td>
            <td style="text-align: center; font-weight: bold;">DESC ITEM</td>
            <td style="text-align: center; font-weight: bold;">PROVEEDOR</td>
            <td style="text-align: center; font-weight: bold;">NIT PROVEEDOR</td>
            <td style="text-align: center; font-weight: bold;">TIPO</td>
            <td style="text-align: center; font-weight: bold;">VALOR TOTAL (OC)</td>
            <td style="text-align: center; font-weight: bold;">CAUSACION</td>
            <td style="text-align: center; font-weight: bold;">OBSERVACIONES</td>
            <td style="text-align: center; font-weight: bold;">FECHA CREACION</td>
            <td style="text-align: center; font-weight: bold;">FECHA ENVIO (PRODUCCI&Oacute;N)</td>
            <td style="text-align: center; font-weight: bold;">ESTADO</td>
        </tr>
        @foreach ($ordenes as $orden)
            @foreach ($orden->ordenItems as $ocItem)
                <tr>
                    <td>
                        @if ($ocItem->OrdenCompra->tipo_oc == 1)
                            @if ($ocItem->OrdenCompra->presupuesto->productor_info)
                                {{ $ocItem->OrdenCompra->presupuesto->productor_info->name }}
                            @endif
                        @else
                            {{ $ocItem->OrdenCompra->naturalInfo->productor->name }}
                        @endif
                    </td>
                    <td>{{ $ocItem->itemPresupuesto->presto->cod_cc }}</td>
                    <td>{{ $ocItem->itemPresupuesto->presto->gestion->nom_proyecto_cot }}</td>
                    <td>{{ $ocItem->desc_oc }}</td>
                    @if ($ocItem->OrdenCompra->tipo_oc == 1)
                        <td>{{ $ocItem->OrdenCompra->proveedor->tercero }}</td>
                        <td>{{ $ocItem->OrdenCompra->proveedor->documento }}</td>
                    @else
                        <td>{{ $ocItem->OrdenCompra->naturalInfo->tercero->nombre }} {{ $ocItem->OrdenCompra->naturalInfo->tercero->apellido }}</td>
                        <td>{{ $ocItem->OrdenCompra->naturalInfo->tercero->cedula }}</td>
                    @endif
                    <td>{{ $ocItem->OrdenCompra->tipo->description }}</td>
                    <td>{{ $ocItem->vtotal_oc }}</td>
                    <td>{{ $ocItem->OrdenCompra->cod_causal }}</td>
                    <td>{{ $ocItem->OrdenCompra->observacion_causal }}</td>
                    <td>{{ $ocItem->created_at }}</td>
                    <td>{{ $ocItem->OrdenCompra->fecha_aprobacion }}</td>
                    <td>
                        @if ($ocItem->OrdenCompra->archivo_comprobante_pago)
                            Pagado
                        @else
                            Por pagar
                        @endif
                    </td>
                </tr>
            @endforeach
        @endforeach
    </table>
</body>
</html>
