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
                {{-- Usamos ?? '-' para que si es null muestre una raya --}}
                <td>{{ $registro->valores_anteriores['cod'] ?? '-' }}</td>
                <td>{{ $registro->valores_anteriores['descripcion'] ?? 'Sin descripción' }}</td>
                <td>{{ $registro->valores_anteriores['cantidad'] ?? 0 }}</td>

                {{-- Para number_format, aseguramos un valor numérico por defecto (0) si viene nulo --}}
                <td>{{ number_format($registro->valores_anteriores['v_unitario'] ?? 0, 2) }}</td>
                <td>{{ number_format($registro->valores_anteriores['v_total'] ?? 0, 2) }}</td>

            </tr>
        @endforeach
    @endif
    </tbody>
</table>
