<div class="card card-body mb-2" x-data>
    <div class="row">
        <div class="col-md-12 mb-3">
            <h3 class="m-0">Orden de compra Empleados</h3>
            <p class="text-sm m-0">Selecciona tu personal y asigna los items nesesarios.</p>
        </div>
    </div>
    <div class="row font-table py-2">
        <div class="col-md-12 table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th class="font-weight-bold bg-gradient-primary text-white text-center">#</th>
                        <th class="font-weight-bold bg-gradient-primary text-white">PROYECTO</th>
                        <th class="font-weight-bold bg-gradient-primary text-white">CENTRO DE COSTOS</th>
                        <th class="font-weight-bold bg-gradient-primary text-white">ITEM</th>
                        <th class="font-weight-bold bg-gradient-primary text-white">NUM ITEM</th>
                        <th class="font-weight-bold bg-gradient-primary text-white">CANT</th>
                        <th class="font-weight-bold bg-gradient-primary text-white">DIAS</th>
                        <th class="font-weight-bold bg-gradient-primary text-white">OTROS</th>
                        <th class="font-weight-bold bg-gradient-primary text-white">V. UNI</th>
                        <th class="font-weight-bold bg-gradient-primary text-white">V. TOTAL</th>
                        <th class="font-weight-bold bg-gradient-primary text-white">SERVICIO</th>
                        <th class="font-weight-bold bg-gradient-primary text-white">CONTRATO</th>
                        @if ((!$queriedOrden) || ($queriedOrden && $queriedOrden->estado_id == 3))
                            <th colspan="2" class="font-weight-bold bg-gradient-primary text-white text-center">ACCIONES</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $key => $item)
                        <tr>
                            <td class="text-center">{{ $key + 1 }}</td>
                            <td>{{ $item['proyecto']['nombre'] }}</td>
                            <td>{{ $item['proyecto']['cod_cc'] }}</td>
                            <td>{{ $item['item']['nombre'] }}</td>
                            <td>{{ $item['item']['display_item'] }}</td>
                            <td>{{ $item['cant'] }}</td>
                            <td>{{ $item['dias'] }}</td>
                            <td>{{ $item['otros'] }}</td>
                            <td>{{ number_format($item['valor_unitario']) }}</td>
                            <td>{{ number_format($item['valor_total']) }}</td>
                            <td>{{ $item['tipo_servicio'] }}</td>
                            <td>{{ $item['tipo_contrato'] }}</td>
                            @if ((!$queriedOrden) || ($queriedOrden && $queriedOrden->estado_id == 3))
                                <td class="d-flex justify-content-center" style="padding: 11px;">
                                    <button class="me-2 btn btn-sm btn-link text-danger p-0" wire:click="deleteItem({{ $key }})" title="Eliminar">
                                        ✖️
                                    </button>
                                    <button class="btn btn-sm btn-link text-primary p-0" wire:click="getItem({{ $key }})" title="Editar">
                                        📝
                                    </button>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- FORMULARIO DE INGRESO DE ÍTEMS -->
    @if ((!$queriedOrden) || ($queriedOrden && $queriedOrden->estado_id == 3))
        <div class="row">
            <div class="col-lg-2">
                <div class="form-group">
                    <label for="proyecto">Proyecto</label>
                    <select id="proyecto" class="form-control" wire:model.change="presupuesto">
                        <option value="">Seleccionar</option>
                        @foreach ($presupuestos as $presupuestoItem)
                            <option value="{{ $presupuestoItem->id }}">{{ $presupuestoItem->cod_cc }}</option>
                        @endforeach
                    </select>
                    @error('presupuesto')
                        <div id="invalid-presupuesto" class="text-invalid text-danger small">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <div class="col-lg-3">
                <div class="form-group">
                    <label for="item_presupuesto">Item</label>
                    <select id="item_presupuesto" class="form-control" wire:model.change="item_presupuesto">
                        <option value="">Seleccionar</option>
                        @foreach ($items_presupuesto as $ip)
                            <option value="{{ $ip->id }}">{{ $ip->descripcion }} - ITEM {{ $ip->num_item }}</option>
                        @endforeach
                    </select>
                    @error('item_presupuesto')
                        <div id="invalid-item_presupuesto" class="text-invalid text-danger small">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <div class="col-lg-1">
                <div class="form-group">
                    <label for="cantidad">Cantidad</label>
                    <input id="cantidad" type="number" class="form-control" wire:model.lazy="cantidad" placeholder="#">
                    @error('cantidad')
                        <div id="invalid-cantidad" class="text-invalid text-danger small">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <div class="col-lg-1">
                <div class="form-group">
                    <label for="dias">Dias</label>
                    <input id="dias" type="number" class="form-control" wire:model.lazy="dias" placeholder="Dias" disabled>
                    @error('dias')
                        <div id="invalid-dias" class="text-invalid text-danger small">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <div class="col-lg-1">
                <div class="form-group">
                    <label for="otros">Otro</label>
                    <input id="otros" type="number" class="form-control" wire:model.lazy="otros" placeholder="Otro" disabled>
                    @error('otros')
                        <div id="invalid-otros" class="text-invalid text-danger small">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <div class="col-lg-2">
                <div class="form-group">
                    <label for="valor_unitario">Valor unitario</label>
                    <input id="valor_unitario" type="text" class="form-control" wire:model.lazy="valor_unitario" placeholder="$">
                    @error('valor_unitario')
                        <div id="invalid-valor_unitario" class="text-invalid text-danger small">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <div class="col-lg-2">
                <div class="form-group">
                    <label for="valor_total">Valor Total</label>
                    <input id="valor_total" type="text" class="form-control" wire:model.lazy="valor_total" placeholder="$" disabled>
                    @error('valor_total')
                        <div id="invalid-valor_total" class="text-invalid text-danger small">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            

            <div class="col-lg-2">
                <div class="form-group">
                    <label for="tipo_contrato">Tipo contrato</label>
                    <select id="tipo_contrato" class="form-control" wire:model.change="tipo_contrato">
                        <option value="">Seleccionar</option>
                        <option value="Contrato de prestación de servicios">Contrato de prestación de servicios</option>
                    </select>
                    @error('tipo_contrato')
                        <div id="invalid-tipo_contrato" class="text-invalid text-danger small">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            @error('items-error')
                <div class="col-12 text-danger small m-0">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- BOTÓN AGREGAR / EDITAR ÍTEM -->
        <div class="row mt-2">
            <div class="col-lg-2">
                <div class="form-group">
                    @if (is_null($selected_item))
                        <button wire:click="newItem" x-on:mouseover="event.target.style.transform = 'rotate(360deg)'" x-on:mouseleave="event.target.style.transform = 'rotate(0deg)'"
                        class="btn avatar border-1 rounded-circle bg-gradient-primary" style="box-shadow: none;" title="Agregar Ítem">
                            <i class="fas fa-plus text-white"></i>
                        </button>
                    @else
                        <button wire:click="actionEdit" x-on:mouseover="event.target.style.transform = 'rotate(360deg)'" x-on:mouseleave="event.target.style.transform = 'rotate(0deg)'"
                        class="btn avatar border-1 rounded-circle bg-gradient-primary" style="box-shadow: none;" title="Actualizar Ítem">
                            <i class="fa-solid fa-pen-to-square text-white"></i>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- BOTÓN FINAL DE GUARDAR / ACTUALIZAR ORDEN -->
    <div class="row">
        <div class="col-md-6">
            @if(!$queriedOrden)
                <button wire:click="uploadOC" class="btn bg-gradient-warning mt-2 mb-0">GENERAR ORDEN DE COMPRA</button>
            @else
                <button wire:click="updateOC" class="btn bg-gradient-warning mt-2 mb-0">ACTUALIZAR ORDEN DE COMPRA</button>
            @endif
        </div>
    </div>
</div>