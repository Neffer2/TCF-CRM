@php
    $totalVenta = array_sum($venta);
    $totalPresupuesto = array_sum($presupuesto);
    $mejorMes = null; $mejorPct = 0;
    foreach ($venta as $i => $v) {
        if (!empty($presupuesto[$i]) && $presupuesto[$i] > 0) {
            $pct = $v / $presupuesto[$i] * 100;
            if ($pct > $mejorPct) { $mejorPct = $pct; $mejorMes = $labels[$i]; }
        }
    }
@endphp
<div class="card crm-chart">
    <div class="crm-chart__head">
        <div>
            <p class="crm-kpi__label">Tendencia mensual · {{ $añoDescripcion }}{{ $alcance ? ' · '.$alcance : '' }}</p>
            <h2>Venta facturada frente al presupuesto</h2>
        </div>
        <div class="crm-chart__legend">
            <span><i class="crm-chart__swatch crm-chart__swatch--venta"></i> Venta facturada</span>
            <span><i class="crm-chart__swatch crm-chart__swatch--meta"></i> Presupuesto</span>
            @if ($cierre)<span><i class="crm-chart__swatch crm-chart__swatch--pron"></i> Pronóstico</span>@endif
        </div>
    </div>
    <div class="crm-chart__canvas">
        <canvas id="crmTendencia"
                data-labels='@json($labels)'
                data-venta='@json($venta)'
                data-presupuesto='@json($presupuesto)'
                data-pronostico='@json($pronostico)'
                height="320"></canvas>
    </div>
    <div class="crm-chart__foot">
        <div><span>Facturado en el año</span><b data-count="{{ $totalVenta }}" data-key="t_venta">${{ number_format($totalVenta, 0, '.', ',') }}</b></div>
        <div><span>Presupuesto del año</span><b data-count="{{ $totalPresupuesto }}" data-key="t_presto">${{ number_format($totalPresupuesto, 0, '.', ',') }}</b></div>
        <div><span>Mejor mes</span><b>{{ $mejorMes ? $mejorMes.' · '.sprintf('%.0f', $mejorPct).' %' : '—' }}</b></div>
    </div>
    @if ($cierre)
        @php $tonoCierre = is_null($cierre['pct']) ? 'tone-neutral' : ($cierre['pct'] >= 100 ? 'tone-ok' : ($cierre['pct'] >= 85 ? 'tone-warn' : 'tone-bad')); @endphp
        <div class="crm-cierre {{ $tonoCierre }}">
            <div class="crm-cierre__main">
                <span class="crm-kpi__label">Pronóstico de cierre {{ $añoDescripcion }}</span>
                <b class="crm-cierre__valor"><small>$</small><span data-count="{{ $cierre['total'] }}" data-key="t_cierre">{{ number_format($cierre['total'], 0, '.', ',') }}</span></b>
                <span class="crm-cierre__nota">A este ritmo, con {{ $cierre['mesesReales'] }} {{ $cierre['mesesReales'] == 1 ? 'mes cerrado' : 'meses cerrados' }} y la estacionalidad de {{ $cierre['base'] ?: 'ningún' }} {{ $cierre['base'] == 1 ? 'año anterior' : 'años anteriores' }}.</span>
            </div>
            <div class="crm-cierre__datos">
                <div><span>vs presupuesto anual</span><b>{{ is_null($cierre['pct']) ? '—' : $cierre['pct'].' %' }}</b><small>{{ $cierre['pct'] !== null && $cierre['pct'] < 100 ? 'faltarían $'.number_format($cierre['presupuesto'] - $cierre['total'], 0, '.', ',') : ($cierre['pct'] !== null ? 'se superaría la meta' : 'sin presupuesto') }}</small></div>
                <div><span>vs {{ (int) $añoDescripcion - 1 }}</span><b>{{ is_null($cierre['variacion']) ? '—' : ($cierre['variacion'] >= 0 ? '+' : '').$cierre['variacion'].' %' }}</b><small>{{ is_null($cierre['anterior']) ? 'sin datos del año anterior' : 'cerró en $'.number_format($cierre['anterior'], 0, '.', ',') }}</small></div>
            </div>
            <details class="crm-info crm-info--inline"><summary aria-label="¿Cómo se calcula el pronóstico?">?</summary>
                <div class="crm-info__pop"><b>Cómo se calcula</b><p>Se toma cómo se repartió la venta mes a mes en los años anteriores (participación promedio de cada mes) y se aplica al ritmo real de este año: cierre = venta de los meses cerrados ÷ participación histórica de esos meses; cada mes que falta = cierre × su participación. El mes en curso cuenta como incompleto. Es una proyección estadística, no un compromiso.</p></div>
            </details>
        </div>
    @endif
</div>
