@extends('layouts.admin.main')
@section('titulo', 'Anticipo jurídico')
@section('hero-style')
    <div class="position-absolute w-100 min-height-300 top-0 crm-hero crm-hero--photo" style="background-image: url('{{ asset('assets/img/hero-2.jpg') }}')"></div>
@endsection
@section('content')
    @livewire('produccion.anticipo-juridico', ['anticipo_id' => $anticipo_id])
@endsection
