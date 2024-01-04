<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        body {
            font-size: 12px; 
            color: black !important;
        }
    </style>
</head>
<body>
    <table>
        <tr>
            <td colspan="7"></td>
            <td colspan="@if ($tipo) 7 @else 10 @endif" style="text-align: right; color: #1b6f08;">
                <b>Integramos servicios especializados en mercadeo.</b>
            </td>
        </tr>
        <tr>
            <td colspan="7"></td>
            <td colspan="@if ($tipo) 7 @else 10 @endif" style="text-align: right">
                .Diseño e implementación de estrategias de Marketing y BTL.
            </td>
        </tr>
        <tr>
            <td colspan="7"></td>
            <td colspan="@if ($tipo) 7 @else 10 @endif" style="text-align: right">
                .Desarrollo y ejecución de actividades de Trade.
            </td>
        </tr>
        <tr>
            <td colspan="7"></td>         
            <td colspan="@if ($tipo) 7 @else 10 @endif" style="text-align: right">
                .Logistica de distribuciones, bodegaje e instalación de POP y Stands.
            </td>
        </tr>
        <tr>
            <td colspan="7">Nit: 900.298.176-1</td>
            <td colspan="@if ($tipo) 7 @else 10 @endif" style="text-align: right">
                .Creamos estrategias de PR y Medios Masivos.
            </td>
        </tr>
        <tr>
            <td colspan="@if ($tipo) 7 @else 8 @endif">IVA REGIMEN COMUN</td>
            <td></td>
        </tr>
    </table> 

    <table>
        <tr>
            <td colspan="@if ($tipo) 8 @else 11 @endif" style="font-weight: bold;">Señor (es) {{ $presto->gestion->contacto->nombre }} {{ $presto->gestion->contacto->apellido }} </td>
            <td colspan="3" style="text-align: center; font-weight: bold;">COTIZACION</td>
            <td colspan="3" style="text-align: center; font-weight: bold; color: red; background-color: #e4e4e4">{{ $presto->cod_cot }}</td>
        </tr>
        <tr>
            <td colspan="@if ($tipo) 8 @else 11 @endif" style="font-weight: bold;">Empresa: {{ $presto->gestion->contacto->empresa }} </td>
            <td colspan="3" style="text-align: center;">Fecha Emisión</td>
            <td colspan="3" style="text-align: center;">Fecha Vencimiento</td>
        </tr>
        <tr>
            <td colspan="@if ($tipo) 8 @else 11 @endif" style="font-weight: bold;">Proyecto: {{ $presto->gestion->nom_proyecto_cot }} </td>
            <td colspan="3" style="text-align: center; background-color: #e4e4e4">{{ date('d/m/Y') }}</td>
            <td colspan="3" style="text-align: center; background-color: #e4e4e4">{{ date('d/m/Y', strtotime("+ $presto->tiempo_factura days")) }}</td>
        </tr>
        <tr>            
            @if (!$tipo) <td colspan="@if ($tipo) 8 @else 11 @endif" style="font-weight: bold;">Centro de costos: {{ $presto->cod_cc }}</td> @else 
            <td colspan="@if ($tipo) 8 @else 11 @endif" style="font-weight: bold;">Ciudad: {{ $presto->gestion->contacto->ciudad }} </td>
            @endif
            <td colspan="3" style="text-align: center; font-weight: bold;">Condiciones De Pago</td>
            <td colspan="3" style="text-align: center; font-weight: bold; background-color: #e4e4e4">{{ $presto->tiempo_factura }} días</td>
        </tr>
    </table>
 
    <table>
        <tbody>
            {{-- Acumula total cotizacion (para sacar las métricas) --}}
            {{-- Acumula total interno --}}
            @php    
                $totalCot = 0;
                $totalInter = 0;
            @endphp
            <tr>
                @if (!$tipo) <td style="text-align: center; font-weight: bold; background-color: #ef6f14; color: white;">ITEM</td> @endif
                <td colspan="6" style="text-align: center; font-weight: bold; background-color: #ef6f14; color: white;">DESCRIPCION</td>
                <td colspan="2" style="text-align: center; font-weight: bold; background-color: #ef6f14; color: white;">CANTIDAD</td>
                @if (!$tipo) <td colspan="2" style="text-align: center; font-weight: bold; background-color: #ef6f14; color: white;">PROVEEDOR</td> @endif
                <td colspan="1" style="text-align: center; font-weight: bold; background-color: #ef6f14; color: white;">DIAS</td>
                <td colspan="1" style="text-align: center; font-weight: bold; background-color: #ef6f14; color: white;">OTROS</td>
                <td colspan="2" style="text-align: center; font-weight: bold; background-color: #ef6f14; color: white;">Vr. UNIT</td>
                <td colspan="2" style="text-align: center; font-weight: bold; background-color: #ef6f14; color: white;">Vr. TOTAL</td>
            </tr>
            @foreach ($items as $key => $item)
                @if (!$item->evento == 1)
                    <tr> 
                        @if (!$tipo) <td style="text-align: center">{{ $key+=1 }}</td> @endif
                        <td colspan="6">{{ $item->descripcion }}</td>
                        <td colspan="2" style="text-align: center">{{ $item->cantidad }}</td>
                        @if (!$tipo)
                            <td colspan="2" style="text-align: center"> @if ($item->proveedorInfo) {{ $item->proveedorInfo->tercero }} @else {{ $item->proveedor }} @endif </td>
                        @endif
                        <td colspan="1" style="text-align: center">{{ $item->dia }}</td>
                        <td colspan="1" style="text-align: center">{{ $item->otros }}</td>

                        @if (!$tipo)
                            <td colspan="2" style="text-align: center">{{ $item->v_unitario }}</td> 
                        @else 
                            <td colspan="2" style="text-align: center">{{ $item->v_unitario_cot }}</td>
                        @endif

                        @if (!$tipo)
                            <td colspan="2" style="text-align: center">{{ $item->v_total }}</td>
                        @else 
                            <td colspan="2" style="text-align: center">{{ $item->v_total_cot }}</td>
                        @endif                                             
                    </tr>            
                @else 
                    <tr>
                        <td class="bold" style="text-align: center; font-weight: bold;" colspan="14">{{ $item->descripcion }}</td>
                    </tr>
                @endif
                @php 
                    $totalCot += $item->v_total_cot; 
                    $totalInter += $item->v_total; 
                @endphp
            @endforeach
    
            <tr></tr> 
            <tr></tr> 
            @if ($tipo)
                <tr></tr> 
                <tr></tr>
            @endif 
    
            @if ($tipo)
                <tr>
                    <td class="text-center" colspan="8">IMPREVISTOS</td>  
                    <td class="text-center" colspan="2" style="text-align: center; background-color: #e4e4e4;">{{ number_format($presto->imprevistos) }} %</td>
                    <td colspan="2"></td>
                    <td class="text-center" colspan="2" style="text-align: center;">{{ $totalCot * ($presto->imprevistos/100) }}</td>
                </tr>
                <tr>
                    <td class="text-center" colspan="8">ADMINISTRACION</td>
                    <td class="text-center" colspan="2" style="text-align: center; background-color: #e4e4e4;">{{ number_format($presto->administracion) }} %</td>
                    <td colspan="2"></td>
                    <td class="text-center" colspan="2" style="text-align: center;">{{ $totalCot * ($presto->administracion/100) }} </td>
                </tr>
                <tr>
                    <td class="text-center" colspan="8">FEE AGENCIA</td>
                    <td class="text-center" colspan="2" style="text-align: center; background-color: #e4e4e4;">{{ number_format($presto->fee) }} %</td>
                    <td colspan="2"></td>
                    <td class="text-center" colspan="2" style="text-align: center;">{{ $totalCot * ($presto->fee/100) }} </td>
                </tr>
                <tr>
                    <td style="text-align: center; font-weight: bold;" colspan="10">Valores No incluyen IVA</td>                    
                    <td colspan="2" style="text-align: center; font-weight: bold; background-color: #ef6f14; color: white;">TOTAL</td>
                    <td colspan="2" style="text-align: center; font-weight: bold;">{{ $presto->venta_proy }} </td>
                </tr>
            @else
                <tr>
                    <td class="text-center" colspan="13"></td>                    
                    <td class="bold text-center" colspan="2" style="text-align: center; font-weight: bold; background-color: #ef6f14; color: white;">TOTAL</td>
                    <td class="text-center" colspan="2" style="text-align: center; font-weight: bold;">{{ $totalInter }} </td>
                </tr>
            @endif
            <tr>
                <td colspan="14"></td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center; font-weight: bold;">
                    NOTA:
                </td>
                <td colspan="12">
                    {{ $presto->notas }}
                </td>
            </tr>
            <tr>
                <td colspan="14" style="text-align: center; font-weight: bold;">Responsable de IVA – Régimen común – actividad económica 7310 – practicar retención de Ica solo en Bogotá D.C.</td>
            </tr>
        </tbody>
    </table>
</body>
</html> 