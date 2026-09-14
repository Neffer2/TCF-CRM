@extends('layouts.comercial.main')
@section('titulo', 'Dashboard')
@section('hero-style')
    {{-- Fotografía de marca con velo en los naranjas Bull (misma cabecera que gerencia) --}}
    <div class="position-absolute w-100 min-height-300 top-0 crm-hero crm-hero--photo" style="background-image: url('{{ asset('assets/img/hero-2.jpg') }}')"></div>
@endsection
@section('content')
  {{-- El layout comercial no envuelve el contenido en una fila (el de admin sí) --}}
  <div class="row">
    <div class="col-12">
        <div class="card mb-4 crm-page-card">
            <div class="crm-page-head">
                <div class="crm-page-head__title crm-perfil">
                    @php $avatar = Auth::user()->avatar ? asset('storage/'.str_replace('public/', '', Auth::user()->avatar)) : null; @endphp
                    <span class="crm-perfil__avatar">
                        @if ($avatar)<img src="{{ $avatar }}" alt="" onerror="this.remove()">@endif
                        <b>{{ mb_strtoupper(mb_substr(Auth::user()->name, 0, 1)) }}</b>
                    </span>
                    <div>
                        <span class="crm-eyebrow">Comercial · Mi cumplimiento</span>
                        <h1>Hola, {{ explode(' ', Auth::user()->name)[0] }}</h1>
                        <p>Tu venta facturada y consolidada frente a tu presupuesto. Filtra por año, mes y cuenta.</p>
                    </div>
                </div>
                @livewire('com.dashboard.filters')
            </div>
        </div>
    </div>
    {{-- Izquierda: indicadores + tendencia mensual. Derecha: estado de facturación. --}}
    <div class="col-12 col-xl-7 crm-col-izquierda">
        @livewire('com.dashboard.block1')
        @livewire('admin.dashboard.tendencia', ['comercialesFijos' => [Auth::id()]])
    </div>
    <div class="col-12 col-xl-5 mt-4 mt-xl-0 crm-col-derecha">
        @livewire('com.dashboard.block2')
    </div>
  </div>
@endsection
