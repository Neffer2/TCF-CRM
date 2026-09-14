@php
    $pesos = function ($v) { return '$'.number_format((float) $v, 0, '.', ','); };
    $fecha = function ($v) { try { $d = \Carbon\Carbon::parse($v); return $d->year < 2000 ? '—' : $d->format('d/m/Y'); } catch (\Throwable $e) { return '—'; } };
    $tono = \App\Http\Livewire\Admin\Dashboard\DetalleEstado::TONOS[$estadoId] ?? 'tone-neutral';
@endphp
<div class="crm-modal {{ $abierto ? 'is-open' : '' }}" x-data @keydown.escape.window="if ($el.classList.contains('is-open')) $wire.cerrar()" role="dialog" aria-modal="true" aria-label="Detalle de {{ $titulo }}">
    @if ($abierto)
        <div class="crm-modal__fondo" wire:click="cerrar"></div>
        <div class="crm-modal__panel">
            <div class="crm-modal__head">
                <div>
                    <span class="crm-eyebrow">Ventas por estado · {{ $periodo }}</span>
                    <h2><span class="crm-chip {{ $tono }}" style="vertical-align: middle; margin-right: 8px">{{ $titulo }}</span> Proyectos por líder y comercial</h2>
                    <p>Cada líder comercial agrupa a sus comerciales; abre un comercial para ver sus proyectos en este estado. Los totales son el valor de los proyectos.</p>
                </div>
                <div class="crm-modal__resumen">
                    <div><span>Proyectos</span><b>{{ number_format($nProyectos, 0, '.', ',') }}</b></div>
                    <div><span>Comerciales</span><b>{{ $nComerciales }}</b></div>
                    <div><span>Valor total</span><b class="is-accent">{{ $pesos($total) }}</b></div>
                </div>
                <button type="button" class="crm-modal__cerrar" wire:click="cerrar" aria-label="Cerrar">×</button>
            </div>
            <div class="crm-modal__cuerpo" wire:loading.class="is-loading">
                @forelse ($lideres as $i => $l)
                    <details class="crm-lider" style="--i:{{ $i }}" {{ $i < 2 ? 'open' : '' }}>
                        <summary>
                            <span class="crm-lider__avatar">{{ $l['id'] ? mb_strtoupper(mb_substr($l['nombre'], 0, 1)) : '?' }}</span>
                            <span class="crm-lider__info">
                                <b>{{ $l['nombre'] }}</b>
                                <span>{{ count($l['comerciales']) }} {{ count($l['comerciales']) == 1 ? 'comercial' : 'comerciales' }} · {{ $l['n'] }} {{ $l['n'] == 1 ? 'proyecto' : 'proyectos' }} · {{ $l['peso'] }} % del total</span>
                            </span>
                            <span class="crm-lider__total"><b>{{ $pesos($l['total']) }}</b><span>valor en este estado</span></span>
                            <span class="crm-lider__chev" aria-hidden="true"></span>
                        </summary>
                        <div class="crm-lider__barra"><span style="width: {{ $l['peso'] }}%"></span></div>
                        <div class="crm-lider__cuerpo">
                            @foreach ($l['comerciales'] as $j => $c)
                                <details class="crm-com" style="--i:{{ $j }}" {{ count($l['comerciales']) == 1 ? 'open' : '' }}>
                                    <summary>
                                        <span class="crm-avatar-mini">{{ mb_strtoupper(mb_substr($c['nombre'], 0, 1)) }}</span>
                                        <span class="crm-com__nombre">
                                            <b>{{ $c['nombre'] }}</b>
                                            <span>{{ $c['n'] }} {{ $c['n'] == 1 ? 'proyecto' : 'proyectos' }}</span>
                                            <div class="crm-bar"><span style="width: {{ $c['peso'] }}%"></span></div>
                                        </span>
                                        <span class="crm-com__total">{{ $pesos($c['total']) }}</span>
                                        <span class="crm-com__chev" aria-hidden="true"></span>
                                    </summary>
                                    <div class="crm-com__proyectos">
                                        @foreach ($c['proyectos'] as $k => $p)
                                            <div class="crm-proy" style="--i:{{ $k }}">
                                                <span class="crm-proy__fecha">{{ $fecha($p['fecha']) }}</span>
                                                <span class="crm-proy__nombre"><b>{{ $p['proyecto'] }}</b><span>{{ $p['cliente'] }}</span>@if ($p['cc'])<small>{{ $p['cc'] }}</small>@endif</span>
                                                <span class="crm-proy__valor">{{ $pesos($p['valor']) }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </details>
                            @endforeach
                        </div>
                    </details>
                @empty
                    <div class="crm-modal__vacio"><strong>No hay proyectos en "{{ $titulo }}" para {{ $periodo }}.</strong>Prueba con otro periodo o quita el filtro de comercial.</div>
                @endforelse
            </div>
        </div>
    @endif
</div>
