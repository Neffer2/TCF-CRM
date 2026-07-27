<div class="card card-body mb-3">
    <!-- ENCABEZADO -->
    <div class="row mb-3 border-bottom pb-2">
        <div class="col-md-12">
            <h4 class="m-0 text-primary font-weight-bold">
                Anticipo Colaborador @if($queriedOrden) #{{ $queriedOrden->id }} @endif
            </h4>
            <p class="text-sm text-muted m-0">Selecciona el productor para cargar los proyectos asignados y generar la Orden de Compra.</p>
        </div>
    </div>

    <!-- PASO 1: PRODUCTOR Y PROYECTO -->
    <div class="row bg-light p-3 rounded mb-3">
        <div class="col-md-6 mb-2">
            <div class="form-group">
                <label for="productor_id" class="form-label font-weight-bold text-xs">
                    1. Productor Encargado: <span class="text-danger">*</span>
                </label>
                <select id="productor_id" class="form-control form-control-sm text-dark" wire:model="productor_id">
                    <option value="" class="text-secondary">-- Seleccionar Productor --</option>
                    @foreach ($productores as $prod)
                        <option value="{{ $prod->id }}" class="text-dark bg-white" style="color: #000 !important;">
                            {{ $prod->nombre ?? $prod->name }} {{ $prod->apellido ?? $prod->last_name }}
                        </option>
                    @endforeach
                </select>
                @error('productor_id') <span class="text-danger text-xs">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="col-md-6 mb-2">
            <div class="form-group">
                <label for="presupuesto" class="form-label font-weight-bold text-xs">
                    2. Proyecto / Centro de Costos: <span class="text-danger">*</span>
                </label>
                <select id="presupuesto" class="form-control form-control-sm" wire:model="presupuesto" @if(empty($proyectos_productor) || count($proyectos_productor) == 0) disabled @endif>
                    <option value="">
                        {{ empty($productor_id) ? '-- Primero selecciona un Productor --' : '-- Seleccionar Proyecto --' }}
                    </option>
                    @foreach ($proyectos_productor as $proj)
                        <option value="{{ $proj->id }}">{{ $proj->cod_cc }} - {{ $proj->nombre }}</option>
                    @endforeach
                </select>
                @error('presupuesto') <span class="text-danger text-xs">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>

    <!-- PASO 2: AGREGAR ÍTEMS DEL PROYECTO -->
    <div class="border-top pt-3">
        <h6 class="text-xs font-weight-bold text-uppercase text-secondary mb-2">Ítems del Proyecto</h6>
        <div class="row">
            <div class="col-md-5 mb-2">
                <div class="form-group">
                    <label for="item_presupuesto" class="form-label font-weight-bold text-xs">Ítem Presupuesto:</label>
                    <select id="item_presupuesto" class="form-control form-control-sm" wire:model="item_presupuesto" @if(empty($items_presupuesto) || count($items_presupuesto) == 0) disabled @endif>
                        <option value="">-- Seleccionar Ítem --</option>
                        @foreach ($items_presupuesto as $item_p)
                            <option value="{{ $item_p->id }}">
                                {{ $item_p->descripcion }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-2 mb-2">
                <div class="form-group">
                    <label class="form-label font-weight-bold text-xs">Cantidad</label>
                    <input type="number" class="form-control form-control-sm" wire:model.defer="cantidad" placeholder="0">
                </div>
            </div>
            <div class="col-md-3 mb-2">
                <div class="form-group">
                    <label class="form-label font-weight-bold text-xs">Valor Unitario</label>
                    <input type="text" class="form-control form-control-sm" wire:model.defer="valor_unitario" placeholder="$ 0">
                </div>
            </div>
            <div class="col-md-2 mb-2 d-flex align-items-end">
                <button type="button" wire:click="newItem" class="btn btn-sm bg-gradient-primary w-100 mb-0">
                    <i class="fas fa-plus me-1"></i> Agregar
                </button>
            </div>
        </div>
    </div>

    <!-- TABLA DE ÍTEMS -->
    <div class="row font-table py-2">
        <div class="col-md-12 table-responsive">
            <table class="table table-bordered table-sm align-items-center mb-0">
                <thead>
                <tr>
                    <th class="bg-gradient-primary text-white text-center text-xs">#</th>
                    <th class="bg-gradient-primary text-white text-xs">PROYECTO / C.C</th>
                    <th class="bg-gradient-primary text-white text-xs">ÍTEM</th>
                    <th class="bg-gradient-primary text-white text-xs text-center">CANT</th>
                    <th class="bg-gradient-primary text-white text-xs text-end">V. UNITARIO</th>
                    <th class="bg-gradient-primary text-white text-xs text-end">V. TOTAL</th>
                    <th class="bg-gradient-primary text-white text-xs text-center">ACCIONES</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($items as $index => $item)
                    <tr>
                        <td class="text-center text-xs">{{ $index + 1 }}</td>
                        <td class="text-xs">{{ $item['cod_cc'] }} - {{ $item['nombre_cc'] }}</td>
                        <td class="text-xs">{{ $item['item_nombre'] }}</td>
                        <td class="text-center text-xs">{{ $item['cantidad'] }}</td>
                        <td class="text-end text-xs">${{ number_format($item['valor_unitario'], 2) }}</td>
                        <td class="text-end text-xs font-weight-bold">${{ number_format($item['valor_total'], 2) }}</td>
                        <td class="text-center">
                            <button class="btn btn-link text-danger p-0 me-1" wire:click="deleteItem({{ $index }})">✖️</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted text-xs py-2">No se han agregado ítems.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- PASO 3: COMERCIAL Y GENERAR OC -->
    <div class="border-top pt-3 mt-3 bg-light p-3 rounded">
        <h6 class="text-xs font-weight-bold text-uppercase text-secondary mb-3">
            3. Datos Comerciales para la Orden de Compra
        </h6>
        <div class="row">
            <div class="col-md-4 mb-2">
                <div class="form-group">
                    <label for="comercial_encargado" class="form-label font-weight-bold text-xs">Comercial Asignado: <span class="text-danger">*</span></label>
                    <input id="comercial_encargado" type="text" class="form-control form-control-sm" wire:model.defer="comercial_encargado" placeholder="Nombre del comercial">
                    @error('comercial_encargado') <span class="text-danger text-xs">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="col-md-4 mb-2">
                <div class="form-group">
                    <label for="concepto_oc" class="form-label font-weight-bold text-xs">Concepto / Asunto OC: <span class="text-danger">*</span></label>
                    <input id="concepto_oc" type="text" class="form-control form-control-sm" wire:model.defer="concepto_oc" placeholder="Ej: Anticipo para producción">
                    @error('concepto_oc') <span class="text-danger text-xs">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="col-md-4 mb-2">
                <div class="form-group">
                    <label for="observaciones_comercial" class="form-label font-weight-bold text-xs">Observaciones:</label>
                    <textarea id="observaciones_comercial" class="form-control form-control-sm" wire:model.defer="observaciones_comercial" rows="1" placeholder="Notas adicionales..."></textarea>
                </div>
            </div>
        </div>

        <div class="row mt-2">
            <div class="col-md-12 text-end">
                @if(!$orden_id)
                    <button type="button" wire:click="uploadOC" class="btn bg-gradient-warning text-white mb-0">
                        <i class="fas fa-file-invoice me-1"></i> GENERAR ORDEN DE COMPRA
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
