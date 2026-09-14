@extends('layouts.productor.main')
@section('hero-style')
    <div class="position-absolute w-100 min-height-300 top-0 crm-hero crm-hero--photo" style="background-image: url('{{ asset('assets/img/hero-2.jpg') }}')"></div>
    <!-- <div class="min-height-300 bg-gradient-warning position-absolute w-100"></div>  -->
@endsection
@section('content')
    @livewire('productor.remision', ['orden' => $orden])     
@endsection 
@section('scripts')
@endsection