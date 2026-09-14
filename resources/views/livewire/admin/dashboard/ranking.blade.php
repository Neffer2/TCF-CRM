<div class="card crm-panel crm-ranking" style="--i:2">
    <div class="card-header">
        <div class="crm-panel__head">
            <div>
                <p class="crm-kpi__label">Ranking del periodo · {{ $periodo }}</p>
                <h2>Comerciales frente a su presupuesto</h2>
            </div>
            <span class="crm-kpi__icon"><i class="ni ni-trophy" aria-hidden="true"></i></span>
        </div>
        <div class="crm-seg" role="tablist" aria-label="Ordenar ranking">
            <button type="button" class="crm-seg__btn {{ $orden === 'cumplimiento' ? 'is-active' : '' }}" wire:click="ordenar('cumplimiento')" role="tab">Por cumplimiento</button>
            <button type="button" class="crm-seg__btn {{ $orden === 'venta' ? 'is-active' : '' }}" wire:click="ordenar('venta')" role="tab">Por venta</button>
        </div>
    </div>
    <div class="card-body">
        @forelse ($filas as $i => $f)
            @php
                $pct = $f['cumplimiento'];
                $tone = is_null($pct) ? 'tone-neutral' : ($pct >= 100 ? 'tone-ok' : ($pct >= 80 ? 'tone-warn' : 'tone-bad'));
            @endphp
            <div class="crm-rank {{ $tone }} {{ $comercial == $f['id'] ? 'is-selected' : '' }}" style="--i:{{ $i }}">
                <span class="crm-rank__pos">{{ $i + 1 }}</span>
                <span class="crm-rank__avatar">
                    @if ($f['avatar'])
                        <img src="{{ asset(str_replace('public/', 'storage/', $f['avatar'])) }}" alt="" onerror="this.remove()">
                    @endif
                    <b>{{ mb_strtoupper(mb_substr($f['nombre'], 0, 1)) }}</b>
                </span>
                <div class="crm-rank__body">
                    <div class="crm-rank__top">
                        <span class="crm-rank__name">{{ $f['nombre'] }}</span>
                        @if ($orden === 'cumplimiento' && !is_null($pct))
                            <span class="crm-rank__venta crm-rank__pct"><span data-count="{{ $pct }}" data-decimals="1" data-key="rank_pct_{{ $f['id'] }}">{{ sprintf('%.1f', $pct) }}</span><small> %</small></span>
                        @else
                            <span class="crm-rank__venta"><small>$</small><span data-count="{{ round($f['venta']) }}" data-key="rank_venta_{{ $f['id'] }}">{{ number_format($f['venta'], 0, '.', ',') }}</span></span>
                        @endif
                    </div>
                    <div class="crm-bar"><span data-width="{{ $f['peso'] }}"></span></div>
                    <div class="crm-rank__foot">
                        <span>Vendió ${{ number_format($f['venta'], 0, '.', ',') }} · meta ${{ number_format($f['presupuesto'], 0, '.', ',') }}</span>
                        @if (!is_null($pct))
                            <span class="crm-chip">{{ $pct >= 100 ? 'En meta' : ($pct >= 80 ? 'Cerca' : 'Lejos') }}</span>
                        @else
                            <span class="crm-chip" style="--crm-tone: var(--crm-ink-3); --crm-tone-soft: var(--crm-paper)">Sin meta</span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <p class="crm-kpi__note">No hay facturación registrada en este periodo.</p>
        @endforelse
    </div>
</div>
