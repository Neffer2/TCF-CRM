@extends('layouts.admin.main')
    @section('nav-hidden')
        g-sidenav-hidden
    @endsection 
    @section('hero-style')
        <div class="position-absolute w-100 min-height-300 top-0" style="background-image: url('{{ asset('assets/img/hero-2.jpg') }}'); background-position-y: 50%;">
            <span class="mask bg-gradient-warning opacity-6"></span>
        </div>
        <!-- <div class="min-height-300 bg-gradient-warning position-absolute w-100"></div>  -->
    @endsection
    @section('content') 
        <div class="col-ms-12 col-md-12 col-xl-12"> 
            <div class="card">
                <div class="card-body d-flex pb-0 justify-content-center mb-4">
                    <div class="">
                        <h4 class="font-weight-bolder mb-0 fadeIn1 fadeInBottom">ESTADOS DE FACTURACI&Oacute;N</h4>
                    </div>
                </div>
            </div> 
        </div>
        <hr>
        <div class="col-ms-12 col-md-12 col-xl-12">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                @livewire('admin.dashboard.filters')
                            </div>
                            <hr>
                            @livewire('admin.estados-facturacion.estados-facturacion')
                        </div>
                    </div>
                </div>
            </div> 
        </div>
        <div class="col-ms-12 col-md-12 col-xl-5">
        </div> 
    @endsection
