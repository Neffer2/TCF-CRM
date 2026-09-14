@extends('layouts.admin.main')
@section('titulo', 'Base comercial general')
@section('hero-style')
    {{-- Fotografía de marca con velo en los naranjas Bull (misma cabecera que el dashboard) --}}
    <div class="position-absolute w-100 min-height-300 top-0 crm-hero crm-hero--photo" style="background-image: url('{{ asset('assets/img/hero-2.jpg') }}')"></div>
@endsection
@section('content')
    <div class="col-12">
        @livewire('admin.generales.base-comercial-general', ['requested_filters' => $filtros])
    </div>
@endsection
