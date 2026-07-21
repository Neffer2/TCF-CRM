<div class="card">
    <div class="card-header pb-0">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h6 class="mb-0">Validaciones de clientes</h6>
                <p class="text-sm text-secondary mb-0">Solicitudes enviadas por comerciales pendientes de aprobación.</p>
            </div>
            <span class="badge bg-gradient-warning">{{ $solicitudes->total() }} pendientes</span>
        </div>
    </div>

    <!-- Filtros actualizados con las nuevas variables del Backend -->
    <div class="card-body pt-3 pb-0">
        <div class="row g-3">
            <div class="col-md-4">
                <label for="buscar" class="form-control-label">Buscar empresa o NIT</label>
                <input id="buscar" type="text" wire:model.debounce.300ms="buscar" class="form-control" placeholder="Nombre comercial, razón social o NIT...">
            </div>
            <div class="col-md-3">
                <label for="filtro_fecha" class="form-control-label">Orden por fecha</label>
                <select id="filtro_fecha" class="form-control" wire:model="fecha">
                    <option value="desc">Más recientes</option>
                    <option value="asc">Más antiguos</option>
                </select>
            </div>
        </div>
    </div>

    <div class="card-body p-0 mt-3">
        <div class="table-responsive">
            <table class="table align-items-center mb-0">
                <thead>
                <tr>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Empresa / Contacto</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">NIT</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Comercial Solicitante</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Estado Solicitud</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Acciones</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($solicitudes as $sol)
                    <tr wire:key="solicitud-{{ $sol->id }}">
                        <td>
                            <div class="d-flex px-2 py-1">
                                <div class="avatar avatar-sm bg-gradient-warning text-white rounded-circle d-flex align-items-center justify-content-center me-3">
                                    {{ strtoupper(substr($sol->nombre ?? 'C', 0, 1)) }}
                                </div>
                                <div class="d-flex flex-column justify-content-center">
                                    <h6 class="mb-0 text-sm">{{ $sol->nombre }}</h6>
                                    <p class="text-xs text-secondary mb-0">{{ $sol->correo }} | {{ $sol->cargo }}</p>
                                </div>
                            </div>
                        </td>
                        <td>
                            <p class="text-xs font-weight-bold mb-0">{{ $sol->nit }}</p>
                            <p class="text-xs text-secondary mb-0">{{ $sol->razon_social }}</p>
                        </td>
                        <td>
                            <p class="text-xs font-weight-bold mb-0">{{ $sol->comercial->name ?? 'Sistema' }}</p>
                            <p class="text-xs text-secondary mb-0">Solicitado: {{ $sol->created_at->format('d/m/Y H:i') }}</p>
                        </td>
                        <td class="align-middle text-center">
                            <span class="badge badge-sm @if($sol->estado == 'Pendiente') bg-gradient-warning @else bg-gradient-success @endif">{{ $sol->estado }}</span>
                        </td>
                        <td class="align-middle text-center">
                            <button type="button" class="btn bg-gradient-info btn-sm mb-0" data-bs-toggle="modal" data-bs-target="#verModal{{ $sol->id }}">Ver detalles</button>

                            @if($sol->estado == 'Pendiente')
                                <button type="button"
                                        wire:click="aprobarSolicitud({{ $sol->id }})"
                                        wire:loading.attr="disabled"
                                        class="btn bg-gradient-success btn-sm mb-0">
                                    <span wire:loading.remove wire:target="aprobarSolicitud({{ $sol->id }})">Aprobar</span>
                                    <span wire:loading wire:target="aprobarSolicitud({{ $sol->id }})">Procesando...</span>
                                </button>
                            @endif
                        </td>
                    </tr>

                    <!-- Modal Detalle Cliente -->
                    <div class="modal fade" id="verModal{{ $sol->id }}" tabindex="-1" role="dialog" aria-labelledby="verModalLabel{{ $sol->id }}" aria-hidden="true" wire:ignore.self>
                        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Revisión de Solicitud Corporativa</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    @if ($errors->has('error_aprobacion'))
                                        <div class="alert alert-danger text-white text-sm" role="alert">
                                            {{ $errors->first('error_aprobacion') }}
                                        </div>
                                    @endif

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label font-weight-bold">Nombre Comercial</label>
                                            <input type="text" class="form-control" value="{{ $sol->nombre }}" disabled>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label font-weight-bold">Razón Social</label>
                                            <input type="text" class="form-control" value="{{ $sol->razon_social }}" disabled>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label font-weight-bold">NIT</label>
                                            <input type="text" class="form-control" value="{{ $sol->nit }}" disabled>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label font-weight-bold">Teléfono Principal</label>
                                            <input type="text" class="form-control" value="{{ $sol->telefono ?? 'No registra' }}" disabled>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label font-weight-bold">Celular / Móvil</label>
                                            <input type="text" class="form-control" value="{{ $sol->numero_telefono }}" disabled>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label font-weight-bold">Dirección Fiscal</label>
                                            <input type="text" class="form-control" value="{{ $sol->direccion }}" disabled>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label font-weight-bold">Página Web</label>
                                            <input type="text" class="form-control" value="{{ $sol->pagina_web ?? 'No registra' }}" disabled>
                                        </div>

                                        <div class="col-md-12"><hr class="horizontal dark"><h6 class="mb-2">Información del Contacto & Facturación</h6></div>

                                        <div class="col-md-4 mb-3">
                                            <label class="form-label font-weight-bold">Correo Electrónico</label>
                                            <input type="text" class="form-control" value="{{ $sol->correo }}" disabled>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label font-weight-bold">Cargo del Contacto</label>
                                            <input type="text" class="form-control" value="{{ $sol->cargo }}" disabled>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label font-weight-bold">Recepción Factura Electrónica</label>
                                            <input type="text" class="form-control" value="{{ $sol->correo_recpcion_facturas }}" disabled>
                                        </div>

                                        <div class="col-md-12 mb-3">
                                            <label class="form-label font-weight-bold d-block">Documentos Adjuntos (RUT / Cámara de Comercio)</label>
                                            @if($sol->adjuntar_archivos)
                                                <a href="{{ asset('storage/' . $sol->adjuntar_archivos) }}" target="_blank" class="btn btn-sm btn-outline-primary mb-0">
                                                    <i class="fas fa-file-download me-2"></i>Ver / Descargar Documento Cargado
                                                </a>
                                            @else
                                                <span class="text-sm text-secondary font-italic">El comercial no adjuntó ningún archivo.</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Cerrar</button>
                                    @if($sol->estado == 'Pending' || $sol->estado == 'Pendiente')
                                        <button type="button"
                                                wire:click="aprobarSolicitud({{ $sol->id }})"
                                                data-bs-dismiss="modal"
                                                class="btn bg-gradient-success">
                                            Aprobar e inscribir cliente
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                @if($solicitudes->isEmpty())
                    <tr>
                        <td colspan="5" class="text-center py-4 text-sm text-secondary">
                            No se encontraron solicitudes que coincidan con los criterios de búsqueda.
                        </td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>

        <!-- Paginación nativa abajo del contenedor -->
        @if($solicitudes->hasPages())
            <div class="card-footer py-3">
                {{ $solicitudes->links() }}
            </div>
        @endif
    </div>
</div>

@if (session('success'))
    <script>
        Swal.fire('¡Proceso Completado!', `{{ session('success') }}`, 'success');
    </script>
@endif
