@extends('layouts.lider-produccion.main')
    @section('hero-style')
        <div class="position-absolute w-100 min-height-300 top-0 crm-hero crm-hero--photo" style="background-image: url('{{ asset('assets/img/hero-2.jpg') }}')"></div>
    @endsection

    @section('content')
        @livewire('admin.produccion.ordenes-compra', ['estado_id' => [8,10]])
    @endsection
