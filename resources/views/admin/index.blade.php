@extends('layouts.admin.main')
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
                        <h4 class="font-weight-bolder mb-0 fadeIn1 fadeInBottom">CUMPLIMIENTO DEL PRESUPUESTO COMERCIAL</h4>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="col-ms-12 col-md-12 col-xl-7">
            <div class="card">
                <div class="card-header pb-0">
                    @livewire('admin.dashboard.filters')
                </div> 
                <div class="card-body pt-1">
                    @livewire('admin.dashboard.block1')
                </div>
            </div>
        </div>
        <div class="col-ms-12 col-md-12 col-xl-5">
            @livewire('admin.dashboard.block2')
        </div>
        <div class="row col-md-12 mt-4">
            {{-- @livewire('admin.dashboard.graphs') --}}
        </div> 
        {{-- <hr class="horizontal dark my-5"> --}}
    @endsection
