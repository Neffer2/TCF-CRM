<div class="card">
    <div class="card-header pb-0">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h6 class="mb-0">Clientes registrados</h6>
                <p class="text-sm text-secondary mb-0">Consulta la informaci&oacute;n comercial y de contacto.</p>
            </div>
            <span class="badge bg-gradient-warning">{{ $clientes->total() }} registros</span>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table align-items-center mb-0">
            <thead>
            <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Informaci&oacute;n personal</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Tipo</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Raz&oacute;n social</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Contacto</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Acciones</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($clientes as $cliente)
                <tr>
                    <td>
                        <div class="d-flex px-2 py-1">
                            <div>
                                <img src="https://www.bullmarketing.com.co/wp-content/uploads/2022/04/cropped-favicon-bull-192x192.png" class="avatar avatar-sm me-3">
                            </div>
                            <div class="d-flex flex-column justify-content-center">
                                <h6 class="mb-0 text-xs">{{ $cliente->NombreCliente }} {{ $cliente->ApellidoCliente }}</h6>
                                <p class="text-xs text-secondary mb-0">{{ $cliente->CodigoCliente }}</p>
                            </div>
                        </div>
                    </td>
                    <td>
                        <p class="text-xs font-weight-bold mb-0">{{ $cliente->TipoCliente }}</p>
                    </td>
                    <td>
                        <p class="text-xs font-weight-bold mb-0">{{ $cliente->RazonSocialCliente }}</p>
                        <p class="text-xs text-secondary mb-0">{{ $cliente->DireccionCliente }}</p>
                    </td>
                    <td>
                        <p class="text-xs font-weight-bold mb-0">{{ $cliente->EmailCliente }}</p>
                        <p class="text-xs text-secondary">{{ $cliente->TelefonoCliente }}</p>
                    </td>
                    <td class="text-center">
                        <button class="btn btn-sm bg-gradient-primary mb-0" data-bs-toggle="modal" data-bs-target="#editModal{{ $cliente->id }}">Editar</button>
                        <button class="btn btn-sm bg-gradient-danger mb-0" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $cliente->id }}">Eliminar</button>
                    </td>
                </tr>
                <!-- Edit Modal -->
                <div class="modal fade" id="editModal{{ $cliente->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        {{-- action="{{ route('update-cliente', $cliente->id) }}" method="POST" --}}
                        <form >
                            @csrf
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Editar</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="CodigoCliente">C&oacute;digo: </label>
                                                <input id="CodigoCliente" name="CodigoCliente_edit" class="form-control @error('CodigoCliente_edit') is-invalid @enderror" placeholder="C&oacute;digo"
                                                       value="{{ $cliente->CodigoCliente }}">
                                                @error('CodigoCliente_edit')
                                                <div id="CodigoCliente_edit" class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="TipoCliente">Tipo de Cliente: </label>
                                                <select id="TipoCliente" name="TipoCliente_edit" class="form-control @error('TipoCliente_edit') is-invalid @enderror">
                                                    <option value="">Seleccionar</option>
                                                    <option value="NATURAL" {{ $cliente->TipoCliente == 'NATURAL' ? 'selected' : '' }}>NATURAL</option>
                                                    <option value="JUR&Iacute;DICO" {{ $cliente->TipoCliente == 'JUR&Iacute;DICO' ? 'selected' : '' }}>JUR&Iacute;DICO</option>
                                                </select>
                                                @error('TipoCliente_edit')
                                                <div id="TipoCliente_edit" class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="nombreCliente">Nombre: </label>
                                                <input id="nombreCliente" name="nombreCliente_edit" class="form-control @error('nombreCliente_edit') is-invalid @enderror" placeholder="Nombre"
                                                       value="{{ $cliente->nombreCliente }}">
                                                @error('nombreCliente_edit')
                                                <div id="nombreCliente_edit" class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="apellidoCliente">Apellido: </label>
                                                <input id="apellidoCliente" name="apellidoCliente_edit" class="form-control @error('apellidoCliente_edit') is-invalid @enderror" placeholder="Apellido"
                                                       value="{{ $cliente->apellidoCliente }}">
                                                @error('apellidoCliente_edit')
                                                <div id="apellidoCliente_edit" class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="RazonSocialCliente">Raz&oacute;n Social: </label>
                                                <input id="RazonSocialCliente" type="text" name="RazonSocialCliente_edit" class="form-control @error('RazonSocialCliente_edit') is-invalid @enderror" placeholder="Raz&oacute;n Social"
                                                       value="{{ $cliente->RazonSocialCliente }}">
                                                @error('RazonSocialCliente_edit')
                                                <div id="RazonSocialCliente_edit" class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="DireccionCliente">Direcci&oacute;n: </label>
                                                <input id="DireccionCliente" type="text" name="DireccionCliente_edit" class="form-control @error('DireccionCliente_edit') is-invalid @enderror" placeholder="Direcci&oacute;n"
                                                       value="{{ $cliente->DireccionCliente }}">
                                                @error('DireccionCliente_edit')
                                                <div id="DireccionCliente_edit" class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="telefonoCliente">Tel&eacute;fono: </label>
                                                <input id="telefonoCliente" type="number" name="telefonoCliente_edit" class="form-control @error('telefonoCliente_edit') is-invalid @enderror" placeholder="Tel&eacute;fono"
                                                       value="{{ $cliente->telefonoCliente }}">
                                                @error('telefonoCliente_edit')
                                                <div id="telefonoCliente_edit" class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="emailCliente">Correo: </label>
                                                <input id="emailCliente" type="email" name="emailCliente_edit" class="form-control @error('emailCliente_edit') is-invalid @enderror" placeholder="Correo"
                                                       value="{{ $cliente->emailCliente }}">
                                                @error('emailCliente_edit')
                                                <div id="emailCliente_edit" class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="DescripcionCliente">Descripci&oacute;n: </label>
                                                <textarea id="DescripcionCliente" name="DescripcionCliente_edit" rows="3" class="form-control @error('DescripcionCliente_edit') is-invalid @enderror" placeholder="Descripci&oacute;n">{{ $cliente->DescripcionCliente }}</textarea>
                                                @error('DescripcionCliente_edit')
                                                <div id="DescripcionCliente_edit" class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn bg-gradient-primary">Guardar cambios</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- Delete Modal -->
                <div class="modal fade" id="deleteModal{{ $cliente->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        {{-- action="{{ route('delete-cliente', $cliente->id) }}" method="POST" --}}
                        <form >
                            @csrf
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Eliminar</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    ¿Desea eliminar a: {{ $cliente->nombreCliente }} {{ $cliente->apellidoCliente }}?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn bg-gradient-danger">Eliminar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach
            <tr>
                <td colspan="5" class="pt-3">{{ $clientes->links() }}</td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
