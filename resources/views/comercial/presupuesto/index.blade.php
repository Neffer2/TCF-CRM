@extends('layouts.comercial.presupuesto')  
@section('nav-hidden')
    g-sidenav-hidden
@endsection
@section('hero-style')
        <div class="position-absolute w-100 min-height-300 top-0" style="background-image: url('{{ asset('assets/img/hero-2.jpg') }}'); background-position-y: 50%;">
            {{-- <div class="min-height-300 bg-gradient-warning position-absolute w-100"></div> --}}
            <span class="mask bg-gradient-warning opacity-6"></span>
        </div>
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
    @livewire("com.presupuesto.presupuesto", ['id_gestion' => $id_gestion]) 
@endsection 

@section('scripts')  
    <script>
        
    </script>
@endsection   