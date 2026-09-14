@php
    use App\Http\Livewire\Admin\Generales\HelisaGeneral as HG;
    $fecha = function ($v) {
        if (!$v) { return null; }
        try { $d = \Carbon\Carbon::parse($v); } catch (\Throwable $e) { return null; }
        return $d->year < 2000 ? null : $d;
    };
    $flecha = function ($col) use ($orden, $dir) { return $orden !== $col ? '' : ($dir === 'asc' ? '↑' : '↓'); };
    $desde = $registros->total() ? $registros->firstItem() : 0;
    $hasta = $registros->total() ? $registros->lastItem() : 0;
@endphp
<div class="crm-base">

    <div class="card mb-4 crm-page-card">
        <div class="crm-page-head">
            <div class="crm-page-head__title">
                <span class="crm-eyebrow">{{ Auth::user()->esLiderComercial() ? 'Líder comercial · Mi equipo' : 'Gerencia · Contabilidad' }}</span>
                <h1>Helisa general</h1>
                <p>Movimientos de facturación importados del sistema contable. La base de factura de cada movimiento es la venta facturada que alimenta los dashboards.</p>
            </div>
            <div class="crm-actions">
                <button type="button" class="crm-btn crm-btn--ghost" wire:click="limpiar" title="Quitar todos los filtros">Limpiar filtros</button>
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
                    <label for="hg_año">Año</label>
                    <select id="hg_año" class="form-control" wire:model="año">
                        <option value="">Todo el histórico</option>
                        @foreach ($años as $a)<option value="{{ $a['id'] }}">{{ $a['description'] }}</option>@endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="hg_mes">Mes</label>
                    <select id="hg_mes" class="form-control" wire:model="mes" {{ empty($meses) ? 'disabled' : '' }}>
                        <option value="">Todo el año</option>
                        @foreach ($meses as $m)<option value="{{ $m['id'] }}">{{ $m['description'] }}</option>@endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="hg_tipo">Documento</label>
                    <select id="hg_tipo" class="form-control {{ $tipo ? 'is-filled' : '' }}" wire:model="tipo">
                        <option value="">Todos los tipos</option>
                        @foreach ($tipos as $t)<option value="{{ $t }}">{{ $t }} · {{ HG::etiquetaTipo($t) }}</option>@endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="hg_cuenta">Cuenta</label>
                    <select id="hg_cuenta" class="form-control {{ $cuenta ? 'is-filled' : '' }}" wire:model="cuenta">
                        <option value="">Todas las cuentas</option>
                        @foreach ($cuentas as $c)<option value="{{ $c['id'] }}">{{ $c['description'] }}</option>@endforeach
                    </select>
                </div>
                <div class="form-group crm-combo">
                    <label for="hg_comercial">Comercial</label>
                    <div class="crm-combo__box">
                        <svg class="crm-combo__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><circle cx="11" cy="11" r="6.5"/><path d="m20 20-4.2-4.2"/></svg>
                        <input id="hg_comercial" type="text" class="form-control" autocomplete="off" placeholder="Todos — escribe o elige" wire:model.debounce.150ms="buscarComercial">
                        @if ($comercial)
                            <button type="button" class="crm-combo__clear" wire:click="limpiarComercial" aria-label="Quitar filtro de comercial">×</button>
                        @else
                            <span class="crm-combo__caret" aria-hidden="true"></span>
                        @endif
                    </div>
                    <ul class="crm-combo__list" role="listbox" aria-label="Comerciales">
                        <li><button type="button" class="crm-combo__item {{ $comercial ? '' : 'is-active' }}" wire:click="elegirComercial(null)" role="option"><span class="crm-combo__avatar">∑</span> Todos los comerciales</button></li>
                        @forelse ($this->comercialesFiltrados as $c)
                            <li><button type="button" class="crm-combo__item {{ $comercial == $c['id'] ? 'is-active' : '' }}" wire:click="elegirComercial({{ $c['id'] }})" role="option"><span class="crm-combo__avatar">{{ mb_strtoupper(mb_substr($c['name'], 0, 1)) }}</span> {{ $c['name'] }}</button></li>
                        @empty
                            <li class="crm-combo__empty">Ningún comercial coincide con "{{ $buscarComercial }}"</li>
                        @endforelse
                    </ul>
                </div>
                <div class="form-group crm-search">
                    <label for="hg_buscar">Buscar</label>
                    <div class="crm-search__box">
                        <svg class="crm-search__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M4 6h16M7 12h10M10 18h4"/></svg>
                        <input id="hg_buscar" type="text" class="form-control" autocomplete="off" placeholder="Centro de costos, tercero, concepto o n.º de documento" wire:model.debounce.300ms="buscar">
                        @if (trim($buscar) !== '')<button type="button" class="crm-combo__clear" wire:click="$set('buscar', '')" aria-label="Borrar búsqueda">×</button>@endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="crm-kpis crm-kpis--mini mb-4">
        <div class="card crm-kpi crm-kpi--mini" style="--i:0">
            <div class="crm-kpi__head"><div>
                <p class="crm-kpi__label">Base facturada {{ $tipo ? '· '.$tipo : '' }} · {{ $alcance }}</p>
                <p class="crm-kpi__value"><small>$</small><span data-count="{{ round($resumen['base']) }}" data-key="hg_base">{{ number_format($resumen['base'], 0, '.', ',') }}</span></p>
            </div></div>
            <span class="crm-kpi__icon"><i class="ni ni-money-coins" aria-hidden="true"></i></span>
        </div>
        <div class="card crm-kpi crm-kpi--mini" style="--i:1">
            <div class="crm-kpi__head"><div>
                <p class="crm-kpi__label">Movimientos</p>
                <p class="crm-kpi__value"><span data-count="{{ $resumen['movimientos'] }}" data-key="hg_mov">{{ number_format($resumen['movimientos'], 0, '.', ',') }}</span></p>
            </div></div>
            <span class="crm-kpi__icon"><i class="ni ni-single-copy-04" aria-hidden="true"></i></span>
        </div>
        <div class="card crm-kpi crm-kpi--mini" style="--i:2">
            <div class="crm-kpi__head"><div>
                <p class="crm-kpi__label">Terceros distintos</p>
                <p class="crm-kpi__value"><span data-count="{{ $resumen['terceros'] }}" data-key="hg_ter">{{ number_format($resumen['terceros'], 0, '.', ',') }}</span></p>
            </div></div>
            <span class="crm-kpi__icon"><i class="ni ni-building" aria-hidden="true"></i></span>
        </div>
        <div class="card crm-kpi crm-kpi--mini" style="--i:3">
            <div class="crm-kpi__head"><div>
                <p class="crm-kpi__label">Comisión {{ $tipo ? '· '.$tipo : '' }}</p>
                <p class="crm-kpi__value"><small>$</small><span data-count="{{ round($resumen['comision']) }}" data-key="hg_com">{{ number_format($resumen['comision'], 0, '.', ',') }}</span></p>
            </div></div>
            <span class="crm-kpi__icon"><i class="ni ni-diamond" aria-hidden="true"></i></span>
        </div>
    </div>

    <div class="card crm-panel crm-panel--tabla" style="--i:1">
        <div class="card-header">
            <div class="crm-panel__head">
                <div>
                    <p class="crm-kpi__label">Por tipo de documento · pulsa una ficha para filtrar</p>
                    <h2>Movimientos de Helisa</h2>
                </div>
                <span class="crm-kpi__icon"><i class="ni ni-archive-2" aria-hidden="true"></i></span>
            </div>
            <div class="crm-estados">
                @foreach ($porTipo as $t => $d)
                    <button type="button" class="crm-estado {{ HG::tonoTipo($t) }} {{ $tipo === $t ? 'is-active' : '' }}" wire:click="filtrarTipo('{{ $t }}')" title="{{ HG::etiquetaTipo($t) }} · ${{ number_format($d['base'], 0, '.', ',') }}">
                        <span class="crm-estado__dot"></span>
                        <span class="crm-estado__name">{{ $t }}</span>
                        <span class="crm-estado__n">{{ number_format($d['n'], 0, '.', ',') }}</span>
                    </button>
                @endforeach
            </div>
        </div>
        <div class="card-body crm-tabla-wrap" wire:loading.class="is-loading">
            <div class="crm-tabla-scroll">
                <table class="crm-table crm-table--data">
                    <thead>
                        <tr>
                            <th><button type="button" class="crm-th" wire:click="ordenar('fecha')">Fecha <i>{{ $flecha('fecha') }}</i></button></th>
                            <th><button type="button" class="crm-th" wire:click="ordenar('tipo_doc')">Documento <i>{{ $flecha('tipo_doc') }}</i></button></th>
                            <th><button type="button" class="crm-th" wire:click="ordenar('nom_tercero')">Tercero <i>{{ $flecha('nom_tercero') }}</i></button></th>
                            <th><button type="button" class="crm-th" wire:click="ordenar('centro')">Centro de costos <i>{{ $flecha('centro') }}</i></button></th>
                            <th class="is-num"><button type="button" class="crm-th" wire:click="ordenar('debito')">Débito <i>{{ $flecha('debito') }}</i></button></th>
                            <th class="is-num"><button type="button" class="crm-th" wire:click="ordenar('credito')">Crédito <i>{{ $flecha('credito') }}</i></button></th>
                            <th class="is-num"><button type="button" class="crm-th" wire:click="ordenar('base_factura')">Base factura <i>{{ $flecha('base_factura') }}</i></button></th>
                            <th class="is-num">Part.</th>
                            <th class="is-num"><button type="button" class="crm-th" wire:click="ordenar('comision')">Comisión <i>{{ $flecha('comision') }}</i></button></th>
                            <th>Comercial</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($registros as $i => $r)
                            @php $f = $fecha($r->fecha); $nombre = optional($r->comercial_user)->name ?: '—'; @endphp
                            <tr style="--i:{{ $i }}">
                                <td class="is-fecha">@if ($f)<b>{{ $f->format('d') }}</b> {{ $f->locale('es')->isoFormat('MMM YYYY') }}@else — @endif</td>
                                <td><span class="crm-chip {{ HG::tonoTipo($r->tipo_doc) }}" title="{{ HG::etiquetaTipo($r->tipo_doc) }}">{{ $r->tipo_doc }}</span> <small class="crm-doc">{{ $r->num_doc }}</small></td>
                                <td class="is-cliente" title="{{ $r->nom_tercero }}"><span class="crm-clamp">{{ $r->nom_tercero }}</span></td>
                                <td class="is-proyecto" title="{{ $r->centro }} · {{ $r->nom_centro_costo }}"><span class="crm-clamp"><b class="crm-cc">{{ trim($r->centro) }}</b> {{ $r->nom_centro_costo }}</span></td>
                                <td class="is-num is-fecha-corta">{{ (float) $r->debito ? number_format($r->debito, 0, '.', ',') : '—' }}</td>
                                <td class="is-num is-fecha-corta">{{ (float) $r->credito ? number_format($r->credito, 0, '.', ',') : '—' }}</td>
                                <td class="is-num is-valor {{ $r->base_factura < 0 ? 'is-neg' : '' }}"><small>$</small>{{ number_format($r->base_factura, 0, '.', ',') }}</td>
                                <td class="is-num is-fecha-corta">{{ $r->porcentaje !== null ? rtrim(rtrim(number_format((float) $r->porcentaje, 1, '.', ''), '0'), '.').' %' : '—' }}</td>
                                <td class="is-num is-fecha-corta">{{ (float) $r->comision ? number_format($r->comision, 0, '.', ',') : '—' }}</td>
                                <td class="is-comercial"><span class="crm-avatar-mini">{{ mb_strtoupper(mb_substr($nombre, 0, 1)) }}</span><span>{{ $nombre }}</span></td>
                            </tr>
                        @empty
                            <tr class="is-empty"><td colspan="10"><div class="crm-empty"><strong>Sin movimientos para este filtro.</strong><span>Prueba con otro año, tipo de documento o texto, o <button type="button" class="crm-link" wire:click="limpiar">quita los filtros</button>.</span></div></td></tr>
                        @endforelse
                    </tbody>
                    @if ($registros->total())
                        <tfoot>
                            <tr class="is-total">
                                <td colspan="6">Base facturada de {{ number_format($registros->total(), 0, '.', ',') }} movimientos{{ $tipo ? ' '.$tipo : '' }}</td>
                                <td class="is-num"><span class="crm-sum"><small>$</small>{{ number_format($baseFiltrada, 0, '.', ',') }}</span></td>
                                <td colspan="3"></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
            <div class="crm-pager">
                <div class="crm-pager__info">
                    Mostrando <b>{{ $desde }}–{{ $hasta }}</b> de <b>{{ number_format($registros->total(), 0, '.', ',') }}</b>
                    <label class="crm-pager__size"><span>por página</span>
                        <select class="form-control" wire:model="porPagina"><option value="25">25</option><option value="50">50</option><option value="100">100</option></select>
                    </label>
                </div>
                <div class="crm-pager__links">{{ $registros->onEachSide(1)->links() }}</div>
            </div>
        </div>
    </div>
</div>
