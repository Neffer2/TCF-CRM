@extends('layouts.admin.main')
    @section('hero-style')
        <div class="position-absolute w-100 min-height-300 top-0" style="background-image: url('{{ asset('assets/img/hero-2.jpg') }}'); background-position-y: 50%;">
            <span class="mask bg-gradient-warning opacity-6"></span>
        </div>
    @endsection

    @section('content') 
        @livewire('productor.ordenes.natural', ['productor' => Auth()->user(), 'orden_id' => $orden_id])
    @endsection 