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
    @if (!$ordenAnticipo)
        <!-- PASO 1: SELECCIÓN / BÚSQUEDA DE ANTICIPO -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h5 class="mb-0 text-primary"><i class="fas fa-file-invoice-dollar me-2"></i> Módulo de Legalización</h5>
                <p class="text-xs text-secondary mb-0">Selecciona un anticipo registrado para ingresar sus gastos y soportes.</p>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="buscar_anticipo" class="form-label text-xs font-weight-bold">Buscar por ID de Orden o Tercero:</label>
                <div class="input-group">
                    <input type="text" id="buscar_anticipo" class="form-control" placeholder="Ingrese ID de anticipo..." wire:model.defer="anticipo_id">
                    <button class="btn btn-outline-primary mb-0" type="button" wire:click="cargarAnticipo(anticipo_id)">
                        <i class="fas fa-search me-1"></i> Cargar
                    </button>
                </div>
            </div>
        </div>

        <!-- Tabla rápida de anticipos recientes -->
        <div class="table-responsive mt-2">
            <table class="table align-items-center table-sm mb-0">
                <thead class="bg-light">
                <tr>
                    <th class="text-xs font-weight-bolder">ID OC</th>
                    <th class="text-xs font-weight-bolder">Tercero / Colaborador</th>
                    <th class="text-xs font-weight-bolder">Fecha</th>
                    <th class="text-xs font-weight-bolder text-end">Monto Anticipado</th>
                    <th class="text-xs font-weight-bolder text-center">Acción</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($anticiposPendientes as $item)
                    <tr>
                        <td class="text-xs font-weight-bold">#{{ $item->id }}</td>
                        <td class="text-xs">
                            {{ $item->naturalInfo->tercero->nombre ?? '' }} {{ $item->naturalInfo->tercero->apellido ?? 'N/A' }}
                        </td>
                        <td class="text-xs">{{ $item->created_at->format('Y-m-d') }}</td>
                        <td class="text-xs text-end font-weight-bold">${{ number_format($item->ordenItems->sum('vtotal_oc'), 2) }}</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-xs bg-gradient-primary mb-0" wire:click="cargarAnticipo({{ $item->id }})">
                                Legalizar
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-xs text-muted py-3">No hay anticipos pendientes por legalizar.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    @else
        <!-- PASO 2: FORMULARIO DE ACTUALIZACIÓN / LEGALIZACIÓN -->
        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
            <div>
                <h5 class="mb-0 text-primary">
                    <i class="fas fa-edit me-2"></i> Legalizando Anticipo #{{ $ordenAnticipo->id }}
                </h5>
                <span class="badge badge-sm bg-gradient-info">
                    {{ $ordenAnticipo->naturalInfo->tercero->nombre ?? '' }} {{ $ordenAnticipo->naturalInfo->tercero->apellido ?? '' }}
                </span>
            </div>
            <button type="button" class="btn btn-sm btn-outline-secondary mb-0" wire:click="resetSeleccion">
                <i class="fas fa-arrow-left me-1"></i> Cambiar Anticipo
            </button>
        </div>

        <!-- RESUMEN DEL ANTICIPO REGISTRADO -->
        <div class="row bg-light p-3 rounded-2 mb-4">
            <div class="col-md-3">
                <p class="text-xs font-weight-bold mb-1 text-secondary">Centro de Costos:</p>
                <p class="text-sm font-weight-bold mb-0">{{ $ordenAnticipo->presupuesto->cod_cc ?? 'N/A' }}</p>
            </div>
            <div class="col-md-3">
                <p class="text-xs font-weight-bold mb-1 text-secondary">Fecha Emisión:</p>
                <p class="text-sm mb-0">{{ $ordenAnticipo->created_at->format('Y-m-d') }}</p>
            </div>
            <div class="col-md-3">
                <p class="text-xs font-weight-bold mb-1 text-secondary">Monto Entregado (Anticipo):</p>
                <p class="text-sm font-weight-bold text-success mb-0">${{ number_format($ordenAnticipo->ordenItems->sum('vtotal_oc'), 2) }}</p>
            </div>
            <div class="col-md-3">
                <p class="text-xs font-weight-bold mb-1 text-secondary">Estado Actual:</p>
                <span class="badge badge-sm bg-gradient-warning">{{ $ordenAnticipo->estado_oc->description ?? 'Pendiente' }}</span>
            </div>
        </div>

        <!-- FORMULARIO DE CAPTURA DE DATOS -->
        <form wire:submit.prevent="guardarLegalizacion">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label text-xs font-weight-bold">Valor Total Legalizado / Gastado ($):</label>
                    <input type="number" step="0.01" class="form-control" wire:model="monto_gastado">
                    @error('monto_gastado') <span class="text-danger text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label text-xs font-weight-bold">Diferencia (Calculada):</label>
                    <input type="text" class="form-control font-weight-bold @if($diferencia > 0) text-danger @elseif($diferencia < 0) text-success @else text-dark @endif"
                           value="${{ number_format($diferencia, 2) }}" readonly>
                    <small class="text-xxs text-muted">
                        @if($diferencia > 0)
                            (El productor gastó más de lo entregado)
                        @elseif($diferencia < 0)
                            (El productor debe reintegrar dinero)
                        @else
                            (Saldo exacto)
                        @endif
                    </small>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label text-xs font-weight-bold">Soporte / Factura (PDF/Imagen):</label>
                    <input type="file" class="form-control" wire:model="soporte_archivo" accept=".pdf,.png,.jpg,.jpeg">
                    @error('soporte_archivo')
                    <span class="text-danger text-xs">{{ $message }}</span>
                    @enderror

                    <!-- Si ya existe un archivo previo guardado en la orden -->
                    @if ($ordenAnticipo->archivo_cot)
                        <div class="mt-1">
                            <a href="{{ Storage::url($ordenAnticipo->archivo_cot) }}" target="_blank" class="text-xs text-primary">
                                <i class="fas fa-paperclip me-1"></i> Ver soporte adjunto actual
                            </a>
                        </div>
                    @endif
                </div>

                <div class="col-md-12 mb-3">
                    <label class="form-label text-xs font-weight-bold">Observaciones / Concepto de gastos:</label>
                    <textarea class="form-control" rows="3" wire:model="observaciones" placeholder="Detalle de legalización o novedades..."></textarea>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-3">
                <button type="button" class="btn btn-light mb-0" wire:click="resetSeleccion">Cancelar</button>
                <button type="submit" class="btn bg-gradient-primary mb-0">
                    <i class="fas fa-save me-1"></i> Guardar Legalización
                </button>
            </div>
        </form>

        <!-- DESGLOSE DE ÍTEMS DE LA OC -->
        <div class="mb-4">
            <h6 class="text-xs font-weight-bold text-secondary mb-2">
                <i class="fas fa-list me-1"></i> Ítems que componen el anticipo (#{{ $ordenAnticipo->id }}):
            </h6>
            <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0">
                    <thead class="bg-light">
                    <tr>
                        <th class="text-xs">Descripción</th>
                        <th class="text-xs text-center">Cant.</th>
                        <th class="text-xs text-center">Días</th>
                        <th class="text-xs text-end">V. Unitario</th>
                        <th class="text-xs text-end">V. Total</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($ordenAnticipo->ordenItems as $ocItem)
                        <tr>
                            <td class="text-xs">{{ $ocItem->desc_oc }}</td>
                            <td class="text-xs text-center">{{ $ocItem->cant_oc }}</td>
                            <td class="text-xs text-center">{{ $ocItem->dias_oc ?? 1 }}</td>
                            <td class="text-xs text-end">${{ number_format($ocItem->vunit_oc, 2) }}</td>
                            <td class="text-xs text-end font-weight-bold">${{ number_format($ocItem->vtotal_oc, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-xs text-muted">Esta orden no tiene ítems registrados.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
