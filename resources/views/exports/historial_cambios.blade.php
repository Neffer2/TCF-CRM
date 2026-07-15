<table>
    <thead>
    <tr>
        <th style="background-color: #ef4444; color: white; font-weight: bold;">Cód</th>
        <th style="background-color: #ef4444; color: white; font-weight: bold;">Descripción del Ítem Modificado</th>
        <th style="background-color: #ef4444; color: white; font-weight: bold;">Cantidad</th>
        <th style="background-color: #ef4444; color: white; font-weight: bold;">V. Unitario</th>
        <th style="background-color: #ef4444; color: white; font-weight: bold;">V. Total Actual</th>
        <th style="background-color: #ef4444; color: white; font-weight: bold;">Estado</th>
    </tr>
    </thead>
    <tbody>
    @if($items->isEmpty())
        <tr>
            <td colspan="6" style="text-align: center; color: #6b7280; font-style: italic;">
                No se presentan modificaciones en los ítems de este presupuesto.
            </td>
        </tr>
    @else
        @foreach($items as $registro)
            <tr>
                @php
                    $base = data_get($registro, 'valores_anteriores', $registro);
                    $cod = data_get($base, 'cod', '-');
                    $descripcion = data_get($base, 'descripcion', 'Sin descripción');
                    $cantidad = data_get($base, 'cantidad', 0);
                    $vUnitario = (float) data_get($base, 'v_unitario', 0);
                    $vTotal = (float) data_get($base, 'v_total', 0);
                    $estado = data_get($base, 'actualizado');
                @endphp
                <td>{{ $cod }}</td>
                <td>{{ $descripcion }}</td>
                <td>{{ $cantidad }}</td>
                <td>{{ number_format($vUnitario, 2) }}</td>
                <td>{{ number_format($vTotal, 2) }}</td>
                <td>{{ is_null($estado) ? '-' : $estado }}</td>
            </tr>
        @endforeach
    @endif
    </tbody>
</table>
