@extends('layouts.admin.main')
    @section('hero-style')
        <div class="position-absolute w-100 min-height-300 top-0" style="background-image: url('{{ asset('assets/img/hero-2.jpg') }}'); background-position-y: 50%;">
            <span class="mask bg-gradient-warning opacity-6"></span>
        </div>
        {{-- <div class="min-height-300 bg-gradient-warning position-absolute w-100"></div>  --}}
    @endsection
    @section('content')
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header d-flex pb-0 justify-content-center">
                    <div class="">
                        <h4 class="font-weight-bolder mb-0">CUMPLIMIENTO DEL PRESUPUESTO COMERCIAL</h4>
                    </div>
                </div>
                <div class="card-body"></div>
            </div>
        </div>
        <hr>
        <div class="col-xl-7">
            <div class="card">
                <div class="card-header pb-0">
                    @livewire('admin.dashboard.filters')
                </div> 
                <div class="card-body pt-1">
                    @livewire('admin.dashboard.block1')
                </div>
            </div>
        </div>
        <div class="col-xl-5 ms-auto mt-xl-0 mt-4">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <h6 class="mb-0 font-weight-bolder">ESTADO DE FACTURACI&Oacute;N</h6>
                        </div>
                        <div class="col-md-4 text-center">
                            <h6 class="mb-0 font-weight-bolder">SUMATORIA DE VENTAS</h6>
                        </div>
                        <div class="col-md-4 text-center">
                            <h6 class="mb-0 font-weight-bolder">% ESTADO POR FACTURAR + VENTA FACTURADA</h6>
                        </div>
                    </div>                    
                </div>
                <div class="card-body pt-1">
                    <div class="row">
                        <div class="col-md-4 text-center d-flex justify-content-center">
                            <div class="">
                                <div class="p-3">
                                    <div class="d-flex">
                                        <div class="numbers">
                                            <p class="text-sm mb-0 text-uppercase font-weight-bold">EJECUCIÓN X FACTURAR</p>
                                            <h5 class="font-weight-bolder mb-0">$87,000</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-center d-flex justify-content-center pb-3">
                            <div class="d-flex align-items-end">
                                <div class="numbers">
                                    <h5 class="font-weight-bolder mb-0">$87,000</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-center d-flex justify-content-center pb-3">
                            <div class="d-flex align-items-end">
                                <div class="numbers">
                                    <h5 class="font-weight-bolder mb-0">%80</h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 text-center d-flex justify-content-center">
                            <div class="">
                                <div class="p-3">
                                    <div class="d-flex">
                                        <div class="numbers">
                                            <p class="text-sm mb-0 text-uppercase font-weight-bold">VENTA<br>EJECUCIÓN</p>
                                            <h5 class="font-weight-bolder mb-0">$87,000</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-center d-flex justify-content-center pb-3">
                            <div class="d-flex align-items-end">
                                <div class="numbers">
                                    <h5 class="font-weight-bolder mb-0">$87,000</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-center d-flex justify-content-center pb-3">
                            <div class="d-flex align-items-end">
                                <div class="numbers">
                                    <h5 class="font-weight-bolder mb-0">%80</h5>
                                </div>
                            </div>
                        </div>
                    </div>  

                    <div class="row">
                        <div class="col-md-4 text-center d-flex justify-content-center">
                            <div class="">
                                <div class="p-3">
                                    <div class="d-flex">
                                        <div class="numbers">
                                            <p class="text-sm mb-0 text-uppercase font-weight-bold">VENTA</p>
                                            <h5 class="font-weight-bolder mb-0">$87,000</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-center d-flex justify-content-center pb-3">
                            <div class="d-flex align-items-end">
                                <div class="numbers">
                                    <h5 class="font-weight-bolder mb-0">$87,000</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-center d-flex justify-content-center pb-3">
                            <div class="d-flex align-items-end">
                                <div class="numbers">
                                    <h5 class="font-weight-bolder mb-0">%80</h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 text-center d-flex justify-content-center">
                            <div class="">
                                <div class="p-3">
                                    <div class="d-flex">
                                        <div class="numbers text-aling">
                                            <p class="text-sm mb-0 text-uppercase font-weight-bold">VENTA TOTAL</p>
                                            <h5 class="font-weight-bolder mb-0">$87,000</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            @livewire('admin.dashboard.graphs')
        </div> 
        {{-- <hr class="horizontal dark my-5"> --}}
    @endsection
