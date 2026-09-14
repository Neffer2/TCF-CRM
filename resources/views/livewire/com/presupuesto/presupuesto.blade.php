<div x-data="">
    @if (( $estadoValidator != 2 && $estadoValidator != 4) || Auth::user()->espacioAdmin())
        <div class="crm-presupuesto">
        @php
            // ---- Cifras del presupuesto (mismas fórmulas de getMetricas), listas para consultarse con el botón "?" ----
            $recargos   = (float) $imprevistos + (float) $administracion + (float) $fee;
            $ventaBase  = $recargos > -100 ? $ventaProyecto / (1 + $recargos / 100) : 0;
            $vImprev    = $ventaBase * ((float) $imprevistos / 100);
            $vAdmin     = $ventaBase * ((float) $administracion / 100);
            $vFee       = $ventaBase * ((float) $fee / 100);
            $margenItemsPct = 100 - ($margenItems * 100);
            $pesos = function ($v) { return '$'.number_format((float) $v, 0, '.', ','); };
            $pct   = function ($v, $d = 1) { return number_format((float) $v, $d, '.', ',').' %'; };
            if ($margenProyecto >= 35)      { $tonoMargen = 'tone-ok';   $lecturaMargen = 'Margen sano (≥ 35 %)'; }
            elseif ($margenProyecto >= 30)  { $tonoMargen = 'tone-warn'; $lecturaMargen = 'En el límite: con 30 % o más no pasa por gerencia'; }
            else                            { $tonoMargen = 'tone-bad';  $lecturaMargen = 'Por debajo del 30 %: requiere validación de gerencia'; }
            $estadoNombre = optional($presupuesto->estado)->description ?: 'Sin estado';
            $tonoEstado = ['Aprobado' => 'tone-ok', 'Revision' => 'tone-warn', 'Editable' => 'tone-muted'][$estadoNombre] ?? 'tone-neutral';
            $gestionP = $presupuesto->gestion; $contactoP = optional($gestionP)->contacto;
            $soloLectura = Auth::user()->espacioAdmin();
        @endphp

        {{-- ================= CABECERA ================= --}}
        <div class="card mb-3 crm-page-card crm-presupuesto__head">
            <div class="crm-page-head crm-page-head--compacta">
                <div class="crm-page-head__title">
                    <span class="crm-eyebrow">Presupuesto {{ $presupuesto->cod_cot ? '· cotización '.$presupuesto->cod_cot : '' }}{{ $presupuesto->cod_cc ? ' · centro de costos '.$presupuesto->cod_cc : '' }}</span>
                    <h1>{{ optional($gestionP)->nom_proyecto_cot ?: 'Proyecto sin nombre' }}</h1>
                    <p>
                        <b>{{ optional($contactoP)->empresa ?: 'Cliente sin registrar' }}</b>
                        @if (optional($contactoP)->nombre) · contacto {{ $contactoP->nombre }} @endif
                        @if (optional($contactoP)->ciudad) · {{ $contactoP->ciudad }} @endif
                        @if (optional($gestionP)->comercial) · comercial {{ $gestionP->comercial->name }} @endif
                    </p>
                </div>
                <div class="crm-presupuesto__estado">
                    <span class="crm-chip {{ $tonoEstado }}">{{ $estadoNombre }}</span>
                    <span class="crm-chip {{ $presupuesto->cod_cc ? 'tone-neutral' : 'tone-muted' }}">{{ $presupuesto->cod_cc ? 'Con centro de costos' : 'Sin centro de costos' }}</span>
                </div>
            </div>
        </div>

        {{-- ================= CIFRAS (con "?" para ver cómo se calcula cada una) + PARÁMETROS EN LÍNEA ================= --}}
        <div class="card mb-4 crm-panel crm-presupuesto__barra" style="--i:1">
            <div class="crm-cifras">
                <div class="crm-cifra tone-neutral" style="--i:0">
                    <span class="crm-cifra__label">Venta del proyecto</span>
                    <span class="crm-cifra__valor"><small>$</small><span data-count="{{ round($ventaProyecto) }}" data-key="pp_venta">{{ number_format($ventaProyecto, 0, '.', ',') }}</span></span>
                    <details class="crm-info"><summary aria-label="¿Cómo se calcula la venta del proyecto?">?</summary>
                        <div class="crm-info__pop"><b>Venta del proyecto</b><p>Venta base (Σ V. total cliente de los ítems) + recargos sobre esa base.</p>
                            <span>{{ $pesos($ventaBase) }} + imprevistos {{ $pct($imprevistos) }} ({{ $pesos($vImprev) }}) + administración {{ $pct($administracion) }} ({{ $pesos($vAdmin) }}) + fee {{ $pct($fee) }} ({{ $pesos($vFee) }})</span><em>= {{ $pesos($ventaProyecto) }}</em></div>
                    </details>
                </div>
                <div class="crm-cifra tone-muted" style="--i:1">
                    <span class="crm-cifra__label">Costos</span>
                    <span class="crm-cifra__valor"><small>$</small><span data-count="{{ round($costosProyecto) }}" data-key="pp_costos">{{ number_format($costosProyecto, 0, '.', ',') }}</span></span>
                    <details class="crm-info"><summary aria-label="¿Cómo se calculan los costos?">?</summary>
                        <div class="crm-info__pop"><b>Costos del proyecto</b><p>Suma del V. total interno de todos los ítems: lo que le cuesta a Bull (proveedores, personal, etc.).</p><span>Σ V. total interno</span><em>= {{ $pesos($costosProyecto) }}</em></div>
                    </details>
                </div>
                <div class="crm-cifra {{ $tonoMargen }}" style="--i:2">
                    <span class="crm-cifra__label">Margen bruto</span>
                    <span class="crm-cifra__valor"><small>$</small><span data-count="{{ round($margenBruto) }}" data-key="pp_bruto">{{ number_format($margenBruto, 0, '.', ',') }}</span></span>
                    <details class="crm-info"><summary aria-label="¿Cómo se calcula el margen bruto?">?</summary>
                        <div class="crm-info__pop"><b>Margen bruto</b><p>La ganancia en pesos: venta del proyecto menos costos.</p><span>{{ $pesos($ventaProyecto) }} − {{ $pesos($costosProyecto) }}</span><em>= {{ $pesos($margenBruto) }}</em></div>
                    </details>
                </div>
                <div class="crm-cifra {{ $tonoMargen }} crm-cifra--destacada" style="--i:3" title="{{ $lecturaMargen }}">
                    <span class="crm-cifra__label">Margen del proyecto</span>
                    <span class="crm-cifra__valor"><span data-count="{{ sprintf('%.1f', $margenProyecto) }}" data-decimals="1" data-key="pp_margen">{{ sprintf('%.1f', $margenProyecto) }}</span><small> %</small></span>
                    <span class="crm-cifra__nota">{{ $lecturaMargen }}</span>
                    <details class="crm-info"><summary aria-label="¿Cómo se calcula el margen del proyecto?">?</summary>
                        <div class="crm-info__pop"><b>Margen del proyecto</b><p>El margen bruto como porcentaje de la venta. Si queda por debajo del 30 %, el presupuesto pasa a validación de gerencia.</p><span>{{ $pesos($margenBruto) }} ÷ {{ $pesos($ventaProyecto) }} × 100</span><em>= {{ $pct($margenProyecto) }}</em></div>
                    </details>
                </div>
                <div class="crm-cifra tone-neutral" style="--i:4">
                    <span class="crm-cifra__label">Margen de los ítems</span>
                    <span class="crm-cifra__valor"><span data-count="{{ sprintf('%.1f', $margenItemsPct) }}" data-decimals="1" data-key="pp_items">{{ sprintf('%.1f', $margenItemsPct) }}</span><small> %</small></span>
                    <details class="crm-info"><summary aria-label="¿Cómo se calcula el margen de los ítems?">?</summary>
                        <div class="crm-info__pop"><b>Margen de los ítems</b><p>Margen promedio de los ítems antes de recargos: 100 % menos la proporción de costo sobre venta (solo ítems con utilidad definida). En la tabla, la columna Utilidad muestra ese margen por ítem: 100 − (costo ÷ precio × 100).</p><span>100 − (Σ costo interno ÷ Σ venta cliente × 100) = 100 − {{ $pct($margenItems * 100) }}</span><em>= {{ $pct($margenItemsPct) }}</em></div>
                    </details>
                </div>
            </div>

            <div class="crm-params">
                <span class="crm-params__titulo">Recargos sobre la venta base
                    <details class="crm-info"><summary aria-label="¿Qué son los recargos?">?</summary>
                        <div class="crm-info__pop"><b>Recargos</b><p>Porcentajes que se suman sobre la venta base (Σ precios al cliente) y aumentan la venta del proyecto: imprevistos (colchón para costos no previstos), administración (gastos administrativos) y fee de agencia (honorarios de Bull). El tiempo es el plazo de facturación pactado en días.</p><span>Hoy suman {{ $pct($recargos) }}</span><em>= {{ $pesos($vImprev + $vAdmin + $vFee) }}</em></div>
                    </details>
                </span>
                <label class="crm-param"><span>Imprevistos</span><input type="text" inputmode="decimal" wire:model.lazy="imprevistos" placeholder="0" @if ($soloLectura) disabled @endif class="form-control @error('imprevistos') is-invalid @enderror"><em>%</em><i>{{ $pesos($vImprev) }}</i></label>
                <label class="crm-param"><span>Administración</span><input type="text" inputmode="decimal" wire:model.lazy="administracion" placeholder="0" @if ($soloLectura) disabled @endif class="form-control @error('administracion') is-invalid @enderror"><em>%</em><i>{{ $pesos($vAdmin) }}</i></label>
                <label class="crm-param"><span>Fee agencia</span><input type="text" inputmode="decimal" wire:model.lazy="fee" placeholder="0" @if ($soloLectura) disabled @endif class="form-control @error('fee') is-invalid @enderror"><em>%</em><i>{{ $pesos($vFee) }}</i></label>
                <label class="crm-param"><span>Tiempo</span><input type="text" inputmode="numeric" wire:model.lazy="tiempoFactura" placeholder="30" @if ($soloLectura) disabled @endif class="form-control @error('tiempoFactura') is-invalid @enderror"><em>días</em></label>
                <details class="crm-notas">
                    <summary>Notas de la cotización {{ trim((string) $notas) !== '' ? '·' : '' }} <b>{{ trim((string) $notas) !== '' ? \Illuminate\Support\Str::limit(trim($notas), 40) : 'sin notas' }}</b></summary>
                    <textarea wire:model.lazy="notas" rows="3" class="form-control" placeholder="Condiciones o aclaraciones que van en la cotización" @if ($soloLectura) disabled @endif></textarea>
                </details>
            </div>
        </div>

            {{-- Actualizacion --}}
            @if (Auth::user()->espacioAdmin())
                <div class="row mt-2">
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-header p-0 mt-3 col-md-12">
                                <div class="row px-3">
                                    <div class="col-md-12">
                                        <h3 class="mb-0">Justificaci&oacute;n comercial</h3>
                                        <p class="text-sm mb-0">Revisa la justificaci&oacute;n que el comercial escribi&oacute; para t&iacute;.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-2">
                                <div class="form-group">
                                    <textarea name="justificacion" @if(Auth::user()->espacioAdmin()) disabled @endif id="justificacion" cols="10" rows="2" class="form-control" wire:model="justificacion" class="form-control @error('justificacion') is-invalid @elseif(strlen($justificacion) > 0) is-valid @enderror"></textarea>
                                    @error('justificacion')
                                    <small id="justificacion" class="text-danger bold">
                                        {{ $message }}
                                    </small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-header p-0 mt-3 col-md-12">
                                <div class="row px-3">
                                    <div class="col-md-12">
                                        <h3 class="mb-0">Justificaci&oacute;n lider comercial</h3>
                                        <p class="text-sm mb-0">Expl&iacute;cale al comercial porqu&eacute; ha sido rechazado el presupuesto.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-2">
                                <div class="form-group">
                                    <textarea name="justificacion_lider_comercial" @if(!in_array(Auth::user()->rol, [1, 12]) || ($presupuesto->estado_id != 4 && Auth::user()->comerciales()->exists())) disabled @endif
                                    id="justificacion_lider_comercial" cols="10" rows="2" class="form-control"
                                              wire:model="justificacion_lider_comercial" class="form-control @error('justificacion_lider_comercial') is-invalid @elseif(strlen($justificacion_lider_comercial) > 0) is-valid @enderror"></textarea>
                                    @error('justificacion_lider_comercial')
                                    <small id="justificacion_lider_comercial" class="text-danger bold">
                                        {{ $message }}
                                    </small>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <button class="btn bg-gradient-warning m-0"
                                            @if (!in_array(Auth::user()->rol, [1, 12]) || ($presupuesto->estado_id != 4 && Auth::user()->comerciales()->exists())) disabled @endif
                                            wire:click="rechazar" wire:loading.attr="disabled">Rechazar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-header p-0 mt-3 col-md-12">
                                <div class="row px-3">
                                    <div class="col-md-12">
                                        <h3 class="mb-0">Justificaci&oacute;n gerencia</h3>
                                        <p class="text-sm mb-0">Expl&iacute;cale al comercial porqu&eacute; ha sido rechazado el presupuesto.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-2">
                                <div class="form-group">
                                    <textarea name="justificacion_gerencia" @if(Auth::user()->rol != 1 || $presupuesto->estado_id != 5 || (Auth::user()->id != 8 && Auth::user()->id != 10)) disabled @endif
                                    id="justificacion_gerencia" cols="10" rows="2" class="form-control" wire:model="justificacion_gerencia"
                                              class="form-control @error('justificacion_gerencia') is-invalid @elseif(strlen($justificacion_gerencia) > 0) is-valid @enderror"></textarea>
                                    @error('justificacion_gerencia')
                                    <small id="justificacion_gerencia" class="text-danger bold">
                                        {{ $message }}
                                    </small>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <button class="btn bg-gradient-warning m-0"
                                            @if (Auth::user()->rol != 1 || $presupuesto->estado_id != 5 || (Auth::user()->id != 8 && Auth::user()->id != 10)) disabled @endif
                                            wire:click="rechazar" wire:loading.attr="disabled">Rechazar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if(Auth::user()->can('gestionar-presupuestos-especiales'))
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-header p-0 mt-3 col-md-12">
                                <div class="row px-3">
                                    <div class="col-md-12">
                                        <h3 class="mb-0">Revisión de Cambios</h3>
                                        <p class="text-sm mb-0">Confirmas que has revisado las últimas actualizaciones.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-2 text-center">
                                @if(optional($presupuesto)->notificacion_actualizacion)
                                    <div class="alert alert-warning text-white text-sm mb-2 p-2" role="alert">
                                        <i class="fas fa-exclamation-circle me-1"></i> Modificaciones pendientes.
                                    </div>
                                    
                                    {{-- Emitir evento global a Livewire para disparar 'marcarVisto' --}}
                                    <button class="btn bg-gradient-info w-100 mb-0" 
                                            wire:click="marcarComoVisto({{ $presupuesto->id_gestion }})"
                                            wire:loading.attr="disabled">
                                        <i class="fas fa-check-circle me-1"></i> Confirmar Revisión
                                    </button>
                                @else
                                    <div class="alert alert-success text-white text-sm mb-0 p-2" role="alert">
                                        <i class="fas fa-check-double me-1"></i> Presupuesto revisado
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                </div>
            @endif

            @if (( $justificacion_compras || $justificacion_lider_comercial || $justificacion_gerencia ) && !Auth::user()->espacioAdmin())
                <div class="row mt-2">
                    @if ($justificacion_compras)
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header p-0 mt-3 col-md-12">
                                    <div class="row px-3">
                                        <div class="col-md-12">
                                            <h3 class="mb-0">
                                                Justificaci&oacute;n compras
                                            </h3>
                                            <p class="text-sm mb-0">Revisa el porqu&eacute; fue rechazado tu presupuesto.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-2">
                                    <div class="form-group">
                                        <textarea name="justificacion_compras" @if(Auth::user()->rol != 1) disabled @endif id="justificacion_compras" cols="10" rows="1" class="form-control" wire:model="justificacion_compras" class="form-control @error('justificacion_compras') is-invalid @elseif(strlen($justificacion_compras) > 0) is-valid @enderror"></textarea>
                                        @error('justificacion_compras')
                                        <small id="justificacion_compras" class="text-danger bold">
                                            {{ $message }}
                                        </small>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($justificacion_lider_comercial)
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header p-0 mt-3 col-md-12">
                                    <div class="row px-3">
                                        <div class="col-md-12">
                                            <h3 class="mb-0">
                                                Justificaci&oacute;n lider comercial
                                            </h3>
                                            <p class="text-sm mb-0">Revisa el porqu&eacute; fue rechazado tu presupuesto.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-2">
                                    <div class="form-group">
                                        <textarea name="justificacion_lider_comercial" @if(Auth::user()->rol != 1) disabled @endif
                                        id="justificacion_lider_comercial" cols="10" rows="1" class="form-control"
                                                  wire:model="justificacion_lider_comercial" class="form-control @error('justificacion_lider_comercial') is-invalid @elseif(strlen($justificacion_lider_comercial) > 0) is-valid @enderror"></textarea>
                                        @error('justificacion_lider_comercial')
                                        <small id="justificacion_lider_comercial" class="text-danger bold">
                                            {{ $message }}
                                        </small>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($justificacion_gerencia)
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header p-0 mt-3 col-md-12">
                                    <div class="row px-3">
                                        <div class="col-md-12">
                                            <h3 class="mb-0">
                                                Justificaci&oacute;n gerencia
                                            </h3>
                                            <p class="text-sm mb-0">Revisa el porqu&eacute; fue rechazado tu presupuesto.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-2">
                                    <div class="form-group">
                                    <textarea name="justificacion_gerencia" @if(Auth::user()->rol != 1) disabled @endif
                                    id="justificacion_gerencia" cols="10" rows="1" class="form-control"
                                              wire:model="justificacion_gerencia" class="form-control @error('justificacion_gerencia') is-invalid @elseif(strlen($justificacion_gerencia) > 0) is-valid @enderror"></textarea>
                                        @error('justificacion_gerencia')
                                        <small id="justificacion_gerencia" class="text-danger bold">
                                            {{ $message }}
                                        </small>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <div class="table-responsive my-3 rounded bg-white">
            <table class="table mb-0">
                <thead>
                <tr>
                    @if(Auth::user()->rol == 2)
                    <th class="font-weight-bold font-table bg-gradient-info text-white"></th>
                    @endif
                    <th class="font-weight-bold font-table bg-gradient-info text-white"  title="Código del tarifario">COD</th>

                    <th class="font-weight-bold font-table bg-gradient-warning text-white" title="Nombre del ítem">ÍTEM</th>
                    <th class="font-weight-bold font-table bg-gradient-warning text-white" title="Unidades o personas">CANTIDAD</th>
                    <th class="font-weight-bold font-table bg-gradient-warning text-white" title="Días de servicio; multiplica la cantidad">DÍA</th>
                    <th class="font-weight-bold font-table bg-gradient-warning text-white" title="Otros costos que se suman al total interno">OTROS</th>
                    <th class="font-weight-bold font-table bg-gradient-warning text-white">DESCRIPCIÓN</th>
                    <th class="font-weight-bold font-table bg-gradient-warning text-white" title="Costo unitario para Bull">V. UNITARIO INTERNO (costo)</th>
                    <th class="font-weight-bold font-table bg-gradient-warning text-white" title="Costo total = cantidad × días × V. unitario interno (+ otros)">V. TOTAL INTERNO (costo)</th>
                    <th class="font-weight-bold font-table bg-rentabilidad text-white" title="Precio unitario = V. unitario interno ÷ utilidad">V. UNITARIO CLIENTE (precio)</th>
                    <th class="font-weight-bold font-table bg-rentabilidad text-white" title="Precio total = cantidad × días × V. unitario cliente">V. TOTAL CLIENTE (precio)</th>
                    @if ($presupuesto->gestion->claro)
                        <th class="font-weight-bold font-table bg-gradient-warning text-white">V. TOTAL CLIENTE</th>
                    @endif
                    <th class="font-weight-bold font-table bg-gradient-warning text-white">PROVEEDOR</th>
                    <th class="font-weight-bold font-table bg-gradient-warning text-white" title="Margen del ítem en %: 100 − (costo ÷ precio × 100)">UTILIDAD (margen %)</th>

                    <th class="font-weight-bold font-table bg-gradient-success text-white">MES</th>
                    <th class="font-weight-bold font-table bg-gradient-success text-white">DIAS</th>
                    <th class="font-weight-bold font-table bg-gradient-success text-white">CIUDAD</th>
                    
                    @if ($rentabilidadView)
                        <th class="font-weight-bold font-table bg-rentabilidad text-white" title="V. total cliente − V. total interno">RENTABILIDAD ($)</th>
                    @endif

                    @if (!Auth::user()->espacioAdmin())
                        <th colspan="3" class="font-weight-bold font-table bg-gradient-primary text-white">ACCIONES</th>
                    @endif
                </tr>
                </thead>
                <tbody id="sortable-body">
                @foreach ($items as $key => $item)
                    @if ($item->evento)
                        {{-- ================= CASO 0: EVENTOS ================= --}}
                        <tr wire:key="item-{{ $item->id }}" data-id="{{ $item->id }}" class="font-weight-bold font-table bg-gradient-info text-white">
                            <td class="text-center">⚬</td>
                            <td colspan="@if ($rentabilidadView) 16 @else 13 @endif" class="text-center">
                                {{ $item->descripcion }}
                            </td>
                            @if (!Auth::user()->espacioAdmin())
                                <td><button wire:click="deleteItem({{ $item->id }})">✖️</button></td>
                                <td><button wire:click="getDataEdit({{ $item->id }})">📝</button></td>
                            @endif
                        </tr>
                    @else
                        @php
                            $idsEspeciales = [2, 208, 197, 214, 145, 181, 210, 206];
                            $userRol = Auth::user()->rol;
                        @endphp
                        {{-- ================= CONDICIÓN 1: ROL 1 CON ITEM ESPECIAL (Evalúa 'actualizado_con') ================= --}}
                        @if (Auth::user()->rol == 1 && in_array(Auth::user()->id, $idsEspeciales))
                            <tr wire:key="item-{{ $item->id }}" data-id="{{ $item->id }}"
                                class="{{ $item->actualizado_con > 0 ? 'text-white' : '' }}"
                                style="background-color: {{ $item->actualizado_con == 2 ? '#6f42c1' : ($item->actualizado_con == 1 ? '#ffbb17' : ($item->actualizado_con == 3 ? '#e65c00' : 'transparent')) }};">

                                <td class="font-weight-bold font-table">{{ $item->cod }}</td>
                                <td class="font-weight-bold font-table">{{ $item->num_item }}</td>
                                <td class="font-weight-bold font-table">{{ $item->cantidad }}</td>
                                <td class="font-weight-bold font-table">{{ $item->dia }}</td>
                                <td class="font-weight-bold font-table">{{ $item->otros }}</td>
                                <td class="font-weight-bold font-table">
                                    <textarea cols="30" rows="1" readonly>{{ $item->descripcion }}</textarea>
                                </td>
                                <td class="font-weight-bold font-table">$ {{ number_format($item->v_unitario) }}</td>
                                <td class="font-weight-bold font-table">$ {{ number_format($item->v_total) }}</td>
                                <td class="font-weight-bold font-table">$ {{ number_format($item->v_unitario_cot) }}</td>
                                <td class="font-weight-bold font-table">$ {{ number_format($item->v_total_cliente) }}</td>
                                <td class="font-weight-bold font-table">
                                    @forelse ($item->proveedores as $proveedorItem)
                                    {{ $proveedorItem->tercero }} <br>
                                @empty
                                    {{ $item->proveedor_legacy }}
                                @endforelse
                                </td>
                                <td class="font-weight-bold font-table">{{ number_format(100 - ($item->margen_utilidad * 100), 2) }} %</td>
                                <td class="font-weight-bold font-table">{{ $item->mesDescription->description ?? '' }}</td>
                                <td class="font-weight-bold font-table">{{ $item->dias }}</td>
                                <td class="font-weight-bold font-table">{{ $item->ciudad }}</td>
                                @if ($rentabilidadView)
                                    <td class="font-weight-bold font-table">$ {{ number_format($item->rentabilidad) }}</td>
                                @endif
                            </tr>

                        {{-- ================= CONDICIÓN 2: ROL 1 ESTÁNDAR (Evalúa 'actualizado') ================= --}}
                        @elseif (Auth::user()->espacioAdmin())
                            <tr wire:key="item-{{ $item->id }}" data-id="{{ $item->id }}"
                                class="{{ $item->actualizado > 0 ? 'text-white' : '' }}"
                                style="background-color: {{ $item->actualizado == 2 ? '#6f42c1' : ($item->actualizado == 1 ? '#ffbb17' : ($item->actualizado == 3 ? '#e65c00' : 'transparent')) }};">

                                <td class="font-weight-bold font-table">{{ $item->cod }}</td>
                                <td class="font-weight-bold font-table">{{ $item->num_item }}</td>
                                <td class="font-weight-bold font-table">{{ $item->cantidad }}</td>
                                <td class="font-weight-bold font-table">{{ $item->dia }}</td>
                                <td class="font-weight-bold font-table">{{ $item->otros }}</td>
                                <td class="font-weight-bold font-table">
                                    <textarea cols="30" rows="1" readonly>{{ $item->descripcion }}</textarea>
                                </td>
                                <td class="font-weight-bold font-table">$ {{ number_format($item->v_unitario) }}</td>
                                <td class="font-weight-bold font-table">$ {{ number_format($item->v_total) }}</td>
                                <td class="font-weight-bold font-table">$ {{ number_format($item->v_unitario_cot) }}</td>
                                <td class="font-weight-bold font-table">$ {{ number_format($item->v_total_cliente) }}</td>
                                <td class="font-weight-bold font-table">
                                    @forelse ($item->proveedores as $proveedorItem)
                                    {{ $proveedorItem->tercero }} <br>
                                @empty
                                    {{ $item->proveedor_legacy }}
                                @endforelse
                                </td>
                                <td class="font-weight-bold font-table">{{ number_format(100 - ($item->margen_utilidad * 100), 2) }} %</td>
                                <td class="font-weight-bold font-table">{{ $item->mesDescription->description ?? '' }}</td>
                                <td class="font-weight-bold font-table">{{ $item->dias }}</td>
                                <td class="font-weight-bold font-table">{{ $item->ciudad }}</td>
                                @if ($rentabilidadView)
                                    <td class="font-weight-bold font-table">$ {{ number_format($item->rentabilidad) }}</td>
                                @endif
                            </tr>

                        {{-- ================= CONDICIÓN 3: ROL 2 / COMERCIAL (Evalúa 'actualizado' + Acciones adicionales) ================= --}}
                        @elseif ($userRol == 2)
                            <tr wire:key="item-{{ $item->id }}" data-id="{{ $item->id }}"
                                class="{{ $item->actualizado > 0 ? 'text-white' : '' }}"
                                style="background-color: {{ $item->actualizado == 2 ? '#6f42c1' : ($item->actualizado == 1 ? '#ffbb17' : ($item->actualizado == 3 ? '#e65c00' : 'transparent')) }};">

                                <td class="text-center cursor-move drag-handle">☰</td>
                                <td class="font-weight-bold font-table">{{ $item->cod }}</td>
                                <td class="font-weight-bold font-table">{{ $item->num_item }}</td>
                                <td class="font-weight-bold font-table">{{ $item->cantidad }}</td>
                                <td class="font-weight-bold font-table">{{ $item->dia }}</td>
                                <td class="font-weight-bold font-table">{{ $item->otros }}</td>
                                <td class="font-weight-bold font-table">
                                    <textarea cols="30" rows="1" readonly>{{ $item->descripcion }}</textarea>
                                </td>
                                <td class="font-weight-bold font-table">$ {{ number_format($item->v_unitario) }}</td>
                                <td class="font-weight-bold font-table">$ {{ number_format($item->v_total) }}</td>
                                <td class="font-weight-bold font-table">$ {{ number_format($item->v_unitario_cot) }}</td>
                                <td class="font-weight-bold font-table">$ {{ number_format($item->v_total_cliente) }}</td>
                                <td class="font-weight-bold font-table">
                                    @forelse ($item->proveedores as $proveedorItem)
                                    {{ $proveedorItem->tercero }} <br>
                                @empty
                                    {{ $item->proveedor_legacy }}
                                @endforelse
                                </td>
                                <td class="font-weight-bold font-table">{{ number_format(100 - ($item->margen_utilidad * 100), 2) }} %</td>
                                <td class="font-weight-bold font-table">{{ $item->mesDescription->description ?? '' }}</td>
                                <td class="font-weight-bold font-table">{{ $item->dias }}</td>
                                <td class="font-weight-bold font-table">{{ $item->ciudad }}</td>
                                @if ($rentabilidadView)
                                    <td class="font-weight-bold font-table">$ {{ number_format($item->rentabilidad) }}</td>
                                @endif
                                <td class="font-weight-bold">
                                    <div class="form-check">
                                        <input wire:change="changeDisponibilidad({{ $item->id }})" class="form-check-input" type="checkbox" @if ($item->disponible) checked @endif>
                                    </div>
                                </td>
                                <td class="font-weight-bold font-table">
                                    @if (!$presupuesto->cod_cc)
                                        <button wire:click="deleteItem({{ $item->id }})">✖️</button>
                                    @endif
                                </td>
                                <td class="font-weight-bold font-table">
                                    <button wire:click="getDataEdit({{ $item->id }})">📝</button>
                                </td>
                            </tr>
                        @endif
                    @endif
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="card card-frame p-3">
            <div class="row mt-2">
                @if (Auth::user()->rol == 2 || Auth::user()->rol == 5)
                    <div class="col-md-11 p-2">
                        <div class="row gy-0 mb-3" style="gap: 5px 0">
                            <div class="col-md-2">
                                <div class="form-group mb-0">
                                    <label for="cod" title="Código del tarifario">COD</label>
                                    <select type="number" class="form-control @error('cod') is-invalid @elseif(strlen($cod) > 0) is-valid @enderror"
                                            placeholder="Cod" required wire:model.lazy="cod">
                                        <option value="">Seleccionar</option>
                                        <option value="0">---- Sin tarifario ----</option>
                                        @foreach ($tarifario as $item)
                                            <option value="{{ $item->id }}" title="{{ $item->concepto }} {{ $item->caso }} - {{ number_format($item->v_unidad) }}">{{ $item->concepto }} {{ $item->caso }} - {{ number_format($item->v_unidad) }}</option>
                                        @endforeach
                                    </select>
                                    @error('cod')
                                    <div id="cod" class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group mb-0">
                                    <label for="cantidad" title="Unidades o personas">CANTIDAD</label>
                                    <input type="number" class="form-control @error('cantidad') is-invalid @elseif(strlen($cantidad) > 0) is-valid @enderror"
                                           placeholder="Cantidad" required wire:model.lazy="cantidad">
                                    @error('cantidad')
                                    <div id="cantidad" class="invalid-feedback">
                                        {!! $message !!}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group mb-0">
                                    <label for="dia">D&Iacute;A</label>
                                    <input type="number" class="form-control @error('dia') is-invalid @elseif(strlen($dia) > 0) is-valid @enderror"
                                           placeholder="D&iacute;a" required wire:model.lazy="dia">
                                    @error('dia')
                                    <div id="dia" class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group mb-0">
                                    <label for="otros">OTROS (MESES)</label>
                                    <input type="number" class="form-control @error('otros') is-invalid @elseif(strlen($otros) > 0) is-valid @enderror"
                                           placeholder="Otros" required wire:model.lazy="otros">
                                    @error('otros')
                                    <div id="otros" class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-0">
                                    <label for="descripcion">DESCRIPCI&Oacute;N</label>
                                    <textarea id="descripcion" cols="30" rows="1" class="form-control @error('descripcion') is-invalid @elseif(strlen($descripcion) > 0) is-valid @enderror"
                                              placeholder="Descripci&oacute;n" required wire:model.lazy="descripcion"></textarea>
                                    @error('descripcion')
                                    <div id="descripcion" class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group mb-0">
                                    <label for="valor_unitario">V. UNITARIO (INTERNO)</label>
                                    <input type="text" class="form-control @error('valor_unitario') is-invalid @elseif(strlen($valor_unitario) > 0) is-valid @enderror"
                                           placeholder="Valor unitario" required wire:model.lazy="valor_unitario" x-mask:dynamic="$money($input)">
                                    @error('valor_unitario')
                                    <div id="valor_unitario" class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group mb-0">
                                    <label for="valor_total">V. TOTAL (INTERNO)</label>
                                    <input type="text" class="form-control @error('valor_total') is-invalid @elseif(strlen($valor_total) > 0) is-valid @enderror"
                                           placeholder="Valor total" disabled required wire:model.lazy="valor_total" x-mask:dynamic="$money($input)">
                                    @error('valor_total')
                                    <div id="valor_total" class="invalid-feedback">
                                        {!! $message !!}
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-1">
                                <div class="form-group mb-0">
                                    <label for="valor_unitario_cliente" title="Precio unitario = V. unitario interno ÷ utilidad">V. UNITARIO CLIENTE (precio)</label>
                                    <input id="valor_unitario_cliente" type="text" class="form-control @error('valor_unitario_cliente') is-invalid @elseif(strlen($valor_unitario_cliente) > 0) is-valid @enderror"
                                           placeholder="Valor unitario cliente" required wire:model.lazy="valor_unitario_cliente" x-mask:dynamic="$money($input)">
                                    @error('valor_unitario_cliente')
                                    <div id="valor_unitario_cliente" class="invalid-feedback">
                                        {{ $message }}  
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-1">
                                <div class="form-group mb-0">
                                    <label for="valor_total_cliente">V. TOTAL CLIENTE</label>
                                    <input id="valor_total_cliente" type="text" class="form-control @error('valor_total_cliente') is-invalid @elseif(strlen($valor_total_cliente) > 0) is-valid @enderror"
                                           placeholder="Valor total cliente" disabled required wire:model.lazy="valor_total_cliente" x-mask:dynamic="$money($input)">
                                    @error('valor_total_cliente')
                                    <div id="valor_total_cliente" class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-1">
                                <div class="form-group mb-0">
                                    <label for="utilidad">UTILIDAD {{ is_numeric($utilidad) ? '(' . ($utilidad * 100) . '%)' : '(0%)' }}</label>
                                    <input id="utilidad" type="text" class="form-control @error('utilidad') is-invalid @elseif(strlen($utilidad) > 0) is-valid @enderror"
                                        placeholder="Utilidad" required wire:model.lazy="utilidad">
                                    @error('utilidad')
                                    <div id="utilidad_error" class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group mb-0">
                                    <label for="mes">MES</label>
                                    <select class="form-control @error('mes') is-invalid @elseif(strlen($mes) > 0) is-valid @enderror"
                                            placeholder="Mes" required wire:model.lazy="mes" required>
                                        <option value="">Seleccionar</option>
                                        @foreach ($meses as $mes)
                                            <option value="{{ $mes->id }}">{{ $mes->description }}</option>
                                        @endforeach
                                    </select>
                                    @error('mes')
                                    <div id="mes" class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group mb-0">
                                    <label for="dias">D&Iacute;AS</label>
                                    <input type="number" class="form-control @error('dias') is-invalid @elseif(strlen($dias) > 0) is-valid @enderror"
                                           placeholder="Dias" required wire:model.lazy="dias">
                                    @error('dias')
                                    <div id="dias" class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group mb-0">
                                    <label for="ciudad">CIUDAD</label>
                                    <select type="text" class="form-control @error('ciudad') is-invalid @elseif(strlen($ciudad) > 0) is-valid @enderror"
                                            placeholder="Ciudad" required wire:model.lazy="ciudad">
                                        <option selected value="">Seleccionar</option>
                                        @foreach ($ciudades as $ciudad)
                                            <option value="{{ $ciudad }}">{{ $ciudad }}</option>
                                        @endforeach
                                    </select>
                                    @error('ciudad')
                                    <div id="ciudad" class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-0">
                                    <label for="proveedor">PROVEEDOR</label>
                                    {{-- <input type="text" class="form-control @error('proveedor') is-invalid @elseif(strlen($proveedor) > 0) is-valid @enderror"
                                    placeholder="Proveedor" required wire:model.lazy="proveedor"> --}}
                                    <select class="form-control select-multiple" @error('proveedor') is-invalid @enderror
                                    placeholder="Proveedor" required wire:model.lazy="proveedor" multiple>
                                        @foreach ($categorias_proveedor as $categoria)
                                            <optgroup label="{{ $categoria->description }}">
                                                @foreach ($categoria->proveedores as $proveedor)
                                                    <option value="{{ $proveedor->id }}">{{ $proveedor->tercero }} - {{ $proveedor->categoria->description }}</option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                    @error('proveedor')
                                    <div id="proveedor" class="text-invalid">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-0">
                                    <label for="justificacion">JUSTIFICACI&Oacute;N</label>
                                    <textarea name="justificacion" @if(Auth::user()->espacioAdmin()) disabled @endif id="justificacion" cols="5" rows="2" class="form-control"
                                              wire:model="justificacion" class="form-control @error('justificacion') is-invalid @elseif(strlen($justificacion) > 0) is-valid @enderror"
                                              @if($presupuesto->cod_cc) placeholder="Explícale a compras tu presupuesto." @else placeholder="Si es necesario, explícale a compras tu presupuesto." @endif></textarea>
                                    @error('justificacion')
                                    <small id="justificacion" class="text-danger">
                                        {{ $message }}
                                    </small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-8 d-flex p-2">
                        <button wire:click="new_item" class="btn btn-icon btn-3 bg-gradient-warning mb-0 me-1" type="button">
                            <span class="btn-inner--icon"><i class="ni ni-fat-add"></i></span>
                            <span class="btn-inner--text">Item</span>
                        </button>

                        <button wire:click="new_event" class="btn btn-icon btn-3 bg-gradient-info mb-0 me-1" type="button">
                            <span class="btn-inner--icon"><i class="ni ni-fat-add"></i></span>
                            <span class="btn-inner--text">Evento</span>
                        </button>

                        <button wire:click="actionEdit()" class="btn btn-icon btn-3 bg-gradient-primary mb-0 me-1" type="button">
                            <span class="btn-inner--icon"><i class="ni ni-ruler-pencil"></i></span>
                            <span class="btn-inner--text">Editar</span>
                        </button>
                        <button class="btn btn-icon btn-3 bg-gradient-success mb-0 me-1" data-bs-toggle="modal" data-bs-target="#staticBackdrop" type="button">
                            <span class="btn-inner--icon"><i class="ni ni-single-copy-04"></i></span>
                            <span class="btn-inner--text">Exportar</span>
                        </button>

                        <!-- Modal -->
                        <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Exportar</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <h2 class="fs-5">Documentos Cliente</h2>
                                        <button wire:click="cotizacionPdf" class="btn btn-icon btn-3 bg-gradient-warning mb-0 me-1" type="button" data-bs-dismiss="modal">
                                            <span class="btn-inner--icon"><i class="ni ni-single-copy-04"></i></span>
                                            <span class="btn-inner--text">Cotizaci&oacute;n PDF</span>
                                        </button>

                                        <button wire:click="cotizacionExcel" class="btn btn-icon btn-3 bg-gradient-success mb-0 me-1" type="button" data-bs-dismiss="modal">
                                            <span class="btn-inner--icon"><i class="ni ni-single-copy-04"></i></span>
                                            <span class="btn-inner--text">Cotizaci&oacute;n Excel</span>
                                        </button>
                                        <hr class="horizontal dark">
                                        <h2 class="fs-5">Documentos Interno</h2>
                                        @if ($presupuesto->cod_cc)
                                            <button wire:click="internoPdf" class="btn btn-icon btn-3 bg-gradient-warning mb-0 me-1" type="button" data-bs-dismiss="modal">
                                                <span class="btn-inner--icon"><i class="ni ni-single-copy-04"></i></span>
                                                <span class="btn-inner--text">Interno PDF</span>
                                            </button>

                                            <button wire:click="internoExcel" class="btn btn-icon btn-3 bg-gradient-success mb-0 me-1" type="button" data-bs-dismiss="modal">
                                                <span class="btn-inner--icon"><i class="ni ni-single-copy-04"></i></span>
                                                <span class="btn-inner--text">Cotizaci&oacute;n Excel</span>
                                            </button>
                                        @endif
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-check form-switch me-1">
                            <input wire:click="toggelRentabilidad" class="form-check-input" type="checkbox" id="flexSwitchCheckDefault"
                                   @if ($rentabilidadView) checked @endif>
                            <label class="form-check-label" for="flexSwitchCheckDefault">Vista rentabilidad</label>
                        </div>
                    </div>

                    <div class="col-md-4 d-flex justify-content-end p-2"ñ>
                        <button class="btn btn-icon btn-3 bg-gradient-warning mb-0 me-1" type="button" data-bs-toggle="modal" data-bs-target="#exampleModal" wire:loading.attr="disabled">
                            <span class="btn-inner--icon"><i class="ni ni-check-bold"></i></span>
                            <span class="btn-inner--text">Enviar a aprobaci&oacute;n</span>
                        </button>

                        <!-- Modal -->
                        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h3 class="modal-title" id="exampleModalLabel">¿Estas seguro?</h3>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        Verifica que tu presupuesto est&eacute; completo antes de enviarlo.
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Cancelar</button>
                                        <button wire:click="aprobacion" type="button" class="btn bg-gradient-warning" data-bs-dismiss="modal">Enviar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif (Auth::user()->espacioAdmin())
                    @if ($estadoValidator == 2 && Auth::user()->revisaPresupuestos())
                        {{-- CONTROLLER --}}
                        {{-- <div class="col-md-12 p-2">
                            <div class="row gy-0">
                                <div class="col-md-3">
                                    <div class="form-group mb-0">
                                        <label for="centroCostos">CENTRO DE COSTOS</label>
                                        <select id="centroCostos"
                                                class="form-control @error('centroCostos') is-invalid @elseif(strlen($centroCostos) > 0) is-valid @enderror"
                                                required
                                                wire:model.lazy="centroCostos"
                                                @if($this->presupuesto && $this->presupuesto->cod_cc) disabled @endif>
                                            <option value="">-- Seleccione un Centro de Costos / Cliente --</option>
                                            @forelse($clientes ?? [] as $cliente)
                                                <option value="{{ $cliente->CodigoCliente }}">
                                                    ({{ $cliente->CodigoCliente }})
                                                </option>
                                            @empty
                                                <option value="" disabled>No hay clientes registrados en la base de datos</option>
                                            @endforelse
                                        </select>
                                        @error('centroCostos')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                        <button wire:click="updateCentro" wire:loading.attr="disabled" class="btn btn-icon btn-3 bg-gradient-warning mb-0 mt-2" type="button">
                                            <span class="btn-inner--icon" wire:loading.remove wire:target="updateCentro">
                                                <i class="ni ni-ruler-pencil"></i>
                                            </span>
                                                <span class="btn-inner--icon" wire:loading wire:target="updateCentro">
                                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                            </span>
                                                <span class="btn-inner--text">Guardar</span>
                                        </button>
                                    </div>

                                </div>
                                <div class="col-md-6 py-1">
                                    <div class="form-check form-switch me-1">
                                        <input wire:click="toggelRentabilidad" class="form-check-input" type="checkbox" id="flexSwitchCheckDefault">
                                        <label class="form-check-label" for="flexSwitchCheckDefault">Vista rentabilidad</label>
                                    </div>
                                    <button class="btn btn-icon btn-3 bg-gradient-success mb-0 me-1" data-bs-toggle="modal" data-bs-target="#staticBackdrop" type="button">
                                        <span class="btn-inner--icon"><i class="ni ni-single-copy-04"></i></span>
                                        <span class="btn-inner--text">Exportar</span>
                                    </button>

                                    <!-- Modal -->
                                    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Exportar</h1>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="modal-body">
                                                        <h2 class="fs-5">Documentos Cliente</h2>
                                                        <button wire:click="cotizacionPdf" class="btn btn-icon btn-3 bg-gradient-warning mb-0 me-1" type="button" data-bs-dismiss="modal">
                                                            <span class="btn-inner--icon"><i class="ni ni-single-copy-04"></i></span>
                                                            <span class="btn-inner--text">Cotizaci&oacute;n PDF</span>
                                                        </button>

                                                        <button wire:click="cotizacionExcel" class="btn btn-icon btn-3 bg-gradient-success mb-0 me-1" type="button" data-bs-dismiss="modal">
                                                            <span class="btn-inner--icon"><i class="ni ni-single-copy-04"></i></span>
                                                            <span class="btn-inner--text">Cotizaci&oacute;n Excel</span>
                                                        </button>
                                                        <hr class="horizontal dark">
                                                        <h2 class="fs-5">Documentos Interno</h2>
                                                        @if ($presupuesto->cod_cc)
                                                            <button wire:click="internoPdf" class="btn btn-icon btn-3 bg-gradient-warning mb-0 me-1" type="button" data-bs-dismiss="modal">
                                                                <span class="btn-inner--icon"><i class="ni ni-single-copy-04"></i></span>
                                                                <span class="btn-inner--text">Interno PDF</span>
                                                            </button>

                                                            <button wire:click="internoExcel" class="btn btn-icon btn-3 bg-gradient-success mb-0 me-1" type="button" data-bs-dismiss="modal">
                                                                <span class="btn-inner--icon"><i class="ni ni-single-copy-04"></i></span>
                                                                <span class="btn-inner--text">Cotizaci&oacute;n Excel</span>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> --}}
                    @elseif ($estadoValidator == 4 && in_array(Auth::user()->rol, [1, 12]))
                        {{-- LIDER COMERCIAL --}}
                        <div class="col-md-12 p-2">
                            <div class="row gy-0">
                                <div class="col-md-3">


                                    <div class="col-md-7">
                                        <div class="form-group mb-0">
                                            <!-- Selector apuntando a clientes_parametro_cc -->
                                            <label for="clienteSeleccionado">CLIENTE</label>
                                            <select id="clienteSeleccionado"
                                                    class="form-control @error('centroCostos') is-invalid @enderror"
                                                    required
                                                    wire:model.live="clienteSeleccionado"
                                                    @if($this->presupuesto && $this->presupuesto->cod_cc) disabled @endif>
                                                <option value="">-- Seleccione un Cliente / CC --</option>
                                                @forelse($clientesParametros ?? [] as $parametro)
                                                    <option value="{{ $parametro->id }}">
                                                        ({{ $parametro->codigo_cc }}) {{ $parametro->nombre_empresa ?? $parametro->cliente ?? '' }}
                                                    </option>
                                                @empty
                                                    <option value="" disabled>No hay parámetros de CC registrados</option>
                                                @endforelse
                                            </select>

                                            <!-- Input enlazado a la propiedad almacenada $centroCostos -->
                                            <div class="mt-2">
                                                <label class="small font-weight-bold text-muted">CENTRO DE COSTOS</label>
                                                <input type="text"
                                                       class="form-control @error('centroCostos') is-invalid @elseif(strlen($centroCostos) > 0) is-valid @enderror"
                                                       wire:model="centroCostos"
                                                       value="{{ $centroCostos }}"
                                                       readonly
                                                       style="background-color: #e9ecef; font-weight: bold; font-family: monospace;">
                                            </div>

                                            @error('centroCostos')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                            @enderror

                                            <!-- Botón de Aprobación -->
                                            <button wire:click="updateCentro" wire:loading.attr="disabled" class="btn btn-icon btn-3 bg-gradient-warning mb-0 mt-3" type="button">
                                                <span class="btn-inner--icon" wire:loading.remove wire:target="updateCentro">
                                                    <i class="ni ni-ruler-pencil"></i>
                                                </span>
                                                <span class="btn-inner--icon" wire:loading wire:target="updateCentro">
                                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                </span>
                                                <span class="btn-inner--text">Aprobar</span>
                                            </button>
                                        </div>
                                    </div>


                                </div>
                                {{-- <div class="col-md-3">
                                    <button wire:click="validacionLiderComercial" wire:loading.attr="disabled" class="btn btn-icon btn-3 bg-gradient-warning mb-0 mt-1" type="button">
                                        <span class="btn-inner--icon"><i class="ni ni-ruler-pencil"></i></span>
                                        <span class="btn-inner--text">Aprobar</span>
                                    </button>
                                </div> --}}
                                <div class="col-md-6 d-flex align-items-center justify-content-start pb-2">
                                    <div class="form-check form-switch p-0 me-1">
                                        <button class="btn btn-icon btn-3 bg-gradient-success mb-0 me-1" data-bs-toggle="modal" data-bs-target="#staticBackdrop" type="button">
                                            <span class="btn-inner--icon"><i class="ni ni-single-copy-04"></i></span>
                                            <span class="btn-inner--text">Exportar</span>
                                        </button>
                                        <!-- Modal -->
                                        <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Exportar</h1>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="modal-body">
                                                            <h2 class="fs-5">Documentos Cliente</h2>
                                                            <button wire:click="cotizacionPdf" class="btn btn-icon btn-3 bg-gradient-warning mb-0 me-1" type="button" data-bs-dismiss="modal">
                                                                <span class="btn-inner--icon"><i class="ni ni-single-copy-04"></i></span>
                                                                <span class="btn-inner--text">Cotizaci&oacute;n PDF</span>
                                                            </button>

                                                            <button wire:click="cotizacionExcel" class="btn btn-icon btn-3 bg-gradient-success mb-0 me-1" type="button" data-bs-dismiss="modal">
                                                                <span class="btn-inner--icon"><i class="ni ni-single-copy-04"></i></span>
                                                                <span class="btn-inner--text">Cotizaci&oacute;n Excel</span>
                                                            </button>
                                                            <hr class="horizontal dark">
                                                            <h2 class="fs-5">Documentos Interno</h2>
                                                            @if ($presupuesto->cod_cc)
                                                                <button wire:click="internoPdf" class="btn btn-icon btn-3 bg-gradient-warning mb-0 me-1" type="button" data-bs-dismiss="modal">
                                                                    <span class="btn-inner--icon"><i class="ni ni-single-copy-04"></i></span>
                                                                    <span class="btn-inner--text">Interno PDF</span>
                                                                </button>

                                                                <button wire:click="internoExcel" class="btn btn-icon btn-3 bg-gradient-success mb-0 me-1" type="button" data-bs-dismiss="modal">
                                                                    <span class="btn-inner--icon"><i class="ni ni-single-copy-04"></i></span>
                                                                    <span class="btn-inner--text">Cotizaci&oacute;n Excel</span>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-check form-switch me-1">
                                            <input wire:click="toggelRentabilidad" class="form-check-input" type="checkbox" id="flexSwitchCheckDefault">
                                            <label class="form-check-label" for="flexSwitchCheckDefault">Vista rentabilidad</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif ($estadoValidator == 5 && Auth::user()->can('aprobar-presupuestos'))
                        {{-- GERENCIA --}}
                        <div class="col-md-12 p-2">
                            <div class="row gy-0">
                                <div class="col-md-3">
                                    <button wire:click="validacionGerencia" wire:loading.attr="disabled" class="btn btn-icon btn-3 bg-gradient-warning mb-0 mt-1" type="button">
                                        <span class="btn-inner--icon"><i class="ni ni-ruler-pencil"></i></span>
                                        <span class="btn-inner--text">Aprobar</span>
                                    </button>
                                </div>
                                <div class="col-md-6 py-1">
                                    <div class="form-check form-switch me-1">
                                        <div class="form-check form-switch me-1">
                                            <input wire:click="toggelRentabilidad" class="form-check-input" type="checkbox" id="flexSwitchCheckDefault">
                                            <label class="form-check-label" for="flexSwitchCheckDefault">Vista rentabilidad</label>
                                        </div>
                                        <button class="btn btn-icon btn-3 bg-gradient-success mb-0 me-1" data-bs-toggle="modal" data-bs-target="#staticBackdrop" type="button">
                                            <span class="btn-inner--icon"><i class="ni ni-single-copy-04"></i></span>
                                            <span class="btn-inner--text">Exportar</span>
                                        </button>

                                        <!-- Modal -->
                                        <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Exportar</h1>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="modal-body">
                                                            <h2 class="fs-5">Documentos Cliente</h2>
                                                            <button wire:click="cotizacionPdf" class="btn btn-icon btn-3 bg-gradient-warning mb-0 me-1" type="button" data-bs-dismiss="modal">
                                                                <span class="btn-inner--icon"><i class="ni ni-single-copy-04"></i></span>
                                                                <span class="btn-inner--text">Cotizaci&oacute;n PDF</span>
                                                            </button>

                                                            <button wire:click="cotizacionExcel" class="btn btn-icon btn-3 bg-gradient-success mb-0 me-1" type="button" data-bs-dismiss="modal">
                                                                <span class="btn-inner--icon"><i class="ni ni-single-copy-04"></i></span>
                                                                <span class="btn-inner--text">Cotizaci&oacute;n Excel</span>
                                                            </button>
                                                            <hr class="horizontal dark">
                                                            <h2 class="fs-5">Documentos Interno</h2>
                                                            @if ($presupuesto->cod_cc)
                                                                <button wire:click="internoPdf" class="btn btn-icon btn-3 bg-gradient-warning mb-0 me-1" type="button" data-bs-dismiss="modal">
                                                                    <span class="btn-inner--icon"><i class="ni ni-single-copy-04"></i></span>
                                                                    <span class="btn-inner--text">Interno PDF</span>
                                                                </button>

                                                                <button wire:click="internoExcel" class="btn btn-icon btn-3 bg-gradient-success mb-0 me-1" type="button" data-bs-dismiss="modal">
                                                                    <span class="btn-inner--icon"><i class="ni ni-single-copy-04"></i></span>
                                                                    <span class="btn-inner--text">Cotizaci&oacute;n Excel</span>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="col-md-12 py-1 d-flex justify-content-center">
                        @if ($presupuesto && $presupuesto->cod_cc)
                            <!-- Botón Activo cuando hay Centro de Costos -->
                            <button wire:click="exportarFijo" wire:loading.attr="disabled" class="btn btn-icon btn-3 bg-gradient-success mb-0 me-1" type="button">
                                <span class="btn-inner--icon" wire:loading.remove wire:target="exportarFijo">
                                    <i class="ni ni-cloud-download-95"></i>
                                </span>
                                <!-- Spinner de carga de Livewire mientras se genera el Excel con todas las hojas -->
                                <span class="btn-inner--icon" wire:loading wire:target="exportarFijo">
                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                </span>
                                <span class="btn-inner--text">Exportar Historial</span>
                            </button>
                        @else
                            <!-- Botón Deshabilitado si falta el Centro de Costos -->
                            <button class="btn btn-icon btn-3 bg-gradient-secondary mb-0 me-1" type="button" disabled title="Debe guardar el Centro de Costos primero para habilitar el historial">
                                <span class="btn-inner--icon"><i class="ni ni-cloud-download-95"></i></span>
                                <span class="btn-inner--text">Exportar Historial</span>
                            </button>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    @elseif($estadoValidator == 2 || $estadoValidator == 4 || $estadoValidator == 5)
        <div class="card card-frame p-5">
            <h3 class="text-center">
                Tu presupuesto est&aacute; siendo validado por
                @switch ($estadoValidator)
                    @case (2)
                        {{ 'el equipo de controller.' }}
                        @break
                    @case (4)
                        {{ 'el Lider Comercial.' }}
                        @break
                    @case (5)
                        {{ 'Gerencia.' }}
                        @break
                @endswitch
            </h3>
            <div class="d-flex justify-content-center">
                <div class="spinner-grow text-primary" role="status">
                    <span class="sr-only"></span>
                </div>
                <div class="spinner-grow text-success" role="status">
                    <span class="sr-only"></span>
                </div>
                <div class="spinner-grow text-warning" role="status">
                    <span class="sr-only"></span>
                </div>
                <div class="spinner-grow text-info" role="status">
                    <span class="sr-only"></span>
                </div>
            </div>
        </div>
    @endif

        <style>
            #sortable-body {
                -webkit-user-select: none;
                -moz-user-select: none;
                -ms-user-select: none;
                user-select: none;
            }

            /* El handle específicamente, por si acaso el problema es solo ahí */
            .drag-handle {
                cursor: move;
                -webkit-user-select: none;
                -moz-user-select: none;
                -ms-user-select: none;
                user-select: none;
                -webkit-touch-callout: none; /* iOS Safari */
            }

            /* Evita el "ghost" de imagen/texto al arrastrar en Firefox/Safari */
            .drag-handle-row {
                -webkit-user-drag: none;
            }
        </style>
</div>
