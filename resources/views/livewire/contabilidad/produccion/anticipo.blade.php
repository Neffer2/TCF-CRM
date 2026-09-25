@php
    // Anticipo de productor (sin orden de compra): se causa sobre el presupuesto, los ítems, la firma y las evidencias
    $pesos = function ($v) { return '$'.number_format((float) $v, 0, ',', '.'); };
    $fecha = function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('d/m/Y') : '—'; };
    $archivo = function ($ruta) { return $ruta ? asset('storage/'.preg_replace('#^public/#', '', $ruta)) : null; };
    $items = $anticipo->anticipoItems()->with('itemPresupuesto')->get();
    $evidencias = $anticipo->evidencias()->with('itemPresupuesto')->get();
    $proyecto = $anticipo->presupuesto;
    $gestion = optional($proyecto)->gestion;
@endphp
<div class="card">
    <div class="card-header p-0 px-3 mt-3 position-relative z-index-1 col-md-12">
        <h3 class="mb-0">Causaci&oacute;n &middot; Anticipo de productor #{{ $anticipo->id }}</h3>
        <p class="text-sm mb-0">
            {{ optional($proyecto)->cod_cc ?: 'Sin centro de costos' }} &middot; {{ optional($gestion)->nom_proyecto_cot ?: 'Proyecto sin nombre' }}
            &middot; Productor: {{ optional($anticipo->productor_info)->name ?: '—' }} &middot; Estado: {{ optional($anticipo->estado)->description }}
        </p>
    </div>
    <div class="card-body pt-2">
        <div class="row">
            <div class="col-md-5">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <h6 style="margin: 0">Valor del anticipo:</h6>
                            <input type="text" class="form-control" value="{{ $pesos($anticipo->total_anticipo) }}" disabled>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <h6 style="margin: 0">Solicitado el:</h6>
                            <input type="text" class="form-control" value="{{ $fecha($anticipo->fecha_solicitud) }}" disabled>
                        </div>
                    </div>
                </div>
                @if ($anticipo->observaciones_revision_lider)
                    <p class="text-sm mb-1"><b>L&iacute;der de producci&oacute;n:</b> {{ $anticipo->observaciones_revision_lider }}</p>
                @endif
                @if ($anticipo->observaciones_revision_gerencia)
                    <p class="text-sm mb-1"><b>Gerencia:</b> {{ $anticipo->observaciones_revision_gerencia }}</p>
                @endif
                @if ($anticipo->observaciones_revision_evidencias)
                    <p class="text-sm mb-1"><b>Evidencias aprobadas:</b> {{ $anticipo->observaciones_revision_evidencias }}</p>
                @endif
                @if ($anticipo->cod_causal)
                    <p class="text-sm mb-2"><b>Ya causado</b> con el n&uacute;mero {{ $anticipo->cod_causal }} el {{ $fecha($anticipo->fecha_causal) }}.</p>
                @else
                    <div class="form-group">
                        <h6 style="margin: 0">N&uacute;mero de causaci&oacute;n:</h6>
                        <input id="causa_cod" wire:model="causa_cod" type="text" class="form-control">
                        @error('causa_cod')
                            <div class="text-invalid">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <h6 style="margin: 0">Observaciones (opcional al causar, obligatoria al devolver):</h6>
                        <div style="font-size: 9px;">Si lo devuelves, el productor recibe este motivo y vuelve a enviar las evidencias.</div>
                        <textarea id="observacion_causacion" wire:model="observacion_causacion" class="form-control"></textarea>
                        @error('observacion_causacion')
                            <div class="text-invalid">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-12 p-0">
                        <button wire:loading.attr="disabled" class="btn bg-gradient-success" data-bs-toggle="modal" data-bs-target="#causado">Marcar como causado</button>
                        <button type="button" class="btn bg-gradient-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">Devolver al productor</button>

                        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="deleteModalLabel">Devolver el anticipo</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">¿Devolver este anticipo al productor con la observaci&oacute;n escrita?</div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn bg-gradient-danger" data-bs-dismiss="modal" wire:click="rechazar">Devolver</button>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="causado" tabindex="-1" aria-labelledby="causadoLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="causadoLabel">Marcar como causado</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">¿Marcar como causado este anticipo por {{ $pesos($anticipo->total_anticipo) }}? Pasa a Tesorer&iacute;a para el pago.</div>
                                    <div class="modal-footer">
                                        <button wire:click="store" wire:loading.attr="disabled" data-bs-dismiss="modal" class="btn bg-gradient-success">Marcar como causado</button>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="spinner-border text-warning ms-1" role="status" wire:loading>
                            <span class="sr-only">Cargando...</span>
                        </div>
                        @error('error')
                            <div class="text-invalid">{{ $message }}</div>
                        @enderror
                    </div>
                @endif
            </div>
            <div class="col-md-7">
                <h6>Documentos</h6>
                <p class="text-sm mb-2">
                    @if ($anticipo->firma_productor)
                        <a href="{{ $archivo($anticipo->firma_productor) }}" target="_blank"><span class="btn-inner--icon"><i class="ni ni-single-copy-04"></i></span> Firma del productor</a>
                    @else
                        Sin firma registrada.
                    @endif
                </p>
                <h6>Evidencias ({{ $evidencias->count() }})</h6>
                @if ($evidencias->isEmpty())
                    <p class="text-sm text-secondary">Sin evidencias cargadas.</p>
                @else
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">&Iacute;tem</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Fecha</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Foto</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Observaci&oacute;n</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($evidencias as $e)
                                    <tr>
                                        <td class="text-xs">{{ optional($e->itemPresupuesto)->descripcion }}</td>
                                        <td class="text-xs">{{ $fecha($e->fecha_evidencia) }}</td>
                                        <td><a href="{{ $archivo($e->foto_evidencia) }}" target="_blank"><img src="{{ $archivo($e->foto_evidencia) }}" height="48" alt=""></a></td>
                                        <td class="text-xs">{{ $e->observacion_evidencia }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
            <div class="col-12 mt-3">
                <h6>&Iacute;tems del anticipo</h6>
                <div class="table-responsive">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">&Iacute;tem</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Descripci&oacute;n</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-end">Cantidad</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-end">V. unitario</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-end">V. total</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-end">Anticipo</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-end">Saldo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $it)
                                <tr>
                                    <td class="text-xs">{{ $it->display_item ?: optional($it->itemPresupuesto)->num_item }}</td>
                                    <td class="text-xs">{{ $it->desc }}</td>
                                    <td class="text-xs text-end">{{ $it->cant }}</td>
                                    <td class="text-xs text-end">{{ $pesos($it->vunit) }}</td>
                                    <td class="text-xs text-end">{{ $pesos($it->vtotal) }}</td>
                                    <td class="text-xs text-end font-weight-bold">{{ $pesos($it->vanticipo) }}</td>
                                    <td class="text-xs text-end">{{ $pesos($it->saldo) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="5" class="text-end text-xs">Total del anticipo</th>
                                <th class="text-end text-xs">{{ $pesos($anticipo->total_anticipo) }}</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
