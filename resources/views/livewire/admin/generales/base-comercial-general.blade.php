@php
    use App\Http\Livewire\Admin\Generales\BaseComercialGeneral as BC;
    $fecha = function ($v) {
        if (!$v) { return null; }
        try { $d = \Carbon\Carbon::parse($v); } catch (\Throwable $e) { return null; }
        return $d->year < 2000 ? null : $d;
    };
    $flecha = function ($col) use ($orden, $dir) {
        if ($orden !== $col) { return ''; }
        return $dir === 'asc' ? '↑' : '↓';
    };
    $desde = $registros->total() ? $registros->firstItem() : 0;
    $hasta = $registros->total() ? $registros->lastItem() : 0;
@endphp
<div class="crm-base">

    {{-- Encabezado + filtros --}}
    <div class="card mb-4 crm-page-card">
        <div class="crm-page-head">
            <div class="crm-page-head__title">
                <span class="crm-eyebrow">Gerencia · Base comercial</span>
                <h1>Base comercial general</h1>
                <p>Todos los proyectos registrados por el equipo comercial, con su valor y estado. Filtra, ordena y exporta.</p>
            </div>
            <div class="crm-actions">
                <button type="button" class="crm-btn crm-btn--ghost" wire:click="limpiar" title="Quitar todos los filtros">
                    Limpiar filtros
                </button>
                <button type="button" class="crm-btn" wire:click="exportar" wire:loading.attr="disabled" wire:target="exportar">
                    <span wire:loading.remove wire:target="exportar">Exportar Excel</span>
                    <span wire:loading wire:target="exportar">Generando…</span>
                    <span class="crm-btn__icon" aria-hidden="true">↓</span>
                </button>
            </div>
        </div>
        <div class="crm-page-filters">
            <div class="crm-filters">
                <div class="form-group">
                    <label for="bc_año">Año</label>
                    <select id="bc_año" class="form-control" wire:model="año">
                        <option value="">Todo el histórico</option>
                        @foreach ($años as $a)
                            <option value="{{ $a['id'] }}">{{ $a['description'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="bc_mes">Mes</label>
                    <select id="bc_mes" class="form-control" wire:model="mes" {{ empty($meses) ? 'disabled' : '' }}>
                        <option value="">Todo el año</option>
                        @foreach ($meses as $m)
                            <option value="{{ $m['id'] }}">{{ $m['description'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="bc_estado">Estado</label>
                    <select id="bc_estado" class="form-control {{ $estado ? 'is-filled' : '' }}" wire:model="estado">
                        <option value="">Todos los estados</option>
                        @foreach ($estados as $e)
                            <option value="{{ $e['id'] }}">{{ BC::etiquetaEstado($e['description']) }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Comercial: lista desplegable en la que se puede escribir --}}
                <div class="form-group crm-combo">
                    <label for="bc_comercial">Comercial</label>
                    <div class="crm-combo__box">
                        <svg class="crm-combo__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><circle cx="11" cy="11" r="6.5"/><path d="m20 20-4.2-4.2"/></svg>
                        <input id="bc_comercial" type="text" class="form-control" autocomplete="off"
                               placeholder="Todos — escribe o elige" wire:model.debounce.150ms="buscarComercial">
                        @if ($comercial)
                            <button type="button" class="crm-combo__clear" wire:click="limpiarComercial" title="Quitar filtro de comercial" aria-label="Quitar filtro de comercial">×</button>
                        @else
                            <span class="crm-combo__caret" aria-hidden="true"></span>
                        @endif
                    </div>
                    <ul class="crm-combo__list" role="listbox" aria-label="Comerciales">
                        <li>
                            <button type="button" class="crm-combo__item {{ $comercial ? '' : 'is-active' }}" wire:click="elegirComercial(null)" role="option">
                                <span class="crm-combo__avatar">∑</span> Todos los comerciales
                            </button>
                        </li>
                        @forelse ($this->comercialesFiltrados as $c)
                            <li>
                                <button type="button" class="crm-combo__item {{ $comercial == $c['id'] ? 'is-active' : '' }}" wire:click="elegirComercial({{ $c['id'] }})" role="option">
                                    <span class="crm-combo__avatar">{{ mb_strtoupper(mb_substr($c['name'], 0, 1)) }}</span> {{ $c['name'] }}
                                </button>
                            </li>
                        @empty
                            <li class="crm-combo__empty">Ningún comercial coincide con "{{ $buscarComercial }}"</li>
                        @endforelse
                    </ul>
                </div>

                <div class="form-group crm-search">
                    <label for="bc_buscar">Buscar</label>
                    <div class="crm-search__box">
                        <svg class="crm-search__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M4 6h16M7 12h10M10 18h4"/></svg>
                        <input id="bc_buscar" type="text" class="form-control" autocomplete="off"
                               placeholder="Centro de costos, cliente o proyecto" wire:model.debounce.300ms="centro">
                        @if (trim($centro) !== '')
                            <button type="button" class="crm-combo__clear" wire:click="$set('centro', '')" title="Borrar búsqueda" aria-label="Borrar búsqueda">×</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Resumen del filtro --}}
    <div class="crm-kpis crm-kpis--mini mb-4">
        <div class="card crm-kpi crm-kpi--mini" style="--i:0">
            <div class="crm-kpi__head"><div>
                <p class="crm-kpi__label">Valor {{ $estado ? 'del estado' : 'total' }} · {{ $alcance }}</p>
                <p class="crm-kpi__value"><small>$</small><span data-count="{{ round($resumen['valor']) }}" data-key="bc_valor">{{ number_format($resumen['valor'], 0, '.', ',') }}</span></p>
            </div></div>
            <span class="crm-kpi__icon"><i class="ni ni-money-coins" aria-hidden="true"></i></span>
        </div>
        <div class="card crm-kpi crm-kpi--mini" style="--i:1">
            <div class="crm-kpi__head"><div>
                <p class="crm-kpi__label">Registros</p>
                <p class="crm-kpi__value"><span data-count="{{ $resumen['registros'] }}" data-key="bc_reg">{{ number_format($resumen['registros'], 0, '.', ',') }}</span></p>
            </div></div>
            <span class="crm-kpi__icon"><i class="ni ni-bullet-list-67" aria-hidden="true"></i></span>
        </div>
        <div class="card crm-kpi crm-kpi--mini" style="--i:2">
            <div class="crm-kpi__head"><div>
                <p class="crm-kpi__label">Clientes distintos</p>
                <p class="crm-kpi__value"><span data-count="{{ $resumen['clientes'] }}" data-key="bc_cli">{{ number_format($resumen['clientes'], 0, '.', ',') }}</span></p>
            </div></div>
            <span class="crm-kpi__icon"><i class="ni ni-building" aria-hidden="true"></i></span>
        </div>
        <div class="card crm-kpi crm-kpi--mini" style="--i:3">
            <div class="crm-kpi__head"><div>
                <p class="crm-kpi__label">Comerciales con registros</p>
                <p class="crm-kpi__value"><span data-count="{{ $resumen['comerciales'] }}" data-key="bc_com">{{ $resumen['comerciales'] }}</span></p>
            </div></div>
            <span class="crm-kpi__icon"><i class="ni ni-single-02" aria-hidden="true"></i></span>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="card crm-panel crm-panel--tabla" style="--i:1">
        <div class="card-header">
            <div class="crm-panel__head">
                <div>
                    <p class="crm-kpi__label">Distribución por estado · pulsa una ficha para filtrar</p>
                    <h2>Proyectos de la base comercial</h2>
                </div>
                <span class="crm-kpi__icon"><i class="ni ni-archive-2" aria-hidden="true"></i></span>
            </div>
            <div class="crm-estados">
                @foreach ($estados as $e)
                    @php $d = $porEstado[$e['id']] ?? null; @endphp
                    @if ($d)
                        <button type="button" class="crm-estado {{ BC::tonoEstado($e['id']) }} {{ $estado == $e['id'] ? 'is-active' : '' }}"
                                wire:click="filtrarEstado({{ $e['id'] }})" title="${{ number_format($d['valor'], 0, '.', ',') }}">
                            <span class="crm-estado__dot"></span>
                            <span class="crm-estado__name">{{ BC::etiquetaEstado($e['description']) }}</span>
                            <span class="crm-estado__n">{{ number_format($d['n'], 0, '.', ',') }}</span>
                        </button>
                    @endif
                @endforeach
            </div>
        </div>

        <div class="card-body crm-tabla-wrap" wire:loading.class="is-loading">
            <div class="crm-tabla-scroll">
                <table class="crm-table crm-table--data">
                    <thead>
                        <tr>
                            <th><button type="button" class="crm-th" wire:click="ordenar('fecha')">Fecha <i>{{ $flecha('fecha') }}</i></button></th>
                            <th><button type="button" class="crm-th" wire:click="ordenar('nom_cliente')">Cliente <i>{{ $flecha('nom_cliente') }}</i></button></th>
                            <th><button type="button" class="crm-th" wire:click="ordenar('nom_proyecto')">Proyecto <i>{{ $flecha('nom_proyecto') }}</i></button></th>
                            <th><button type="button" class="crm-th" wire:click="ordenar('cod_cc')">Centro de costos <i>{{ $flecha('cod_cc') }}</i></button></th>
                            <th class="is-num"><button type="button" class="crm-th" wire:click="ordenar('valor_proyecto')">Valor <i>{{ $flecha('valor_proyecto') }}</i></button></th>
                            <th><button type="button" class="crm-th" wire:click="ordenar('id_estado')">Estado <i>{{ $flecha('id_estado') }}</i></button></th>
                            <th><button type="button" class="crm-th" wire:click="ordenar('fecha_inicio')">Inicio <i>{{ $flecha('fecha_inicio') }}</i></button></th>
                            <th><button type="button" class="crm-th" wire:click="ordenar('dura_mes')">Fin <i>{{ $flecha('dura_mes') }}</i></button></th>
                            <th>Comercial</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($registros as $i => $r)
                            @php
                                $f = $fecha($r->fecha); $fi = $fecha($r->fecha_inicio); $ff = $fecha($r->dura_mes);
                                $nombre = optional($r->comercial)->name ?: '—';
                                $descEstado = optional($r->estado_cuenta)->description ?: '';
                            @endphp
                            <tr style="--i:{{ $i }}">
                                <td class="is-fecha">
                                    @if ($f)<b>{{ $f->format('d') }}</b> {{ $f->locale('es')->isoFormat('MMM YYYY') }}@else — @endif
                                </td>
                                <td class="is-cliente" title="{{ $r->nom_cliente }}"><span class="crm-clamp">{{ $r->nom_cliente }}</span></td>
                                <td class="is-proyecto" title="{{ $r->nom_proyecto }}"><span class="crm-clamp">{{ $r->nom_proyecto }}</span></td>
                                {{-- El centro de costos suele traer "código - nombre del proyecto": se muestra el código y el resto en el tooltip --}}
                                <td class="is-cc" title="{{ $r->cod_cc }}">{{ $r->cod_cc ? trim(explode(' - ', $r->cod_cc, 2)[0]) : '—' }}</td>
                                <td class="is-num is-valor"><small>$</small>{{ number_format($r->valor_proyecto, 0, '.', ',') }}</td>
                                <td><span class="crm-chip {{ BC::tonoEstado($r->id_estado) }}">{{ BC::etiquetaEstado($descEstado) }}</span></td>
                                <td class="is-fecha-corta">{{ $fi ? $fi->format('d/m/Y') : '—' }}</td>
                                <td class="is-fecha-corta">{{ $ff ? $ff->format('d/m/Y') : '—' }}</td>
                                <td class="is-comercial">
                                    <span class="crm-avatar-mini">{{ mb_strtoupper(mb_substr($nombre, 0, 1)) }}</span>
                                    <span>{{ $nombre }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr class="is-empty">
                                <td colspan="9">
                                    <div class="crm-empty">
                                        <strong>Sin resultados para este filtro.</strong>
                                        <span>Prueba con otro año, estado o texto de búsqueda, o <button type="button" class="crm-link" wire:click="limpiar">quita los filtros</button>.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if ($registros->total())
                        <tfoot>
                            <tr class="is-total">
                                <td colspan="4">Total de {{ number_format($registros->total(), 0, '.', ',') }} proyectos{{ $estado ? ' en este estado' : '' }}</td>
                                <td class="is-num"><span class="crm-sum"><small>$</small>{{ number_format($valorFiltrado, 0, '.', ',') }}</span></td>
                                <td colspan="4"></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>

            <div class="crm-pager">
                <div class="crm-pager__info">
                    Mostrando <b>{{ $desde }}–{{ $hasta }}</b> de <b>{{ number_format($registros->total(), 0, '.', ',') }}</b>
                    <label class="crm-pager__size">
                        <span>por página</span>
                        <select class="form-control" wire:model="porPagina">
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </label>
                </div>
                <div class="crm-pager__links">
                    {{ $registros->onEachSide(1)->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
