@extends('layouts.asistente.main')
@section('titulo', 'Sin comercial asignado')
@section('hero-style')
    <div class="position-absolute w-100 min-height-300 top-0 crm-hero crm-hero--photo" style="background-image: url('{{ asset('assets/img/hero-2.jpg') }}')"></div>
@endsection
@section('content')
    <div class="col-12">
        <div class="card crm-page-card">
            <div class="crm-page-head">
                <div class="crm-page-head__title">
                    <span class="crm-eyebrow">Ejecutivo de cuenta</span>
                    <h1>Aún no tienes un comercial asignado</h1>
                    <p>Tu cuenta está activa, pero para ver la base comercial, los prospectos y Helisa necesitas estar asignado a un comercial. Pídele al administrador del CRM que te asigne uno.</p>
                </div>
                <div class="crm-actions"><a class="crm-btn crm-btn--ghost" href="{{ route('mi-perfil') }}">Ver mi perfil</a></div>
            </div>
        </div>
    </div>
@endsection
