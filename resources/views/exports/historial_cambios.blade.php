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
            <td class="font-table" style="text-align: center">{{ number_format(100-($margenItems * 100), 4) }}</td>
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
            <!--<th style="background-color: #ef4444; color: white; font-weight: bold;">Codigo</th>-->
            <th style="background-color: #ef4444; color: white; font-weight: bold;">Item</th>
            <th style="background-color: #ef4444; color: white; font-weight: bold;">Descripción del Ítem Actual</th>
            <th style="background-color: #ef4444; color: white; font-weight: bold;">Cantidad</th>
            <th style="background-color: #ef4444; color: white; font-weight: bold;">V. Unitario Interno</th>
            <th style="background-color: #ef4444; color: white; font-weight: bold;">V. Total Interno</th>
            <th style="background-color: #ef4444; color: white; font-weight: bold;">V. Unitario Cliente</th>
            <th style="background-color: #ef4444; color: white; font-weight: bold;">V. Total Cliente</th>
            <th style="background-color: #ef4444; color: white; font-weight: bold;">Proveedor</th>
            <th style="background-color: #ef4444; color: white; font-weight: bold;">Utilidad</th>
            <th style="background-color: #ef4444; color: white; font-weight: bold;">Rentabilidad</th>
            <th style="background-color: #ef4444; color: white; font-weight: bold;">Disponible</th>
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
                @if(!$registro->evento == 1)
                    @php
                        $base = data_get($registro, 'valores_anteriores', $registro);
                        
                        // 1. Extraemos la propiedad 'actualizado' (sea de $registro o $base)
                        $actualizado = data_get($base, 'actualizado', 0);

                        // 2. Evaluamos el color HEX exacto según el estado
                        $bgColor = match ((int)$actualizado) {
                            2 => '#6f42c1', // Morado
                            1 => '#ffbb17', // Amarillo
                            3 => '#e65c00', // Naranja
                            default => null // Sin color de fondo (transparente)
                        };

                        // Construimos la propiedad de estilo solo si existe color
                        $rowStyle = $bgColor ? "background-color: {$bgColor};" : '';

                        $num_item = data_get($base, 'num_item', '-');
                        $descripcion = data_get($base, 'descripcion', 'Sin descripción');
                        $cantidad = data_get($base, 'cantidad', 0);
                        $vUnitario = (float) data_get($base, 'v_unitario', 0);
                        $vTotal = (float) data_get($base, 'v_total', 0);
                        $vUnitarioCliente = (float) data_get($base, 'v_unitario_cot', 0);
                        $vTotalCliente = (float) data_get($base, 'v_total_cliente', 0);
                        $proveedorRaw = data_get($base, 'proveedor');
                        $utilidad = (float) data_get($base, 'margen_utilidad');
                        $rentabilidad = (float) data_get($base, 'rentabilidad', 0);
                        $disponibleRaw = data_get($base, 'disponible', null);

                        if (is_null($disponibleRaw)) {
                            $disponible = '-';
                        } else {
                            $disponible = ((int) $disponibleRaw === 1) ? 'Sí' : 'No';
                        }
                    @endphp
                    {{-- Aplicamos el color de fondo directamente a la fila TR --}}
                    <tr>
                        <td style="{{ $rowStyle }}">{{ $num_item }}</td>
                        <td style="{{ $rowStyle }}">{{ $descripcion }}</td>
                        <td style="{{ $rowStyle }}">{{ $cantidad }}</td>
                        <td style="{{ $rowStyle }}">{{ number_format($vUnitario, 2) }}</td>
                        <td style="{{ $rowStyle }}">{{ number_format($vTotal, 2) }}</td>
                        <td style="{{ $rowStyle }}">{{ number_format($vUnitarioCliente, 2) }}</td>
                        <td style="{{ $rowStyle }}">{{ number_format($vTotalCliente, 2) }}</td>
                        <td class="font-weight-bold font-table" style="{{ $rowStyle }}">
                            @if ($proveedores_item = @unserialize($proveedorRaw))
                                @foreach ($proveedores_item as $p)
                                    {{ @$proveedores->find($p)->tercero }} <br>
                                @endforeach
                            @else
                                @if ($proveedores->find($proveedorRaw))
                                    {{ $proveedores->find($proveedorRaw)->tercero }}
                                @else
                                    {{ $proveedorRaw }}
                                @endif
                            @endif
                        </td>
                        <td style="{{ $rowStyle }}">{{ number_format(100-($utilidad * 100), 2) }}%</td>
                        <td style="{{ $rowStyle }}">{{ number_format($rentabilidad, 2) }}</td>
                        <td style="{{ $rowStyle }}">{{ $disponible }}</td>
                    </tr>
                @else
                    <tr>
                        @php
                            $base = data_get($registro, 'valores_anteriores', $registro);
                            $descripcion = data_get($base, 'descripcion', 'Sin descripción');
                        @endphp
                        <td class="bold" style="text-align: center; font-weight: bold;" colspan="14">{{ $descripcion }}</td>
                    </tr>
                @endif
            @endforeach
        @endif
        </tbody>
    </table>
</body>
</html>
