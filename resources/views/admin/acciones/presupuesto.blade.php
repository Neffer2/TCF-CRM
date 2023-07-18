@extends('layouts.admin.main')
@section('hero-style')
    <div class="position-absolute w-100 min-height-300 top-0" style="background-image: url('{{ asset('assets/img/hero-2.jpg') }}'); background-position-y: 50%;">
        <span class="mask bg-gradient-warning opacity-6"></span>
    </div>
    {{-- <div class="min-height-300 bg-primary position-absolute w-100"></div>  --}}
@endsection 
  
@section('content') 
    <div x-data="menu" class="row">
        <div class="col-lg-3 col-md-12">
            <div class="nav-wrapper position-relative end-0">
                <ul class="nav nav-pills nav-fill flex-column p-1" role="tablist">
                    <li x-on:click="Toggle('lista')" class="nav-item">
                        <a class="nav-link mb-0 px-0 py-1 active" data-bs-toggle="tab" href="#profile-tabs-vertical" role="tab" aria-controls="preview" aria-selected="true">
                            Presupuestos 
                        </a>
                    </li>
                    <li x-on:click="Toggle('asignar')" class="nav-item">
                        <a class="nav-link mb-0 px-0 py-1" data-bs-toggle="tab" href="#dashboard-tabs-vertical" role="tab" aria-controls="code" aria-selected="false">
                            Asignar
                        </a> 
                    </li>
                    <li x-on:click="Toggle('conf')" class="nav-item">
                        <a class="nav-link mb-0 px-0 py-1" data-bs-toggle="tab" href="#payments-tabs-vertical" role="tab" aria-controls="code" aria-selected="false">
                            Configurar
                        </a>
                    </li> 
                </ul>
            </div>

            <hr>
            <div class="card-header" x-show="conf" x-transition x-cloak>
                @livewire('admin.presupuestos.nuevo-año')  
                <hr>
            </div>
        </div>

        {{-- Presupuestos list --}}
        <div class="col-md-9 col-sm-12" x-show="lista" x-transition x-cloak>
            @livewire('admin.presupuestos.presupuestos')
        </div> 

        {{-- Asignar --}}
        <div class="col-md-9 col-sm-12" x-show="asignar" x-transition x-cloak>
            @livewire('admin.presupuestos.asignar-presupuesto')
        </div>     

        {{-- conf --}}
        <div class="col-md-9 col-sm-12" x-show="conf" x-transition x-cloak>
            @livewire('admin.presupuestos.conf')
        </div> 
    </div>
@endsection 
@section('scripts')
    <script> 
        function menu (){
            return {
                lista: true,
                asignar: false,
                conf: false,
                Toggle(item){
                    if (item === "lista"){
                        this.lista = true
                        this.asignar = false
                        this.conf = false
                    }else if(item === "asignar"){
                        this.lista = false
                        this.asignar = true
                        this.conf = false
                    }else if(item === "conf"){
                        this.lista = false
                        this.asignar = false
                        this.conf = true
                    }
                }
            }
        }
    </script>
@endsection