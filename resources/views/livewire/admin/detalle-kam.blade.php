@php
    use App\Services\GestionKamDatos as D;
    use App\Http\Livewire\Admin\DetalleKam as K;
    $M = fn ($v) => D::millones($v);
    $n = fn ($v) => number_format((float) $v, 0, ',', '.');
    $fecha = function ($v) { try { return \Carbon\Carbon::parse($v)->format('d/m/Y'); } catch (\Throwable $e) { return '—'; } };
    $hace = fn ($d) => $d <= 0 ? 'hoy' : ($d === 1 ? 'hace 1 día' : 'hace '.$d.' días');
    $primer = $kam ? explode(' ', trim($kam))[0] : '';
    $r = $resumen;
@endphp
<div class="crm-modal {{ $abierto ? 'is-open' : '' }}" x-data
     @keydown.escape.window="if ($el.classList.contains('is-open')) $wire.cerrar()"
     role="dialog" aria-modal="true" aria-label="Proyectos de {{ $kam }}">
    @if ($abierto && $r)
        <div class="crm-modal__fondo" wire:click="cerrar"></div>
        <div class="crm-modal__panel kam-panel">

            {{-- ============ QUIÉN Y SU AÑO EN UNA FRASE ============ --}}
            <header class="kam-panel__head">
                <div class="kam-panel__quien">
                    <span class="crm-eyebrow">Gestión comercial · {{ $anio }}</span>
                    <h2>{{ $kam }}</h2>
                    <span class="kam-panel__equipo">{{ $equipo ? 'Equipo de '.$equipo : 'Sin líder asignado' }}</span>
                </div>
                <button type="button" class="crm-modal__cerrar" wire:click="cerrar" aria-label="Cerrar">×</button>
                @php
                    // La frase se arma en PHP: las directivas de Blade pegadas a una palabra no se compilan.
                    $frase = e($primer).' abrió <b>'.$n($r['gestiones']).' gestiones</b> en '.$anio.'. '
                        .'<b class="kam-c-venta">'.$n($r['ventas']).'</b> terminaron en venta ('.number_format($r['conversion'], 0).' %), '
                        .($r['perdidas'] == 0 ? 'ninguna se perdió' : '<b class="kam-c-perdida">'.$n($r['perdidas']).'</b> '.($r['perdidas'] == 1 ? 'se perdió' : 'se perdieron'))
                        .' y <b>'.$n($r['en_curso']).'</b> siguen en curso'
                        .($r['quietas'] ? ', <b class="kam-c-quieta">'.$n($r['quietas']).'</b> de ellas quietas' : '').'.';
                    if ($r['vendido'] > 0) {
                        $frase .= ' Vendió <b>'.e($M($r['vendido'])).'</b>: <b class="kam-c-venta">'.e($M($r['facturado'])).'</b> ya facturado o cerrado ('.$r['pct_facturado'].' %)'
                            .($r['por_facturar'] > 0 ? ', <b class="kam-c-quieta">'.e($M($r['por_facturar'])).'</b> ejecutado y sin facturar' : '')
                            .($r['ejecucion'] > 0 ? ' y <b>'.e($M($r['ejecucion'])).'</b> todavía en ejecución' : '').'.';
                    }
                @endphp
                <p class="kam-panel__frase">{!! $frase !!}</p>
                <div class="kam-panel__cifras">
                    <div><span>Se volvió venta</span><b>{{ number_format($r['conversion'], 0) }} %</b><i>{{ $n($r['ventas']) }} de {{ $n($r['gestiones']) }}</i></div>
                    <div><span>Vendido</span><b title="{{ '$'.number_format($r['vendido'], 0, ',', '.') }}">{{ $M($r['vendido']) }}</b><i>{{ $M($r['en_juego']) }} más en curso</i></div>
                    <div class="kam-panel__cifra-fact">
                        <span>Ya facturado o cerrado</span><b>{{ $r['pct_facturado'] }} %</b>
                        <div class="kam-mezcla">
                            @foreach (D::reparto($r) as $clase => $pct)@if ($pct > 0)<i class="{{ $clase }}" style="flex: {{ $pct }}"></i>@endif @endforeach
                        </div>
                        <i>{{ $r['por_facturar'] > 0 ? $M($r['por_facturar']).' esperan factura' : 'nada pendiente de facturar' }}</i>
                    </div>
                    <div class="{{ $r['quietas'] ? 'is-alerta' : '' }}"><span>Quietas</span><b>{{ $n($r['quietas']) }}</b><i>sin moverse en {{ $diasQuieta }} días</i></div>
                </div>
            </header>

            {{-- ============ PREGUNTAS (PESTAÑAS) Y BÚSQUEDA ============ --}}
            <nav class="kam-vistas" aria-label="Qué quieres ver">
                <div class="kam-vistas__tabs" role="tablist">
                    @foreach (K::VISTAS as $clave => [$titulo, $explica])
                        <button type="button" role="tab" wire:key="tab-{{ $clave }}" aria-selected="{{ $vista === $clave ? 'true' : 'false' }}"
                                class="kam-tab {{ $vista === $clave ? 'is-activa' : '' }} {{ in_array($clave, ['por_facturar', 'quietas']) && ($conteos[$clave] ?? 0) ? 'is-atencion' : '' }}"
                                wire:click="verVista('{{ $clave }}')">
                            {{ $titulo }} <b>{{ $n($conteos[$clave] ?? 0) }}</b>
                        </button>
                    @endforeach
                </div>
                <div class="crm-search__box kam-vistas__buscar">
                    <svg class="crm-search__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg>
                    <input type="text" class="form-control" placeholder="Buscar proyecto, cliente o centro de costos" wire:model.debounce.400ms="buscar" aria-label="Buscar">
                </div>
            </nav>
            <p class="kam-vistas__explica">{{ K::VISTAS[$vista][1] }}</p>

            {{-- ============ PROYECTOS ============ --}}
            <div class="crm-modal__cuerpo kam-panel__cuerpo" wire:loading.class="is-loading">
                @if ($proyectos->isEmpty())
                    <div class="crm-empty" style="padding: 48px 20px">
                        <strong>{{ trim($buscar) !== '' ? 'Nada coincide con "'.$buscar.'".' : 'No hay proyectos en "'.K::VISTAS[$vista][0].'".' }}</strong>
                        <span>{{ trim($buscar) !== '' ? 'Prueba con otro texto o mira la pestaña Todas.' : 'Elige otra pestaña para verlos.' }}</span>
                    </div>
                @else
                    <table class="kam-p">
                        <thead>
                            <tr>
                                <th>Proyecto y cliente</th>
                                <th>Etapa</th>
                                <th class="is-num">Valor</th>
                                <th>Facturación</th>
                                <th>Último movimiento</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($proyectos as $p)
                                @php
                                    $etapa = D::ETAPAS[$p->id_estado] ?? ['nombre' => 'Sin etapa', 'explica' => ''];
                                    $quieta = $p->grupo === 'curso' && $p->dias_quieta > $diasQuieta;
                                    $ev = $p->estado_base ? (D::ESTADO_VENTA[$p->estado_base] ?? [ucfirst(mb_strtolower($p->estado_base)), 'is-curso']) : null;
                                @endphp
                                <tr class="{{ $quieta ? 'is-quieta' : '' }} {{ $p->revisar ? 'is-revisar' : '' }}">
                                    <td class="kam-p__proyecto">
                                        <b>{{ $p->proyecto ?: 'Proyecto sin nombre' }}</b>
                                        <span>{{ $p->cliente ?: 'Cliente sin registrar' }}{{ $p->contacto ? ' · '.$p->contacto : '' }}</span>
                                        @if ($p->centro)
                                            <a class="kam-cc" href="{{ route('presupuesto', ['gestion' => $p->id]) }}" target="_blank" rel="noopener" title="Abrir el presupuesto en otra pestaña">{{ $p->centro }} ↗</a>
                                        @elseif ($p->presupuesto_id)
                                            <a class="kam-cc is-sin" href="{{ route('presupuesto', ['gestion' => $p->id]) }}" target="_blank" rel="noopener">presupuesto sin centro de costos ↗</a>
                                        @endif
                                    </td>
                                    <td class="kam-p__etapa">
                                        <span class="kam-etapa is-{{ $p->grupo }} {{ $quieta ? 'is-quieta' : '' }}">{{ $etapa['nombre'] }}</span>
                                        @if ($p->grupo === 'perdida')
                                            <span class="kam-p__sub">{{ $p->causa ? 'Causa: '.$p->causa : 'Sin causa registrada' }}</span>
                                        @else
                                            <span class="kam-p__sub">{{ $quieta ? 'Quieta: nadie la mueve hace '.$p->dias_quieta.' días' : $etapa['explica'] }}</span>
                                        @endif
                                        @if ($p->revisar)<span class="kam-p__revisar">{{ $p->motivo_revisar }}</span>@endif
                                    </td>
                                    <td class="is-num">
                                        <b title="{{ '$'.number_format($p->valor, 0, ',', '.') }}">{{ $M($p->valor) }}</b>
                                        <span class="kam-p__sub">{{ $p->grupo === 'venta' ? 'vendido' : (in_array($p->id_estado, [1, 2]) ? 'estimado' : 'cotizado') }}</span>
                                    </td>
                                    <td class="kam-p__fact">
                                        @if ($p->grupo !== 'venta')
                                            <span class="kam-p__sub">no aplica: todavía no es venta</span>
                                        @else
                                            @php [$cobroTxt, $cobroCls] = D::COBRO[$p->cobro]; @endphp
                                            <span class="kam-cobro {{ $cobroCls }}">{{ $cobroTxt }}</span>
                                            @if ($p->por_facturar > 0)
                                                <span class="kam-p__sub"><b>{{ $M($p->por_facturar) }}</b> por facturar</span>
                                            @endif
                                            <span class="kam-p__sub" title="Lo que registra la tabla de facturación de Helisa en el centro de costos">
                                                @if (!$p->centro) sin centro de costos: no se puede cruzar con Helisa
                                                @elseif ($p->helisa > 0) Helisa registra {{ $M($p->helisa) }} ({{ $p->valor > 0 ? round($p->helisa / $p->valor * 100) : 0 }} %)
                                                @else Helisa no registra nada todavía
                                                @endif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="kam-p__mov">
                                        <b class="{{ $quieta ? 'kam-c-quieta' : '' }}">{{ $hace($p->dias_quieta) }}</b>
                                        <span class="kam-p__sub">abierta el {{ $fecha($p->abierta) }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

                <details class="kam-datos">
                    <summary>Sobre estos datos</summary>
                    <ul>
                        <li><b>Valor</b> es lo que el comercial cotizó en la gestión. En las ventas coincide con el presupuesto aprobado y con la base comercial.</li>
                        <li><b>Facturación</b> es el estado de la venta en la base comercial (facturada, ejecutada por facturar, en ejecución). Debajo se muestra lo que registra Helisa en el centro de costos, como verificación.</li>
                        <li>La tabla de Helisa del CRM no es la contabilidad completa. Si una venta dice "facturada" y Helisa no registra nada, aparece en <b>Revisar datos</b>.</li>
                        <li><b>Último movimiento</b> es la última vez que alguien cambió la gestión. En curso y sin moverse más de {{ $diasQuieta }} días cuenta como <b>quieta</b>.</li>
                    </ul>
                </details>
            </div>
        </div>
    @endif
</div>
