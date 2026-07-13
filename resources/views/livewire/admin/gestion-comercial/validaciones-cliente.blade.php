<div class="card">
    <div class="card-header p-0 px-3 mt-3">
        <div class="row">
            <div class="col-md-12">
                <h3 class="mb-0">Validaciones de Contactos</h3>
                <p class="text-sm mb-0">Solicitudes enviadas por comerciales pendientes de aprobación.</p>
            </div>
            <!-- Filtros existentes -->
            <div class="col-md-3 mb-0 mt-2">
                <label for="cod_cliente">Buscar Solicitante o Contacto:</label>
                <input type="text" wire:model="cod_cliente" class="form-control" placeholder="Nombre del contacto...">
            </div>
            <div class="col-md-2 mb-0 mt-2">
                <label for="filtro_fecha">Fecha:</label>
                <select id="filtro_fecha" class="form-control" wire:model="fecha">
                    <option value="asc">M&aacute;s antiguos</option>
                    <option value="desc">M&aacute;s recientes</option>
                </select>
            </div>
        </div>
    </div>

    <div class="card-body p-0 mt-3">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                <tr>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Contacto Solicitado</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Comercial Solicitante</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tipo</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Estado Solicitud</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Acciones</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($solicitudes as $sol)
                    @php
                        // Extraemos la Razón Social temporal guardada en el JSON
                        $datosEmpresa = json_decode($sol->nueva_empresa_datos, true);
                        $razonSocial = $datosEmpresa['razon_social'] ?? 'N/A';
                    @endphp
                    <tr wire:key="solicitud-{{ $sol->id }}">
                        <td>
                            <div class="d-flex px-2 py-1">
                                <div class="d-flex flex-column justify-content-center">
                                    <h6 class="mb-0 text-sm">{{ $sol->nombre_cliente }} {{ $sol->apellido_cliente }}</h6>
                                    <p class="text-xs text-secondary mb-0">{{ $sol->email_cliente }}</p>
                                </div>
                            </div>
                        </td>
                        <td>
                            <p class="text-xs font-weight-bold mb-0">{{ $sol->comercial->name ?? 'Sistema' }}</p>
                            <p class="text-xs text-secondary mb-0">Solicitado: {{ $sol->created_at->format('d/m/Y H:i') }}</p>
                        </td>
                        <td class="align-middle text-center text-sm">
                            <span class="text-xs font-weight-bold">{{ $sol->tipo_cliente }}</span>
                        </td>
                        <td class="align-middle text-center">
                            <span class="badge badge-sm bg-gradient-warning">{{ $sol->estado }}</span>
                        </td>
                        <td class="align-middle text-center">
                            <!-- Botón Ver Detalles (Abre el Modal) -->
                            <button type="button" class="btn bg-gradient-info btn-sm m-0" data-bs-toggle="modal" data-bs-target="#verModal{{ $sol->id }}">Ver Detalles</button>

                            <!-- Botón de Procesar Aprobación Directa -->
                            @if($sol->estado == 'Pendiente')
                                <button type="button"
                                        wire:click="aprobarSolicitud({{ $sol->id }})"
                                        wire:loading.attr="disabled"
                                        class="btn bg-gradient-success btn-sm m-0">
                                    <span wire:loading.remove wire:target="aprobarSolicitud({{ $sol->id }})">Aprobar</span>
                                    <span wire:loading wire:target="aprobarSolicitud({{ $sol->id }})">Procesando...</span>
                                </button>
                            @endif
                        </td>
                    </tr>

                    <!-- Modal de Detalles y Campos Complementarios -->
                    <div class="modal fade" id="verModal{{ $sol->id }}" tabindex="-1" role="dialog" aria-labelledby="verModalLabel{{ $sol->id }}" aria-hidden="true" wire:ignore.self>
                        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Revisión de Solicitud de Contacto</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <!-- Datos Reales del Formulario -->
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label font-weight-bold">Nombre Completo:</label>
                                            <input type="text" class="form-control" value="{{ $sol->nombre_cliente }} {{ $sol->apellido_cliente }}" disabled>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label font-weight-bold">Razón Social:</label>
                                            <input type="text" class="form-control" value="{{ $razonSocial }}" disabled>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label font-weight-bold">Correo Electrónico:</label>
                                            <input type="text" class="form-control" value="{{ $sol->email_cliente }}" disabled>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label font-weight-bold">Dirección de Contacto:</label>
                                            <input type="text" class="form-control" value="{{ $sol->direccion_cliente ?? 'No registra' }}" disabled>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label font-weight-bold">Teléfono:</label>
                                            <input type="text" class="form-control" value="{{ $sol->telefono_cliente ?? 'No registra' }}" disabled>
                                        </div>

                                        <!-- 📌 CAMPOS COMPLEMENTARIOS (SOLO VISUALES / FRONTEND POR AHORA) -->
                                        <div class="col-md-12"><hr class="horizontal dark"><h6>Campos Complementarios (Fase de Diseño)</h6></div>

                                        <div class="col-md-4 mb-3">
                                            <label class="form-label text-primary">Sector Económico:</label>
                                            <input type="text" class="form-control border-primary" placeholder="Por definir (Solo visual)" disabled>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label text-primary">Límite de Crédito Propuesto:</label>
                                            <input type="text" class="form-control border-primary" placeholder="$ 0.00 (Solo visual)" disabled>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label text-primary">Segmentación / Tag:</label>
                                            <select class="form-control border-primary" disabled>
                                                <option>Selección bloqueada (Solo visual)</option>
                                            </select>
                                        </div>

                                        <div class="col-md-12 mb-3">
                                            <label class="form-label font-weight-bold">Comentarios del Comercial:</label>
                                            <textarea class="form-control" rows="2" disabled>{{ $sol->descripcion_cliente ?? 'Sin comentarios' }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Cerrar</button>
                                    @if($sol->estado == 'Pendiente')
                                        <button type="button"
                                                wire:click="aprobarSolicitud({{ $sol->id }})"
                                                data-bs-dismiss="modal"
                                                class="btn bg-gradient-success">
                                            Aprobar e Inscribir Cliente
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@if (session('success'))
    <script>
        Swal.fire('¡Proceso Completado!', `{{ session('success') }}`, 'success');
    </script>
@endif
