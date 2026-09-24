@php
    use App\Services\GestionKamDatos as D;
    $M = fn ($v) => D::millones($v);
    $n = fn ($v) => number_format((float) $v, 0, ',', '.');
    $t = $total;
    $flecha = fn ($c) => $orden === $c ? ($dir === 'desc' ? '↓' : '↑') : '';
    $tonoConv = fn ($c) => $c >= 70 ? 'is-venta' : ($c >= 40 ? 'is-quieta' : 'is-perdida');
@endphp
<div class="crm-base kam">
    {{-- ================= CABECERA Y FILTROS ================= --}}
    <div class="card mb-4 crm-page-card">
        <div class="crm-page-head">
            <div class="crm-page-head__title">
                <span class="crm-eyebrow">Gerencia · Comercial</span>
                <h1>Gestión comercial por KAM</h1>
                <p>Qué abrió cada comercial en el año, cuánto convirtió en venta, cuánto de esa venta ya se facturó y qué tiene quieto. Pulsa un comercial para ver sus proyectos uno por uno.</p>
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
                    <label for="kam_lider">Equipo</label>
                    <select id="kam_lider" class="form-control {{ $lider ? 'is-filled' : '' }}" wire:model="lider" @if (Auth::user()->esLiderComercial()) disabled @endif>
                        <option value="">Todos los equipos</option>
                        @foreach ($lideres as $l)<option value="{{ $l['id'] }}">Equipo de {{ $l['name'] }}</option>@endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="kam_dias">Una gestión está quieta si no se mueve en</label>
                    <select id="kam_dias" class="form-control is-filled" wire:model="diasQuieta">
                        @foreach ([15, 30, 60, 90] as $d)<option value="{{ $d }}">{{ $d }} días</option>@endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= EL AÑO EN UNA FRASE ================= --}}
    <section class="kam-frase" aria-label="Resumen del año">
        <p>
            En <b>{{ $anio }}</b> {{ $lider ? 'el equipo' : 'los comerciales' }} abrieron <b>{{ $n($t['gestiones']) }} gestiones</b>.
            <b class="kam-c-venta">{{ $n($t['ventas']) }}</b> terminaron en venta ({{ number_format($t['conversion'], 0) }} %),
            @if ($t['perdidas'] == 0) ninguna se perdió @else <b class="kam-c-perdida">{{ $n($t['perdidas']) }}</b> {{ $t['perdidas'] == 1 ? 'se perdió' : 'se perdieron' }} @endif
            y <b>{{ $n($t['en_curso']) }}</b> siguen en curso,
            de las cuales <b class="kam-c-quieta">{{ $n($t['quietas']) }}</b> llevan más de {{ $diasQuieta }} días sin moverse.
        </p>
        <p>
            Lo vendido suma <b>{{ $M($t['vendido']) }}</b>: <b class="kam-c-venta">{{ $M($t['facturado']) }}</b> ya está facturado o cerrado ({{ $t['pct_facturado'] }} %),
            <b class="kam-c-quieta">{{ $M($t['por_facturar']) }}</b> ya se ejecutó y falta facturarlo,
            y <b>{{ $M($t['ejecucion']) }}</b> todavía está en ejecución.
            @if ($t['revisar_valor'] > 0)
                Otros <b class="kam-c-perdida">{{ $M($t['revisar_valor']) }}</b> tienen datos que no cuadran entre pantallas.
            @endif
        </p>
        <span class="kam-nota">M = millones de pesos</span>
    </section>

    {{-- ================= CUATRO PREGUNTAS ================= --}}
    <div class="kam-preguntas">
        <div class="kam-pregunta">
            <span class="kam-pregunta__q">¿Cuánto abrieron?</span>
            <b class="kam-pregunta__v">{{ $n($t['gestiones']) }}</b>
            <span class="kam-pregunta__d">gestiones creadas en {{ $anio }}</span>
        </div>
        <div class="kam-pregunta">
            <span class="kam-pregunta__q">¿Cuánto se volvió venta?</span>
            <b class="kam-pregunta__v">{{ number_format($t['conversion'], 1, ',', '.') }} <small>%</small></b>
            <span class="kam-pregunta__d">{{ $n($t['ventas']) }} de {{ $n($t['gestiones']) }} gestiones</span>
        </div>
        <div class="kam-pregunta">
            <span class="kam-pregunta__q">¿Cuánto de lo vendido ya se facturó?</span>
            <b class="kam-pregunta__v">{{ $t['pct_facturado'] }} <small>%</small></b>
            @php $rep = \App\Services\GestionKamDatos::reparto($t); @endphp
            <div class="kam-mezcla" role="img" aria-label="Reparto de lo vendido por estado de facturación">
                @foreach ($rep as $clase => $pct)@if ($pct > 0)<i class="{{ $clase }}" style="flex: {{ $pct }}"></i>@endif @endforeach
            </div>
            <span class="kam-pregunta__d">{{ $M($t['por_facturar']) }} ejecutados esperan factura</span>
        </div>
        <div class="kam-pregunta {{ $t['quietas'] ? 'is-alerta' : '' }}">
            <span class="kam-pregunta__q">¿Qué se está quedando quieto?</span>
            <b class="kam-pregunta__v">{{ $n($t['quietas']) }}</b>
            <span class="kam-pregunta__d">de {{ $n($t['en_curso']) }} en curso, sin moverse en {{ $diasQuieta }} días</span>
        </div>
    </div>

    {{-- ================= EMBUDO ================= --}}
    <section class="card kam-embudo">
        <div class="kam-sec-head">
            <h2>Dónde está cada gestión</h2>
            <p>Una gestión avanza de izquierda a derecha. El número es cuántas hay en esa etapa; el valor es lo que se cotizó o estimó.</p>
        </div>
        @php
            $pasos = collect($embudo)->where('grupo', '!=', 'perdida');
            $perdida = collect($embudo)->firstWhere('grupo', 'perdida');
        @endphp
        <div class="kam-flujo">
            <ol class="kam-pasos">
                @foreach ($pasos as $e)
                    <li class="kam-paso is-{{ $e['grupo'] }}">
                        <span class="kam-paso__nombre">{{ $e['nombre'] }}</span>
                        <span class="kam-paso__explica">{{ $e['explica'] }}</span>
                        <b class="kam-paso__n">{{ $n($e['n']) }}</b>
                        <div class="kam-barra"><i class="is-{{ $e['grupo'] }}" style="width: {{ round($e['n'] / $maxEmbudo * 100) }}%"></i></div>
                        <span class="kam-paso__valor" title="{{ '$'.number_format($e['valor'], 0, ',', '.') }}">{{ $M($e['valor']) }}{{ in_array($e['id'], [1, 2]) ? ' estimado' : '' }}</span>
                    </li>
                @endforeach
            </ol>
            @if ($perdida)
                <div class="kam-paso is-perdida kam-paso--aparte">
                    <span class="kam-paso__nombre">{{ $perdida['nombre'] }}</span>
                    <span class="kam-paso__explica">{{ $perdida['explica'] }}</span>
                    <b class="kam-paso__n">{{ $n($perdida['n']) }}</b>
                    <div class="kam-barra"><i class="is-perdida" style="width: {{ round($perdida['n'] / $maxEmbudo * 100) }}%"></i></div>
                    <span class="kam-paso__valor">{{ $M($perdida['valor']) }}</span>
                </div>
            @endif
        </div>
    </section>

    {{-- ================= TABLA POR COMERCIAL ================= --}}
    <section class="card kam-tabla">
        <div class="kam-sec-head kam-sec-head--fila">
            <div>
                <h2>Cada comercial</h2>
                <p>Pulsa un título para ordenar. Pulsa una fila para ver sus proyectos.</p>
            </div>
            <div class="kam-leyenda" aria-label="Qué significa cada color en las barras">
                <span><i class="is-venta"></i> Listo: venta · facturado</span>
                <span><i class="is-curso"></i> En proceso: en curso · en ejecución</span>
                <span><i class="is-quieta"></i> Pide atención: quieta · por facturar</span>
                <span><i class="is-perdida"></i> Problema: perdida · datos que no cuadran</span>
            </div>
        </div>
        <div class="crm-tabla-scroll" wire:loading.class="is-loading">
            <table class="kam-t">
                <thead>
                    <tr>
                        <th><button type="button" wire:click="ordenarPor('nombre')">Comercial {{ $flecha('nombre') }}</button></th>
                        <th><button type="button" wire:click="ordenarPor('gestiones')">Gestiones y su estado {{ $flecha('gestiones') }}</button></th>
                        <th class="is-num"><button type="button" wire:click="ordenarPor('conversion')">Se volvió venta {{ $flecha('conversion') }}</button></th>
                        <th class="is-num"><button type="button" wire:click="ordenarPor('vendido')">Vendido {{ $flecha('vendido') }}</button></th>
                        <th><button type="button" wire:click="ordenarPor('pct_facturado')">Cómo va la facturación {{ $flecha('pct_facturado') }}</button></th>
                        <th class="is-num"><button type="button" wire:click="ordenarPor('quietas')">Quietas {{ $flecha('quietas') }}</button></th>
                        <th aria-label="Ver proyectos"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($filas as $f)
                        @php
                            $q = $f->quietas; $c = $f->en_curso - $q;
                            $den = max(1, $f->gestiones);
                        @endphp
                        <tr wire:click="$emit('abrirKam', {{ $f->id }}, {{ $anio }}, {{ $diasQuieta }})" title="Ver los proyectos de {{ $f->nombre }}">
                            <td class="kam-t__quien">
                                <b>{{ $f->nombre }}</b>
                                <span>{{ $f->lider ? 'Equipo de '.$f->lider : 'Sin líder asignado' }}</span>
                            </td>
                            <td class="kam-t__mezcla">
                                <div class="kam-mezcla" role="img" aria-label="{{ $f->ventas }} ventas, {{ $c }} en curso, {{ $q }} quietas, {{ $f->perdidas }} perdidas">
                                    @if ($f->ventas)<i class="is-venta" style="flex: {{ $f->ventas }}" title="{{ $f->ventas }} ventas"></i>@endif
                                    @if ($c)<i class="is-curso" style="flex: {{ $c }}" title="{{ $c }} en curso"></i>@endif
                                    @if ($q)<i class="is-quieta" style="flex: {{ $q }}" title="{{ $q }} quietas"></i>@endif
                                    @if ($f->perdidas)<i class="is-perdida" style="flex: {{ $f->perdidas }}" title="{{ $f->perdidas }} perdidas"></i>@endif
                                </div>
                                <span class="kam-t__sub">{{ $n($f->gestiones) }} gestiones · {{ $n($f->ventas) }} ventas · {{ $n($f->en_curso) }} en curso</span>
                            </td>
                            <td class="is-num">
                                @if ($f->poco_volumen)
                                    <b class="kam-pct is-poco" title="Menos de {{ $minimo }} gestiones: el porcentaje no es comparable">{{ number_format($f->conversion, 0) }} %</b>
                                    <span class="kam-t__sub">pocas gestiones</span>
                                @else
                                    <b class="kam-pct {{ $tonoConv($f->conversion) }}">{{ number_format($f->conversion, 0) }} %</b>
                                    <span class="kam-t__sub">{{ $n($f->ventas) }} de {{ $n($f->gestiones) }}</span>
                                @endif
                            </td>
                            <td class="is-num">
                                <b title="{{ '$'.number_format($f->vendido, 0, ',', '.') }}">{{ $M($f->vendido) }}</b>
                                @if ($f->en_juego > 0)<span class="kam-t__sub">+ {{ $M($f->en_juego) }} en curso</span>@endif
                            </td>
                            <td class="kam-t__fact">
                                @if ($f->vendido > 0)
                                    <div class="kam-fact">
                                        <div class="kam-mezcla" role="img" aria-label="{{ $f->pct_facturado }} % facturado">
                                            @foreach ($f->reparto as $clase => $pct)@if ($pct > 0)<i class="{{ $clase }}" style="flex: {{ $pct }}"></i>@endif @endforeach
                                        </div>
                                        <b>{{ $f->pct_facturado }} %</b>
                                    </div>
                                    <span class="kam-t__sub">{{ $f->por_facturar > 0 ? $M($f->por_facturar).' ejecutado sin facturar' : ($f->ejecucion > 0 ? 'lo demás en ejecución' : 'todo facturado') }}</span>
                                @else
                                    <span class="kam-t__sub">sin ventas en el año</span>
                                @endif
                            </td>
                            <td class="is-num">
                                @if ($f->quietas)
                                    <b class="kam-pct is-quieta">{{ $n($f->quietas) }}</b>
                                    <span class="kam-t__sub">de {{ $n($f->en_curso) }} en curso</span>
                                @else
                                    <span class="kam-t__sub">ninguna</span>
                                @endif
                            </td>
                            <td class="kam-t__ir"><span>Ver proyectos</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="7"><div class="crm-empty" style="padding: 40px 20px"><strong>Sin gestiones en {{ $anio }}.</strong><span>Cambia el año o el equipo.</span></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <details class="kam-datos">
            <summary>Cómo se calcula cada columna</summary>
            <ul>
                <li><b>Gestiones y su estado:</b> todas las gestiones que el comercial abrió en el año, repartidas por cómo están hoy.</li>
                <li><b>Se volvió venta:</b> ventas ÷ gestiones abiertas en el año. Quien abrió menos de {{ $minimo }} gestiones aparece al final, porque con tan pocas un porcentaje no dice nada.</li>
                <li><b>Vendido:</b> valor de las gestiones que llegaron a venta. Debajo, lo que todavía está en curso.</li>
                <li><b>Cómo va la facturación:</b> sale del estado de cada venta en la base comercial (facturada, ejecutada por facturar, en ejecución), el mismo que usa el dashboard. Lo que registra Helisa se usa para las facturadas en parte y como verificación.</li>
                <li>La tabla de Helisa del CRM no es la contabilidad completa: {{ $n($t['sin_helisa']) }} ventas marcadas como facturadas no aparecen en ella. Se listan en el panel de cada comercial, pestaña <b>Revisar datos</b>.</li>
                <li><b>Quietas:</b> gestiones en curso sin ningún cambio en los días elegidos arriba.</li>
                <li>Solo <b>{{ $n($t['perdidas']) }}</b> gestiones del año están marcadas como perdidas. Mientras no se cierren las que no prosperan, siguen contando como "en curso".</li>
            </ul>
        </details>
    </section>
</div>
