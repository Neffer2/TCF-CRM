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
                <td>{{ $registro->valores_anteriores['cod'] }}</td>
                <td>{{ $registro->valores_anteriores['descripcion'] }}</td>
                <td>{{ $registro->valores_anteriores['cantidad'] }}</td>
                <td>{{ number_format($registro->valores_anteriores['v_unitario'], 2) }}</td>
                <td>{{ number_format($registro->valores_anteriores['v_total'], 2) }}</td>
                <td>{{ $registro->created_at->format('Y-m-d H:i') }}</td>
            </tr>
        @endforeach
    @endif
    </tbody>
</table>
