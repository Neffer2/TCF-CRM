@php
    $filtros = ['año' => $año, 'mes' => $mes, 'comercial' => $comercial];
    $pct = function ($v) { return max(0, min(100, (float) $v)); };
@endphp
<div class="card crm-panel" style="--i:1">
    <div class="card-header">
        <div class="crm-panel__head">
            <div>
                <p class="crm-kpi__label">Ventas por estado</p>
                <h2>Estado de facturación</h2>
            </div>
            <span class="crm-kpi__icon"><i class="ni ni-single-copy-04" aria-hidden="true"></i></span>
        </div>
    </div>
    <div class="card-body">
        <table class="crm-table">
            <thead>
                <tr>
                    <th>Concepto</th>
                    <th>Valor</th>
                    <th>Sumatoria</th>
                </tr>
            </thead>
            <tbody>
                <tr style="--i:0">
                    <td>
                        <a href="{{ route('base-comercial-general', $filtros + ['estado' => 3]) }}" target="_blank" title="Ver el detalle en la base comercial">
                            <span class="crm-dot" style="--crm-dot: var(--crm-warn)"></span> Ejecución por facturar
                        </a>
                    </td>
                    <td>$<span data-count="{{ round($xfacturar) }}" data-key="xfacturar">{{ number_format($xfacturar, 0, '.', ',') }}</span></td>
                    <td><span class="crm-sum">$<span data-count="{{ round($sum_1) }}" data-key="sum1">{{ number_format($sum_1, 0, '.', ',') }}</span></span></td>
                </tr>
                <tr style="--i:1">
                    <td>
                        <a href="{{ route('base-comercial-general', $filtros + ['estado' => 7]) }}" target="_blank" title="Ver el detalle en la base comercial">
                            <span class="crm-dot" style="--crm-dot: var(--crm-accent)"></span> Venta en ejecución
                        </a>
                    </td>
                    <td>$<span data-count="{{ round($ventaejecucion) }}" data-key="ventaejecucion">{{ number_format($ventaejecucion, 0, '.', ',') }}</span></td>
                    <td><span class="crm-sum">$<span data-count="{{ round($sum_2) }}" data-key="sum2">{{ number_format($sum_2, 0, '.', ',') }}</span></span></td>
                </tr>
                <tr style="--i:2">
                    <td>
                        <a href="{{ route('base-comercial-general', $filtros + ['estado' => 6]) }}" target="_blank" title="Ver el detalle en la base comercial">
                            <span class="crm-dot" style="--crm-dot: var(--crm-ok)"></span> Venta
                        </a>
                    </td>
                    <td>$<span data-count="{{ round($venta) }}" data-key="venta">{{ number_format($venta, 0, '.', ',') }}</span></td>
                    <td><span class="crm-sum">$<span data-count="{{ round($sum_3) }}" data-key="sum3">{{ number_format($sum_3, 0, '.', ',') }}</span></span></td>
                </tr>
                <tr class="is-total" style="--i:3">
                    <td>Venta total</td>
                    <td></td>
                    <td><span class="crm-sum">$<span data-count="{{ round($ventatotal) }}" data-key="ventatotal">{{ number_format($ventatotal, 0, '.', ',') }}</span></span></td>
                </tr>
            </tbody>
        </table>

        <div class="crm-mix">
            <h3>Composición del cumplimiento</h3>
            <p>Porcentaje del presupuesto cubierto al sumar, en orden, lo facturado y cada estado de la venta.</p>
            <div class="crm-mix__grid">
                <div class="crm-mix__item" style="--i:0">
                    <div class="k">VF + EXF</div>
                    <div class="v"><span data-count="{{ sprintf('%.1f', $per_1) }}" data-decimals="1" data-key="per1">{{ sprintf('%.1f', $per_1) }}</span><small> %</small></div>
                    <div class="crm-bar"><span data-width="{{ $pct($per_1) }}"></span></div>
                </div>
                <div class="crm-mix__item" style="--i:1">
                    <div class="k">VF + EXF + VE</div>
                    <div class="v"><span data-count="{{ sprintf('%.1f', $per_2) }}" data-decimals="1" data-key="per2">{{ sprintf('%.1f', $per_2) }}</span><small> %</small></div>
                    <div class="crm-bar"><span data-width="{{ $pct($per_2) }}"></span></div>
                </div>
                <div class="crm-mix__item" style="--i:2">
                    <div class="k">VF + EXF + VE + V</div>
                    <div class="v"><span data-count="{{ sprintf('%.1f', $per_3) }}" data-decimals="1" data-key="per3">{{ sprintf('%.1f', $per_3) }}</span><small> %</small></div>
                    <div class="crm-bar"><span data-width="{{ $pct($per_3) }}"></span></div>
                </div>
            </div>
            <div class="crm-legend">
                <span><b>VF</b> venta facturada</span>
                <span><b>EXF</b> ejecución por facturar</span>
                <span><b>VE</b> venta en ejecución</span>
                <span><b>V</b> venta</span>
            </div>
        </div>
    </div>
</div>
