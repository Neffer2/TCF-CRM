<div class="card">
    <div class="card-header p-0 px-3 mt-3">
        <div class="row">
            <div class="col-md-12">
                <h3 class="mb-0">Validaciones</h3>
                <p class="text-sm mb-0">Clientes por validar..</p>
            </div>
            <div class="col-md-2 form-group mb-0"> <!-- Se amplió un poco el ancho para que no se corte -->
                <label for="año">Año:</label>
                <select wire:model="año" class="form-control">
                    <option value="">Seleccionar</option>
                    @foreach ($añosList as $a)
                        <option value="{{ $a->id }}">{{ $a->description }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 mb-0">
                <label for="cod_cliente">Buscar:</label>
                <input type="text" wire:model="cod_cliente" class="form-control" placeholder="C&oacute;digo de cliente">
            </div>
            <div class="col-md-2 mb-0">
                <label for="filtro_fecha">Fecha:</label>
                <select id="filtro_fecha" class="form-control" wire:model="fecha">
                    <option value="asc">M&aacute;s antiguos</option>
                    <option value="desc">M&aacute;s recientes</option>
                </select>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                <tr>
                    <th colspan="1" class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">DATOS DE CLIENTE</th>
                    <th colspan="5" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">M&eacute;tricas</th>
                    <th colspan="3" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Acciones</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($clientes as $cl)
                    <!-- Se usa estricta y correctamente la variable del ciclo: $cl -->
                    <tr wire:key="cliente-{{ $cl->id }}">
                        <td style="width: 16rem;">
                            <div class="d-flex px-2 py-1" title="{{ $cl->NombreCliente }} {{ $cl->ApellidoCliente }}">
                                <div>
                                    <img src="https://www.bullmarketing.com.co/wp-content/uploads/2022/04/cropped-favicon-bull-192x192.png" class="avatar avatar-sm me-3">
                                </div>
                                <div class="d-flex flex-column justify-content-center">
                                    <h6 class="mb-0 text-xs">{{ $cl->NombreCliente }} {{ $cl->ApellidoCliente }}</h6>
                                    <p class="text-xs text-secondary mb-0">{{ $cl->CodigoCliente }}</p>
                                </div>
                            </div>
                        </td>
                        <td>
                            <p class="text-xs font-weight-bold mb-0">Fecha registro</p>
                            <p class="text-xs text-secondary mb-0">{{ $cl->created_at ? $cl->created_at->format('d/m/Y') : 'N/A' }}</p>
                        </td>
                        <td>
                            <p class="text-xs font-weight-bold mb-0">Tipo</p>
                            <p class="text-xs text-secondary mb-0">{{ $cl->TipoCliente }}</p>
                        </td>
                        <td>
                            <p class="text-xs font-weight-bold mb-0">Raz&oacute;n Social</p>
                            <p class="text-xs text-secondary mb-0">{{ $cl->RazonCliente }}</p>
                        </td>
                        <td>
                            <p class="text-xs font-weight-bold mb-0">Tel&eacute;fono</p>
                            <p class="text-xs text-secondary mb-0">{{ $cl->TelefonoCliente }}</p>
                        </td>
                        <td>
                            <p class="text-xs font-weight-bold mb-0">Estado</p>
                            <p class="text-xs text-secondary mb-0">{{ $cl->estado->description ?? 'Sin Estado' }}</p>
                        </td>
                        @if (Auth::user()->rol == 1)
                            <td class="d-flex align-items-start">
                                <button type="button" class="btn bg-gradient-primary m-0 me-1" data-bs-toggle="modal" data-bs-target="#verModal{{ $cl->id }}">Ver</button>
                                <select @if($cl->estado_id == 1) disabled @endif class="form-control w-auto mb-1" wire:change="cambioEstado({{ $cl->id }}, event.currentTarget.value)" wire:loading.attr="disabled">
                                    <option value selected>Cambiar estado</option>
                                    @foreach ($estadosList as $est)
                                        @if ( $est->id != 3 )
                                            <option value="{{ $est->id }}">{{ $est->description }}</option>
                                        @endif
                                    @endforeach
                                    <option value="3">Rechazar</option>
                                </select>
                            </td>
                        @else
                            <td class="d-flex align-items-start">
                                <button type="button" class="btn bg-gradient-warning" data-bs-toggle="modal" data-bs-target="#verModal{{ $cl->id }}">Ver</button>
                            </td>
                        @endif
                    </tr>

                    <!-- Modal Ver -->
                    <div class="modal fade" id="verModal{{ $cl->id }}" tabindex="-1" role="dialog" aria-labelledby="verModalLabel{{ $cl->id }}" aria-hidden="true" wire:ignore.self>
                        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="verModalLabel{{ $cl->id }}">Datos del cliente</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label>C&oacute;digo:</label>
                                            <input type="text" class="form-control" value="{{ $cl->CodigoCliente }}" disabled>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label>Tipo de Cliente:</label>
                                            <input type="text" class="form-control" value="{{ $cl->TipoCliente }}" disabled>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label>Nombre:</label>
                                            <input type="text" class="form-control" value="{{ $cl->NombreCliente }}" disabled>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label>Apellido:</label>
                                            <input type="text" class="form-control" value="{{ $cl->ApellidoCliente }}" disabled>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label>Raz&oacute;n Social:</label>
                                            <input type="text" class="form-control" value="{{ $cl->RazonCliente }}" disabled>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label>Direcci&oacute;n:</label>
                                            <input type="text" class="form-control" value="{{ $cl->DireccionCliente }}" disabled>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label>Tel&eacute;fono:</label>
                                            <input type="text" class="form-control" value="{{ $cl->TelefonoCliente }}" disabled>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label>Correo:</label>
                                            <input type="text" class="form-control" value="{{ $cl->EmailCliente }}" disabled>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label>Estado:</label>
                                            <input type="text" class="form-control" value="{{ $cl->estado->description ?? 'Sin Estado' }}" disabled>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label>Descripci&oacute;n:</label>
                                            <textarea class="form-control" rows="3" disabled>{{ $cl->DescripcionCliente }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <a class="btn bg-gradient-primary" href="{{ route('ver-cliente', $cl->id) }}" target="_blank">Ver en p&aacute;gina completa</a>
                                    <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Cerrar</button>
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
