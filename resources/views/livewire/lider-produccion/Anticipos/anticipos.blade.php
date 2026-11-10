<div class="card">
    <!-- Mensajes de Alerta -->
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show mx-3 mt-3 text-white" role="alert">
            <span class="alert-text">{{ session('message') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Card Header: Título y Filtro de Búsqueda -->
    <div class="card-header p-0 px-3 mt-3">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h3 class="mb-0">Lista de Anticipos por Aprobar</h3>
                <p class="text-sm mb-0">Gestión y consulta de anticipos registrados.</p>
            </div>
            <div class="col-md-4 mb-0">
                <label for="search">Buscar:</label>
                <input type="text" 
                       id="search" 
                       wire:model.debounce.300ms="search" 
                       class="form-control" 
                       placeholder="Buscar por ID o N° Orden...">
            </div>
        </div>
    </div>

    <!-- Card Body: Tabla Principal de Anticipos -->
    <div class="card-body p-0 mt-3">
        <div class="table-responsive">
            <table class="table mb-0 align-items-center">
                <thead>
                    <tr>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">DATOS DE ORDEN / ANTICIPO</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">FECHA SOLICITUD</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">MONTO ANTICIPO</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">TIPO DE ANTICIPO</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">ESTADO</th>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ACCIONES</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ordenes as $anticipo)
                        <tr>
                            <td style="min-width: 14rem;">
                                <div class="d-flex px-2 py-1">
                                    <div>
                                        <img src="https://www.bullmarketing.com.co/wp-content/uploads/2022/04/cropped-favicon-bull-192x192.png" 
                                             class="avatar avatar-sm me-3" 
                                             alt="avatar">
                                    </div>
                                    <div class="d-flex flex-column justify-content-center">
                                        <h6 class="mb-0 text-xs">
                                            Anticipo #{{ $anticipo->id }}
                                        </h6>
                                        @if ($anticipo->ordenCompra?->presupuesto?->cod_cc)
                                            <p class="text-xs text-secondary mb-0">
                                                CC: {{ $anticipo->ordenCompra->presupuesto->cod_cc }}
                                            </p>
                                        @elseif ($anticipo->ordenCompra?->id)
                                            <p class="text-xs text-secondary mb-0">
                                                OC #{{ $anticipo->ordenCompra->id }}
                                            </p>
                                        @else
                                            <p class="text-xs text-secondary mb-0">
                                                Sin información
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td>
                                <p class="text-xs font-weight-bold mb-0">
                                    {{ $anticipo->created_at ? $anticipo->created_at->format('d/m/Y H:i') : 'N/A' }}
                                </p>
                            </td>

                            <td>
                                <p class="text-xs font-weight-bold mb-0">
                                    $ {{ number_format($anticipo->total_anticipo ?? 0, 2) }}
                                </p>
                            </td>

                            <td>
                                <p class="text-xs text-secondary mb-0">
                                    {{ $anticipo->ordenCompra?->tipo?->description ?? 'N/A' }}
                                </p>
                            </td>

                            <td>
                                <span class="badge badge-sm bg-gradient-secondary">
                                    {{ $anticipo->estado->description ?? ($anticipo->estado->nombre ?? 'Sin Estado') }}
                                </span>
                            </td>

                            <td class="align-middle text-center">
                                <button type="button" 
                                        wire:click="verDetalle({{ $anticipo->id }})" 
                                        class="btn btn-sm bg-gradient-primary m-0">
                                    Ver Detalle
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <p class="text-xs text-secondary mb-0">No se encontraron anticipos registrados.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="row p-3">
            <div class="col-md-6">
                <span class="text-xs text-secondary mb-0">
                    Total de registros: {{ count($ordenes) }}
                </span>
            </div>
        </div>
    </div>

    <!-- MODAL DE DETALLE COMPLETO Y APROBACIÓN -->
    @if ($showModal && $selectedAnticipo)
        <!-- Overlay oscuro independiente del flujo del card -->
        <div class="modal-backdrop fade show" style="z-index: 1040;"></div>

        <!-- Contenedor del Modal con Centrado Flexbox Absoluto -->
        <div class="modal fade show d-flex align-items-center justify-content-center" 
             tabindex="-1" 
             role="dialog"
             style="display: flex !important; position: fixed; top: 0; left: 150px; width: 100vw; height: 100vh; z-index: 1050; overflow-x: hidden; overflow-y: auto;">
            
            <div class="modal-dialog modal-dialog-centered modal-xl w-100 my-auto" role="document">
                <div class="modal-content shadow-lg border-0">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title">
                            Detalle del Anticipo #{{ $selectedAnticipo->id }} 
                            @if($selectedAnticipo->ordenCompra)
                                - Orden de Compra #{{ $selectedAnticipo->ordenCompra->id }}
                            @endif
                        </h5>
                        <button type="button" class="btn-close text-dark" wire:click="cerrarModal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body" style="max-height: 75vh; overflow-y: auto;">
                        <!-- Cabecera Informativa -->
                        <div class="card card-body shadow-none border mb-3">
                            <div class="row">
                                <div class="col-md-3 col-6 mb-2">
                                    <span class="text-xs text-secondary">Monto Solic. Anticipo:</span>
                                    <h6 class="mb-0 text-primary">$ {{ number_format($selectedAnticipo->total_anticipo ?? 0, 2) }}</h6>
                                </div>
                                <div class="col-md-3 col-6 mb-2">
                                    <span class="text-xs text-secondary">Fecha Solicitud:</span>
                                    <h6 class="mb-0">{{ $selectedAnticipo->created_at ? $selectedAnticipo->created_at->format('d/m/Y H:i') : 'N/A' }}</h6>
                                </div>
                                <div class="col-md-3 col-6 mb-2">
                                    <span class="text-xs text-secondary">Centro de Costos / OC:</span>
                                    <h6 class="mb-0">{{ $selectedAnticipo->ordenCompra?->presupuesto?->cod_cc ?? ('OC #' . ($selectedAnticipo->ordenCompra?->id ?? 'N/A')) }}</h6>
                                </div>
                                <div class="col-md-3 col-6 mb-2">
                                    <span class="text-xs text-secondary">Estado Actual:</span>
                                    <div>
                                        <span class="badge badge-sm bg-gradient-info">
                                            {{ $selectedAnticipo->estado->description ?? ($selectedAnticipo->estado->nombre ?? 'Sin Estado') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tabla de Ítems de la Orden de Compra -->
                        <h6 class="mb-2">Ítems de la Orden de Compra</h6>
                        <div class="table-responsive border rounded mb-3">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="font-weight-bold bg-gradient-primary text-white text-xs py-2 text-center">No. ITEM</th>
                                        <th class="font-weight-bold bg-gradient-primary text-white text-xs py-2 text-center">CANT</th>
                                        <th class="font-weight-bold bg-gradient-primary text-white text-xs py-2 text-center">DIAS</th>
                                        <th class="font-weight-bold bg-gradient-primary text-white text-xs py-2 text-center">OTROS</th>
                                        <th class="font-weight-bold bg-gradient-primary text-white text-xs py-2">DESCRIPCIÓN</th>
                                        <th class="font-weight-bold bg-gradient-primary text-white text-xs py-2 text-center">V. UNI</th>
                                        <th class="font-weight-bold bg-gradient-primary text-white text-xs py-2 text-center">V. TOTAL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($selectedAnticipo->ordenCompra && $selectedAnticipo->ordenCompra->ordenItems && $selectedAnticipo->ordenCompra->ordenItems->count() > 0)
                                        @foreach ($selectedAnticipo->ordenCompra->ordenItems as $item)
                                            <tr>
                                                <td class="text-center text-xs align-middle">{{ $item->num_item }}</td>
                                                <td class="text-center text-xs align-middle">{{ $item->cant_oc }}</td>
                                                <td class="text-center text-xs align-middle">{{ $item->dias_oc }}</td>
                                                <td class="text-center text-xs align-middle">{{ $item->otros_oc }}</td>
                                                <td class="align-middle" style="min-width: 250px;">
                                                    <textarea disabled class="form-control form-control-sm text-xs bg-light" rows="2" style="resize: none;">{{ $item->desc_oc }}</textarea>
                                                </td>
                                                <td class="text-center text-xs align-middle">$ {{ number_format($item->vunit_oc, 2) }}</td>
                                                <td class="text-center text-xs align-middle font-weight-bold">$ {{ number_format($item->vtotal_oc, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="7" class="text-center py-3">
                                                <p class="text-xs text-secondary mb-0">Esta Orden de Compra no contiene ítems registrados.</p>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        <div class="form-group mb-0">
                            <label for="justificacion_productor" class="form-control-label text-xs font-weight-bold text-primary">
                                Justificacion del productor
                            </label>
                            <textarea class="form-control" rows="3" disabled>{{ $selectedAnticipo->justificacion_productor ?? 'N/A'}}</textarea>
                        </div>

                        <div class="form-group mb-0">
                            <label for="justificacion_rechazo" class="form-control-label text-xs font-weight-bold text-danger">
                                Justificación / Motivo de Rechazo <span class="text-muted">(Requerido para rechazar)</span>:
                            </label>
                            <textarea id="justificacion_rechazo" 
                                      wire:model.defer="justificacion_rechazo" 
                                      class="form-control form-control-sm @error('justificacion_rechazo') is-invalid @enderror" 
                                      rows="3" 
                                      placeholder="Escriba aquí la razón del rechazo..."></textarea>

                            @error('justificacion_rechazo')
                                <div class="invalid-feedback d-block text-xs mt-1">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <!-- Footer del Modal -->
                    <div class="modal-footer d-flex justify-content-between bg-light">
                        <button type="button" class="btn btn-secondary mb-0" wire:click="cerrarModal">
                            Cerrar
                        </button>
                        <div>
                            <button type="button" 
                                    wire:click="rechazarAnticipo" 
                                    wire:loading.attr="disabled"
                                    class="btn btn-danger me-2 mb-0">
                                Rechazar
                            </button>
                            <button type="button" 
                                    wire:click="aprobarAnticipo" 
                                    wire:loading.attr="disabled"
                                    class="btn btn-success mb-0">
                                Aprobar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>