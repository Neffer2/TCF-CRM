@php
    // Tono semántico según el cumplimiento: >=100 % en meta, 80–99 % cerca, <80 % lejos.
    $tono = function ($pct) {
        if ($pct >= 100) return ['tone-ok', 'En meta'];
        if ($pct >= 80)  return ['tone-warn', 'Cerca de la meta'];
        return ['tone-bad', 'Por debajo del presupuesto'];
    };
    [$tonoMes, $estadoMes]   = $tono($cumpli_venta_men);
    [$tonoAcum, $estadoAcum] = $tono($cumpli_acum_venta_men);
    $pctMes  = max(0, min(100, (float) $cumpli_venta_men));
    $pctAcum = max(0, min(100, (float) $cumpli_acum_venta_men));
    $porCumplir = max(0, min(100, (float) $presto_x_cumplir));
    $logrado = 100 - $porCumplir;
    $difMes  = $venta_facturada - $presto_mensual;
    $difAcum = $venta_consolidada - $presto_acumulado;
    // Diferencia contra la meta, abreviada para que quepa en la tarjeta ($8.687 M)
    $abrev = function ($v) {
        $a = abs($v);
        if ($a >= 1e9) return '$'.number_format($a / 1e9, 2, '.', ',').' mil M';
        if ($a >= 1e6) return '$'.number_format($a / 1e6, 1, '.', ',').' M';
        return '$'.number_format($a, 0, '.', ',');
    };
