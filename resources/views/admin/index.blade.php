@extends('layouts.admin.main')
    @section('hero-style')
        {{-- Fotografía de marca (equipo + gráfica naranja) con velo en los naranjas Bull --}}
        <div class="position-absolute w-100 min-height-300 top-0 crm-hero crm-hero--photo" style="background-image: url('{{ asset('assets/img/hero-2.jpg') }}')"></div>
    @endsection
    @section('content')
        @php $saludNotif = in_array(Auth::user()->rol, [1, 20]) ? \App\Services\Notificador::salud() : null; @endphp
        @if ($saludNotif && $saludNotif['fallidas_24h'] > 0)
            <div class="col-12">
                <a class="crm-aviso crm-aviso--bad mb-4" href="{{ route('notificaciones') }}">
                    <span class="crm-aviso__icono"><i class="ni ni-notification-70" aria-hidden="true"></i></span>
                    <span><b>{{ $saludNotif['fallidas_24h'] }} {{ $saludNotif['fallidas_24h'] == 1 ? 'notificación falló' : 'notificaciones fallaron' }} en las últimas 24 horas.</b>
                    Última: {{ optional($saludNotif['ultima_fallida'])->canal }} · {{ optional($saludNotif['ultima_fallida'])->evento }} — {{ \Illuminate\Support\Str::limit(optional($saludNotif['ultima_fallida'])->error, 90) }}</span>
                    <span class="crm-aviso__accion">Ver y reintentar →</span>
                </a>
            </div>
        @endif
        <div class="col-12">
            <div class="card mb-4 crm-page-card">
                <div class="crm-page-head">
                    <div class="crm-page-head__title">
                        @if (Auth::user()->esLiderComercial())
                            <span class="crm-eyebrow">Líder comercial · Mi equipo</span>
                            <h1>Cumplimiento del presupuesto de mi equipo</h1>
                            <p>Venta facturada y consolidada de tus comerciales frente a su presupuesto. Filtra por año, mes y comercial.</p>
                        @else
                            <span class="crm-eyebrow">Gerencia · Comercial</span>
                            <h1>Cumplimiento del presupuesto comercial</h1>
                            <p>Venta facturada y consolidada frente al presupuesto. Filtra por año, mes, líder y comercial.</p>
                        @endif
                    </div>
                    @livewire('admin.dashboard.filters')
                </div>
            </div>
        </div>
        {{-- Izquierda: indicadores + tendencia mensual. Derecha: estado de facturación + ranking.
             Ambas columnas son flex para que la última tarjeta estire y no quede hueco. --}}
        <div class="col-12 col-xl-7 crm-col-izquierda">
            @livewire('admin.dashboard.block1')
            @livewire('admin.dashboard.tendencia')
        </div>
        <div class="col-12 col-xl-5 mt-4 mt-xl-0 crm-col-derecha">
            @livewire('admin.dashboard.block2')
            @livewire('admin.dashboard.ranking')
        </div>
        {{-- Panel a pantalla completa: detalle de un estado de la venta --}}
        @livewire('admin.dashboard.detalle-estado')
    @endsection
