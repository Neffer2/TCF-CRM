<div class="card">
    <div class="card-header pb-0">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h6 class="mb-0">Clientes registrados</h6>
                <p class="text-sm text-secondary mb-0">Consulta la información comercial, corporativa y de facturación.</p>
            </div>
            <span class="badge bg-gradient-warning">{{ $clientes->total() }} registros</span>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table align-items-center mb-0">
            <thead>
            <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Empresa (Nombre Comercial)</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">NIT / Razón Social</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Ubicación / Sitio Web</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Contacto & Facturación</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Acciones</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($clientes as $cliente)
                <tr wire:key="cliente-{{ $cliente->id }}">
                    <td>
                        <div class="d-flex px-2 py-1">
                            <div>
                                <img src="https://www.bullmarketing.com.co/wp-content/uploads/2022/04/cropped-favicon-bull-192x192.png" class="avatar avatar-sm me-3" alt="logo">
                            </div>
                            <div class="d-flex flex-column justify-content-center">
                                <h6 class="mb-0 text-sm">{{ $cliente->nombre }}</h6>
                                <p class="text-xs text-secondary mb-0">ID Sistema: #{{ $cliente->id }}</p>
                            </div>
                        </div>
                    </td>
                    <td>
                        <p class="text-xs font-weight-bold mb-0">{{ $cliente->nit }}</p>
                        <p class="text-xs text-secondary mb-0">{{ $cliente->razon_social }}</p>
                    </td>
                    <td>
                        <p class="text-xs font-weight-bold mb-0">{{ $cliente->direccion }}</p>
                        <p class="text-xs text-secondary mb-0">
                            @if($cliente->pagina_web)
                                <a href="{{ $cliente->pagina_web }}" target="_blank" class="text-secondary">{{ $cliente->pagina_web }}</a>
                            @else
                                No registra web
                            @endif
                        </p>
                    </td>
                    <td>
                        <p class="text-xs font-weight-bold mb-0">{{ $cliente->correo }} ({{ $cliente->cargo }})</p>
                        <p class="text-xs text-secondary mb-0">Facturación: {{ $cliente->correo_recpcion_facturas }}</p>
                        <p class="text-xs text-secondary mb-0">Móvil: {{ $cliente->numero_telefono }}</p>
                    </td>
                    <td class="text-center align-middle">
                        <button class="btn btn-sm bg-gradient-primary mb-0" data-bs-toggle="modal" data-bs-target="#editModal{{ $cliente->id }}">Ver</button>
                    </td>
                </tr>

                <!-- Edit Modal -->
                <div class="modal fade" id="editModal{{ $cliente->id }}" tabindex="-1" role="dialog" aria-labelledby="editModalLabel{{ $cliente->id }}" aria-hidden="true" wire:ignore.self>
                    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                        <form wire:submit.prevent="update({{ $cliente->id }})">
                            @csrf
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editModalLabel{{ $cliente->id }}">Ver Información del Cliente</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="form-group mb-0">
                                                <label class="form-control-label">Nombre Comercial</label>
                                                <input type="text" name="nombre" class="form-control" value="{{ $cliente->nombre }}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-0">
                                                <label class="form-control-label">Razón Social</label>
                                                <input type="text" name="razon_social" class="form-control" value="{{ $cliente->razon_social }}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group mb-0">
                                                <label class="form-control-label">NIT / Identificación</label>
                                                <input type="text" name="nit" class="form-control" value="{{ $cliente->nit }}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group mb-0">
                                                <label class="form-control-label">Teléfono Fijo</label>
                                                <input type="text" name="telefono" class="form-control" value="{{ $cliente->telefono }}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group mb-0">
                                                <label class="form-control-label">Número Celular</label>
                                                <input type="text" name="numero_telefono" class="form-control" value="{{ $cliente->numero_telefono }}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-0">
                                                <label class="form-control-label">Dirección</label>
                                                <input type="text" name="direccion" class="form-control" value="{{ $cliente->direccion }}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-0">
                                                <label class="form-control-label">Página Web</label>
                                                <input type="url" name="pagina_web" class="form-control" value="{{ $cliente->pagina_web }}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group mb-0">
                                                <label class="form-control-label">Cargo del Contacto</label>
                                                <input type="text" name="cargo" class="form-control" value="{{ $cliente->cargo }}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group mb-0">
                                                <label class="form-control-label" disabled="">Correo de Contacto</label>
                                                <input type="email" name="correo" class="form-control" value="{{ $cliente->correo }}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group mb-0">
                                                <label class="form-control-label">Recepción de Facturas</label>
                                                <input type="email" name="correo_recpcion_facturas" class="form-control" value="{{ $cliente->correo_recpcion_facturas }}" readonly>
                                            </div>
                                        </div>
                                        @if($cliente->adjuntar_archivos)
                                            <div class="col-md-12 mt-2">
                                                <label class="form-control-label d-block">Documentación Adjunta</label>
                                                <a href="{{ asset('storage/' . $cliente->adjuntar_archivos) }}" target="_blank" class="btn btn-sm btn-outline-secondary mb-0">
                                                    <i class="fas fa-file-pdf me-2"></i>Ver documento actual
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Volver</button>
                                    <button type="button" class="btn bg-gradient-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $cliente->id }}" data-bs-dismiss="modal">Eliminar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Delete Modal -->
                <div class="modal fade" id="deleteModal{{ $cliente->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel{{ $cliente->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <form wire:submit.prevent="eliminarCliente({{ $cliente->id }})">
                            @csrf
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="deleteModalLabel{{ $cliente->id }}">Eliminar Registro de Cliente</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body text-start">
                                    ¿Está completamente seguro de que desea eliminar permanentemente al cliente corporativo <strong>{{ $cliente->nombre }}</strong> con NIT <strong>{{ $cliente->nit }}</strong>? Esta acción no se puede deshacer.
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn bg-gradient-danger" data-bs-dismiss="modal">Eliminar de forma permanente</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach

            @if($clientes->isEmpty())
                <tr>
                    <td colspan="5" class="text-center py-4 text-sm text-secondary">
                        No hay clientes registrados en la plataforma.
                    </td>
                </tr>
            @endif
            </tbody>
        </table>
    </div>

    <!-- Paginación con estilo Bootstrap en el pie de la tabla -->
    @if($clientes->hasPages())
        <div class="card-footer py-3">
            {{ $clientes->links() }}
        </div>
    @endif
</div>
