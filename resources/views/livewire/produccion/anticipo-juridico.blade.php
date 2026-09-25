@php
    use App\Services\AnticipoJuridicoFlujo as F;
    $pesos = fn ($v) => '$'.number_format((float) $v, 0, ',', '.');
    $fecha = fn ($v) => $v ? \Carbon\Carbon::parse($v)->format('d/m/Y') : '—';
    $fechaHora = fn ($v) => $v ? \Carbon\Carbon::parse($v)->format('d/m/Y · h:i a') : '—';
    $pct = fn ($v) => rtrim(rtrim(number_format((float) $v, 2, ',', '.'), '0'), ',').' %';
    $url = fn ($ruta) => $ruta ? asset(preg_replace('#^public/#', 'storage/', $ruta)) : null;
    $tipoArchivo = fn ($ruta) => strtoupper(pathinfo((string) $ruta, PATHINFO_EXTENSION) ?: 'DOC');
    $superaUmbral = F::requiereGerencia($total);

    $estado = $a ? (int) $a->estado_id : null;
    $manos = $a ? F::enManosDe($a) : null;
    $gestion = $a ? optional($a->presupuesto)->gestion : null;
    $devolucion = $a ? $a->pasos->whereIn('accion', ['rechazar_lider', 'rechazar_controller', 'rechazar_gerencia', 'devolver_contabilidad'])->last() : null;
    $acuerdo = $proveedor && $proveedor->anticipo !== null && $proveedor->anticipo !== '' ? (float) $proveedor->anticipo : null;
    $pctActual = $editando ? (float) (is_numeric($porcentaje) ? $porcentaje : 0) : (float) optional($a)->porcentaje_anticipo;

    // Estado en una frase y su tono
    if (!$a) {
        $frase = 'Un anticipo jurídico es un pago por adelantado a un proveedor con NIT. Lo revisan el líder de producción y el Controller; si supera '.$pesos($umbral).' también lo aprueba Gerencia. Aprobado, se genera la orden de anticipo, Contabilidad lo causa y Tesorería lo paga.';
        $tono = 'tone-neutral'; $chip = 'Nuevo';
    } else {
        $chip = optional($a->estado)->description ?: 'Sin estado';
        switch ($estado) {
            case F::REV_LIDER:         $frase = 'Lo está revisando el líder de producción.'; $tono = 'tone-warn'; break;
            case F::REV_CONTROLLER:    $frase = 'Lo aprobó el líder de producción y ahora lo revisa el Controller.'; $tono = 'tone-warn'; break;
            case F::REV_GERENCIA:      $frase = 'Lo aprobaron el líder y el Controller. Como supera '.$pesos($umbral).', ahora lo aprueba Gerencia.'; $tono = 'tone-warn'; break;
            case F::APROBADO:          $frase = 'Está aprobado'.($a->cod_orden_anticipo ? ' con la orden de anticipo '.$a->cod_orden_anticipo : '').'. Contabilidad debe causarlo.'; $tono = 'tone-ok'; break;
            case F::CAUSADO:           $frase = 'Contabilidad lo causó el '.$fecha($a->fecha_causal).'. Tesorería debe pagarlo.'; $tono = 'tone-ok'; break;
            case F::PAGADO:            $frase = 'Pagado el '.$fecha($a->fecha_comprobante_pago).'. El flujo terminó.'; $tono = 'tone-ok'; break;
            case F::RECH_CONTABILIDAD: $frase = 'Contabilidad se lo devolvió al Controller, que debe revisarlo de nuevo.'; $tono = 'tone-bad'; break;
            case F::RECH_LIDER:
            case F::RECH_CONTROLLER:
            case F::RECH_GERENCIA:     $frase = 'Se lo devolvieron al productor para que lo corrija y lo vuelva a enviar.'; $tono = 'tone-bad'; break;
            case 6:                    $frase = 'Este anticipo fue anulado.'; $tono = 'tone-bad'; break;
            default:                   $frase = 'Estado: '.$chip.'.'; $tono = 'tone-neutral';
        }
    }
    $decisiones = array_values(array_diff($acciones, ['reenviar', 'anular', 'retirar']));
    $accionAnular = in_array('anular', $acciones, true) ? 'anular' : (in_array('retirar', $acciones, true) ? 'retirar' : null);
@endphp

