<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                
                <!-- Encabezado -->
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">Detalle de Orden de Compra Natural</h5>
                        <p class="text-sm mb-0">Visualización de información del tercero, ítems y gestión de aprobación / rechazo.</p>
                    </div>
                    <div>
                        <span class="badge bg-gradient-info text-sm">ID Orden: {{ $queriedOrden->id ?? 'N/A' }}</span>
                    </div>
                </div>

                <div class="card-body">
                    
                    <!-- Alertas -->
                    @if (session()->has('success'))
                        <div class="alert alert-success text-white" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Información del Tercero / Persona Natural -->
                    <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3">Información del Tercero</h6>
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-control-label text-xs font-weight-bold">Nombre</label>
                                <input type="text" class="form-control" value="{{ $nombre }}" disabled>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-control-label text-xs font-weight-bold">Apellido</label>
                                <input type="text" class="form-control" value="{{ $apellido }}" disabled>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-control-label text-xs font-weight-bold">Cédula</label>
                                <input type="text" class="form-control" value="{{ $cedula }}" disabled>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-control-label text-xs font-weight-bold">Correo Electrónico</label>
                                <input type="text" class="form-control" value="{{ $correo }}" disabled>
                            </div>
                        </div>
                        <div class="col-md-3 mt-3">
                            <div class="form-group">
                                <label class="form-control-label text-xs font-weight-bold">Teléfono</label>
                                <input type="text" class="form-control" value="{{ $telefono }}" disabled>
                            </div>
                        </div>
                        <div class="col-md-3 mt-3">
                            <div class="form-group">
                                <label class="form-control-label text-xs font-weight-bold">Ciudad</label>
                                <input type="text" class="form-control" value="{{ $ciudad }}" disabled>
                            </div>
                        </div>
                        <div class="col-md-3 mt-3">
                            <div class="form-group">
                                <label class="form-control-label text-xs font-weight-bold">Banco</label>
                                <input type="text" class="form-control" value="{{ $banco }}" disabled>
                            </div>
                        </div>
                    </div>

                    <hr class="horizontal dark">

                    <div class="mb-3">
                        @include('partials.orden-compra-adjuntos', ['orden' => $queriedOrden])
                    </div>

                    <!-- Tabla de Ítems (Solo Lectura) -->
                    <h6 class="mb-3">Ítems de la Orden Natural</h6>
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Proyecto</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Ítem / Descripción</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Servicio / Contrato</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Cant.</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Días</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Otros</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">V. Unitario</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">V. Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($items as $item)
                                    <tr>
                                        <td>
                                            <span class="text-xs font-weight-bold mb-0 ps-3">{{ $item['proyecto']['nombre'] }} (CC: {{ $item['proyecto']['cod_cc'] }})</span>
                                        </td>
                                        <td>
                                            <span class="text-xs font-weight-bold mb-0">#{{ $item['item']['display_item'] }} - {{ $item['item']['nombre'] }}</span>
                                        </td>
                                        <td>
                                            <span class="text-xs text-secondary">{{ $item['tipo_servicio'] }} / {{ $item['tipo_contrato'] }}</span>
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
                                            <span class="text-xs font-weight-bold">$ {{ number_format($item['valor_unitario'], 2) }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="text-xs font-weight-bold text-success">$ {{ number_format($item['valor_total'], 2) }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-3">
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

                    <hr class="horizontal dark">

                    @if($queriedOrden->evidencias && $queriedOrden->evidencias->isNotEmpty())
                        <h6 class="mb-3">Evidencias Adjuntas</h6>
                        <div class="table-responsive p-0 mb-4">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Fecha</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Foto</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Observaciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($queriedOrden->evidencias as $evidencia)
                                        @php
                                            $fotoUrl = asset(str_replace("public", "storage", $evidencia->foto_evidencia));
                                        @endphp
                                        <tr>
                                            <td>
                                                <span class="text-xs font-weight-bold text-secondary mb-0">
                                                    {{ $evidencia->fecha_evidencia }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ $fotoUrl }}" target="_blank">
                                                    <img src="{{ $fotoUrl }}" class="border-radius-lg shadow-sm" height="50" alt="Evidencia">
                                                </a>
                                            </td>
                                            <td>
                                                <span class="text-xs text-secondary mb-0">
                                                    {{ $evidencia->observacion_evidencia ?? 'Sin observaciones' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="col-md-12 mb-4">
                            <h5 class="mb-2">Vista previa de la cuenta de cobro</h5>
                            {{-- wire:ignore evita que Livewire recargue el PDF en cada render --}}
                            <div class="d-none d-md-block w-100" wire:ignore>
                                <embed src="{{ asset('storage/' . $queriedOrden->archivo_cuenta_cobro) }}" width="100%" height="800" type="application/pdf">
                            </div>
                        </div>
                        <hr class="horizontal dark">
                    @elseif($queriedOrden)
                        <div class="mb-4">
                            <h5 class="mb-2">Vista previa de la orden de compra</h5>
                            <a href="{{ route('orden-compra-lider.pdf', $queriedOrden) }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-file-pdf me-1" aria-hidden="true"></i> Abrir PDF de la OC
                            </a>
                            <div class="d-none d-md-block w-100" wire:ignore>
                                <embed src="{{ route('orden-compra-lider.pdf', $queriedOrden) }}" width="100%" height="900" type="application/pdf" title="Vista previa de la orden de compra natural">
                            </div>
                        </div>
                    @endif

                    <!-- Sección de Aprobación o Rechazo -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-control-label text-xs font-weight-bold text-danger">Justificación de Rechazo (Obligatorio si se rechaza)</label>
                                <textarea wire:model.defer="justificacion_rechazo" class="form-control" rows="3" placeholder="Motivo por el cual se rechaza la orden..."></textarea>
                                @error('justificacion_rechazo') <span class="text-danger text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        @if(!$queriedOrden->archivo_cuenta_cobro)
                            <div class="col-md-6">
                                <label>Justificación del Productor</label>
                                <textarea class="form-control" rows="3" disabled>{{ $justificacion_productor ?? 'N/A' }}</textarea>
                            </div>
                        @endif
                    </div>

                    @error('customError')
                        <div class="alert alert-danger text-white">{{ $message }}</div>
                    @enderror
                    <!-- Botones de Acción Final -->
                    <div class="row mt-4">
                        <div class="col-12 d-flex justify-content-end gap-2">
                            <button type="button" wire:click="cambiarEstado(12)" class="btn bg-gradient-danger mb-0">
                                <i class="fas fa-times me-1"></i> Rechazar Orden
                            </button>
                            <button type="button" wire:click="cambiarEstado(3)" class="btn bg-gradient-success mb-0">
                                <i class="fas fa-check me-1"></i> Aprobar Orden
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
