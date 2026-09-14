@extends('layouts.admin.main')
@section('hero-style')
    <div class="position-absolute w-100 min-height-300 top-0 crm-hero crm-hero--photo" style="background-image: url('{{ asset('assets/img/hero-2.jpg') }}')"></div>
    <!-- <div class="min-height-300 bg-gradient-warning position-absolute w-100"></div>  -->
@endsection
@section('content')
    {{-- Finalmente se decide que el módulo esté en Admin y no en producción --}}
    @livewire('productor.terceros.personal')
@endsection