<div class="aj" data-crm-quieto x-data x-on:aj-hecho.window="$nextTick(() => window.scrollTo({ top: 0, behavior: 'smooth' }))">

    @if ($hecho)
        <div class="aj-hecho" role="status"><span class="aj-hecho__icono">✓</span><span>{{ $hecho }}</span></div>
    @endif

    {{-- ============ QUÉ ES, EN QUÉ VA Y CUÁNTO ============ --}}
    <section class="aj-panel">
        <div class="aj-head">
            <div class="aj-head__texto">
                <span class="crm-eyebrow">{{ $a ? 'Anticipo jurídico #'.$a->id : 'Nuevo anticipo jurídico' }}{{ $a && $a->cod_orden_anticipo ? ' · '.$a->cod_orden_anticipo : '' }}</span>
                <h1>{{ $a ? (optional($a->proveedor)->tercero ?: 'Proveedor sin registrar') : 'Solicitar un anticipo jurídico' }}</h1>
                <p>{{ $frase }}</p>
            </div>
            <div class="aj-head__valor">
                <span>Valor a anticipar</span>
                <b>{{ $total > 0 ? $pesos($total) : '—' }}</b>
                <span class="crm-chip {{ $tono }}">{{ $chip }}</span>
            </div>
        </div>

        <ol class="aj-ruta" aria-label="Recorrido del anticipo">
            @foreach ($recorrido as $clave => $r)
                @php
                    $marca = ['hecho' => '✓', 'devuelto' => '↺', 'no_aplica' => '–'][$r['sit']] ?? $loop->iteration;
                    if ($r['sit'] === 'hecho') {
                        $detalle = $r['paso'] ? optional($r['paso']->usuario)->name.' · '.$fecha($r['paso']->created_at) : 'Registrado con el flujo anterior';
                    } elseif ($r['sit'] === 'actual') {
                        $detalle = $a ? 'Lo tiene ahora' : 'Tú, ahora';
                    } elseif ($r['sit'] === 'devuelto') {
                        $detalle = 'Se lo devolvieron';
                    } else {
                        $detalle = null;
                    }
                @endphp
                <li class="is-{{ $r['sit'] }}" @if ($r['sit'] === 'actual' || $r['sit'] === 'devuelto') aria-current="step" @endif>
                    <div class="aj-ruta__marca"><span class="aj-ruta__punto">{{ $marca }}</span><span class="aj-ruta__linea"></span></div>
                    <b>{{ $r['titulo'] }}</b>
                    <small>{{ $r['que'] }}</small>
                    @if ($detalle)<em>{{ $detalle }}</em>@endif
                </li>
            @endforeach
        </ol>
    </section>

    <div class="aj-grid">
        {{-- ============ IZQUIERDA: FORMULARIO (productor) O DATOS ============ --}}
        <div class="aj-col">
            @if ($editando)
                <section class="aj-panel">
                    @error('general')<div class="aj-general" role="alert">{{ $message }}</div>@enderror
                    @if ($a && $devolucion)
                        <div class="aj-nota tone-bad" style="margin: 0 0 20px">
                            <b>{{ optional($devolucion->usuario)->name ?: 'Un revisor' }} te lo devolvió</b> el {{ $fechaHora($devolucion->created_at) }}:
                            <q>{{ $devolucion->observacion }}</q>
                            Corrige lo que haga falta y vuelve a enviarlo; empieza otra vez por el líder de producción.
                        </div>
                    @endif

                    <div class="aj-form">
                        {{-- Paso 1: sobre qué --}}
                        <div class="aj-paso">
                            <div>
                                <span class="aj-paso__n">Paso 1</span>
                                <h3>¿Sobre qué es el anticipo?</h3>
                            </div>
                            @if (!$a)
                                <div class="crm-seg" role="tablist" aria-label="Tipo de solicitud">
                                    <button type="button" role="tab" class="crm-seg__btn {{ $modo === 'oc' ? 'is-active' : '' }}" wire:click="$set('modo', 'oc')" aria-selected="{{ $modo === 'oc' ? 'true' : 'false' }}">Tengo la orden de compra</button>
                                    <button type="button" role="tab" class="crm-seg__btn {{ $modo === 'borrador' ? 'is-active' : '' }}" wire:click="$set('modo', 'borrador')" aria-selected="{{ $modo === 'borrador' ? 'true' : 'false' }}">Aún no hay orden de compra</button>
                                </div>
                            @endif

                            @if ($modo === 'oc')
                                @if ($a)
                                    <div class="aj-nota" style="margin: 0">Sobre la orden de compra <b>{{ optional($a->ordenCompra)->cod_oc ?: '#'.$a->oc_id }}</b> por {{ $pesos($base) }}. La orden no se cambia en una corrección.</div>
                                @else
                                    <div class="aj-campo">
                                        <label for="aj_oc">Orden de compra jurídica aprobada</label>
                                        <select id="aj_oc" class="form-control @error('oc_id') is-mal @enderror" wire:model="oc_id">
                                            <option value="">{{ $ordenes->isEmpty() ? 'No tienes órdenes jurídicas aprobadas sin anticipo' : 'Elige una orden' }}</option>
                                            @foreach ($ordenes as $o)
                                                <option value="{{ $o->id }}">{{ $o->cod_oc ? 'OC '.$o->cod_oc : 'Orden #'.$o->id }} · {{ optional($o->proveedor)->tercero }} · {{ $pesos($o->valor_orden) }} · {{ optional($o->presupuesto)->cod_cc }}</option>
                                            @endforeach
                                        </select>
                                        @error('oc_id')<span class="aj-error">{{ $message }}</span>@enderror
                                        <span class="aj-ayuda">Solo aparecen tus órdenes jurídicas aprobadas de este año que todavía no tienen anticipo. Si el proveedor aún no tiene orden, usa "Aún no hay orden de compra".</span>
                                    </div>
                                    @if ($orden)
                                        <dl class="aj-datos">
                                            <div><dt>Proveedor</dt><dd>{{ optional($orden->proveedor)->tercero }}<small>NIT {{ optional($orden->proveedor)->documento }}</small></dd></div>
                                            <div><dt>Proyecto</dt><dd>{{ optional(optional($orden->presupuesto)->gestion)->nom_proyecto_cot ?: '—' }}<small>{{ optional($orden->presupuesto)->cod_cc }}</small></dd></div>
                                        </dl>
                                    @endif
                                @endif
                            @else
                                <div class="aj-fila">
                                    <div class="aj-campo">
                                        <label for="aj_cc">Proyecto (centro de costos)</label>
                                        @if ($a)
                                            <div class="aj-nota" style="margin: 0">{{ optional($a->presupuesto)->cod_cc }}</div>
                                        @else
                                            <select id="aj_cc" class="form-control @error('presupuesto_id') is-mal @enderror" wire:model="presupuesto_id">
                                                <option value="">{{ $presupuestos->isEmpty() ? 'No tienes proyectos aprobados' : 'Elige el proyecto' }}</option>
                                                @foreach ($presupuestos as $pp)
                                                    <option value="{{ $pp->id }}">{{ $pp->cod_cc }}</option>
                                                @endforeach
                                            </select>
                                            @error('presupuesto_id')<span class="aj-error">{{ $message }}</span>@enderror
                                        @endif
                                    </div>
                                    <div class="aj-campo">
                                        <label for="aj_prov">Proveedor (persona jurídica)</label>
                                        @if ($proveedor_id && $proveedor)
                                            <div class="aj-elegido">
                                                <div><b>{{ $proveedor->tercero }}</b><small>NIT {{ $proveedor->documento }}{{ $proveedor->ciudad ? ' · '.$proveedor->ciudad : '' }}</small></div>
                                                <button type="button" class="aj-link" wire:click="quitarProveedor">Cambiar</button>
                                            </div>
                                        @else
                                            <div class="crm-search__box">
                                                <svg class="crm-search__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg>
                                                <input id="aj_prov" type="text" class="form-control @error('proveedor_id') is-mal @enderror" style="padding-left: 36px" placeholder="Escribe el nombre o el NIT" wire:model.debounce.300ms="buscarProveedor" autocomplete="off">
                                            </div>
                                            @if (mb_strlen(trim($buscarProveedor)) >= 2)
                                                <ul class="aj-resultados">
                                                    @forelse ($proveedores as $pv)
                                                        <li><button type="button" wire:click="elegirProveedor({{ $pv->id }})"><span>{{ $pv->tercero }}</span><small>NIT {{ $pv->documento }}</small></button></li>
                                                    @empty
                                                        <li class="aj-ayuda" style="padding: 8px 10px; margin: 0">Ningún proveedor jurídico coincide con "{{ $buscarProveedor }}".</li>
                                                    @endforelse
                                                </ul>
                                            @endif
                                            @error('proveedor_id')<span class="aj-error">{{ $message }}</span>@enderror
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Paso 2: cuánto --}}
                        <div class="aj-paso">
                            <div>
                                <span class="aj-paso__n">Paso 2</span>
                                <h3>¿Cuánto se anticipa?</h3>
                            </div>
                            <div class="aj-fila">
                                @if ($modo === 'borrador' && $ordenesVinculables->isNotEmpty())
                                    <div class="aj-campo">
                                        <label for="aj_vincular">Esta compra ya tiene orden de compra: vincúlala (opcional)</label>
                                        <select id="aj_vincular" class="form-control @error('oc_vincular') is-mal @enderror" wire:model="oc_vincular">
                                            <option value="">Seguir sin orden de compra</option>
                                            @foreach ($ordenesVinculables as $ov)
                                                <option value="{{ $ov->id }}">{{ $ov->cod_oc ?: 'Orden #'.$ov->id }} · {{ $pesos($ov->valor_orden) }}</option>
                                            @endforeach
                                        </select>
                                        @error('oc_vincular')<span class="aj-error">{{ $message }}</span>@else<span class="aj-ayuda">Al vincularla, el anticipo se calcula sobre el valor de la orden y esta queda con su anticipo.</span>@enderror
                                    </div>
                                @endif
                                @if ($modo === 'borrador' && !$oc_vincular)
                                    <div class="aj-campo">
                                        <label for="aj_base">Valor base (el de la cotización)</label>
                                        <input id="aj_base" type="text" inputmode="numeric" class="form-control @error('valor_base') is-mal @enderror" placeholder="Ej. 12.500.000" wire:model.debounce.400ms="valor_base">
                                        @error('valor_base')<span class="aj-error">{{ $message }}</span>@else<span class="aj-ayuda">En pesos, sin centavos. No puede superar el costo total del proyecto.</span>@enderror
                                    </div>
                                @endif
                                <div class="aj-campo">
                                    <label for="aj_pct">Porcentaje de anticipo</label>
                                    <div class="aj-pct"><input id="aj_pct" type="number" min="1" max="100" step="1" class="form-control @error('porcentaje') is-mal @enderror" placeholder="Ej. 50" wire:model.debounce.400ms="porcentaje"></div>
                                    @error('porcentaje')<span class="aj-error">{{ $message }}</span>@else<span class="aj-ayuda">Entre 1 y 100 {{ $modo === 'oc' ? 'sobre el valor de la orden.' : 'sobre el valor base.' }}</span>@enderror
                                </div>
                            </div>

                            <div class="aj-calculo" aria-live="polite">
                                <div><span>{{ $modo === 'oc' ? 'Valor de la orden' : 'Valor base' }}</span><b>{{ $base > 0 ? $pesos($base) : '—' }}</b></div>
                                <i>×</i>
                                <div><span>Porcentaje</span><b>{{ $pctActual > 0 ? $pct($pctActual) : '—' }}</b></div>
                                <i>=</i>
                                <div class="is-total"><span>Valor a anticipar</span><b>{{ $total > 0 ? $pesos($total) : '—' }}</b></div>
                            </div>
                            @if ($total > 0)
                                <div class="aj-nota {{ $superaUmbral ? 'tone-warn' : 'tone-ok' }}" style="margin-top: 0">
                                    @if ($superaUmbral)
                                        <b>Supera {{ $pesos($umbral) }}:</b> lo revisan el líder de producción y el Controller, y al final lo aprueba Gerencia.
                                    @else
                                        <b>Es de {{ $pesos($umbral) }} o menos:</b> lo revisan el líder de producción y el Controller; no pasa por Gerencia.
                                    @endif
                                </div>
                            @endif
                            @if ($acuerdo !== null && $pctActual > 0 && $pctActual > $acuerdo)
                                <div class="aj-nota tone-warn" style="margin-top: 0">El acuerdo registrado con este proveedor es de <b>{{ $pct($acuerdo) }}</b> de anticipo{{ $proveedor->plazo ? ' ('.trim($proveedor->plazo).')' : '' }}. Si pides más, explícale al Controller por qué en la observación.</div>
                            @endif
                        </div>

                        {{-- Paso 3: soportes y observación --}}
                        <div class="aj-paso">
                            <div>
                                <span class="aj-paso__n">Paso 3</span>
                                <h3>Soportes y observación</h3>
                            </div>
                            @if ($a && $a->evidencias->isNotEmpty())
                                <ul class="aj-archivos">
                                    @foreach ($a->evidencias as $ev)
                                        <li wire:key="ev-{{ $ev->id }}">
                                            <span class="aj-archivo__tipo">{{ $tipoArchivo($ev->foto_evidencia) }}</span>
                                            <a href="{{ $url($ev->foto_evidencia) }}" target="_blank" rel="noopener">{{ $ev->observacion_evidencia ?: 'Soporte' }}</a>
                                            <small>{{ $fecha($ev->created_at) }}</small>
                                            <button type="button" class="aj-quitar" wire:click="quitarSoporte({{ $ev->id }})" wire:loading.attr="disabled">Quitar</button>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                            @if ($soportes)
                                <ul class="aj-archivos">
                                    @foreach ($soportes as $i => $s)
                                        <li class="is-nuevo" wire:key="nuevo-{{ $i }}">
                                            <span class="aj-archivo__tipo">{{ strtoupper($s->getClientOriginalExtension()) }}</span>
                                            <span class="aj-archivo__nombre">{{ $s->getClientOriginalName() }}</span>
                                            <small>{{ number_format($s->getSize() / 1024, 0, ',', '.') }} KB</small>
                                            <button type="button" class="aj-quitar" wire:click="quitarSoporteNuevo({{ $i }})">Quitar</button>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                            <label class="aj-subir">
                                <input type="file" wire:model="nuevos" multiple accept=".pdf,.jpg,.jpeg,.png">
                                <span wire:loading.remove wire:target="nuevos">+ Adjuntar cotización o remisión (PDF o imagen, máx. 10 MB)</span>
                                <span wire:loading wire:target="nuevos">Subiendo…</span>
                            </label>
                            @error('soportes')<span class="aj-error">{{ $message }}</span>@enderror
                            @error('nuevos.*')<span class="aj-error">{{ $message }}</span>@enderror
                            <span class="aj-ayuda" style="margin-top: -6px">{{ $modo === 'oc' ? 'Opcional: la orden ya es el soporte principal. Adjunta la cotización o la remisión si ayudan al Controller.' : 'Obligatorio: sin orden de compra, el soporte (la cotización del proveedor) es lo que revisa el Controller.' }}</span>

                            <div class="aj-campo">
                                <label for="aj_obs">{{ $a ? 'Qué corregiste' : 'Observación para los revisores' }}{{ $a ? '' : ' (opcional)' }}</label>
                                <textarea id="aj_obs" rows="3" class="form-control @error('observacion') is-mal @enderror" maxlength="1000" placeholder="{{ $a ? 'Ej. Ajusté el porcentaje al 50 % acordado y adjunté la cotización firmada.' : 'Ej. El proveedor exige el anticipo para reservar la fecha del evento.' }}" wire:model.defer="observacion"></textarea>
                                @error('observacion')<span class="aj-error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="aj-botones">
                        <button type="button" class="crm-btn" wire:click="enviar" wire:loading.attr="disabled" wire:target="enviar,nuevos">
                            <span wire:loading.remove wire:target="enviar">{{ $a ? 'Enviar corrección' : 'Enviar a revisión' }}</span>
                            <span wire:loading wire:target="enviar">Enviando…</span>
                            <span class="crm-btn__icon" aria-hidden="true">→</span>
                        </button>
                    </div>
                    <span class="aj-ayuda">Al enviarlo le llega un correo al líder de producción.</span>
                </section>
            @else
                <section class="aj-panel">
                    <h2 class="aj-titulo">Datos del anticipo</h2>
                    <p class="aj-sub">{{ $a->oc_id ? 'Se pidió sobre una orden de compra jurídica aprobada.' : 'Se pidió sin orden de compra previa (borrador): el valor base sale de la cotización del proveedor.' }}</p>
                    <dl class="aj-datos">
                        <div><dt>Proyecto</dt><dd>{{ optional($gestion)->nom_proyecto_cot ?: '—' }}<small>{{ optional(optional($gestion)->contacto)->empresa ?: 'Cliente sin registrar' }}</small></dd></div>
                        <div><dt>Centro de costos</dt><dd>{{ optional($a->presupuesto)->cod_cc ?: '—' }}</dd></div>
                        <div><dt>Proveedor</dt><dd>{{ optional($a->proveedor)->tercero ?: '—' }}<small>NIT {{ optional($a->proveedor)->documento ?: '—' }}{{ optional($a->proveedor)->ciudad ? ' · '.$a->proveedor->ciudad : '' }}</small></dd></div>
                        <div><dt>Base</dt><dd>{{ $a->oc_id ? 'Orden de compra '.(optional($a->ordenCompra)->cod_oc ?: '#'.$a->oc_id) : 'Sin orden de compra' }}<small>{{ $a->oc_id ? 'Valor de la orden' : 'Valor base según cotización' }}: {{ $pesos($a->valor_base) }}</small></dd></div>
                        <div><dt>Solicitó</dt><dd>{{ optional($a->productor_info)->name ?: '—' }}<small>{{ $fechaHora($a->fecha_solicitud) }}</small></dd></div>
                        @if ($acuerdo !== null)
                            <div><dt>Acuerdo con el proveedor</dt><dd>{{ $pct($acuerdo) }} de anticipo<small>{{ trim((string) optional($a->proveedor)->plazo) ?: 'Sin plazo registrado' }}</small></dd></div>
                        @endif
                    </dl>
                    <div class="aj-calculo">
                        <div><span>{{ $a->oc_id ? 'Valor de la orden' : 'Valor base' }}</span><b>{{ $pesos($a->valor_base) }}</b></div>
                        <i>×</i>
                        <div><span>Porcentaje</span><b>{{ $pct($a->porcentaje_anticipo) }}</b></div>
                        <i>=</i>
                        <div class="is-total"><span>Valor a anticipar</span><b>{{ $pesos($a->total_anticipo) }}</b></div>
                    </div>
                    @if ($acuerdo !== null && (float) $a->porcentaje_anticipo > $acuerdo)
                        <div class="aj-nota tone-warn">Pide <b>{{ $pct($a->porcentaje_anticipo) }}</b> y el acuerdo registrado con el proveedor es de <b>{{ $pct($acuerdo) }}</b>. Revisa la observación del productor.</div>
                    @endif
                </section>

                <section class="aj-panel">
                    <h2 class="aj-titulo">Soportes y documentos</h2>
                    <p class="aj-sub">Todo lo que se adjuntó en el camino. Se abren en otra pestaña.</p>
                    @php
                        $docs = [];
                        if ($a->archivo_orden_anticipo) { $docs[] = ['PDF', 'Orden de anticipo '.$a->cod_orden_anticipo, $url($a->archivo_orden_anticipo), 'Generada el '.$fecha($a->fecha_orden_anticipo)]; }
                        if ($a->comprobante_pago) { $docs[] = ['PDF', 'Comprobante de pago', $url($a->comprobante_pago), 'Tesorería · '.$fecha($a->fecha_comprobante_pago)]; }
                        if ($a->ordenCompra && $a->ordenCompra->archivo_cot) { $docs[] = [$tipoArchivo($a->ordenCompra->archivo_cot), 'Cotización de la orden de compra', $url($a->ordenCompra->archivo_cot), 'De la orden']; }
                        foreach ($a->evidencias as $ev) { $docs[] = [$tipoArchivo($ev->foto_evidencia), $ev->observacion_evidencia ?: 'Soporte', $url($ev->foto_evidencia), 'Productor · '.$fecha($ev->created_at)]; }
                    @endphp
                    @if (empty($docs))
                        <p class="aj-ayuda" style="margin: 0">No hay documentos adjuntos.</p>
                    @else
                        <ul class="aj-archivos">
                            @foreach ($docs as [$tipo, $nombre, $enlace, $meta])
                                <li><span class="aj-archivo__tipo">{{ $tipo }}</span><a href="{{ $enlace }}" target="_blank" rel="noopener">{{ $nombre }}</a><small>{{ $meta }}</small></li>
                            @endforeach
                        </ul>
                    @endif
                    @if ($a->cod_causal)
                        <div class="aj-nota">Causado por Contabilidad con el número <b>{{ $a->cod_causal }}</b> el {{ $fecha($a->fecha_causal) }}{{ $a->observacion_causal ? '. Observación: '.$a->observacion_causal : '' }}.</div>
                    @endif
                </section>
            @endif
        </div>

        {{-- ============ DERECHA: TU PASO Y EL RECORRIDO ============ --}}
        <div class="aj-col">
            @if ($a && $decisiones)
                @php
                    $esContabilidad = in_array('causar', $decisiones, true);
                    $esTesoreria = in_array('pagar', $decisiones, true);
                    $aprobar = collect($decisiones)->first(fn ($d) => str_starts_with($d, 'aprobar_'));
                    $devolver = collect($decisiones)->first(fn ($d) => str_starts_with($d, 'rechazar_'));
                    if ($aprobar === 'aprobar_lider') {
                        $quien = 'líder de producción'; $siAprueba = 'Si lo apruebas, pasa al Controller.'; $txtAprobar = 'Aprobar y pasar al Controller';
                    } elseif ($aprobar === 'aprobar_controller' && $estado === F::RECH_CONTABILIDAD) {
                        $quien = 'Controller'; $siAprueba = 'Contabilidad te lo devolvió. Si ya está aclarado, apruébalo y vuelve a Contabilidad con la misma orden de anticipo.'; $txtAprobar = 'Aprobar y volver a Contabilidad';
                    } elseif ($aprobar === 'aprobar_controller') {
                        $quien = 'Controller';
                        $siAprueba = F::requiereGerencia($a->total_anticipo)
                            ? 'Si lo apruebas, pasa a Gerencia porque supera '.$pesos($umbral).'.'
                            : 'Si lo apruebas, queda aprobado: se genera la orden de anticipo y pasa a Contabilidad.';
                        $txtAprobar = F::requiereGerencia($a->total_anticipo) ? 'Aprobar y pasar a Gerencia' : 'Aprobar';
                    } elseif ($aprobar === 'aprobar_gerencia') {
                        $quien = 'Gerencia'; $siAprueba = 'Supera '.$pesos($umbral).', por eso te toca. Si lo apruebas, se genera la orden de anticipo y pasa a Contabilidad.'; $txtAprobar = 'Aprobar';
                    } elseif ($esContabilidad) {
                        $quien = 'Contabilidad'; $siAprueba = 'Escribe el número de la causación para pasarlo a Tesorería. Si algo no cuadra, devuélvelo al Controller explicando qué.';
                    } else {
                        $quien = 'Tesorería'; $siAprueba = 'Sube el comprobante de pago en PDF. Se le enviará al productor, a compras y al proveedor.';
                    }
                @endphp
                <section class="aj-panel aj-accion">
                    <span class="crm-eyebrow">Te toca · {{ $quien }}</span>
                    <h2 class="aj-titulo">{{ $esContabilidad ? 'Causar el anticipo' : ($esTesoreria ? 'Registrar el pago' : 'Revisar y decidir') }}</h2>
                    <p class="aj-sub">{{ $siAprueba }}</p>
                    @if ($estado === F::RECH_CONTABILIDAD && $a->observaciones_contabilidad)
                        <div class="aj-nota tone-bad" style="margin: -4px 0 16px">Motivo de Contabilidad: <q>{{ $a->observaciones_contabilidad }}</q></div>
                    @endif
                    @error('general')<div class="aj-general" role="alert">{{ $message }}</div>@enderror

                    @if ($esContabilidad)
                        <div class="aj-campo" style="margin-bottom: 14px">
                            <label for="aj_causal">Número de la causación</label>
                            <input id="aj_causal" type="text" inputmode="numeric" class="form-control @error('cod_causal') is-mal @enderror" wire:model.defer="cod_causal" placeholder="Solo dígitos">
                            @error('cod_causal')<span class="aj-error">{{ $message }}</span>@enderror
                        </div>
                    @endif
                    @if ($esTesoreria)
                        <div class="aj-campo" style="margin-bottom: 14px">
                            <label>Comprobante de pago (PDF)</label>
                            <label class="aj-subir">
                                <input type="file" wire:model="comprobante" accept=".pdf">
                                <span wire:loading.remove wire:target="comprobante">{{ $comprobante ? $comprobante->getClientOriginalName() : '+ Elegir el PDF del comprobante' }}</span>
                                <span wire:loading wire:target="comprobante">Subiendo…</span>
                            </label>
                            @error('comprobante')<span class="aj-error">{{ $message }}</span>@enderror
                        </div>
                    @endif

                    <div class="aj-campo">
                        <label for="aj_decision">{{ $esTesoreria ? 'Observación (opcional)' : ($devolver || $esContabilidad ? 'Observación (obligatoria si lo devuelves)' : 'Observación (opcional)') }}</label>
                        <textarea id="aj_decision" rows="3" maxlength="1000" class="form-control @error('observacion') is-mal @enderror" wire:model.defer="obs_decision" placeholder="{{ $esTesoreria ? 'Ej. Pagado por transferencia.' : ($esContabilidad ? 'Ej. Falta el RUT actualizado del proveedor.' : 'Ej. El porcentaje no coincide con el acuerdo del proveedor.') }}"></textarea>
                        @error('observacion')<span class="aj-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="aj-botones">
                        @if ($aprobar)
                            <button type="button" class="crm-btn" wire:click="decidir('{{ $aprobar }}')" wire:loading.attr="disabled" wire:target="decidir">{{ $txtAprobar }} <span class="crm-btn__icon" aria-hidden="true">✓</span></button>
                        @endif
                        @if ($esContabilidad)
                            <button type="button" class="crm-btn" wire:click="decidir('causar')" wire:loading.attr="disabled" wire:target="decidir">Causar y pasar a Tesorería <span class="crm-btn__icon" aria-hidden="true">✓</span></button>
                            <button type="button" class="crm-btn crm-btn--ghost aj-devolver" wire:click="decidir('devolver_contabilidad')" wire:loading.attr="disabled" wire:target="decidir">Devolver al Controller</button>
                        @endif
                        @if ($esTesoreria)
                            <button type="button" class="crm-btn" wire:click="decidir('pagar')" wire:loading.attr="disabled" wire:target="decidir,comprobante">Registrar el pago <span class="crm-btn__icon" aria-hidden="true">✓</span></button>
                        @endif
                        @if ($devolver)
                            <button type="button" class="crm-btn crm-btn--ghost aj-devolver" wire:click="decidir('{{ $devolver }}')" wire:loading.attr="disabled" wire:target="decidir">Devolver al productor</button>
                        @endif
                    </div>
                </section>
            @elseif ($a && !$editando)
                @php
                    $esperaTxt = [
                        'lider' => 'Le toca al líder de producción.',
                        'controller' => 'Le toca al Controller.',
                        'gerencia' => 'Le toca a Gerencia (lo aprueban quienes tienen el permiso de revisión de anticipos).',
                        'contabilidad' => 'Le toca a Contabilidad: causarlo.',
                        'tesoreria' => 'Le toca a Tesorería: pagarlo.',
                        'productor' => 'Le toca al productor: corregirlo y reenviarlo.',
                    ][$manos] ?? ($estado === F::PAGADO ? 'Ya está pagado; no hay nada pendiente.' : 'No hay nada pendiente.');
                    // Si quien mira tiene el rol del paso pero algo lo impide (por ejemplo, es su propia solicitud), se le dice por qué
                    $motivo = ($manos && F::es(auth()->user(), $manos)) ? F::impedimento($a, auth()->user(), [
                        'lider' => 'aprobar_lider', 'controller' => 'aprobar_controller', 'gerencia' => 'aprobar_gerencia',
                        'contabilidad' => 'causar', 'tesoreria' => 'pagar', 'productor' => 'reenviar'][$manos]) : null;
                @endphp
                <section class="aj-panel">
                    <div class="aj-espera">
                        <span class="aj-espera__icono" aria-hidden="true">{{ $estado === F::PAGADO ? '✓' : '⏳' }}</span>
                        <div>
                            <h2 class="aj-titulo">{{ $estado === F::PAGADO ? 'Flujo terminado' : '¿Qué sigue?' }}</h2>
                            <p class="aj-sub" style="margin-bottom: 0">{{ $esperaTxt }}{{ $motivo ? ' '.$motivo : '' }}</p>
                        </div>
                    </div>
                </section>
            @endif

            @if ($a && $similares->isNotEmpty() && (int) auth()->user()->rol !== 7)
                <section class="aj-panel">
                    <h2 class="aj-titulo">Otros anticipos a este proveedor en este proyecto</h2>
                    <p class="aj-sub">Revisa que no sea la misma compra pedida dos veces.</p>
                    <ul class="aj-historia">
                        @foreach ($similares as $s)
                            <li class="tone-neutral"><span class="aj-historia__punto"></span><b>#{{ $s->id }}</b> · {{ $pesos($s->total_anticipo) }} · {{ optional($s->estado)->description }}{{ $s->oc_id ? ' · con orden de compra' : ' · sin orden de compra' }}</li>
                        @endforeach
                    </ul>
                </section>
            @endif

            @if ($accionAnular)
                <section class="aj-panel">
                    <h2 class="aj-titulo">{{ $accionAnular === 'anular' ? 'Anular el anticipo' : 'Retirar la solicitud' }}</h2>
                    <p class="aj-sub">Sale del flujo y queda anulado{{ $a->oc_id ? '; su orden de compra queda libre para otro anticipo' : '' }}. Escribe el motivo: se le avisa al productor y al Controller.</p>
                    <div class="aj-campo">
                        <textarea rows="2" maxlength="1000" class="form-control @error('observacion') is-mal @enderror" wire:model.defer="obs_anulacion" placeholder="Ej. El proveedor ya no va a prestar el servicio."></textarea>
                        @error('observacion')<span class="aj-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="aj-botones">
                        <button type="button" class="crm-btn crm-btn--ghost aj-devolver" wire:click="anular" wire:loading.attr="disabled" wire:target="anular"
                                onclick="return confirm('¿Anular este anticipo? No se puede deshacer.')">{{ $accionAnular === 'anular' ? 'Anular el anticipo' : 'Retirar mi solicitud' }}</button>
                    </div>
                </section>
            @endif

            @if ($a)
                <section class="aj-panel">
                    <h2 class="aj-titulo">Recorrido</h2>
                    <p class="aj-sub">Cada decisión, quién la tomó y por qué.</p>
                    <ol class="aj-historia">
                        @foreach ($a->pasos as $p)
                            @php
                                $tonoPaso = str_starts_with($p->accion, 'rechazar_') || in_array($p->accion, ['devolver_contabilidad', 'anular', 'retirar', 'anular_por_oc'], true) ? 'tone-bad' : (str_starts_with($p->accion, 'aprobar_') || in_array($p->accion, ['causar', 'pagar'], true) ? 'tone-ok' : 'tone-neutral');
                                $rolTxt = [7 => 'Productor', 6 => 'Líder de producción', 11 => 'Controller', 9 => 'Contabilidad', 8 => 'Tesorería'][$p->rol] ?? (str_ends_with($p->accion, '_gerencia') ? 'Gerencia' : 'CRM');
                            @endphp
                            <li class="{{ $tonoPaso }}">
                                <span class="aj-historia__punto"></span>
                                <b>{{ optional($p->usuario)->name ?: 'Usuario eliminado' }}</b> · {{ F::TEXTOS[$p->accion] ?? $p->accion }}
                                <small>{{ $rolTxt }} · {{ $fechaHora($p->created_at) }}</small>
                                @if ($p->observacion)<blockquote>{{ $p->observacion }}</blockquote>@endif
                            </li>
                        @endforeach
                    </ol>
                </section>
            @endif
        </div>
    </div>
</div>
