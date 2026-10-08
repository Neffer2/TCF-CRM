<div>
    @if (session()->has('message'))
        <div class="alert alert-success text-white mx-3" role="alert">
            {{ session('message') }}
        </div>
    @endif

    <!-- CARD PRINCIPAL CON LA TABLA -->
    <div class="card">
        <!-- Card Header y Filtros -->
        <div class="card-header p-0 px-3 mt-3">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h3 class="mb-0">Lista de Anticipos</h3>
                    <p class="text-sm mb-0">Gestión y consulta de anticipos registrados.</p>
                </div>
                <div class="col-md-4 mb-0">
                    <label for="search">Buscar:</label>
                    <input type="text" id="search" wire:model.debounce.300ms="search" class="form-control" placeholder="Buscar por ID, OC o CC...">
                </div>
            </div>
        </div>

        <div class="card-body p-0 mt-3">
            <div class="table-responsive">
                <table class="table mb-0 align-items-center">
                    <thead>
                        <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">DATOS DE ORDEN / ANTICIPO</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">FECHA SOLICITUD</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">MONTO</th>
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
                                            <img src="https://www.bullmarketing.com.co/wp-content/uploads/2022/04/cropped-favicon-bull-192x192.png" class="avatar avatar-sm me-3" alt="avatar">
                                        </div>
                                        <div class="d-flex flex-column justify-content-center">
                                            <h6 class="mb-0 text-xs">Anticipo #{{ $anticipo->id }}</h6>
                                            @if ($anticipo->ordenCompra?->presupuesto?->cod_cc)
                                                <p class="text-xs text-secondary mb-0">{{ $anticipo->ordenCompra->presupuesto->cod_cc }}</p>
                                            @elseif ($anticipo->ordenCompra?->id)
                                                <p class="text-xs text-secondary mb-0">OC #{{ $anticipo->ordenCompra->id }}</p>
                                            @else
                                                <p class="text-xs text-secondary mb-0">Sin información</p>
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
                                    <span class="badge badge-sm {{ $anticipo->estado_id == 6 ? 'bg-gradient-danger' : 'bg-gradient-info' }}">
                                        {{ $anticipo->estado->description ?? 'Sin Estado' }}
                                    </span>
                                </td>

                                <td class="align-middle text-center">
                                    <button class="btn btn-sm bg-gradient-primary m-0" wire:click="verDetalle({{ $anticipo->id }})">
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
        </div>
    </div>

    <!-- MODAL DE DETALLE Y RE-EDICIÓN -->
    <div class="modal fade @if($showModal) show d-block @endif" tabindex="-1" style="background: rgba(0,0,0,0.5);" id="modalDetalleAnticipo">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        Detalles del Anticipo #{{ $selectedAnticipo?->id }}
                    </h5>
                    <button type="button" class="btn-close text-dark" wire:click="cerrarModal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    @if ($selectedAnticipo)
                        <!-- Alerta informativa en caso de rechazo -->
                        @if ($selectedAnticipo->estado_id == 6)
                            <div class="alert alert-danger text-white mb-3" role="alert">
                                <strong>Anticipo Rechazado:</strong> {{ $selectedAnticipo->justificacion_rechazo ?? 'Sin observación especificada.' }}
                                <br><small>Puedes corregir el porcentaje de anticipo a continuación y reenviarlo a validación.</small>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">Orden de Compra / CC:</label>
                                <p class="form-control-plaintext text-sm">
                                    {{ $selectedAnticipo->ordenCompra?->presupuesto?->cod_cc ?? ('OC #' . $selectedAnticipo->ordenCompra?->id) }}
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">Valor total de la OC</label>
                                <p class="form-control-plaintext text-sm">
                                    $ {{ number_format($totalOrden) }}
                                </p>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">Estado Actual:</label>
                                <p class="form-control-plaintext text-sm">
                                    <span class="badge badge-sm {{ $selectedAnticipo->estado_id == 6 ? 'bg-gradient-danger' : 'bg-gradient-info' }}">
                                        {{ $selectedAnticipo->estado?->description ?? 'N/A' }}
                                    </span>
                                </p>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">Porcentaje de Anticipo (%):</label>
                                @if ($selectedAnticipo->estado_id == 6)
                                    <input type="number" step="0.01" class="form-control @error('porcentaje_anticipo') is-invalid @enderror" wire:model="porcentaje_anticipo">
                                    @error('porcentaje_anticipo')
                                        <span class="text-danger text-xs">{{ $message }}</span>
                                    @enderror
                                @else
                                    <p class="form-control-plaintext text-sm">{{ $selectedAnticipo->porcentaje_anticipo }}%</p>
                                @endif
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">Monto Total Calculado:</label>
                                <p class="form-control-plaintext text-sm text-success font-weight-bold">
                                    $ {{ number_format($total_anticipo ?? $selectedAnticipo->total_anticipo, 2) }}
                                </p>
                            </div>

                            @if ($selectedAnticipo->observacion_causal)
                                <div class="col-md-12 mb-3">
                                    <label class="form-label font-weight-bold">Observación / Causal:</label>
                                    <p class="form-control-plaintext text-sm">{{ $selectedAnticipo->observacion_causal }}</p>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="cerrarModal">Cerrar</button>
                    @if ($selectedAnticipo?->estado_id == 6)
                        <button type="button" class="btn bg-gradient-success" wire:click="reenviarValidacion">
                            Reenviar a Validación
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>