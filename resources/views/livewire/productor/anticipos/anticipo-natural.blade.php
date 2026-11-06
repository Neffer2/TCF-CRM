<div class="card {{ $anticipo_id ? 'p-3' : '' }}">
    <div class="card-header text-center font-weight-bold bg-gradient-info text-white p-0">
        SOLICITUD ANTICIPO NATURAL
    </div>

    <div class="row font-table px-4">
        <div class="col-md-6 mt-3">
            <div class="table-responsive">
                <table class="table mb-1">
                    @php $pres = $orden?->presupuesto_resuelto; @endphp
                    <tr style="height: 35px;">
                        <td class="font-weight-bold">Cliente:</td>
                        <td>{{ $pres?->gestion?->contacto?->empresa }}</td>
                    </tr>
                    <tr style="height: 35px;">
                        <td class="font-weight-bold">Proyecto:</td>
                        <td>{{ $pres?->gestion?->nom_proyecto_cot }}</td>
                    </tr>
                    <tr style="height: 35px;">
                        <td class="font-weight-bold">Centro de Costos:</td>
                        <td>{{ $pres?->cod_cc }}</td>
                    </tr>
                    <tr style="height: 35px;">
                        <td class="font-weight-bold">Ciudad:</td>
                        <td>{{ $pres?->presupuestoItems?->first()?->ciudad }}</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="col-md-6 mt-3">
            <div class="form-group m-0">
                <label><b>Proveedor:</b> {{ $orden?->proveedor?->tercero }}</label>
                <textarea class="form-control" disabled>{{ $orden?->observaciones_negociacion }}</textarea>
            </div>
        </div>
    </div>

    <div class="row font-table px-4">
        <div class="col-md-12">
            <div class="card card-body table-responsive mb-3 rounded bg-whitem p-0">
                <table class="table">
                    <thead>
                    <tr>
                        @foreach (['No. ITEM', 'CANT', 'DIAS', 'OTROS', 'DESCRIPCIÓN', 'V. UNI', 'V. TOTAL'] as $th)
                            <th class="font-weight-bold bg-gradient-info text-white">{{ $th }}</th>
                        @endforeach
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($orden?->ordenItems ?? [] as $item)
                        <tr>
                            <td class="text-center">{{ $item->num_item }}</td>
                            <td class="text-center">{{ $item->cant_oc }}</td>
                            <td class="text-center">{{ $item->dias_oc }}</td>
                            <td class="text-center">{{ $item->otros_oc }}</td>
                            <td><textarea disabled cols="30" rows="1">{{ $item->desc_oc }}</textarea></td>
                            <td class="text-center">{{ number_format($item->vunit_oc) }}</td>
                            <td class="text-center">{{ number_format($item->vtotal_oc) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row px-4">
        <div class="col-md-12">
            <div class="card card-body">
                @if (Auth::user()->rol == 7 && ! $queriedAnticipo)
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="orden_compra_natural">Orden de compra:</label>
                                <select id="orden_compra_natural" class="form-control" wire:model="orden_compra">
                                    <option value="">Seleccione una orden de compra</option>
                                    @foreach ($ordenes as $oc)
                                        <option value="{{ $oc->id }}">
                                            {{ $oc->cod_oc ?? 'OC #' . $oc->id }} | {{ $oc->presupuesto_resuelto?->cod_cc }} - {{ $oc->naturalInfo?->tercero?->tercero ?? $oc->proveedor?->tercero }}
                                        </option>
                                    @endforeach
                                </select>
                                @if ($ordenes->isEmpty())
                                    <small class="text-muted">No se encontraron órdenes disponibles.</small>
                                @endif
                                @error('orden_compra') <div class="text-invalid">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="tipo_oc_anticipo">Tipo de anticipo:</label>
                                <select id="tipo_oc_anticipo" class="form-control" wire:model="tipo_oc">
                                    @foreach ($tipos as $tipo)
                                        <option value="{{ $tipo->id }}">{{ $tipo->description }}</option>
                                    @endforeach
                                </select>
                                @error('tipo_oc') <div class="text-invalid">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Valor Orden</label>
                                <input class="form-control" disabled value="{{ $orden ? number_format($totalOrden) : '' }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="porcentaje_anticipo_natural">% Anticipo:</label>
                                <input type="number" id="porcentaje_anticipo_natural" class="form-control"
                                       wire:model="porcentaje_anticipo" min="1" max="100" step="1">
                                @error('porcentaje_anticipo') <div class="text-invalid">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Total:</label>
                                <input type="text" class="form-control" disabled
                                       value="{{ $total_anticipo ? number_format($total_anticipo) : '' }}">
                            </div>
                        </div>
                    </div>
                    <button class="btn bg-gradient-info" wire:click="nuevoAnticipoNatural"
                            wire:loading.attr="disabled">Crear Anticipo</button>

                @elseif (Auth::user()->rol == 1 && $queriedAnticipo)
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Orden de compra:</label>
                                <input type="text" class="form-control" disabled
                                       value="{{ $orden?->cod_oc ?? 'OC #' . $orden?->id }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Valor Orden</label>
                                <input class="form-control" disabled value="{{ number_format($totalOrden) }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="porcentaje_anticipo_natural">% Anticipo:</label>
                                <input type="number" id="porcentaje_anticipo_natural" class="form-control"
                                       wire:model="porcentaje_anticipo" min="1" max="100" step="1">
                                @error('porcentaje_anticipo') <div class="text-invalid">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Total:</label>
                                <input type="text" class="form-control" disabled
                                       value="{{ $total_anticipo ? number_format($total_anticipo) : '' }}">
                            </div>
                        </div>
                    </div>
                    <button class="btn bg-gradient-info" wire:click="actualizarAnticipoNatural">Aprobar</button>
                @endif
            </div>
        </div>
    </div>
</div>