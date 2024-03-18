<head>
    <style>
        body {
            font-size: 12px;
            color: black !important;
        }
 
        table {
            border-collapse: collapse;
            color: black !important;
        }

        p {
            margin: 0;
            padding: 5px;
        }

        .border-dark {
            border-color: black !important;
        }

        .hero tr {
            border: 2px solid black;
        }

        .hero td {
            
        }

        .header tr {
            border: 2px solid black;
            border-top: none;
        }

        .header td {
            border: 2px solid black;
            border-top: none;
        }

        .info-inner tr {
            border: none;
        }

        .info-inner td {
            border: 2px solid black;
        }

        .presupuesto tr {
            border: 2px solid black;
            border-top: none;
        }

        .presupuesto th {
            border: 2px solid black;
            border-top: none;
        }

        .presupuesto td {
            border: 2px solid black;
        }

        .bg-orange {
            background-color: #F05A22;
            color: white;
        }

        .table {
            width: 100%;
        }

        .bold {
            font-weight: bold;
        }

        .text-end {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .border {
            border: 2px solid black; 
            border-top: none;
        }

        .padding-1  {
            padding-left: 2rem; 
            padding-right: 2rem;
        }

        .space {
            border-top: none;
            border-bottom: none;
            padding-top: 50px;
            padding-bottom: 50px;
        }
    </style>
</head>
<body>
    <div class="container">
        <table class="hero table">
            <tr>
                <td class="bold" style="border-right: none; padding: 5px;">
                    <img src="https://www.bullmarketing.com.co/wp-content/uploads/2022/02/Logo-bull-negro_2-e1664286411369.png" alt="BUllmarketing logo" height="80"><br>
                    Nit: 900.298.176-1 <br>
                    IVA REGIMEN COMUN
                </td> 
                <td class="text-end" style="border-left: none">
                    <p style="line-height: 22px;">
                        <span class="bold" style="color: green;">Integramos servicios especializados en mercadeo </span><br>
                        .Diseño e implementación de estrategias de Marketing y BTL. <br>
                        .Desarrollo y ejecución de actividades de Trade. <br>
                        .Logistica de distribuciones, bodegaje e instalación de POP y Stands. <br>
                        .Creamos estrategias de PR y Medios Masivos.   
                    </p>
                </td>
            </tr>
        </table>
        <table class="header table">
            <tr>
                <td class="bold" style="width: 68%;">
                    Señor (es) {{ $presto->gestion->contacto->nombre }} {{ $presto->gestion->contacto->apellido }}<br>
                    Empresa: {{ $presto->gestion->contacto->empresa }} <br> 	 			 
                    Proyecto: {{ $presto->gestion->nom_proyecto_cot }} <br>
                    @if (!$tipo) Centro de costos: {{ $presto->cod_cc }} @else Ciudad: {{ $presto->gestion->contacto->ciudad }} @endif
                </td>
                <td style="padding: 0;">
                    <table class="info-inner table text-center">
                        <tr>
                            <td class="bold" style="border-left: none; border-top: none;">COTIZACION</td>
                            <td class="bold" style="border-right: none; border-top: none; color: red; background-color: rgb(228, 228, 228);">{{ $presto->cod_cot }}</td>
                        </tr>
                        <tr>
                            <td style="border-left: none; border-top: none;">Fecha Emisión</td>
                            <td style="border-right: none; border-top: none;">Fecha Vencimiento</td>
                        </tr>
                        <tr>
                            <td style="border-left: none; border-top: none; background-color: rgb(228, 228, 228);">{{ date('d/m/Y') }}</td>
                            <td style="border-right: none; border-top: none; background-color: rgb(228, 228, 228);">{{ date('d/m/Y', strtotime("+ $presto->tiempo_factura days")) }}</td>
                        </tr>
                        <tr>                            
                            <td class="bold" style="border-left: none; border-bottom: none;">Condiciones De Pago</td>
                            <td class="bold" style="border-right: none; border-bottom: none; background-color: rgb(228, 228, 228);">{{ $presto->tiempo_factura }} días</td>
                        </tr>
                    </table>
                </td> 
            </tr>
        </table>

        <table class="presupuesto table">
            <thead>
                <tr>
                    @if (!$tipo) <th class="bg-orange">ITEM</th> @endif
                    <th class="bg-orange" style="width: 45%;">DESCRIPCION</th>
                    <th class="bg-orange">CANTIDAD</th>
                    <th class="bg-orange">DIAS</th>                    
                    <th class="bg-orange">OTROS</th>                    
                    <th class="bg-orange">Vr. UNIT</th>
                    <th class="bg-orange">Vr. TOTAL</th>                 
                </tr>
            </thead>
            <tbody>
                {{-- Acumula total cotizacion (para sacar las métricas) --}}
                {{-- Acumula total interno --}}
                @php    
                    $totalCot = 0;
                    $totalInter = 0;
                @endphp
                @foreach ($items as $key => $item)
                    @if (!$item->evento == 1)
                        <tr>                                
                            @if (!$tipo) <td class="text-center">{{ $key+=1 }}</td> @endif

                            <td>{{ $item->descripcion }}</td>
                            <td class="text-center">{{ $item->cantidad }}</td>
                            <td class="text-center">{{ $item->dia }}</td>
                            <td class="text-center">{{ $item->otros }}</td>

                            @if (!$tipo)
                                <td class="text-center">$ {{ number_format($item->v_unitario) }}</td> 
                            @else 
                                <td class="text-center">$ {{ number_format($item->v_unitario_cot) }}</td>
                            @endif

                            @if (!$tipo)
                                <td class="text-center">$ {{ number_format($item->v_total) }}</td>
                            @else 
                                <td class="text-center">$ {{ number_format($item->v_total_cot) }}</td>
                            @endif 
                        </tr>
                    @else 
                        <tr>
                            <td class="bold" style="border-right: none" colspan="@if ($tipo) 6 @else 7 @endif">{{ $item->descripcion }}</td>
                        </tr>
                    @endif
                    @php 
                        $totalCot += $item->v_total_cot; 
                        $totalInter += $item->v_total; 
                    @endphp
                @endforeach             

                <tr class="space">
                    <td class="space"></td>
                    <td class="space"></td>
                    <td class="space"></td>
                    <td class="space"></td>
                    <td class="space"></td>
                    <td class="space"></td>
                    @if (!$tipo) <td class="space"></td> @endif
                </tr>                

                @if ($tipo)
                    <tr>
                        <td class="text-center">IMPREVISTOS</td>  
                        <td></td> 
                        <td></td>
                        <td class="text-center" style="background-color: rgb(228, 228, 228);">{{ number_format($presto->imprevistos, 2) }} %</td>
                        <td></td>
                        <td class="text-center"> $ {{ number_format($totalCot * ($presto->imprevistos/100), 2) }}</td>
                    </tr>
                    <tr>
                        <td class="text-center">ADMINISTRACION</td>
                        <td></td>
                        <td></td>
                        <td class="text-center" style="background-color: rgb(228, 228, 228);">{{ number_format($presto->administracion, 2) }} %</td>
                        <td></td>
                        <td class="text-center">$ {{ number_format($totalCot * ($presto->administracion/100), 2) }} </td>
                    </tr>
                    <tr>
                        <td class="text-center">FEE AGENCIA</td>
                        <td></td>
                        <td></td> 
                        <td class="text-center" style="background-color: rgb(228, 228, 228);">{{ number_format($presto->fee, 2) }} %</td>
                        <td></td>
                        <td class="text-center"> $ {{ number_format($totalCot * ($presto->fee/100), 2) }} </td>
                    </tr>
                    <tr>
                        <td class="text-center" colspan="4">Valores No incluyen IVA</td>                    
                        <td class="bold text-center bg-orange">TOTAL</td>
                        <td class="text-center bold">
                            $ {{ number_format($presto->venta_proy, 2) }}
                        </td>
                    </tr>
                @else 
                    <tr>
                        <td class="text-center" colspan="5"></td>                    
                        <td class="bold text-center bg-orange">TOTAL</td>
                        <td class="text-center bold">
                            $ {{ number_format($totalInter, 2) }}
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>

        <div class="border">
            <p><b>NOTA:</b> {{ $presto->notas }} </p>
        </div>

        <div class="border text-center">
            <div class="bold">
                Responsable de IVA – Régimen común – actividad económica 7310 – practicar retención de Ica solo en Bogotá D.C.
            </div>
        </div>
    </div>
</body>