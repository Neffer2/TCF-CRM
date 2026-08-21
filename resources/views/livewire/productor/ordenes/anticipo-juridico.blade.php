<div class="card {{ $anticipo_id ? 'p-3' : '' }}">
    <div class="card-header text-center font-weight-bold bg-gradient-primary text-white p-0">
        SOLICITUD ANTICIPO JURÍDICO
    </div>
    <div class="row font-table px-4">
        <div class="col-md-6 mt-3">
            <div class="table-responsive">
                <table class="table mb-1">
                    <tr style="height: 35px;">
                        <td class="font-weight-bold">Cliente:</td>
                        @if ($orden)
                            <td>{{ $orden->presupuesto->gestion->contacto->empresa }}</td>
                        @endif
                    </tr>
                    <tr style="height: 35px;">
                        <td class="font-weight-bold">Proyecto:</td>
                        @if ($orden)
                            <td>{{ $orden->presupuesto->gestion->nom_proyecto_cot }}</td>
                        @endif
                    </tr>
                    <tr style="height: 35px;">
                        <td class="font-weight-bold">Centro de Costos:</td>
                        @if ($orden)
                            <td>{{ $orden->presupuesto->cod_cc }}</td>
                        @endif
                    </tr>
                    <tr style="height: 35px;">
                        <td class="font-weight-bold">Ciudad:</td>
                        @if ($orden)
                            <td>{{ $orden->presupuesto->presupuestoItems[0]->ciudad }}</td>
                        @endif
                    </tr>
                </table>
            </div>
        </div>
        <div class="col-md-6 mt-3">
            <div class="table-responsive">
                <table class="table mb-1">
                    <tr>
                        <td>
                            <div class="form-group m-0">
                                <label for="proveedor"><b>Proveedor:</b>
                                    @if ($orden)
                                        {{ $orden->proveedor->tercero }}
                                    @endif
                                </label>
                                <textarea class="form-control" disabled>@if ($orden) {{ $orden->proveedor->OrdenCompra->last()->observaciones_negociacion }} @endif</textarea>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="row font-table px-4">
        <div class="col-md-12">
            <div class="card card-body table-responsive mb-3 rounded bg-whitem p-0">
                <table class="table">
                    <thead>
                    <tr>
                        <th class="font-weight-bold bg-gradient-primary text-white">No. ITEM</th>
                        <th class="font-weight-bold bg-gradient-primary text-white">CANT</th>
                        <th class="font-weight-bold bg-gradient-primary text-white">DIAS</th>
                        <th class="font-weight-bold bg-gradient-primary text-white">OTROS</th>
                        <th class="font-weight-bold bg-gradient-primary text-white">CARACTERISTICAS</th>
                        <th class="font-weight-bold bg-gradient-primary text-white">V. UNI</th>
                        <th class="font-weight-bold bg-gradient-primary text-white">V. TOTAL</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if ($orden)
                        @foreach ($orden->ordenItems as $item)
                            <tr>
                                <td class="text-center">{{ $item->display_item }}</td>
                                <td class="text-center">{{ $item->cant_oc }}</td>
                                <td class="text-center">{{ $item->dias_oc }}</td>
                                <td class="text-center">{{ $item->otros_oc }}</td>
                                <td>
                                    <textarea disabled cols="30" rows="1">{{ $item->desc_oc }}</textarea>
                                </td>
                                <td class="text-center">{{ number_format($item->vunit_oc) }}</td>
                                <td class="text-center">{{ number_format($item->vtotal_oc) }}</td>
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="row px-4">
        <div class="col-md-12">
            <div class="card card-body">
                {{-- Gestión por parte del productor --}}
                @if (Auth::user()->rol == 7)
                    @if (! $queriedAnticipo)
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="observaciones">Orden de compra:</label>
                                    <select name="orden_compra" id="orden_compra" class="form-control" wire:model="orden_compra">
                                        <option value="">Seleccione una orden de compra</option>
                                        @foreach ($ordenes as $orden)
                                            <option value="{{ $orden->id }}">{{ $orden->presupuesto->cod_cc }} - {{ $orden->proveedor->tercero }} </option>
                                        @endforeach
                                    </select>
                                    @error('orden_compra')
                                    <div id="orden_compra" class="text-invalid">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="observaciones">Valor Orden</label>
                                    <input class="form-control" disabled @if($orden_compra) value="{{ number_format($ordenes->find($orden_compra)->ordenItems->sum('vtotal_oc')) }}" @endif>
                                    @error('observaciones')
                                    <div id="observaciones" class="text-invalid">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="observaciones">% Anticipo:</label>
                                    <input type="number" name="porcentaje_anticipo" id="porcentaje_anticipo" class="form-control" wire:model="porcentaje_anticipo" min="0" max="100" step="1">
                                    @error('porcentaje_anticipo')
                                    <div id="porcentaje_anticipo" class="text-invalid">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="total_anticipo">Total:</label>
                                    <input type="text" class="form-control" wire:model="total_anticipo" disabled x-mask:dynamic="$money($input)">
                                    @error('total_anticipo')
                                    <div id="total_anticipo" class="text-invalid">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <button class="btn bg-gradient-primary" wire:click="nuevoAnticipoJuridico">Crear Anticipo</button>
                            </div>
                        </div>
                    @endif
                @elseif (Auth::user()->rol == 1)
                    <div class="row px-4">
                        <div class="col-md-12">
                            <div class="card card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="observaciones">Orden de compra:</label>
                                            <input type="text" class="form-control" wire:model="orden_compra" disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="valor_orden">Valor Orden</label>
                                            <input class="form-control" disabled value="{{ number_format($orden->ordenItems->sum('vtotal_oc')) }}" x-mask:dynamic="$money($input)">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="observaciones">% Anticipo:</label>
                                            <input type="number" name="porcentaje_anticipo" id="porcentaje_anticipo" class="form-control" wire:model="porcentaje_anticipo" min="0" max="100" step="1">
                                            @error('porcentaje_anticipo')
                                            <div id="porcentaje_anticipo" class="text-invalid">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="total_anticipo">Total:</label>
                                            <input type="text" class="form-control" wire:model="total_anticipo" disabled x-mask:dynamic="$money($input)">
                                            @error('total_anticipo')
                                            <div id="total_anticipo" class="text-invalid">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <button class="btn bg-gradient-primary" wire:click="actualizarAnticipoJuridico">Aprobar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
