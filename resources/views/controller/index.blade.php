@extends('layouts.admin.main')
@section('titulo', 'Dashboard Controller')
@section('hero-style')
    <div class="position-absolute w-100 min-height-300 top-0 crm-hero crm-hero--photo" style="background-image: url('{{ asset('assets/img/hero-2.jpg') }}')"></div>
@endsection
@section('content')
@php
    $money = function ($v) { return '$'.number_format((float) $v, 0, '.', ','); };
    $fila = function ($p) {
        return [
            'proyecto'  => optional($p->gestion)->nom_proyecto_cot ?: 'Sin nombre',
            'comercial' => optional(optional($p->gestion)->comercial)->name ?: '—',
            'cc'        => $p->cod_cc ?: '—',
        ];
    };
@endphp
    <div class="col-12">
        <div class="card mb-4 crm-page-card">
            <div class="crm-page-head">
                <div class="crm-page-head__title">
                    <span class="crm-eyebrow">Controller · Revisión financiera</span>
                    <h1>Hola, {{ explode(' ', Auth::user()->name)[0] }}</h1>
                    <p>Lo que está pendiente de revisión y lo último aprobado. Desde aquí entras a los presupuestos, las actualizaciones, los consumidos y los reportes.</p>
                </div>
                <div class="crm-actions">
                    <a class="crm-btn crm-btn--ghost" href="{{ route('actualizaciones') }}">Actualizaciones</a>
                    <a class="crm-btn" href="{{ route('presupuesto-proyecto') }}">Presupuestos <span class="crm-btn__icon" aria-hidden="true">→</span></a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="crm-kpis crm-kpis--mini mb-4">
            <a class="card crm-kpi crm-kpi--mini tone-warn" style="--i:0" href="{{ route('presupuesto-proyecto') }}">
                <div class="crm-kpi__head"><div>
                    <p class="crm-kpi__label">Cambios por revisar</p>
                    <p class="crm-kpi__value"><span data-count="{{ $resumen['cambios'] }}" data-key="ctrl_cambios">{{ $resumen['cambios'] }}</span></p>
                    <p class="crm-kpi__note">Presupuestos aprobados con modificaciones posteriores.</p>
                </div></div>
                <span class="crm-kpi__icon"><i class="ni ni-notification-70" aria-hidden="true"></i></span>
            </a>
            <a class="card crm-kpi crm-kpi--mini tone-neutral" style="--i:1" href="{{ route('presupuesto-proyecto') }}">
                <div class="crm-kpi__head"><div>
                    <p class="crm-kpi__label">En validación</p>
                    <p class="crm-kpi__value"><span data-count="{{ $resumen['validacion_lider'] + $resumen['validacion_gerencia'] }}" data-key="ctrl_val">{{ $resumen['validacion_lider'] + $resumen['validacion_gerencia'] }}</span></p>
                    <p class="crm-kpi__note">{{ $resumen['validacion_lider'] }} con líder comercial · {{ $resumen['validacion_gerencia'] }} con gerencia.</p>
                </div></div>
                <span class="crm-kpi__icon"><i class="ni ni-time-alarm" aria-hidden="true"></i></span>
            </a>
            <a class="card crm-kpi crm-kpi--mini tone-ok" style="--i:2" href="{{ route('presupuesto-proyecto') }}">
                <div class="crm-kpi__head"><div>
                    <p class="crm-kpi__label">Aprobados este mes</p>
                    <p class="crm-kpi__value"><span data-count="{{ $resumen['aprobados_mes'] }}" data-key="ctrl_apr">{{ $resumen['aprobados_mes'] }}</span></p>
                    <p class="crm-kpi__note">Con centro de costos asignado desde el 1.º del mes.</p>
                </div></div>
                <span class="crm-kpi__icon"><i class="ni ni-check-bold" aria-hidden="true"></i></span>
            </a>
            <div class="card crm-kpi crm-kpi--mini tone-neutral" style="--i:3">
                <div class="crm-kpi__head"><div>
                    <p class="crm-kpi__label">Órdenes de compra en revisión</p>
                    <p class="crm-kpi__value"><span data-count="{{ $resumen['oc_revision'] }}" data-key="ctrl_oc">{{ $resumen['oc_revision'] }}</span></p>
                    <p class="crm-kpi__note">@if (!is_null($resumen['oc_gerencia'])){{ $resumen['oc_gerencia'] }} nóminas esperan validación de gerencia.@else Pendientes de aprobación en producción.@endif</p>
                </div></div>
                <span class="crm-kpi__icon"><i class="ni ni-cart" aria-hidden="true"></i></span>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-6 crm-col-izquierda">
        <div class="card crm-panel crm-panel--tabla" style="--i:1">
            <div class="card-header">
                <div class="crm-panel__head">
                    <div>
                        <p class="crm-kpi__label">Requieren tu revisión</p>
                        <h2>Presupuestos con cambios pendientes</h2>
                    </div>
                    <span class="crm-kpi__icon"><i class="ni ni-notification-70" aria-hidden="true"></i></span>
                </div>
            </div>
            <div class="card-body crm-tabla-wrap">
                <div class="crm-tabla-scroll">
                    <table class="crm-table crm-table--data">
                        <thead><tr><th>Proyecto</th><th>Comercial</th><th>Centro</th><th class="is-num">Margen</th><th></th></tr></thead>
                        <tbody>
                            @forelse ($cambios as $i => $p)
                                @php $f = $fila($p); @endphp
                                <tr style="--i:{{ $i }}">
                                    <td class="is-cliente" title="{{ $f['proyecto'] }}"><span class="crm-clamp">{{ $f['proyecto'] }}</span></td>
                                    <td class="is-comercial"><span class="crm-avatar-mini">{{ mb_strtoupper(mb_substr($f['comercial'], 0, 1)) }}</span><span>{{ $f['comercial'] }}</span></td>
                                    <td class="is-cc">{{ $f['cc'] }}</td>
                                    <td class="is-num is-valor">{{ sprintf('%.1f', $p->margen_proy) }} %</td>
                                    <td class="is-num"><a class="crm-link" href="{{ route('presupuesto', $p->id_gestion) }}">Ver</a></td>
                                </tr>
                            @empty
                                <tr class="is-empty"><td colspan="5"><div class="crm-empty"><strong>Sin cambios pendientes.</strong><span>Todos los presupuestos aprobados están revisados.</span></div></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card crm-panel crm-panel--tabla" style="--i:2">
            <div class="card-header">
                <div class="crm-panel__head">
                    <div>
                        <p class="crm-kpi__label">En validación</p>
                        <h2>Esperando a líder comercial o gerencia</h2>
                    </div>
                    <span class="crm-kpi__icon"><i class="ni ni-time-alarm" aria-hidden="true"></i></span>
                </div>
            </div>
            <div class="card-body crm-tabla-wrap">
                <div class="crm-tabla-scroll">
                    <table class="crm-table crm-table--data">
                        <thead><tr><th>Proyecto</th><th>Comercial</th><th>Estado</th><th class="is-num">Margen</th><th></th></tr></thead>
                        <tbody>
                            @forelse ($pendientes as $i => $p)
                                @php $f = $fila($p); @endphp
                                <tr style="--i:{{ $i }}">
                                    <td class="is-cliente" title="{{ $f['proyecto'] }}"><span class="crm-clamp">{{ $f['proyecto'] }}</span></td>
                                    <td class="is-comercial"><span class="crm-avatar-mini">{{ mb_strtoupper(mb_substr($f['comercial'], 0, 1)) }}</span><span>{{ $f['comercial'] }}</span></td>
                                    <td><span class="crm-chip {{ $p->estado_id == 5 ? 'tone-bad' : 'tone-warn' }}">{{ $p->estado_id == 5 ? 'Gerencia' : 'Líder comercial' }}</span></td>
                                    <td class="is-num is-valor">{{ sprintf('%.1f', $p->margen_proy) }} %</td>
                                    <td class="is-num"><a class="crm-link" href="{{ route('presupuesto', $p->id_gestion) }}">Ver</a></td>
                                </tr>
                            @empty
                                <tr class="is-empty"><td colspan="5"><div class="crm-empty"><strong>Nada en validación.</strong><span>No hay presupuestos esperando aprobación.</span></div></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-6 mt-4 mt-xl-0 crm-col-derecha">
        <div class="card crm-panel crm-panel--tabla" style="--i:3">
            <div class="card-header">
                <div class="crm-panel__head">
                    <div>
                        <p class="crm-kpi__label">Últimos aprobados</p>
                        <h2>Presupuestos con centro de costos</h2>
                    </div>
                    <span class="crm-kpi__icon"><i class="ni ni-check-bold" aria-hidden="true"></i></span>
                </div>
            </div>
            <div class="card-body crm-tabla-wrap">
                <div class="crm-tabla-scroll">
                    <table class="crm-table crm-table--data">
                        <thead><tr><th>Fecha</th><th>Proyecto</th><th>Comercial</th><th>Centro</th><th class="is-num">Venta</th></tr></thead>
                        <tbody>
                            @forelse ($aprobados as $i => $p)
                                @php $f = $fila($p); $d = $p->fecha_cc ? \Carbon\Carbon::parse($p->fecha_cc) : null; @endphp
                                <tr style="--i:{{ $i }}">
                                    <td class="is-fecha">@if ($d)<b>{{ $d->format('d') }}</b> {{ $d->locale('es')->isoFormat('MMM YYYY') }}@else — @endif</td>
                                    <td class="is-cliente" title="{{ $f['proyecto'] }}"><a class="crm-link" href="{{ route('presupuesto', $p->id_gestion) }}"><span class="crm-clamp">{{ $f['proyecto'] }}</span></a></td>
                                    <td class="is-comercial"><span class="crm-avatar-mini">{{ mb_strtoupper(mb_substr($f['comercial'], 0, 1)) }}</span><span>{{ $f['comercial'] }}</span></td>
                                    <td class="is-cc">{{ $f['cc'] }}</td>
                                    <td class="is-num is-valor"><small>$</small>{{ number_format((float) $p->venta_proy, 0, '.', ',') }}</td>
                                </tr>
                            @empty
                                <tr class="is-empty"><td colspan="5"><div class="crm-empty"><strong>Aún no hay presupuestos aprobados.</strong></div></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
