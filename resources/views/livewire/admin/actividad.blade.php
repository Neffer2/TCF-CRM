@php
    $tono = ['sesion' => 'tone-neutral', 'pagina' => 'tone-muted', 'accion' => 'tone-warn', 'datos' => 'tone-ok'];
    $nombreTipo = ['sesion' => 'Sesión', 'pagina' => 'Página', 'accion' => 'Acción', 'datos' => 'Datos'];
    $desdeN = $registros->total() ? $registros->firstItem() : 0; $hastaN = $registros->total() ? $registros->lastItem() : 0;
    $fmt = function ($v) { return is_array($v) ? json_encode($v, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : (is_null($v) ? '—' : (string) $v); };
@endphp
<div class="crm-base">
    <div class="card mb-4 crm-page-card">
        <div class="crm-page-head">
            <div class="crm-page-head__title">
                <span class="crm-eyebrow">Desarrollo · Auditoría</span>
                <h1>Registro de actividad</h1>
                <p>Todo lo que pasa en el CRM: quién entra y cuándo, qué páginas ve, qué acciones ejecuta (con los datos que envía) y qué registros cambia, con el valor anterior y el nuevo. Las claves nunca se guardan.</p>
            </div>
        </div>
        <div class="crm-page-filters">
            <div class="crm-filters">
                <div class="form-group">
                    <label for="ac_usuario">Usuario</label>
                    <select id="ac_usuario" class="form-control {{ $usuario ? 'is-filled' : '' }}" wire:model="usuario"><option value="">Todos</option>@foreach ($usuarios as $u)<option value="{{ $u['id'] }}">{{ $u['name'] }}</option>@endforeach</select>
                </div>
                <div class="form-group">
                    <label for="ac_tipo">Tipo</label>
                    <select id="ac_tipo" class="form-control {{ $tipo ? 'is-filled' : '' }}" wire:model="tipo"><option value="">Todo</option><option value="sesion">Sesiones</option><option value="pagina">Páginas vistas</option><option value="accion">Acciones</option><option value="datos">Cambios de datos</option></select>
                </div>
                <div class="form-group"><label for="ac_desde">Desde</label><input id="ac_desde" type="date" class="form-control" wire:model="desde"></div>
                <div class="form-group"><label for="ac_hasta">Hasta</label><input id="ac_hasta" type="date" class="form-control" wire:model="hasta"></div>
                <div class="form-group crm-search">
                    <label for="ac_buscar">Buscar</label>
                    <div class="crm-search__box">
                        <svg class="crm-search__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M4 6h16M7 12h10M10 18h4"/></svg>
                        <input id="ac_buscar" type="text" class="form-control" placeholder="Acción, ruta, registro (ordenes_compra:123), dato o IP" wire:model.debounce.300ms="buscar">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="crm-kpis crm-kpis--cinco mb-4">
        <div class="card crm-kpi crm-kpi--mini tone-neutral" style="--i:0"><div class="crm-kpi__head"><div><p class="crm-kpi__label">Usuarios activos hoy</p><p class="crm-kpi__value"><span data-count="{{ $resumen['usuarios_hoy'] }}" data-key="ac_u">{{ $resumen['usuarios_hoy'] }}</span></p></div></div><span class="crm-kpi__icon"><i class="ni ni-single-02" aria-hidden="true"></i></span></div>
        <div class="card crm-kpi crm-kpi--mini tone-neutral" style="--i:1"><div class="crm-kpi__head"><div><p class="crm-kpi__label">Inicios de sesión hoy</p><p class="crm-kpi__value"><span data-count="{{ $resumen['sesiones_hoy'] }}" data-key="ac_s">{{ $resumen['sesiones_hoy'] }}</span></p></div></div><span class="crm-kpi__icon"><i class="ni ni-key-25" aria-hidden="true"></i></span></div>
        <div class="card crm-kpi crm-kpi--mini tone-warn" style="--i:2"><div class="crm-kpi__head"><div><p class="crm-kpi__label">Acciones hoy</p><p class="crm-kpi__value"><span data-count="{{ $resumen['acciones_hoy'] }}" data-key="ac_a">{{ $resumen['acciones_hoy'] }}</span></p></div></div><span class="crm-kpi__icon"><i class="ni ni-bold" aria-hidden="true"></i></span></div>
        <div class="card crm-kpi crm-kpi--mini tone-ok" style="--i:3"><div class="crm-kpi__head"><div><p class="crm-kpi__label">Cambios de datos hoy</p><p class="crm-kpi__value"><span data-count="{{ $resumen['cambios_hoy'] }}" data-key="ac_c">{{ $resumen['cambios_hoy'] }}</span></p></div></div><span class="crm-kpi__icon"><i class="ni ni-ruler-pencil" aria-hidden="true"></i></span></div>
        <div class="card crm-kpi crm-kpi--mini {{ $resumen['fallidos_hoy'] ? 'tone-bad' : 'tone-muted' }}" style="--i:4"><div class="crm-kpi__head"><div><p class="crm-kpi__label">Intentos de acceso fallidos hoy</p><p class="crm-kpi__value"><span data-count="{{ $resumen['fallidos_hoy'] }}" data-key="ac_f">{{ $resumen['fallidos_hoy'] }}</span></p></div></div><span class="crm-kpi__icon"><i class="ni ni-lock-circle-open" aria-hidden="true"></i></span></div>
    </div>

    <div class="card crm-panel crm-panel--tabla" style="--i:1">
        <div class="card-header">
            <div class="crm-panel__head">
                <div>
                    <p class="crm-kpi__label">Más activos en el periodo: @foreach ($porUsuario as $pu){{ $pu->usuario }} ({{ $pu->n }})@if(!$loop->last) · @endif @endforeach</p>
                    <h2>Trazabilidad</h2>
                </div>
                <span class="crm-kpi__icon"><i class="ni ni-time-alarm" aria-hidden="true"></i></span>
            </div>
        </div>
        <div class="card-body crm-tabla-wrap" wire:loading.class="is-loading">
            <div class="crm-tabla-scroll">
                <table class="crm-table crm-table--data">
                    <thead><tr><th>Cuándo</th><th>Usuario</th><th>Tipo</th><th>Qué hizo</th><th>Registro</th><th>Ruta</th><th class="is-num">ms</th><th>IP</th><th></th></tr></thead>
                    <tbody>
                        @forelse ($registros as $i => $r)
                            <tr style="--i:{{ min($i, 30) }}">
                                <td class="is-fecha-corta" style="white-space: nowrap">{{ $r->created_at->format('d/m/Y H:i:s') }}</td>
                                <td class="is-comercial"><span class="crm-avatar-mini">{{ mb_strtoupper(mb_substr($r->usuario ?: '?', 0, 1)) }}</span><span>{{ $r->usuario ?: 'Anónimo' }}</span></td>
                                <td><span class="crm-chip {{ $tono[$r->tipo] ?? 'tone-muted' }}">{{ $nombreTipo[$r->tipo] ?? $r->tipo }}</span></td>
                                <td class="is-cliente" title="{{ $r->accion }}"><span class="crm-clamp">{{ $r->accion }}</span></td>
                                <td class="is-cc">{{ $r->objeto ?: '—' }}</td>
                                <td class="is-proyecto"><span class="crm-clamp">{{ $r->metodo ? $r->metodo.' ' : '' }}{{ $r->ruta ?: '—' }}{{ $r->estado_http ? ' · '.$r->estado_http : '' }}</span></td>
                                <td class="is-num is-fecha-corta">{{ $r->duracion_ms ?? '—' }}</td>
                                <td class="is-fecha-corta">{{ $r->ip }}</td>
                                <td class="is-num">@if ($r->detalle)<button type="button" class="crm-link" wire:click="ver({{ $r->id }})">{{ $abierto == $r->id ? 'Ocultar' : 'Datos' }}</button>@endif</td>
                            </tr>
                            @if ($abierto == $r->id && $r->detalle)
                                <tr class="crm-nt-detalle"><td colspan="9">
                                    @if ($r->tipo === 'datos')
                                        <table class="crm-cambios">
                                            <thead><tr><th>Campo</th><th>Antes</th><th>Después</th></tr></thead>
                                            <tbody>
                                            @foreach ($r->detalle as $campo => $v)
                                                <tr><td><b>{{ $campo }}</b></td>
                                                    @if (is_array($v) && array_key_exists('antes', $v))<td>{{ $fmt($v['antes']) }}</td><td>{{ $fmt($v['despues']) }}</td>@else<td colspan="2">{{ $fmt($v) }}</td>@endif
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <div class="crm-nt-cuerpo"><pre class="crm-json">{{ json_encode($r->detalle, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre></div>
                                    @endif
                                    <p class="crm-kpi__note" style="margin-top:6px">Navegador: {{ $r->navegador }}</p>
                                </td></tr>
                            @endif
                        @empty
                            <tr class="is-empty"><td colspan="9"><div class="crm-empty"><strong>Sin actividad en este filtro.</strong><span>Amplía las fechas o quita el filtro de usuario.</span></div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="crm-pager">
                <div class="crm-pager__info">Mostrando <b>{{ $desdeN }}–{{ $hastaN }}</b> de <b>{{ number_format($registros->total(), 0, '.', ',') }}</b></div>
                <div class="crm-pager__links">{{ $registros->onEachSide(1)->links() }}</div>
            </div>
        </div>
    </div>
</div>
