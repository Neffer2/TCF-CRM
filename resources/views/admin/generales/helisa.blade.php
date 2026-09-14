@extends('layouts.admin.main')
@section('titulo', 'Helisa general')
@section('hero-style')
    <div class="position-absolute w-100 min-height-300 top-0 crm-hero crm-hero--photo" style="background-image: url('{{ asset('assets/img/hero-2.jpg') }}')"></div>
@endsection
@section('content')
    <div class="col-12">
        @livewire('admin.generales.helisa-general')
    </div>
@endsection
