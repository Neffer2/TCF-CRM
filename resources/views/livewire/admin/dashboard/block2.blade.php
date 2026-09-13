@php
    $filtros = ['año' => $año, 'mes' => $mes, 'comercial' => $comercial];
    $pct = function ($v) { return max(0, min(100, (float) $v)); };
@endphp
<div class="card crm-panel h-100">
    <div class="card-header">
        <h2>Estado de facturación</h2>
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
                <tr>
                    <td>
                        <a href="{{ route('base-comercial-general', $filtros + ['estado' => 3]) }}" target="_blank" title="Ver el detalle en la base comercial">
                            <span class="crm-dot" style="--crm-dot: var(--crm-warn)"></span> Ejecución por facturar
                        </a>
                    </td>
                    <td>${{ number_format($xfacturar, 0, '.', ',') }}</td>
                    <td><span class="crm-sum">${{ number_format($sum_1, 0, '.', ',') }}</span></td>
                </tr>
                <tr>
                    <td>
                        <a href="{{ route('base-comercial-general', $filtros + ['estado' => 7]) }}" target="_blank" title="Ver el detalle en la base comercial">
                            <span class="crm-dot" style="--crm-dot: var(--crm-accent)"></span> Venta en ejecución
                        </a>
                    </td>
                    <td>${{ number_format($ventaejecucion, 0, '.', ',') }}</td>
                    <td><span class="crm-sum">${{ number_format($sum_2, 0, '.', ',') }}</span></td>
                </tr>
                <tr>
                    <td>
                        <a href="{{ route('base-comercial-general', $filtros + ['estado' => 6]) }}" target="_blank" title="Ver el detalle en la base comercial">
                            <span class="crm-dot" style="--crm-dot: var(--crm-ok)"></span> Venta
                        </a>
                    </td>
                    <td>${{ number_format($venta, 0, '.', ',') }}</td>
                    <td><span class="crm-sum">${{ number_format($sum_3, 0, '.', ',') }}</span></td>
                </tr>
                <tr class="is-total">
                    <td>Venta total</td>
                    <td></td>
                    <td><span class="crm-sum">${{ number_format($ventatotal, 0, '.', ',') }}</span></td>
                </tr>
            </tbody>
        </table>

        <div class="crm-mix">
            <h3>Composición del cumplimiento</h3>
            <p>Porcentaje del presupuesto cubierto al sumar, en orden, lo facturado y cada estado de la venta.</p>
            <div class="crm-mix__grid">
                <div class="crm-mix__item">
                    <div class="k">VF + EXF</div>
                    <div class="v"><span countto="{{ $per_1 }}">{{ sprintf('%.1f', $per_1) }}</span><small> %</small></div>
                    <div class="crm-bar"><span style="width: {{ $pct($per_1) }}%"></span></div>
                </div>
                <div class="crm-mix__item">
                    <div class="k">VF + EXF + VE</div>
                    <div class="v"><span countto="{{ $per_2 }}">{{ sprintf('%.1f', $per_2) }}</span><small> %</small></div>
                    <div class="crm-bar"><span style="width: {{ $pct($per_2) }}%"></span></div>
                </div>
                <div class="crm-mix__item">
                    <div class="k">VF + EXF + VE + V</div>
                    <div class="v"><span countto="{{ $per_3 }}">{{ sprintf('%.1f', $per_3) }}</span><small> %</small></div>
                    <div class="crm-bar"><span style="width: {{ $pct($per_3) }}%"></span></div>
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
