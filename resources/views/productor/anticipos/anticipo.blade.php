@extends('layouts.productor.main')
    @section('hero-style')
        <div class="position-absolute w-100 min-height-300 top-0 crm-hero crm-hero--photo" style="background-image: url('{{ asset('assets/img/hero-2.jpg') }}')"></div>
    @endsection

    @section('content')
        @if ($tipo == 1)

        @else
            @livewire('productor.ordenes.anticipo-productor', ['anticipo_id' => $anticipo_id])
        @endif
    @endsection
