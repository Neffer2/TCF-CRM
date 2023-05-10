@extends('layouts.comercial.presupuesto')  
@section('nav-hidden')
    g-sidenav-hidden
@endsection
@section('content')     
    <div class="card card-frame">
        <div class="row justify-content-md-center">
            <div class="col-md-3 m-2">
                <div class="card">
                    <div class="table-responsive">
                        <table class="table">
                            <tr>
                                <td class="font-weight-bold font-table">MARGEN GENERAL</td>
                                <td class="font-table">0</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold font-table">VENTA PROYECTO</td>
                                <td class="font-table">0</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold font-table">COSTOS DEL PROYECTO</td>
                                <td class="font-table">0</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold font-table">MARGEN DEL PROYECTO</td>
                                <td class="font-table">0</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold font-table">MARGEN BRUTO (PESOS)</td>
                                <td class="font-table">0</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-3 m-2">
                <div class="card">
                    <div class="table-responsive">
                        <table class="table">
                            <tr>
                                <td class="font-weight-bold font-table">CONTACTO</td>
                                <td class="font-table">0</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold font-table">CLIENTE</td>
                                <td class="font-table">0</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold font-table">PROYECTO</td>
                                <td class="font-table">0</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold font-table">CIUDAD</td>
                                <td class="font-table">0</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div> 
    {{-- <div class="row mt-2">
        <div class="col-md-2 ps-0 pe-1">
            <div class="card card-frame p-2 bg-gradient-info text-white" style="border-radius: 8px">
                <div class="row">
                    <div class="col-md-3 font-weight-bold font-table">COD</div>
                    <div class="col-md-4 font-weight-bold font-table">REVISAR</div>
                    <div class="col-md-5 font-weight-bold font-table">CONCEPTO</div> 
                </div>
            </div>
        </div>
        <div class="col-md-8 ps-0 pe-1">
            <div class="card card-frame p-2 bg-gradient-warning text-white" style="border-radius: 8px">
                <div class="row">
                    <div class="col-md-1 font-weight-bold font-table">ITEM</div>
                    <div class="col-md-1 font-weight-bold font-table">CANTIDAD</div>
                    <div class="col-md-1 font-weight-bold font-table">DIA</div>
                    <div class="col-md-1 font-weight-bold font-table">OTROS</div>
                    <div class="col-md-2 font-weight-bold font-table">DESCRIPCION</div>
                    <div class="col-md-2 font-weight-bold font-table">V. UNITARIO</div>
                    <div class="col-md-1 font-weight-bold font-table">V. TOTAL</div>
                    <div class="col-md-2 font-weight-bold font-table">PROVEEDOR</div>
                    <div class="col-md-1 font-weight-bold font-table">UTILIDAD</div>
                </div>
            </div>
        </div>
        <div class="col-md-2 p-0">
                <div class="card card-frame p-2 bg-gradient-success text-white" style="border-radius: 8px">
                <div class="row">
                    <div class="col-md-4 font-weight-bold font-table">MES</div>
                    <div class="col-md-4 font-weight-bold font-table">DIAS</div>
                    <div class="col-md-4 font-weight-bold font-table">CIUDAD</div>
                </div>
            </div>
        </div>
    </div>

    @for ($i = 0; $i < 2; $i++)
        <div class="row pt-1">
            <div class="col-md-2">
                <div class="row item-table-background-tarifario">
                    <div class="col-md-3 p-0 font-weight-bold font-table">
                        <input type="text">
                    </div>
                    <div class="col-md-4 p-0 font-weight-bold font-table">
                        <input type="text">
                    </div>
                    <div class="col-md-5 p-0 font-weight-bold font-table">
                        <input type="text">
                    </div> 
                </div>
            </div>
            <div class="col-md-8">
                <div class="row item-table-background-interno">
                    <div class="col-md-1 p-0 font-weight-bold font-table">
                        <input type="text">
                    </div>
                    <div class="col-md-1 p-0 font-weight-bold font-table">
                        <input type="text">
                    </div>
                    <div class="col-md-1 p-0 font-weight-bold font-table">
                        <input type="text">
                    </div>
                    <div class="col-md-1 p-0 font-weight-bold font-table">
                        <input type="text">
                    </div>
                    <div class="col-md-2 p-0 font-weight-bold font-table">
                        <input type="text">
                    </div>
                    <div class="col-md-2 p-0 font-weight-bold font-table">
                        <input type="text">
                    </div>
                    <div class="col-md-1 p-0font-weight-bold font-table">
                        <input type="text">
                    </div>
                    <div class="col-md-2 p-0 font-weight-bold font-table">
                        <input type="text">
                    </div>
                    <div class="col-md-1 p-0 font-weight-bold font-table">
                        <input type="text">
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="row item-table-background-control">
                    <div class="col-md-4 p-0 font-weight-bold font-table">
                        <input type="text">
                    </div>
                    <div class="col-md-4 p-0 font-weight-bold font-table">
                        <input type="text">
                    </div>
                    <div class="col-md-4 p-0 font-weight-bold font-table">
                        <input type="text">
                    </div>
                </div>
            </div>
        </div>
    @endfor --}}
    
    <div class="table-responsive mt-2 rounded">
        <table class="table">
            <thead>
                <tr>
                    <th class="font-weight-bold font-table bg-gradient-info text-white small" >COD</th>
                    <th class="font-weight-bold font-table bg-gradient-info text-white small">REVISAR</th>
                    <th class="font-weight-bold font-table bg-gradient-info text-white small">CONCEPTO</th>

                    <th class="font-weight-bold font-table bg-gradient-warning text-white small">ITEM</th>
                    <th class="font-weight-bold font-table bg-gradient-warning text-white small">CANTIDAD</th>
                    <th class="font-weight-bold font-table bg-gradient-warning text-white small">DIA</th>
                    <th class="font-weight-bold font-table bg-gradient-warning text-white small">OTROS</th>
                    <th class="font-weight-bold font-table bg-gradient-warning text-white">DESCRIPCION</th>
                    <th class="font-weight-bold font-table bg-gradient-warning text-white">V. UNITARIO</th>
                    <th class="font-weight-bold font-table bg-gradient-warning text-white">V. TOTAL</th>
                    <th class="font-weight-bold font-table bg-gradient-warning text-white">PROVEEDOR</th>
                    <th class="font-weight-bold font-table bg-gradient-warning text-white">UTILIDAD</th>

                    <th class="font-weight-bold font-table bg-gradient-success text-white small">MES</th>
                    <th class="font-weight-bold font-table bg-gradient-success text-white small">DIAS</th>
                    <th class="font-weight-bold font-table bg-gradient-success text-white small">CIUDAD</th>
                </tr>
            </thead>
            <tbody>
                @for ($i = 0; $i < 5; $i++)
                    <tr>
                        <td class="font-weight-bold font-table">
                            <input type="text" class="small"> 
                        </td>
                        <td class="font-weight-bold font-table">
                            <input type="text" class="small">
                        </td>
                        <td class="font-weight-bold font-table">
                            <input type="text" class="small">
                        </td>
    
                        <td class="font-weight-bold font-table">
                            <input type="text" class="small">
                        </td>
                        <td class="font-weight-bold font-table">
                            <input type="text" class="small">
                        </td>
                        <td class="font-weight-bold font-table">
                            <input type="text" class="small">
                        </td>
                        <td class="font-weight-bold font-table">
                            <input type="text" class="small">
                        </td>
                        <td class="font-weight-bold font-table">
                            <textarea name="" id="" cols="30" rows="1"></textarea>
                        </td>
                        <td class="font-weight-bold font-table">
                            <input type="text">
                        </td>
                        <td class="font-weight-bold font-table">
                            <input type="text">
                        </td>
                        <td class="font-weight-bold font-table">
                            <input type="text">
                        </td>
                        <td class="font-weight-bold font-table">
                            <input type="text">
                        </td>
    
                        <td class="font-weight-bold font-table">
                            <input type="text" class="small">
                        </td>
                        <td class="font-weight-bold font-table">
                            <input type="text" class="small">
                        </td>
                        <td class="font-weight-bold font-table">
                            <input type="text" class="small">
                        </td>
                    </tr>
                @endfor
            </tbody>
        </table>
    </div>          
              

    {{-- <div class="row table-responsive">
        <table class="table">
            <tr>
                <td class="font-weight-bold font-table">COD</td>
                <td class="font-weight-bold font-table">REVISAR</td>
                <td class="font-weight-bold font-table">CONCEPTO</td>

                <td class="font-weight-bold font-table">ITEM</td>
                <td class="font-weight-bold font-table">CANTIDAD</td>
                <td class="font-weight-bold font-table">DIA</td>
                <td class="font-weight-bold font-table">OTROS</td>
                <td class="font-weight-bold font-table">DESCRIPCION</td>
                <td class="font-weight-bold font-table">V. UNITARIO</td>
                <td class="font-weight-bold font-table">V. TOTAL</td>
                <td class="font-weight-bold font-table">PROVEEDOR</td>
                <td class="font-weight-bold font-table">UTILIDAD</td>            

                <td class="font-weight-bold font-table">COD</td>
                <td class="font-weight-bold font-table">REVISAR</td>
                <td class="font-weight-bold font-table">CONCEPTO</td>
            </tr>
        </table>
    </div> --}}

    <div class="row mt-2">
        <div class="col-md-2">
            <a x-on:click="show_form" href="javascript:;" class="avatar border-1 rounded-circle bg-gradient-warning">
                <i class="fas fa-plus text-white" aria-hidden="true"></i>
            </a>
        </div>
    </div>
@endsection 

@section('scripts')  
    <script>
        
    </script>
@endsection   