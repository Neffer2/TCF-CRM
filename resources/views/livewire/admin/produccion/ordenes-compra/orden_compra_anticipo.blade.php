<div class="card card-body mb-3">
    <!-- MENSAJES DE ALERTA REACTIVOS -->
    @if ($successMessage || session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show text-white mb-3" role="alert">
            <i class="fas fa-check-circle me-1"></i> {{ $successMessage ?? session('success') }}
            <button type="button" class="btn-close text-white" data-bs-dismiss="alert" aria-label="Close" wire:click="$set('successMessage', null)">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if ($errorMessage || session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show text-white mb-3" role="alert">
            <i class="fas fa-exclamation-circle me-1"></i> {{ $errorMessage ?? session('error') }}
            <button type="button" class="btn-close text-white" data-bs-dismiss="alert" aria-label="Close" wire:click="$set('errorMessage', null)">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    <!-- ENCABEZADO -->
    <div class="row mb-3 border-bottom pb-2">
        <div class="col-md-12 d-flex justify-content-between align-items-center">
            <div>
                <h4 class="m-0 text-primary font-weight-bold">
                    @if($modo === 'ver')
                        Detalle de Orden de Compra #{{ $orden_id }}
                    @else
                        Nuevo Anticipo Colaborador
                    @endif
                </h4>
                <p class="text-sm text-muted m-0">
                    {{ $modo === 'ver' ? 'Consulta la información de la orden generada.' : 'Selecciona el productor para cargar los proyectos asignados.' }}
                </p>
            </div>

            @if($modo === 'ver')
                <button type="button" wire:click="resetearFormulario" class="btn btn-sm btn-outline-secondary mb-0">
                    <i class="fas fa-plus me-1"></i> Crear Nueva Orden
                </button>
            @endif
        </div>
    </div>

    <!-- PASO 1: PRODUCTOR Y PROYECTO -->
    <div class="row bg-light p-3 rounded mb-3">

        <!-- SELECT PRODUCTOR -->
        <div class="col-md-3">
            <label class="text-xxs font-weight-bold text-secondary mb-1">Productor:</label>
            <select class="form-select form-select-sm" wire:model="filtro_productor_id">
                <option value="">-- Seleccionar Productor --</option>
                @foreach ($productores as $prod)
                    <option value="{{ $prod->id }}">{{ $prod->name ?? $prod->nombre }}</option>
                @endforeach
            </select>
        </div>

        <!-- SELECT AÑO -->
        <div class="col-md-2">
            <label class="text-xxs font-weight-bold text-secondary mb-1">Año:</label>
            <select class="form-select form-select-sm" wire:model="filtro_anio_id">
                <option value="">-- Año --</option>
                @foreach ($catalogo_anos as $a)
                    <option value="{{ $a->id }}">{{ $a->description }}</option>
                @endforeach
            </select>
        </div>

        <!-- SELECT MES -->
        <div class="col-md-2">
            <label class="text-xxs font-weight-bold text-secondary mb-1">Mes:</label>
            <select class="form-select form-select-sm" wire:model="filtro_mes">
                <option value="">-- Mes --</option>
                @foreach ($catalogo_meses as $m)
                    <option value="{{ $m->id }}">{{ $m->description ?? $m->nombre }}</option>
                @endforeach
            </select>
        </div>

        <!-- SELECT PROYECTO (APROBADO) -->
        <div class="col-md-5">
            <label class="text-xxs font-weight-bold text-secondary mb-1">Proyecto (Aprobados):</label>

            @php
                $proyectos_productor = $this->proyectosFiltrados;
            @endphp

            <select class="form-select form-select-sm"
                    wire:model="filtro_presupuesto_id"
                    @if(!$filtro_productor_id || $proyectos_productor->isEmpty()) disabled @endif>

                @if(!$filtro_productor_id)
                    <option value="">-- Seleccione un Productor primero --</option>
                @elseif($proyectos_productor->isEmpty())
                    <option value="">-- Sin proyectos aprobados en este periodo --</option>
                @else
                    <option value="">-- Seleccione un Proyecto --</option>
                    @foreach ($proyectos_productor as $proyecto)
                        <option value="{{ $proyecto->id }}">
                            {{ $proyecto->cod_cc }}
                        </option>
                    @endforeach
                @endif
            </select>
        </div>

    </div>

    <!-- PASO 2: AGREGAR ÍTEMS DEL PROYECTO -->
    <div class="border-top pt-3">
        <h6 class="text-xs font-weight-bold text-uppercase text-secondary mb-2">Ítems del Proyecto</h6>
        <div class="row">
            <div class="col-md-4 mb-2">
                <div class="form-group">
                    <label for="item_presupuesto" class="form-label font-weight-bold text-xs">Ítem Presupuesto:</label>
                    <select id="item_presupuesto" class="form-control form-control-sm" wire:model.live="item_presupuesto" @if(empty($items_presupuesto) || count($items_presupuesto) == 0) disabled @endif>
                        <option value="">-- Seleccionar Ítem --</option>
                        @foreach ($items_presupuesto ?? [] as $item_p)
                            <option value="{{ $item_p->id }}">
                                {{ $item_p->descripcion ?? $item_p->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('item_presupuesto') <span class="text-danger text-xxs">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- CANTIDAD -->
            <div class="col-md-2 mb-2">
                <div class="form-group">
                    <label class="form-label font-weight-bold text-xs">Cantidad</label>
                    <input type="number" step="any" class="form-control form-control-sm" wire:model.live="cantidad" placeholder="0">
                    @error('cantidad') <span class="text-danger text-xxs">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- VALOR UNITARIO -->
            <div class="col-md-2 mb-2">
                <div class="form-group">
                    <label class="form-label font-weight-bold text-xs">Valor Unitario</label>
                    <input type="number" step="any" class="form-control form-control-sm" wire:model.live="valor_unitario" placeholder="$ 0">
                    @error('valor_unitario') <span class="text-danger text-xxs">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- dia -->
            <div class="col-md-2 mb-2">
                <div class="form-group">
                    <label class="form-label font-weight-bold text-xs">Días</label>
                    <input type="number" step="any" class="form-control form-control-sm" wire:model.live="dia" placeholder="0">
                    @error('dia') <span class="text-danger text-xxs">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- VISTA PREVIA TOTAL CALCULADO -->
            <div class="col-md-2 mb-2">
                <div class="form-group">
                    <label class="form-label font-weight-bold text-xs text-primary">Valor Total (Previo)</label>
                    <input type="text" class="form-control form-control-sm bg-light font-weight-bold text-primary"
                           value="${{ number_format($this->valorTotalPreview, 2) }}" readonly>
                </div>
            </div>

            <!-- BOTÓN AGREGAR -->
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
                @forelse ($items ?? [] as $index => $item)
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
            3. Datos del productor para la Orden de Compra
        </h6>
        <div class="row">
            <div class="col-md-4 mb-2">
                <div class="form-group">
                    <label for="comercial_encargado" class="form-label font-weight-bold text-xs">Productor Asignado: <span class="text-danger">*</span></label>
                    <input id="comercial_encargado" type="text" class="form-control form-control-sm" wire:model.defer="comercial_encargado" placeholder="Nombre del productor">
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
                @if($modo === 'nuevo')
                    <button type="button" wire:click="uploadOC" class="btn bg-gradient-warning text-white mb-0">
                        <i class="fas fa-file-invoice me-1"></i> GENERAR ORDEN DE COMPRA
                    </button>
                @else
                    <span class="badge bg-gradient-success p-2">
                <i class="fas fa-check-circle me-1"></i> ORDEN #{{ $orden_id }} APROBADA
            </span>
                @endif
            </div>
        </div>
    </div>
</div>
