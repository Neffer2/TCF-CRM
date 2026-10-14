@extends('layouts.lider-produccion.main')

@section('hero-style')
    <div class="position-absolute w-100 min-height-300 top-0" style="background-image: url('{{ asset('assets/img/hero-2.jpg') }}'); background-position-y: 50%;">
        <span class="mask bg-gradient-warning opacity-6"></span>
    </div>
@endsection

@section('content')        
    <div class="card"> 
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    @livewire('lider-produccion.anticipos.lista-anticipo')
                </div>
            </div>
        </div>
    </div>
@endsection