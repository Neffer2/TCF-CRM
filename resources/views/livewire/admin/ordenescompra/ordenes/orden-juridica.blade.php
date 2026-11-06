<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                
                <!-- Encabezado -->
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <div>
                        @if($orden_compra && $orden_compra->archivo_remision)
                            <h5 class="mb-3">Detalle de Remision Juridica</h5>
                        @elseif($orden_compra && $orden_compra->archivo_cot)
                            <h5 class="mb-3">Detalle de Orden de Compra Juridica</h5>
                        @endif
                        <p class="text-sm mb-0">Visualización de información y gestión de aprobación / rechazo.</p>
                    </div>
                    <div>
                        <span class="badge bg-gradient-info text-sm">ID Orden: {{ $orden_compra->id ?? 'N/A' }}</span>
                    </div>
                </div>

                <div class="card-body">
                    
                    <!-- Alertas de error o éxito -->
                    @if (session()->has('success'))
                        <div class="alert alert-success text-white" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @error('customError')
                        <div class="alert alert-danger text-white" role="alert">
                            {{ $message }}
                        </div>
                    @enderror

                    <!-- Información General -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-control-label text-xs font-weight-bold">Proveedor</label>
                                <input type="text" class="form-control" value="{{ $proveedor_nombre }}" disabled>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-control-label text-xs font-weight-bold">Fecha de Envío</label>
                                <input type="text" class="form-control" value="{{ $orden_compra->fecha_envio_produccion ?? 'N/A' }}" disabled>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="form-control-label text-xs font-weight-bold">Archivo de Cotización</label>
                                <div>
                                    @if($orden_compra && $orden_compra->archivo_cot)
                                        <a href="{{ Storage::url($orden_compra->archivo_cot) }}" target="_blank" class="btn btn-sm bg-gradient-secondary mt-1">
                                            <i class="fas fa-file-pdf me-1"></i> Ver Cotización
                                        </a>
                                    @else
                                        <span class="text-sm text-secondary">No adjunto</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @if($orden_compra && $orden_compra->archivo_remision)
                            <!-- Archivo de Remisión -->
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="form-control-label text-xs font-weight-bold d-block">Archivo de Remisión</label>
                                    <div>
                                       <a href="{{ Storage::url($orden_compra->archivo_remision) }}" target="_blank" class="btn btn-sm bg-gradient-secondary mt-1">
                                            <i class="fas fa-file-pdf me-1"></i> Ver Remisión
                                        </a>            
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <hr class="horizontal dark">

                    <!-- Tabla de Ítems (Solo Lectura) -->
                    <h6 class="mb-3">Ítems de la Orden de Compra</h6>
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Ítem #</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Descripción</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Cant.</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Días</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Otros</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">V. Unitario</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">V. Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ocItems as $item)
                                    <tr>
                                        <td>
                                            <span class="text-xs font-weight-bold mb-0 ps-3">{{ $item['num_item'] }}</span>
                                        </td>
                                        <td>
                                            <span class="text-xs font-weight-bold mb-0">{{ $item['desc'] }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="text-xs font-weight-bold">{{ $item['cant'] }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="text-xs font-weight-bold">{{ $item['dias'] }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="text-xs font-weight-bold">{{ $item['otros'] }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="text-xs font-weight-bold">$ {{ number_format($item['vUnit'], 2) }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="text-xs font-weight-bold text-success">$ {{ number_format($item['vTotal'], 2) }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-3">
                                            <span class="text-secondary text-sm">No hay ítems registrados en esta orden.</span>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Total general -->
                    <div class="row mt-3">
                        <div class="col-12 text-end">
                            <h5>Total Orden: <span class="text-success">$ {{ number_format($totalOrden, 2) }}</span></h5>
                        </div>
                    </div>

                    @if(!$orden_compra || !$orden_compra->archivo_remision)
                        <div class="col-md-12 mb-4">
                            <!-- Visualizador PDF para pantallas medianas y grandes -->
                            <div class="d-none d-md-block w-100">
                                <embed src="{{ route('orden-compra.pdf', $orden_compra) }}" width="100%" height="900" type="application/pdf">
                            </div>
                            
                            <!-- Sección de descarga para móviles o respaldo -->
                            <div class="mt-3 text-center">
                                <p class="text-muted">Este archivo se guarda de manera automática. Si no lo puedes visualizar, descárgalo aquí:</p>
                                <a href="{{ route('orden-compra.pdf', $orden_compra) }}" class="btn btn-primary" download>
                                    <i class="fas fa-download"></i> Descargar Orden de Compra
                                </a>
                            </div>
                        </div>
                    

                        {{-- Campo: Código de Orden de Compra Automático --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="cod_oc">Código de orden de compra a generar:</label>
                                <input type="text" id="cod_oc" class="form-control" wire:model="cod_oc" readonly>
                                <small class="text-muted">Código provisional. El definitivo se asigna al aprobar.</small>
                                @error('cod_oc')
                                    <div class="text-invalid text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Aviso informativo de generación automática --}}
                        <div class="col-md-6 d-flex align-items-center">
                            <div class="alert alert-light border w-100 mb-0 py-2">
                                <small><i class="ni ni-bell-55 text-warning"></i> Al hacer clic en <strong>Aprobar</strong>, el sistema generará y almacenará el PDF oficial de forma automática.</small>
                            </div>
                        </div>
                    @endif
                    
                    <hr class="horizontal dark">

                    @if ($puedeAprobar)
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label text-xs font-weight-bold text-danger">Justificación de Rechazo (Obligatorio si se rechaza)</label>
                                    <textarea wire:model.defer="justificacion_rechazo" class="form-control" rows="3"></textarea>
                                    @error('justificacion_rechazo') <span class="text-danger text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label>Justificación del Productor</label>
                                <textarea class="form-control" rows="3" disabled>{{ $orden_compra->justificacion_productor ?? 'N/A' }}</textarea>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12 d-flex justify-content-end gap-2">
                                <button type="button" wire:click="cambiarEstado('rechazar')" class="btn bg-gradient-danger mb-0">
                                    <i class="fas fa-times me-1"></i> Rechazar Orden
                                </button>
                                <button type="button" wire:click="cambiarEstado('aprobar')" class="btn bg-gradient-success mb-0">
                                    <i class="fas fa-check me-1"></i> Aprobar Orden
                                </button>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-secondary text-white mt-4">
                            Esta orden no está pendiente de tu aprobación.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>