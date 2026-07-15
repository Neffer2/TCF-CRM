<div class="card">
    <div class="card-header pb-0">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h6 class="mb-0">Validaciones de clientes</h6>
                <p class="text-sm text-secondary mb-0">Solicitudes enviadas por comerciales pendientes de aprobaci&oacute;n.</p>
            </div>
            <span class="badge bg-gradient-warning">{{ $solicitudes->count() }} pendientes</span>
        </div>
    </div>

    <div class="card-body pt-3 pb-0">
        <div class="row g-3">
            <div class="col-md-4">
                <label for="cod_cliente" class="form-control-label">Buscar solicitante o cliente</label>
                <input id="cod_cliente" type="text" wire:model="cod_cliente" class="form-control" placeholder="Nombre del cliente o comercial...">
            </div>
            <div class="col-md-3">
                <label for="filtro_fecha" class="form-control-label">Orden por fecha</label>
                <select id="filtro_fecha" class="form-control" wire:model="fecha">
                    <option value="asc">M&aacute;s antiguos</option>
                    <option value="desc">M&aacute;s recientes</option>
                </select>
            </div>
        </div>
    </div>

    <div class="card-body p-0 mt-3">
        <div class="table-responsive">
            <table class="table align-items-center mb-0">
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
                                <div class="avatar avatar-sm bg-gradient-warning text-white rounded-circle d-flex align-items-center justify-content-center me-3">
                                    {{ strtoupper(substr($sol->nombre_cliente ?? 'C', 0, 1)) }}
                                </div>
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

                    <div class="modal fade" id="verModal{{ $sol->id }}" tabindex="-1" role="dialog" aria-labelledby="verModalLabel{{ $sol->id }}" aria-hidden="true" wire:ignore.self>
                        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Revisión de Solicitud de Contacto</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label font-weight-bold">Nombre completo</label>
                                            <input type="text" class="form-control" value="{{ $sol->nombre_cliente }} {{ $sol->apellido_cliente }}" disabled>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label font-weight-bold">Raz&oacute;n social</label>
                                            <input type="text" class="form-control" value="{{ $razonSocial }}" disabled>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label font-weight-bold">Correo electr&oacute;nico</label>
                                            <input type="text" class="form-control" value="{{ $sol->email_cliente }}" disabled>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label font-weight-bold">Direcci&oacute;n de contacto</label>
                                            <input type="text" class="form-control" value="{{ $sol->direccion_cliente ?? 'No registra' }}" disabled>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label font-weight-bold">Tel&eacute;fono</label>
                                            <input type="text" class="form-control" value="{{ $sol->telefono_cliente ?? 'No registra' }}" disabled>
                                        </div>

                                        <div class="col-md-12"><hr class="horizontal dark"><h6 class="mb-0">Campos Complementarios (Fase de Dise&ntilde;o)</h6></div>

                                        <div class="col-md-4 mb-3">
                                            <label class="form-label text-primary">Sector econ&oacute;mico</label>
                                            <input type="text" class="form-control border-primary" placeholder="Por definir (Solo visual)" disabled>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label text-primary">L&iacute;mite de cr&eacute;dito propuesto</label>
                                            <input type="text" class="form-control border-primary" placeholder="$ 0.00 (Solo visual)" disabled>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label text-primary">Segmentaci&oacute;n / Tag</label>
                                            <select class="form-control border-primary" disabled>
                                                <option>Selección bloqueada (Solo visual)</option>
                                            </select>
                                        </div>

                                        <div class="col-md-12 mb-3">
                                            <label class="form-label font-weight-bold">Comentarios del comercial</label>
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
                            No hay solicitudes pendientes para validar.
                        </td>
                    </tr>
                @endif
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
