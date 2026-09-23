@php
    $cop = fn ($v) => '$'.number_format((float) $v, 0, ',', '.');
    $num = fn ($v) => number_format((float) $v, 0, ',', '.');
    $fecha = function ($v) { try { return \Carbon\Carbon::parse($v)->format('d/m/Y'); } catch (\Throwable $e) { return '—'; } };
    $r = $resumen;
@endphp
<div class="crm-modal {{ $abierto ? 'is-open' : '' }}" x-data
     @keydown.escape.window="if ($el.classList.contains('is-open')) $wire.cerrar()"
     role="dialog" aria-modal="true" aria-label="Proyectos de {{ $kam }}">
    @if ($abierto)
        <div class="crm-modal__fondo" wire:click="cerrar"></div>
        <div class="crm-modal__panel">
            <div class="crm-modal__head">
                <div>
                    <span class="crm-eyebrow">Gestión comercial · año {{ $anio }}</span>
                    <h2><span class="crm-avatar-mini" style="vertical-align: middle; margin-right: 8px">{{ mb_strtoupper(mb_substr($kam, 0, 1)) }}</span> {{ $kam }}</h2>
                    <p>Todos sus proyectos del año, agrupados por etapa. <b>Cotizado</b> es el valor de la gestión; <b>facturado</b> es lo que realmente entró por ese centro de costos según Helisa.</p>
                </div>
                <div class="crm-modal__resumen">
                    <div><span>Proyectos</span><b>{{ $num($r['n']) }}</b></div>
                    <div><span>Ganados</span><b>{{ $num($r['ganados']) }}</b></div>
                    <div><span>Cotizado ganado</span><b>{{ $cop($r['valor_ganado']) }}</b></div>
                    <div><span>Facturado real</span><b class="is-accent">{{ $cop($r['facturado']) }}</b></div>
                    @if ($r['quietas'])<div><span>Quietas</span><b class="is-alerta">{{ $num($r['quietas']) }}</b></div>@endif
                </div>
                <button type="button" class="crm-modal__cerrar" wire:click="cerrar" aria-label="Cerrar">×</button>
            </div>

            <div class="crm-modal__barra">
                <div class="crm-search__box">
                    <svg class="crm-search__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg>
                    <input type="text" class="form-control" placeholder="Buscar proyecto, cliente, contacto o centro de costos"
                           wire:model.debounce.400ms="buscar">
                </div>
                <div class="crm-modal__fichas">
                    @foreach ($grupos as $g)
                        <button type="button" class="crm-ficha {{ $g['tono'] }} {{ $etapaAbierta === $g['id'] ? 'is-activa' : '' }}" wire:click="verEtapa({{ $g['id'] }})">
                            <span>{{ $g['etapa'] }}</span>
                            <b>{{ $num($g['n']) }}</b>
                            <i>{{ $cop($g['valor']) }}</i>
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="crm-modal__cuerpo is-in" wire:loading.class="is-loading">
                @if ($r['sin_cc'])
                    <p class="crm-aviso-linea">{{ $num($r['sin_cc']) }} {{ $r['sin_cc'] == 1 ? 'proyecto ganado no tiene' : 'proyectos ganados no tienen' }} centro de costos asignado, así que no se les puede cruzar la facturación real.</p>
                @endif

                @forelse ($grupos as $i => $g)
                    <details class="crm-lider" style="--i:{{ $i }}" {{ $i < 2 || $etapaAbierta ? 'open' : '' }}>
                        <summary>
                            <span class="crm-lider__avatar"><span class="crm-punto {{ $g['tono'] }}"></span></span>
                            <span class="crm-lider__info">
                                <b>{{ $g['etapa'] }}</b>
                                <span>{{ $num($g['n']) }} {{ $g['n'] == 1 ? 'proyecto' : 'proyectos' }}@if ($g['quietas']) · {{ $num($g['quietas']) }} sin mover hace más de {{ $diasEstancado }} días @endif</span>
                            </span>
                            <span class="crm-lider__total">
                                <b>{{ $cop($g['valor']) }}</b>
                                <span>@if ($g['facturado'] > 0) facturado {{ $cop($g['facturado']) }} @else cotizado @endif</span>
                            </span>
                            <span class="crm-lider__chev" aria-hidden="true"></span>
                        </summary>
                        <div class="crm-lider__cuerpo">
                            <div class="crm-tabla-scroll">
                                <table class="crm-table crm-table--data">
                                    <thead><tr>
                                        <th>Proyecto</th>
                                        <th>Cliente</th>
                                        <th>Centro de costos</th>
                                        <th class="is-num">Cotizado</th>
                                        <th class="is-num">Facturado</th>
                                        <th class="is-num" title="Facturado sobre cotizado">Cumple</th>
                                        <th>Abierta</th>
                                        <th class="is-num" title="Días desde el último movimiento">Sin mover</th>
                                    </tr></thead>
                                    <tbody>
                                        @foreach ($g['proyectos'] as $j => $p)
                                            <tr style="--i:{{ min($j, 25) }}" class="{{ $p->quieta ? 'is-quieta' : '' }}">
                                                <td class="is-proyecto">
                                                    <span class="crm-clamp">{{ $p->proyecto ?: 'Sin nombre' }}</span>
                                                    @if ($p->causa)<span class="crm-sub">{{ $p->causa }}</span>@endif
                                                </td>
                                                <td class="is-cliente">
                                                    <span class="crm-clamp">{{ $p->cliente ?: '—' }}</span>
                                                    @if ($p->contacto)<span class="crm-sub">{{ $p->contacto }}</span>@endif
                                                </td>
                                                <td class="is-cc">
                                                    @if ($p->presupuesto_id)
                                                        <a class="crm-link" href="{{ route('presupuesto', ['gestion' => $p->id]) }}" target="_blank" rel="noopener">{{ $p->centro ?: 'Ver presupuesto' }}</a>
                                                    @else
                                                        <span class="crm-sub">sin centro</span>
                                                    @endif
                                                </td>
                                                <td class="is-num">{{ $cop($p->valor) }}</td>
                                                <td class="is-num">{{ $p->facturado > 0 ? $cop($p->facturado) : '—' }}</td>
                                                <td class="is-num">
                                                    @if ($p->facturado > 0 && $p->cumplimiento !== null)
                                                        <span class="crm-chip {{ $p->cumplimiento >= 95 ? 'tone-ok' : ($p->cumplimiento >= 60 ? 'tone-warn' : 'tone-bad') }}">{{ $p->cumplimiento }} %</span>
                                                    @else
                                                        —
                                                    @endif
                                                </td>
                                                <td class="is-fecha-corta">{{ $fecha($p->abierta) }}</td>
                                                <td class="is-num">
                                                    @if ($p->quieta)
                                                        <span class="crm-chip tone-bad">{{ $num($p->dias_quieta) }} días</span>
                                                    @else
                                                        {{ $num($p->dias_quieta) }}
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </details>
                @empty
                    <div class="crm-empty" style="padding: 48px 20px">
                        <strong>Sin proyectos con este filtro.</strong>
                        <span>{{ trim($buscar) !== '' ? 'Prueba con otro texto de búsqueda.' : 'Este comercial no registra gestiones en '.$anio.'.' }}</span>
                    </div>
                @endforelse
            </div>
        </div>
    @endif
</div>
