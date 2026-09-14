@extends('layouts.admin.main')
    @section('hero-style')
        {{-- Fotografía de marca (equipo + gráfica naranja) con velo en los naranjas Bull --}}
        <div class="position-absolute w-100 min-height-300 top-0 crm-hero crm-hero--photo" style="background-image: url('{{ asset('assets/img/hero-2.jpg') }}')"></div>
    @endsection
    @section('content')
        <div class="col-12">
            <div class="card mb-4 crm-page-card">
                <div class="crm-page-head">
                    <div class="crm-page-head__title">
                        <span class="crm-eyebrow">Gerencia · Comercial</span>
                        <h1>Cumplimiento del presupuesto comercial</h1>
                        <p>Venta facturada y consolidada frente al presupuesto. Filtra por año, mes y comercial.</p>
                    </div>
                    @livewire('admin.dashboard.filters')
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-7">
            @livewire('admin.dashboard.block1')
        </div>
        <div class="col-12 col-xl-5 mt-4 mt-xl-0 crm-col-derecha">
            @livewire('admin.dashboard.block2')
            @livewire('admin.dashboard.ranking')
        </div>
        <div class="col-12 mt-4">
            @livewire('admin.dashboard.tendencia')
        </div>
    @endsection
