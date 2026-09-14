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
            <p class="crm-kpi__label">Tendencia mensual · {{ $añoDescripcion }}</p>
            <h2>Venta facturada frente al presupuesto</h2>
        </div>
        <div class="crm-chart__legend">
            <span><i class="crm-chart__swatch crm-chart__swatch--venta"></i> Venta facturada</span>
            <span><i class="crm-chart__swatch crm-chart__swatch--meta"></i> Presupuesto</span>
        </div>
    </div>
    <div class="crm-chart__canvas">
        <canvas id="crmTendencia"
                data-labels='@json($labels)'
                data-venta='@json($venta)'
                data-presupuesto='@json($presupuesto)'
                height="320"></canvas>
    </div>
    <div class="crm-chart__foot">
        <div><span>Facturado en el año</span><b data-count="{{ $totalVenta }}" data-key="t_venta">${{ number_format($totalVenta, 0, '.', ',') }}</b></div>
        <div><span>Presupuesto del año</span><b data-count="{{ $totalPresupuesto }}" data-key="t_presto">${{ number_format($totalPresupuesto, 0, '.', ',') }}</b></div>
        <div><span>Mejor mes</span><b>{{ $mejorMes ? $mejorMes.' · '.sprintf('%.0f', $mejorPct).' %' : '—' }}</b></div>
    </div>
</div>
