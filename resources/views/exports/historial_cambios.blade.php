<html lang="en">
<body>
    {{-- Tabla de informacion --}}
    <table>
        <tr>
            <td colspan="7"></td>
            <td colspan="7" style="text-align: right; color: #1b6f08;">
                <b>Integramos servicios especializados en mercadeo.</b>
            </td>
        </tr>
        <tr>
            <td colspan="7"></td>
            <td colspan="7" style="text-align: right">
                .Diseño e implementación de estrategias de Marketing y BTL.
            </td>
        </tr>
        <tr>
            <td colspan="7"></td>
            <td colspan="7" style="text-align: right">
                .Desarrollo y ejecución de actividades de Trade.
            </td>
        </tr>
        <tr>
            <td colspan="7"></td>
            <td colspan="7" style="text-align: right">
                .Logistica de distribuciones, bodegaje e instalación de POP y Stands.
            </td>
        </tr>
        <tr>
            <td colspan="7">Nit: 900.298.176-1</td>
            <td colspan="7" style="text-align: right">
                .Creamos estrategias de PR y Medios Masivos.
            </td>
        </tr>
        <tr>
            <td colspan="7">IVA REGIMEN COMUN</td>
            <td></td>
        </tr>
    </table>


    {{-- Tabla de margenes de proyecto --}}
    <table>
        <tr>
            <td colspan="0"></td>
            <td class="font-weight-bold font-table" style="text-align: center">MARGEN ITEMS</td>
            <td class="font-table" style="text-align: center">{{ number_format($margenItems, 4) }}</td>
        </tr>
        <tr>
            <td colspan="0"></td>
            <td class="font-weight-bold font-table" style="text-align: center">VENTA PROYECTO</td>
            <td class="font-table" style="text-align: center">{{ number_format($ventaProyecto) }}</td>
        </tr>
        <tr>
            <td colspan="0"></td>
            <td class="font-weight-bold font-table" style="text-align: center">COSTOS DEL PROYECTO</td>
            <td class="font-table" style="text-align: center">{{ number_format($costosProyecto) }}</td>
        </tr>
        <tr>
            <td colspan="0"></td>
            <td class="font-weight-bold font-table" style="text-align: center">MARGEN DEL PROYECTO</td>
            <td class="font-table" style="text-align: center">{{ number_format($margenProyecto, 2) }} %</td>
        </tr>
        <tr>
            <td colspan="0"></td>
            <td class="font-weight-bold font-table" style="text-align: center">MARGEN BRUTO (PESOS)</td>
            <td class="font-table" style="text-align: center">{{ number_format($margenBruto) }}</td>
        </tr>
    </table>

    {{-- Tabla de contenidos mapeados de datos --}}
    <table>
        <thead>
        <tr>
            <th style="background-color: #ef4444; color: white; font-weight: bold;">Item</th>
            <th style="background-color: #ef4444; color: white; font-weight: bold;">Descripción del Ítem Actual</th>
            <th style="background-color: #ef4444; color: white; font-weight: bold;">Cantidad</th>
            <th style="background-color: #ef4444; color: white; font-weight: bold;">V. Unitario</th>
            <th style="background-color: #ef4444; color: white; font-weight: bold;">V. Total Actual</th>
            <th style="background-color: #ef4444; color: white; font-weight: bold;">Proveedor</th>
            <th style="background-color: #ef4444; color: white; font-weight: bold;">Utilidad</th>
            <th style="background-color: #ef4444; color: white; font-weight: bold;">Rentabilidad</th>
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
                        $num_item = data_get($base, 'num_item', '-');
                        $descripcion = data_get($base, 'descripcion', 'Sin descripción');
                        $cantidad = data_get($base, 'cantidad', 0);
                        $vUnitario = (float) data_get($base, 'v_unitario', 0);
                        $vTotal = (float) data_get($base, 'v_total', 0);
                        $proveedor = data_get($base, 'proveedor');
                        $utilidad = (float) data_get($base, 'margen_utilidad');
                        $rentabilidad = (float) data_get($base, 'rentabilidad', 0);
                        $estado = data_get($base, 'actualizado');
                    @endphp
                    <td>{{ $num_item }}</td>
                    <td>{{ $descripcion }}</td>
                    <td>{{ $cantidad }}</td>
                    <td>{{ number_format($vUnitario, 2) }}</td>
                    <td>{{ number_format($vTotal, 2) }}</td>
                    <td>{{ $proveedor }}</td>
                    <td>{{ number_format($utilidad, 2) }}</td>
                    <td>{{ number_format($rentabilidad, 2) }}</td>
                    <td>{{ is_null($estado) ? '-' : $estado }}</td>
                </tr>
            @endforeach
        @endif
        </tbody>
    </table>

    <table></table>
</body>
</html>
