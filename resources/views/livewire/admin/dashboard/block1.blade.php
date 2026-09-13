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
@endphp
<div class="crm-kpis">
    <article class="crm-kpi {{ $tonoMes }}">
        <div class="crm-kpi__head">
            <div>
                <p class="crm-kpi__label">Venta facturada · mes</p>
                <h3 class="crm-kpi__value"><small>$</small>{{ number_format($venta_facturada, 0, '.', ',') }}</h3>
            </div>
            <span class="crm-kpi__icon"><i class="ni ni-money-coins" aria-hidden="true"></i></span>
        </div>
        <div class="crm-kpi__sub">
            <span>Presupuesto mensual</span>
            <b>${{ number_format($presto_mensual, 0, '.', ',') }}</b>
        </div>
        <div class="crm-bar" role="progressbar" aria-valuenow="{{ $pctMes }}" aria-valuemin="0" aria-valuemax="100" aria-label="Cumplimiento de venta mensual">
            <span style="width: {{ $pctMes }}%"></span>
        </div>
        <div class="crm-kpi__foot">
            <span class="crm-chip">{{ $estadoMes }}</span>
            <span class="crm-kpi__pct">{{ sprintf('%.1f', $cumpli_venta_men) }} %</span>
        </div>
    </article>

    <article class="crm-kpi {{ $tonoAcum }}">
        <div class="crm-kpi__head">
            <div>
                <p class="crm-kpi__label">Venta consolidada · acumulado</p>
                <h3 class="crm-kpi__value"><small>$</small>{{ number_format($venta_consolidada, 0, '.', ',') }}</h3>
            </div>
            <span class="crm-kpi__icon"><i class="ni ni-chart-bar-32" aria-hidden="true"></i></span>
        </div>
        <div class="crm-kpi__sub">
            <span>Presupuesto acumulado</span>
            <b>${{ number_format($presto_acumulado, 0, '.', ',') }}</b>
        </div>
        <div class="crm-bar" role="progressbar" aria-valuenow="{{ $pctAcum }}" aria-valuemin="0" aria-valuemax="100" aria-label="Cumplimiento de venta acumulada">
            <span style="width: {{ $pctAcum }}%"></span>
        </div>
        <div class="crm-kpi__foot">
            <span class="crm-chip">{{ $estadoAcum }}</span>
            <span class="crm-kpi__pct">{{ sprintf('%.1f', $cumpli_acum_venta_men) }} %</span>
        </div>
    </article>

    <article class="crm-kpi crm-kpi--wide tone-neutral">
        <div class="crm-kpi__head">
            <div>
                <p class="crm-kpi__label">Presupuesto por cumplir</p>
                <h3 class="crm-kpi__value">{{ sprintf('%.1f', $presto_x_cumplir) }} %</h3>
            </div>
            <span class="crm-kpi__icon"><i class="ni ni-compass-04" aria-hidden="true"></i></span>
        </div>
        <div class="crm-kpi__sub">
            <span>Del presupuesto acumulado del periodo, esta es la parte que aún falta por vender. El anillo muestra lo ya logrado.</span>
            <div class="crm-ring" style="--pct: {{ $logrado }}" role="img" aria-label="{{ sprintf('%.1f', $logrado) }} % logrado, {{ sprintf('%.1f', $porCumplir) }} % por cumplir">
                <span>{{ sprintf('%.0f', $logrado) }}%</span>
            </div>
        </div>
        <div class="crm-kpi__foot">
            <span class="crm-chip">Logrado {{ sprintf('%.1f', $logrado) }} %</span>
            <span class="crm-kpi__pct" style="color: var(--crm-ink-3)">{{ sprintf('%.1f', $porCumplir) }} % pendiente</span>
        </div>
    </article>
</div>
