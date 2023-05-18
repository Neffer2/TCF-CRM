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

        /* 
        tr {
            border: 1px solid black;
        }

        td {
            border: 1px solid black;
        }

        th {
            color: white;
            background-color: orange;
            border-right: 2px solid black;
        } */

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
                    Proyecto: C{{ $presto->gestion->nom_proyecto_cot }} <br>
                    Ciudad: {{ $presto->gestion->contacto->ciudad }}
                </td>
                <td style="padding: 0;">
                    <table class="info-inner table text-center">
                        <tr>
                            <td class="bold" style="border-left: none; border-top: none;">COTIZACION</td>
                            <td class="bold" style="border-right: none; border-top: none; color: red; background-color: rgb(228, 228, 228);">10005</td>
                        </tr>
                        <tr>
                            <td style="border-left: none; border-top: none;">Fecha Emisión</td>
                            <td style="border-right: none; border-top: none;">Fecha Vencimiento</td>
                        </tr>
                        <tr>
                            <td style="border-left: none; border-top: none; background-color: rgb(228, 228, 228);">1/03/2023</td>
                            <td style="border-right: none; border-top: none; background-color: rgb(228, 228, 228);">31/03/2023</td>
                        </tr>
                        <tr>                            
                            <td class="bold" style="border-left: none; border-bottom: none;">Condiciones De Pago</td>
                            <td class="bold" style="border-right: none; border-bottom: none; background-color: rgb(228, 228, 228);">30 días</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <table class="presupuesto table">
            <thead>
                <tr>
                    <th class="bg-orange" style="width: 45%;">DESCRIPCION</th>
                    <th class="bg-orange">CANTIDAD</th>
                    <th class="bg-orange">DIAS</th>                    
                    <th class="bg-orange">OTROS</th>                    
                    <th class="bg-orange">Vr. UNIT</th>
                    <th class="bg-orange">Vr. TOTAL</th>                 
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $item)
                    @if (!$item->evento == 1)
                        <tr>
                            <td>{{ $item->descripcion }}</td>
                            <td class="text-center">{{ $item->cantidada }}</td>
                            <td class="text-center">{{ $item->dia }}</td>
                            <td class="text-center">{{ $item->otros }}</td>
                            <td class="text-center">$ {{ $item->v_unitario_cot }}</td>
                            <td class="text-center">$ {{ $item->v_total_cot }}</td>               
                        </tr>   
                    @else 
                        <tr>
                            <td class="text-center bold" style="border-right: none" colspan="3">{{ $item->descripcion }}</td>
                            <td style="border: none"></td>
                            <td style="border: none"></td>
                            <td style="border: none"></td>
                        </tr>
                    @endif
                @endforeach             

                <tr class="space">
                    <td class="space"></td>
                    <td class="space"></td>
                    <td class="space"></td>
                    <td class="space"></td>
                    <td class="space"></td>
                    <td class="space"></td>
                </tr>
                

                <tr>
                    <td class="text-center">IMPREVISTOS</td>
                    <td></td>
                    <td></td>
                    <td class="text-center" style="background-color: rgb(228, 228, 228);">1%</td>
                    <td></td>
                    <td class="text-center"> $ 3.335.582</td>
                </tr>
                <tr>
                    <td class="text-center">ADMINISTRACION</td>
                    <td></td>
                    <td></td>
                    <td class="text-center" style="background-color: rgb(228, 228, 228);">0%</td>
                    <td></td>
                    <td class="text-center">0</td>
                </tr>
                <tr>
                    <td class="text-center">FEE AGENCIA</td>
                    <td></td>
                    <td></td>
                    <td class="text-center" style="background-color: rgb(228, 228, 228);">10%</td>
                    <td></td>
                    <td class="text-center"> $ 32.688.703 </td>
                </tr>

                <tr>
                    <td class="text-center" colspan="4">Valores No incluyen IVA</td>                    
                    <td class="bold text-center bg-orange">TOTAL</td>
                    <td class="text-center bold">
                        $ {{ $presto->venta_proy }}
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="border bold">
            <p><b>NOTA:</b></p>
        </div>

        <div class="border text-center">
            <div class="bold">
                Responsable de IVA – Régimen común – actividad económica 7310 – practicar retención de Ica solo en Bogotá D.C.
            </div>
        </div>
    </div>
</body>