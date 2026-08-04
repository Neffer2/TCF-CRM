<div class="card card-body">
    <!-- MENSAGES REACTIVOS -->
    @if ($successMessage || session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show text-white mb-3" role="alert">
            <i class="fas fa-check-circle me-1"></i> {{ $successMessage ?? session('success') }}
            <button type="button" class="btn-close text-white" data-bs-dismiss="alert" aria-label="Close" wire:click="$set('successMessage', null)">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if ($errorMessage)
        <div class="alert alert-danger alert-dismissible fade show text-white mb-3" role="alert">
            <i class="fas fa-exclamation-circle me-1"></i> {{ $errorMessage }}
            <button type="button" class="btn-close text-white" data-bs-dismiss="alert" aria-label="Close" wire:click="$set('errorMessage', null)">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (!$ordenAnticipo)
        <!-- BÚSQUEDA Y LISTADO INICIAL DE ANTICIPOS -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h5 class="mb-0 text-primary"><i class="fas fa-file-invoice-dollar me-2"></i> Legalización de Anticipos por Ítem</h5>
                <p class="text-xs text-secondary mb-0">Selecciona un anticipo para legalizar sus ítems individualmente.</p>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6 col-lg-4">
                <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="fas fa-search text-xs"></i></span>
                    <input type="text"
                           class="form-control form-control-sm"
                           placeholder="Buscar por centro de costos (CC)..."
                           wire:model.live.debounce.300ms="searchCC">

                    @if(!empty($searchCC))
                        <button class="btn btn-outline-secondary btn-xs mb-0 px-2"
                                type="button"
                                wire:click="$set('searchCC', '')"
                                title="Limpiar filtro">
                            <i class="fas fa-times"></i>
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table align-items-center table-sm mb-0">
                <thead class="bg-light">
                <tr>
                    <th class="text-xs font-weight-bolder">Centro de costos</th>
                    <th class="text-xs font-weight-bolder">Productor</th>
                    <th class="text-xs font-weight-bolder">Monto Anticipado</th>
                    <th class="text-xs font-weight-bolder text-end">Valor legalizado</th>
                    <th class="text-xs font-weight-bolder text-center">Acción</th>

                </tr>
                </thead>
                <tbody>
                @forelse ($anticiposPendientes as $item)
                    <tr>
                        <td class="text-xs font-weight-bold">{{ $item->presupuesto->cod_cc }}</td>
                        <td class="text-xs">{{$item->presupuesto->productor_info->name}}</td>
                        <td class="text-xs font-weight-bold">${{ number_format($item->ordenItems->sum('vtotal_oc'), 2) }}</td>
                        <td class="text-xs text-end font-weight-bold">${{ number_format($item->ordenItems->sum('monto_legalizado'), 2) }}</td>
                        @if($item->ordenItems->sum('vtotal_oc') == $item->ordenItems->sum('monto_legalizado'))
                            <td class="text-center">
                                <button type="button" class="btn btn-xs bg-gradient-primary mb-0" wire:click="cargarAnticipo({{ $item->id }})">
                                    Ver legalización
                                </button>
                            </td>
                        @else
                            <td class="text-center">
                                <button type="button" class="btn btn-xs bg-gradient-primary mb-0" wire:click="cargarAnticipo({{ $item->id }})">
                                    Legalizar Ítems
                                </button>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-xs text-muted py-3">No hay anticipos pendientes.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

    @else
        <!-- FORMULARIO Y TABLA DE LEGALIZACIÓN ÍTEM POR ÍTEM -->
        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
            <div>
                <h5 class="mb-0 text-primary"><i class="fas fa-tasks me-2"></i> Legalizando Ítems de la OC #{{ $ordenAnticipo->id }}</h5>
                <span class="badge badge-sm bg-gradient-info">
                    {{ $ordenAnticipo->naturalInfo->tercero->nombre ?? '' }} {{ $ordenAnticipo->naturalInfo->tercero->apellido ?? '' }}
                </span>
            </div>
            <button type="button" class="btn btn-sm btn-outline-secondary mb-0" wire:click="resetSeleccion">
                <i class="fas fa-arrow-left me-1"></i> Volver a la lista
            </button>
        </div>

        <!-- RESUMEN GLOBAL DE LA OC -->
        <div class="row bg-light p-3 rounded-2 mb-4 align-items-center">
            <div class="col-md-4">
                <p class="text-xs font-weight-bold mb-1 text-secondary">Total Entregado (Anticipo):</p>
                <p class="text-sm font-weight-bold text-dark mb-0">
                    $ {{ number_format($totalAnticipado, 2, ',', '.') }} <small class="text-xxs text-muted">COP</small>
                </p>
            </div>
            <div class="col-md-4">
                <p class="text-xs font-weight-bold mb-1 text-secondary">Total Gastado (Suma Ítems):</p>
                <p class="text-sm font-weight-bold text-primary mb-0">
                    $ {{ number_format($totalGastado, 2, ',', '.') }} <small class="text-xxs text-muted">COP</small>
                </p>
            </div>
            <div class="col-md-4">
                <p class="text-xs font-weight-bold mb-1 text-secondary">Diferencia Global:</p>
                <p class="text-sm font-weight-bold mb-0 @if($diferenciaGlobal > 0) text-danger @elseif($diferenciaGlobal < 0) text-success @else text-dark @endif">
                    $ {{ number_format($diferenciaGlobal, 2, ',', '.') }} <small class="text-xxs text-muted">COP</small>
                </p>
            </div>
        </div>

        <!-- LISTADO DE ÍTEMS PARA LEGALIZACIÓN INDIVIDUAL -->
        <div class="table-responsive">
            <table class="table table-bordered align-items-center mb-0">
                <thead class="bg-secondary text-white">
                <tr>
                    <th class="text-xs" style="width: 25%;">ÍTEM / DESCRIPCIÓN</th>
                    <th class="text-xs text-end" style="width: 15%;">VALOR APROBADO</th>
                    <th class="text-xs text-end" style="width: 20%;">VALOR GASTADO ($)</th>
                    <th class="text-xs" style="width: 25%;">SOPORTE Y OBSERVACIÓN</th>
                    <th class="text-xs text-center" style="width: 15%;">ACCIÓN</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($itemsLegalizacion as $itemId => $item)
                    @php
                        $esLegalizado = ($item['estado_item'] === 'Legalizado');
                    @endphp
                    <tr class="align-middle @if($esLegalizado) table-light @endif">
                        <!-- Descripción e Info del Ítem -->
                        <td>
                            <p class="text-xs font-weight-bold mb-1">{{ $item['desc_oc'] }}</p>
                            <span class="badge badge-sm @if($esLegalizado) bg-gradient-success @else bg-gradient-warning @endif">
                    <i class="@if($esLegalizado) fas fa-check-circle @else fas fa-clock @endif me-1"></i>
                    {{ $item['estado_item'] }}
                </span>
                        </td>

                        <!-- Valor Aprobado Inicial -->
                        <td class="text-end font-weight-bold text-xs">
                            ${{ number_format($item['vtotal_oc'], 2) }}
                        </td>

                        <!-- Valor Gastado / Legalizado -->
                        <td class="align-middle">
                            @if($esLegalizado)
                                <!-- SI YA ESTÁ LEGALIZADO: Muestra el valor formateado con separador de miles colombiano -->
                                <div class="p-2 bg-light rounded text-end font-weight-bold text-xs text-dark border">
                                    $ {{ number_format($item['monto_gastado'], 2, ',', '.') }} COP
                                </div>
                            @else
                                <!-- SI ESTÁ PENDIENTE: Input numérico con símbolo $ en Input Group -->
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light text-xs font-weight-bold">$</span>
                                    <input type="number"
                                           step="0.01"
                                           min="0"
                                           placeholder="0.00"
                                           class="form-control form-control-sm text-end font-weight-bold @error('itemsLegalizacion.'.$itemId.'.monto_gastado') is-invalid @enderror"
                                           wire:model.lazy="itemsLegalizacion.{{ $itemId }}.monto_gastado">
                                </div>
                            @endif

                            @error('itemsLegalizacion.'.$itemId.'.monto_gastado')
                            <span class="text-xxs text-danger font-weight-bold d-block mt-1">{{ $message }}</span>
                            @enderror
                        </td>

                        <!-- Soporte y Observación -->
                        <td>
                            <!-- Adjuntar Archivo -->
                            @if(!$esLegalizado)
                                <input type="file"
                                       class="form-control form-control-sm mb-1 @error('soportesItems.'.$itemId) is-invalid @enderror"
                                       wire:model="soportesItems.{{ $itemId }}"
                                       accept=".pdf,.png,.jpg,.jpeg">

                                @error('soportesItems.'.$itemId)
                                <span class="text-xxs text-danger font-weight-bold d-block mb-1">{{ $message }}</span>
                                @enderror
                            @endif

                            <!-- Soporte Guardado previamente -->
                            @if (!empty($item['archivo_soporte']))
                                <div class="mb-1">
                                    <a href="{{ Storage::url($item['archivo_soporte']) }}" target="_blank" class="text-xxs text-primary font-weight-bold">
                                        <i class="fas fa-paperclip me-1"></i> Ver Soporte Adjunto
                                    </a>
                                </div>
                            @endif

                            <!-- Campo Observación -->
                            <textarea class="form-control form-control-sm @error('itemsLegalizacion.'.$itemId.'.observacion') is-invalid @enderror"
                                      rows="2"
                                      placeholder="Observación o detalle del gasto..."
                                      wire:model.defer="itemsLegalizacion.{{ $itemId }}.observacion"
                                      @if($esLegalizado) disabled @endif></textarea>

                            @error('itemsLegalizacion.'.$itemId.'.observacion')
                            <span class="text-xxs text-danger font-weight-bold d-block mt-1">{{ $message }}</span>
                            @enderror
                        </td>

                        <!-- Botón Acciones -->
                        <td class="text-center">
                            @if($esLegalizado)
                                <button type="button" class="btn btn-xs bg-gradient-secondary mb-0" disabled title="Este ítem ya se encuentra legalizado">
                                    <i class="fas fa-lock me-1"></i> Completado
                                </button>
                            @else
                                <button type="button" class="btn btn-xs bg-gradient-primary mb-0"
                                        wire:click="guardarLegalizacionItem({{ $itemId }})"
                                        wire:loading.attr="disabled"
                                        wire:target="guardarLegalizacionItem({{ $itemId }}), soportesItems.{{ $itemId }}">
                        <span wire:loading.remove wire:target="guardarLegalizacionItem({{ $itemId }})">
                            <i class="fas fa-save me-1"></i> Legalizar
                        </span>
                                    <span wire:loading wire:target="guardarLegalizacionItem({{ $itemId }})">
                            <i class="fas fa-spinner fa-spin me-1"></i> Guardando...
                        </span>
                                </button>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
