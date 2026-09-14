{{--
    Panel de inicio para Tesorería (paga) y Contabilidad (causa).
    Variables: $modo ('tesoreria'|'contabilidad'), $resumen, $ordenes, $anticipos, $pagosMes (de App\Services\PanelFinanciero).
--}}
@php
    use App\Services\PanelFinanciero;
    $esTeso = $modo === 'tesoreria';
    $r = $resumen;
    $cop = fn ($v) => '$'.number_format($v, 0, ',', '.');
    $fecha = fn ($v) => $v ? \Carbon\Carbon::parse($v)->format('d/m/Y') : '—';
    $maxMes = max(1, max(array_column($pagosMes, 'n')));
    $verbo = $esTeso ? 'Pagar' : 'Causar';
    $rutaOc = $esTeso ? 'anticipo' : 'anticipo-contabilidad';
    $rutaListaOc = $esTeso ? 'anticipos' : 'anticipos-contabilidad';
    $rutaAnt = $esTeso ? 'detalle-anticipo-tesoreria' : 'detalle-anticipo-contabilidad';
    $rutaListaAnt = $esTeso ? 'lista-anticipos-tesoreria' : 'lista-anticipos-contabilidad';
    $hora = now()->hour; $saludo = $hora < 12 ? 'Buenos días' : ($hora < 18 ? 'Buenas tardes' : 'Buenas noches');
