@extends('layouts.productor.main')
    @section('hero-style')
        <div class="position-absolute w-100 min-height-300 top-0 crm-hero crm-hero--photo" style="background-image: url('{{ asset('assets/img/hero-2.jpg') }}')"></div>
    @endsection
    @section('content')
        @livewire('productor.ordenes.nomina', ['productor' => Auth()->user(), 'orden_id' => $orden_id])
    @endsection
