@extends('layouts.admin.main')
    @section('hero-style')
        <div class="position-absolute w-100 min-height-300 top-0" style="background-image: url('{{ asset('assets/img/hero-2.jpg') }}'); background-position-y: 50%;">
            <span class="mask bg-gradient-warning opacity-6"></span>
        </div>
    @endsection

    @section('content')
        @if ($tipo == 1)
            @livewire('productor.ordenes.anticipo-juridico', ['anticipo_id' => $anticipo_id])
        @else
            @livewire('productor.ordenes.anticipo-productor', ['anticipo_id' => $anticipo_id])
        @endif
    @endsection
