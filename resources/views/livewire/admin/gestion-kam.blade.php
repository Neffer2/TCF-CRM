@php
    $cop = fn ($v) => '$'.number_format((float) $v, 0, ',', '.');
    $num = fn ($v) => number_format((float) $v, 0, ',', '.');
    $etapas = [1 => 'Prospecto', 2 => 'Oportunidad', 3 => 'Cotización', 4 => 'Propuesta', 7 => 'Presupuesto', 5 => 'Venta', 6 => 'Perdido'];
    $tonoEtapa = [1 => 'tone-muted', 2 => 'tone-neutral', 3 => 'tone-neutral', 4 => 'tone-warn', 7 => 'tone-warn', 5 => 'tone-ok', 6 => 'tone-bad'];
    $maxEtapa = max(1, collect($etapas)->map(fn ($n, $id) => optional($embudo->get($id))->n ?? 0)->max());
    $tonoConv = fn ($c) => $c >= 70 ? 'tone-ok' : ($c >= 40 ? 'tone-warn' : 'tone-bad');
@endphp
<div class="crm-base crm-kam">
    <div class="card mb-4 crm-page-card">
        <div class="crm-page-head">
            <div class="crm-page-head__title">
                <span class="crm-eyebrow">Gerencia · Comercial</span>
                <h1>Gestión comercial por KAM</h1>
                <p>Cómo trabaja cada comercial su embudo: cuántas oportunidades abre, cuántas cierra, cuánto tarda y cuántas deja quietas. Mide la gestión, no solo la venta facturada.</p>
            </div>
        </div>
        <div class="crm-page-filters">
            <div class="crm-filters">
                <div class="form-group">
                    <label for="kam_anio">Año</label>
                    <select id="kam_anio" class="form-control is-filled" wire:model="anio">
                        @foreach ($anios as $a)<option value="{{ $a }}">{{ $a }}</option>@endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="kam_lider">Líder comercial</label>
                    <select id="kam_lider" class="form-control {{ $lider ? 'is-filled' : '' }}" wire:model="lider"
                            @if (Auth::user()->esLiderComercial()) disabled @endif>
                        <option value="">Todos los equipos</option>
                        @foreach ($lideres as $l)<option value="{{ $l['id'] }}">{{ $l['name'] }}</option>@endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="kam_orden">Ordenar por</label>
                    <select id="kam_orden" class="form-control is-filled" wire:model="orden">
                        <option value="efectividad">Efectividad</option>
                        <option value="gestiones">Oportunidades abiertas en el año</option>
                        <option value="valor">Valor ganado</option>
                        <option value="estancadas">Gestiones quietas</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="kam_dias">Se considera quieta a los</label>
                    <select id="kam_dias" class="form-control is-filled" wire:model="diasEstancado">
                        <option value="15">15 días sin mover</option>
                        <option value="30">30 días sin mover</option>
                        <option value="60">60 días sin mover</option>
                        <option value="90">90 días sin mover</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- ============ CIFRAS DEL PERIODO ============ --}}
    <div class="crm-kpis crm-kpis--cinco mb-4">
        <div class="card crm-kpi crm-kpi--mini tone-neutral" style="--i:0">
            <div class="crm-kpi__head"><div>
                <p class="crm-kpi__label">Gestiones del año</p>
                <p class="crm-kpi__value"><span data-count="{{ $tot['gestiones'] }}" data-key="kam_g">{{ $num($tot['gestiones']) }}</span></p>
                <p class="crm-kpi__note">{{ $tot['kams'] }} comerciales con gestión</p>
            </div></div>
            <span class="crm-kpi__icon"><i class="ni ni-briefcase-24" aria-hidden="true"></i></span>
        </div>
        <div class="card crm-kpi crm-kpi--mini {{ $tonoConv($tot['efectividad']) }}" style="--i:1">
            <div class="crm-kpi__head"><div>
                <p class="crm-kpi__label">Efectividad</p>
                <p class="crm-kpi__value"><span data-count="{{ $tot['efectividad'] }}" data-decimals="1" data-key="kam_c">{{ $tot['efectividad'] }}</span><small> %</small></p>
                <p class="crm-kpi__note">{{ $num($tot['ganadas']) }} de {{ $num($tot['gestiones']) }} gestiones llegaron a venta</p>
            </div></div>
            <span class="crm-kpi__icon"><i class="ni ni-trophy" aria-hidden="true"></i></span>
        </div>
        <div class="card crm-kpi crm-kpi--mini tone-neutral" style="--i:2">
            <div class="crm-kpi__head"><div>
                <p class="crm-kpi__label">Valor ganado</p>
                <p class="crm-kpi__value">{{ $cop($tot['valor_ganado']) }}</p>
                <p class="crm-kpi__note">ticket promedio {{ $cop($tot['ticket']) }}</p>
            </div></div>
            <span class="crm-kpi__icon"><i class="ni ni-money-coins" aria-hidden="true"></i></span>
        </div>
        <div class="card crm-kpi crm-kpi--mini tone-warn" style="--i:3">
            <div class="crm-kpi__head"><div>
                <p class="crm-kpi__label">Días en cerrar</p>
                <p class="crm-kpi__value">@if ($tot['dias_cierre'] !== null)<span data-count="{{ $tot['dias_cierre'] }}" data-key="kam_d">{{ $tot['dias_cierre'] }}</span><small> días</small>@else — @endif</p>
                <p class="crm-kpi__note">promedio desde que se abre la gestión</p>
            </div></div>
            <span class="crm-kpi__icon"><i class="ni ni-watch-time" aria-hidden="true"></i></span>
        </div>
        <div class="card crm-kpi crm-kpi--mini {{ $tot['estancadas'] ? 'tone-bad' : 'tone-muted' }}" style="--i:4">
            <div class="crm-kpi__head"><div>
                <p class="crm-kpi__label">Gestiones quietas</p>
                <p class="crm-kpi__value"><span data-count="{{ $tot['estancadas'] }}" data-key="kam_e">{{ $num($tot['estancadas']) }}</span></p>
                <p class="crm-kpi__note">de {{ $num($tot['abiertas']) }} abiertas, sin mover hace {{ $diasEstancado }} días o más</p>
            </div></div>
            <span class="crm-kpi__icon"><i class="ni ni-time-alarm" aria-hidden="true"></i></span>
        </div>
    </div>

    <div class="row">
        {{-- ============ EMBUDO ============ --}}
        <div class="col-xl-4 mb-4">
            <div class="card crm-panel h-100" style="--i:1">
                <div class="card-header">
                    <div class="crm-panel__head">
                        <div>
                            <p class="crm-kpi__label">Dónde está la gestión</p>
                            <h2>Embudo del año</h2>
                        </div>
                        <span class="crm-kpi__icon"><i class="ni ni-chart-bar-32" aria-hidden="true"></i></span>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="crm-embudo">
                        @foreach ($etapas as $id => $nombre)
                            @php $e = $embudo->get($id); $n = $e->n ?? 0; @endphp
                            <div class="crm-embudo__fila {{ $tonoEtapa[$id] }}" style="--i:{{ $loop->index }}">
                                <div class="crm-embudo__cab">
                                    <span class="crm-embudo__etapa">{{ $nombre }}</span>
                                    <b class="crm-embudo__n">{{ $num($n) }}</b>
                                </div>
                                <div class="crm-bar"><span data-width="{{ round($n / $maxEtapa * 100) }}" style="width: {{ round($n / $maxEtapa * 100) }}%"></span></div>
                                <span class="crm-embudo__valor">{{ $cop($e->valor ?? 0) }}</span>
                            </div>
                        @endforeach
                    </div>
                    <p class="crm-kpi__note" style="margin-top:14px">Venta y Presupuesto cuentan como gestión ganada. Perdido es la única pérdida declarada; las demás siguen abiertas.</p>
                </div>
            </div>
        </div>

        {{-- ============ TABLA POR KAM ============ --}}
        <div class="col-xl-8 mb-4">
            <div class="card crm-panel crm-panel--tabla h-100" style="--i:2">
                <div class="card-header">
                    <div class="crm-panel__head">
                        <div>
                            <p class="crm-kpi__label">{{ $lider ? 'Equipo filtrado' : 'Todos los equipos' }} · año {{ $anio }}</p>
                            <h2>Desempeño por comercial</h2>
                        </div>
                        <span class="crm-kpi__icon"><i class="ni ni-single-02" aria-hidden="true"></i></span>
                    </div>
                </div>
                <div class="card-body crm-tabla-wrap" wire:loading.class="is-loading">
                    <div class="crm-tabla-scroll">
                        <table class="crm-table crm-table--data">
                            <thead><tr>
                                <th>Comercial</th>
                                <th class="is-num" title="Gestiones abiertas en el año">Gestiones</th>
                                <th class="is-num" title="En Venta o Presupuesto">Ganadas</th>
                                <th class="is-num">Perdidas</th>
                                <th class="is-num" title="Todavía en el embudo">Abiertas</th>
                                <th title="Ganadas sobre todas las que abrió en el año">Efectividad</th>
                                <th class="is-num">Valor ganado</th>
                                <th class="is-num" title="Valor ganado entre gestiones ganadas">Ticket</th>
                                <th class="is-num" title="Días promedio entre abrir y ganar">Días</th>
                                <th class="is-num" title="Abiertas sin movimiento">Quietas</th>
                            </tr></thead>
                            <tbody>
                                @forelse ($filas as $i => $f)
                                    <tr style="--i:{{ min($i, 30) }}" class="is-clicable"
                                        wire:click="$emit('abrirKam', {{ $f->id }}, {{ $anio }}, {{ $diasEstancado }})"
                                        title="Ver todos los proyectos de {{ $f->name }}">
                                        <td class="is-comercial">
                                            <span class="crm-avatar-mini">{{ mb_strtoupper(mb_substr($f->name, 0, 1)) }}</span>
                                            <span class="crm-clamp">{{ $f->name }}</span>
                                            <span class="crm-ver" aria-hidden="true">ver →</span>
                                        </td>
                                        <td class="is-num">
                                            {{ $num($f->gestiones) }}
                                            <span class="crm-mini-bar"><i style="width: {{ round($f->gestiones / $maxGestiones * 100) }}%"></i></span>
                                        </td>
                                        <td class="is-num">{{ $num($f->ganadas) }}</td>
                                        <td class="is-num">{{ $num($f->perdidas) }}</td>
                                        <td class="is-num">{{ $num($f->abiertas) }}</td>
                                        <td><span class="crm-chip {{ $f->poco_volumen ? 'tone-muted' : $tonoConv($f->efectividad) }}"
                                                  @if ($f->poco_volumen) title="Menos de {{ $minimo }} gestiones en el año: el porcentaje no es comparable" @endif>{{ number_format($f->efectividad, 1, ',', '.') }} %@if ($f->poco_volumen) <i>·  poco volumen</i>@endif</span></td>
                                        <td class="is-num">{{ $cop($f->valor_ganado) }}</td>
                                        <td class="is-num">{{ $cop($f->ticket) }}</td>
                                        <td class="is-num">{{ $f->dias_cierre !== null ? $f->dias_cierre : '—' }}</td>
                                        <td class="is-num">
                                            @if ($f->estancadas)
                                                <span class="crm-chip {{ $f->pct_estancadas >= 50 ? 'tone-bad' : 'tone-warn' }}">{{ $num($f->estancadas) }} · {{ $f->pct_estancadas }} %</span>
                                            @else
                                                <span class="crm-chip tone-muted">0</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="is-empty"><td colspan="10"><div class="crm-empty"><strong>Sin gestiones en este periodo.</strong><span>Cambia el año o quita el filtro de líder comercial.</span></div></td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <p class="crm-kpi__note" style="margin-top:12px">
                        <b>Efectividad</b>: de todas las gestiones que el comercial abrió en el año, cuántas llegaron a Venta o Presupuesto.
                        Una gestión queda <b>quieta</b> cuando lleva {{ $diasEstancado }} días o más sin ningún movimiento.
                        Quien abrió menos de {{ $minimo }} gestiones en el año aparece al final: su porcentaje no es comparable.
                        <b>Pulsa cualquier fila</b> para ver, uno por uno, todos los proyectos de ese comercial con lo cotizado y lo realmente facturado.
                        @if ($tot['perdidas'] < $tot['gestiones'] * 0.05)
                            <br><b>Ojo con el dato:</b> en este periodo solo {{ $num($tot['perdidas']) }} de {{ $num($tot['gestiones']) }} gestiones están marcadas como <i>Perdido</i>.
                            Mientras el equipo no cierre las que no prosperan, lo que no se ganó queda contado como <i>abierto</i>, no como perdido.
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