@endphp
<div class="crm-kpis crm-kpis--six">

    {{-- Fila 1: mes --}}
    <article class="crm-kpi {{ $tonoMes }}" style="--i:0">
        <div class="crm-kpi__head">
            <div>
                <p class="crm-kpi__label">Venta facturada · mes</p>
                <h3 class="crm-kpi__value"><small>$</small><span data-count="{{ round($venta_facturada) }}">{{ number_format($venta_facturada, 0, '.', ',') }}</span></h3>
            </div>
            <span class="crm-kpi__icon"><i class="ni ni-money-coins" aria-hidden="true"></i></span>
        </div>
        <div class="crm-bar" role="progressbar" aria-valuenow="{{ $pctMes }}" aria-valuemin="0" aria-valuemax="100"><span data-width="{{ $pctMes }}"></span></div>
        <div class="crm-kpi__foot">
            <span class="crm-chip">{{ $estadoMes }}</span>
            <span class="crm-kpi__delta {{ $difMes >= 0 ? 'is-up' : 'is-down' }}">{{ $difMes >= 0 ? '+' : '−' }}{{ $abrev($difMes) }} vs meta</span>
        </div>
    </article>

    <article class="crm-kpi tone-neutral" style="--i:1">
        <div class="crm-kpi__head">
            <div>
                <p class="crm-kpi__label">Presupuesto mensual</p>
                <h3 class="crm-kpi__value"><small>$</small><span data-count="{{ round($presto_mensual) }}">{{ number_format($presto_mensual, 0, '.', ',') }}</span></h3>
            </div>
            <span class="crm-kpi__icon"><i class="ni ni-calendar-grid-58" aria-hidden="true"></i></span>
        </div>
        <p class="crm-kpi__note">Meta de venta del periodo seleccionado.</p>
    </article>

    <article class="crm-kpi {{ $tonoMes }}" style="--i:2">
        <div class="crm-kpi__head">
            <div>
                <p class="crm-kpi__label">Cumplimiento · mes</p>
                <h3 class="crm-kpi__value"><span data-count="{{ sprintf('%.1f', $cumpli_venta_men) }}" data-decimals="1">{{ sprintf('%.1f', $cumpli_venta_men) }}</span><small> %</small></h3>
            </div>
            <span class="crm-kpi__icon"><i class="ni ni-chart-bar-32" aria-hidden="true"></i></span>
        </div>
        <div class="crm-bar crm-bar--thick" role="progressbar" aria-valuenow="{{ $pctMes }}" aria-valuemin="0" aria-valuemax="100"><span data-width="{{ $pctMes }}"></span></div>
        <p class="crm-kpi__note">Venta facturada sobre presupuesto mensual.</p>
    </article>

    {{-- Fila 2: acumulado --}}
    <article class="crm-kpi {{ $tonoAcum }}" style="--i:3">
        <div class="crm-kpi__head">
            <div>
                <p class="crm-kpi__label">Venta consolidada · acumulado</p>
                <h3 class="crm-kpi__value"><small>$</small><span data-count="{{ round($venta_consolidada) }}">{{ number_format($venta_consolidada, 0, '.', ',') }}</span></h3>
            </div>
            <span class="crm-kpi__icon"><i class="ni ni-collection" aria-hidden="true"></i></span>
        </div>
        <div class="crm-bar" role="progressbar" aria-valuenow="{{ $pctAcum }}" aria-valuemin="0" aria-valuemax="100"><span data-width="{{ $pctAcum }}"></span></div>
        <div class="crm-kpi__foot">
            <span class="crm-chip">{{ $estadoAcum }}</span>
            <span class="crm-kpi__delta {{ $difAcum >= 0 ? 'is-up' : 'is-down' }}">{{ $difAcum >= 0 ? '+' : '−' }}{{ $abrev($difAcum) }} vs meta</span>
        </div>
    </article>

    <article class="crm-kpi tone-neutral" style="--i:4">
        <div class="crm-kpi__head">
            <div>
                <p class="crm-kpi__label">Presupuesto acumulado</p>
                <h3 class="crm-kpi__value"><small>$</small><span data-count="{{ round($presto_acumulado) }}">{{ number_format($presto_acumulado, 0, '.', ',') }}</span></h3>
            </div>
            <span class="crm-kpi__icon"><i class="ni ni-archive-2" aria-hidden="true"></i></span>
        </div>
        <p class="crm-kpi__note">Meta acumulada de enero al periodo.</p>
    </article>

    <article class="crm-kpi {{ $tonoAcum }}" style="--i:5">
        <div class="crm-kpi__head">
            <div>
                <p class="crm-kpi__label">Cumplimiento · acumulado</p>
                <h3 class="crm-kpi__value"><span data-count="{{ sprintf('%.1f', $cumpli_acum_venta_men) }}" data-decimals="1">{{ sprintf('%.1f', $cumpli_acum_venta_men) }}</span><small> %</small></h3>
            </div>
            <span class="crm-kpi__icon"><i class="ni ni-chart-pie-35" aria-hidden="true"></i></span>
        </div>
        <div class="crm-bar crm-bar--thick" role="progressbar" aria-valuenow="{{ $pctAcum }}" aria-valuemin="0" aria-valuemax="100"><span data-width="{{ $pctAcum }}"></span></div>
        <p class="crm-kpi__note">Venta consolidada sobre presupuesto acumulado.</p>
    </article>

    {{-- Fila 3: por cumplir --}}
    <article class="crm-kpi crm-kpi--wide tone-neutral" style="--i:6">
        <div class="crm-kpi__head">
            <div>
                <p class="crm-kpi__label">Presupuesto por cumplir</p>
                <h3 class="crm-kpi__value"><span data-count="{{ sprintf('%.1f', $presto_x_cumplir) }}" data-decimals="1">{{ sprintf('%.1f', $presto_x_cumplir) }}</span><small> %</small></h3>
            </div>
            <span class="crm-kpi__icon"><i class="ni ni-compass-04" aria-hidden="true"></i></span>
        </div>
        <div class="crm-kpi__sub">
            <span>Del presupuesto acumulado del periodo, esta es la parte que aún falta por vender. El anillo muestra lo ya logrado.</span>
            <div class="crm-ring" data-ring="{{ $logrado }}" style="--pct: 0" role="img" aria-label="{{ sprintf('%.1f', $logrado) }} % logrado, {{ sprintf('%.1f', $porCumplir) }} % por cumplir">
                <span><b data-count="{{ sprintf('%.0f', $logrado) }}">{{ sprintf('%.0f', $logrado) }}</b>%</span>
            </div>
        </div>
        <div class="crm-kpi__foot">
            <span class="crm-chip">Logrado {{ sprintf('%.1f', $logrado) }} %</span>
            <span class="crm-kpi__pct" style="color: var(--crm-ink-3)">{{ sprintf('%.1f', $porCumplir) }} % pendiente</span>
        </div>
    </article>
</div>