@endphp
<div class="crm-base crm-fin">
    <div class="card mb-4 crm-page-card">
        <div class="crm-page-head crm-page-head--compacta">
            <div class="crm-page-head__title">
                <span class="crm-eyebrow">{{ $esTeso ? 'Tesorería · Pagos' : 'Contabilidad · Causación' }}</span>
                <h1>{{ $saludo }}, {{ explode(' ', Auth::user()->name)[0] }}</h1>
                <p>
                    @if ($esTeso)
                        Aquí ves lo que ya está causado y espera comprobante de pago: órdenes de compra y anticipos de producción. Cada fila abre la orden para subir el comprobante.
                    @else
                        Aquí ves lo aprobado que todavía no tiene causal: órdenes de compra y anticipos de producción. Cada fila abre la orden para causarla.
                    @endif
                </p>
            </div>
            <div class="crm-fin__acciones">
                <a class="btn bg-gradient-primary crm-btn-accion mb-0" href="{{ route($rutaListaOc) }}"><span class="crm-plus" aria-hidden="true"></span> {{ $verbo }} órdenes de compra</a>
                <a class="btn btn-outline-dark crm-btn-accion mb-0" href="{{ route($rutaListaAnt) }}">Anticipos de producción</a>
            </div>
        </div>
    </div>

    <div class="crm-kpis crm-kpis--mini mb-4">
        <div class="card crm-kpi crm-kpi--mini tone-warn" style="--i:0">
            <div class="crm-kpi__head"><div>
                <p class="crm-kpi__label">Órdenes {{ $esTeso ? 'por pagar' : 'por causar' }}</p>
                <p class="crm-kpi__value"><span data-count="{{ $esTeso ? $r['oc_pendientes_anio'] : $r['oc_por_causar_anio'] }}" data-key="fin_oc">{{ $esTeso ? $r['oc_pendientes_anio'] : $r['oc_por_causar_anio'] }}</span></p>
                <p class="crm-kpi__note">de este año · {{ number_format($esTeso ? $r['oc_pendientes'] : $r['oc_por_causar'], 0, ',', '.') }} en total histórico</p>
            </div></div>
            <span class="crm-kpi__icon"><i class="ni ni-single-copy-04" aria-hidden="true"></i></span>
        </div>
        <div class="card crm-kpi crm-kpi--mini tone-neutral" style="--i:1">
            <div class="crm-kpi__head"><div>
                <p class="crm-kpi__label">Valor {{ $esTeso ? 'por pagar' : 'por causar' }} (año)</p>
                <p class="crm-kpi__value">{{ $cop($esTeso ? $r['oc_valor_pendiente_anio'] : $r['oc_valor_por_causar_anio']) }}</p>
                <p class="crm-kpi__note">suma de los ítems de esas órdenes</p>
            </div></div>
            <span class="crm-kpi__icon"><i class="ni ni-money-coins" aria-hidden="true"></i></span>
        </div>
        <div class="card crm-kpi crm-kpi--mini tone-ok" style="--i:2">
            <div class="crm-kpi__head"><div>
                <p class="crm-kpi__label">Órdenes {{ $esTeso ? 'pagadas' : 'causadas' }} este mes</p>
                <p class="crm-kpi__value"><span data-count="{{ $esTeso ? $r['oc_pagadas_mes'] : $r['oc_causadas_mes'] }}" data-key="fin_mes">{{ $esTeso ? $r['oc_pagadas_mes'] : $r['oc_causadas_mes'] }}</span></p>
                <p class="crm-kpi__note">{{ ucfirst(now()->locale('es')->isoFormat('MMMM')) }}</p>
            </div></div>
            <span class="crm-kpi__icon"><i class="ni ni-check-bold" aria-hidden="true"></i></span>
        </div>
        <div class="card crm-kpi crm-kpi--mini {{ ($esTeso ? $r['ant_pendientes'] : $r['ant_por_causar']) ? 'tone-warn' : 'tone-muted' }}" style="--i:3">
            <div class="crm-kpi__head"><div>
                <p class="crm-kpi__label">Anticipos {{ $esTeso ? 'por pagar' : 'por causar' }}</p>
                <p class="crm-kpi__value"><span data-count="{{ $esTeso ? $r['ant_pendientes'] : $r['ant_por_causar'] }}" data-key="fin_ant">{{ $esTeso ? $r['ant_pendientes'] : $r['ant_por_causar'] }}</span></p>
                <p class="crm-kpi__note">{{ $cop($esTeso ? $r['ant_valor_pendiente'] : $r['ant_valor_por_causar']) }} · {{ $esTeso ? $r['ant_pagados_mes'].' pagados' : $r['ant_causados_mes'].' causados' }} este mes</p>
            </div></div>
            <span class="crm-kpi__icon"><i class="ni ni-send" aria-hidden="true"></i></span>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card crm-panel crm-panel--tabla h-100" style="--i:1">
                <div class="card-header">
                    <div class="crm-panel__head">
                        <div>
                            <p class="crm-kpi__label">Las más recientes</p>
                            <h2>Órdenes de compra {{ $esTeso ? 'listas para pagar' : 'pendientes de causar' }}</h2>
                        </div>
                        <a class="crm-link" href="{{ route($rutaListaOc) }}">Ver todas →</a>
                    </div>
                </div>
                <div class="card-body crm-tabla-wrap">
                    <div class="crm-tabla-scroll">
                        <table class="crm-table crm-table--data">
                            <thead><tr><th>Orden</th><th>Centro de costos</th><th>Beneficiario</th><th>Tipo</th><th class="is-num">Valor</th><th>{{ $esTeso ? 'Causada' : 'Aprobada' }}</th><th></th></tr></thead>
                            <tbody>
                                @forelse ($ordenes as $i => $o)
                                    <tr style="--i:{{ $i }}">
                                        <td class="is-cc"><b>{{ $o->cod_oc ?: '#'.$o->id }}</b></td>
                                        <td class="is-proyecto"><span class="crm-clamp">{{ optional($o->presupuesto)->cod_cc ?: 'Sin centro de costos' }}{{ optional(optional($o->presupuesto)->gestion)->nom_proyecto_cot ? ' · '.$o->presupuesto->gestion->nom_proyecto_cot : '' }}</span></td>
                                        <td class="is-cliente"><span class="crm-clamp">{{ PanelFinanciero::beneficiario($o) }}</span></td>
                                        <td><span class="crm-chip tone-muted">{{ optional($o->tipo)->description ?? optional($o->tipo)->descripcion ?? '—' }}</span></td>
                                        <td class="is-num">{{ $cop($o->total ?? 0) }}</td>
                                        <td class="is-fecha-corta">{{ $fecha(($esTeso ? $o->fecha_causal : $o->fecha_aprobacion) ?: $o->updated_at) }}</td>
                                        <td class="is-num"><a class="btn btn-sm bg-gradient-primary mb-0" href="{{ route($rutaOc, ['orden' => $o->id]) }}">{{ $verbo }}</a></td>
                                    </tr>
                                @empty
                                    <tr class="is-empty"><td colspan="7"><div class="crm-empty"><strong>Nada pendiente.</strong><span>Cuando {{ $esTeso ? 'contabilidad cause una orden' : 'gerencia apruebe una orden' }} aparecerá aquí.</span></div></td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-4">
            <div class="card crm-panel mb-4" style="--i:2">
                <div class="card-header">
                    <div class="crm-panel__head">
                        <div><p class="crm-kpi__label">Últimos 6 meses</p><h2>Órdenes {{ $esTeso ? 'pagadas' : 'causadas' }} por mes</h2></div>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="crm-fin__meses">
                        @foreach ($pagosMes as $m)
                            <div class="crm-fin__mes">
                                <span class="crm-fin__mes-n">{{ $m['n'] }}</span>
                                <div class="crm-fin__mes-barra"><span data-height="{{ round($m['n'] / $maxMes * 100) }}" style="height: {{ round($m['n'] / $maxMes * 100) }}%"></span></div>
                                <span class="crm-fin__mes-label">{{ $m['label'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="card crm-panel" style="--i:3">
                <div class="card-header">
                    <div class="crm-panel__head">
                        <div><p class="crm-kpi__label">Producción</p><h2>Anticipos {{ $esTeso ? 'por pagar' : 'por causar' }}</h2></div>
                        <a class="crm-link" href="{{ route($rutaListaAnt) }}">Ver todos →</a>
                    </div>
                </div>
                <div class="card-body pt-0">
                    @forelse ($anticipos as $a)
                        <a class="crm-fin__ant" href="{{ route($rutaAnt, ['anticipo_id' => $a->id]) }}">
                            <div>
                                <b>{{ optional(optional($a->ordenCompra)->presupuesto)->cod_cc ?: 'Anticipo #'.$a->id }}</b>
                                <span>{{ optional($a->productor_info)->name ?: 'Sin productor' }} · {{ number_format($a->porcentaje_anticipo, 0) }}%</span>
                            </div>
                            <strong>{{ $cop($a->total_anticipo) }}</strong>
                        </a>
                    @empty
                        <div class="crm-empty"><strong>Sin anticipos pendientes.</strong><span>Los anticipos que {{ $esTeso ? 'contabilidad cause' : 'gerencia apruebe' }} aparecen aquí.</span></div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
