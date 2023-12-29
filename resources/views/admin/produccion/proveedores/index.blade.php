@extends('layouts.admin.main')
    @section('hero-style')
        <div class="position-absolute w-100 min-height-300 top-0" style="background-image: url('{{ asset('assets/img/hero-2.jpg') }}'); background-position-y: 50%;">
            <span class="mask bg-gradient-warning opacity-6"></span>
        </div>
    @endsection
 
    @section('content')
    <div class="card">
        <div class="card-body px-1 py-3">
            <div class="d-flex align-items-baseline" x-data="{ collapse: true}" x-cloak>
                <button class="btn bg-gradient-warning me-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample" x-on:click="collapse = !collapse">
                    <i class="fas fa-plus text-white" aria-hidden="true" x-show="collapse"></i>
                    <i class="fa-solid fa-minus text-white" aria-hidden="true" x-cloak x-show="!collapse"></i>
                </button>
                <h5><b>Nuevo proveedor</b></h5>
            </div>             
            <div class="collapse" id="collapseExample">
                <div class="card card-body mb-3">
                    @livewire('admin.produccion.proveedores.nuevo-proveedor')
                </div>          
            </div>
            
            @livewire('admin.produccion.proveedores.proveedores')  
        </div>
    </div>
    @endsection   