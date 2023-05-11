<div class="card">
    <div class="table-responsive"> 
        <table class="table">
            <thead> 
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Informaci&oacute;n personal</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">CARGO</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">CONTACTO</th>
                <th colspan="2" class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">ACCIONES</th>
            </thead>  
            <tbody>  
                @foreach ($contactos as $contacto)
                    <tr> 
                        <td>
                            <div class="d-flex px-2 py-1">
                                <div>
                                    <img src="https://www.bullmarketing.com.co/wp-content/uploads/2022/04/cropped-favicon-bull-192x192.png" class="avatar avatar-sm me-3">
                                </div>
                                <div class="d-flex flex-column justify-content-center">
                                    <h6 class="mb-0 text-xs">{{ $contacto->nombre }} {{ $contacto->apellido }}</h6>
                                    <p class="text-xs text-secondary mb-0">{{ $contacto->correo }}</p>
                                </div>
                            </div>
                        </td>
                        <td>
                            <p class="text-xs font-weight-bold mb-0">{{ $contacto->cargo }}</p>
                            <p class="text-xs text-secondary mb-0">{{ $contacto->empresa }}</p>
                        </td>
                        <td>
                            <p class="text-xs font-weight-bold mb-0">{{ $contacto->web }}</p>
                            <p class="text-xs text-secondary">{{ $contacto->celular }}</p>
                        </td>
                        <td colspan="2"> 
                            <button class="btn bg-gradient-primary mb-0" data-bs-toggle="modal" data-bs-target="#editModal{{ $contacto->id }}">Editar</button>
                            <button class="btn bg-gradient-danger mb-0" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $contacto->id }}">Eliminar</button>
                        </td>
                    </tr>
                    <!-- Edit Modal --> 
                    <div class="modal fade" id="editModal{{ $contacto->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <form action="{{ route('update-contacto', $contacto->id) }}" method="POST">
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
                                                    <label for="cargo">Cargo: </label>
                                                    <input id="cargo" name="cargo_edit" class="form-control @error('cargo_edit') is-invalid @enderror" placeholder="Cargo"
                                                    value="{{ $contacto->cargo }}">                
                                                    @error('cargo_edit')
                                                        <div id="cargo_edit" class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="celular">Celular: </label>
                                                    <input id="celular" type="number" name="celular_edit" class="form-control @error('celular_edit') is-invalid @enderror" placeholder="Celular"
                                                    value="{{ $contacto->celular }}"> 
                                                    @error('celular_edit')
                                                        <div id="celular_edit" class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="correo">Correo: </label>
                                                    <input id="correo" type="email" name="correo_edit" class="form-control @error('correo_edit') is-invalid @enderror" placeholder="Correo"
                                                    value="{{ $contacto->correo }}">
                                                    @error('correo_edit')
                                                        <div id="correo_edit" class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="pbx">PBX EXT: </label>
                                                    <input id="pbx" type="text" name="pbx_edit" class="form-control @error('pbx_edit') is-invalid @enderror" placeholder="PBX"
                                                    value="{{ $contacto->pbx }}">
                                                    @error('pbx_edit')
                                                        <div id="pbx_edit" class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="web">Web: </label>
                                                    <input id="web" type="text" name="web_edit" class="form-control @error('web_edit') is-invalid @enderror" placeholder="Web"
                                                    value="{{ $contacto->web }}">
                                                    @error('web_edit')
                                                        <div id="web_edit" class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="direccion">Direcci&oacute;n: </label>
                                                    <input id="direccion" type="text" name="direccion_edit" class="form-control @error('direccion_edit') is-invalid @enderror" placeholder="Direccion"
                                                    value="{{ $contacto->direccion }}">
                                                    @error('direccion_edit')
                                                        <div id="direccion_edit" class="invalid-feedback">
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
                    <div class="modal fade" id="deleteModal{{ $contacto->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <form action="{{ route('delete-contacto', $contacto->id) }}" method="POST"> 
                                @csrf
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Eliminar</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        ¿Desea eliminar a: {{ $contacto->nombre }} {{ $contacto->apellido }}?
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
                    <td colspan="6" class="d-flex">{{ $contactos->links() }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>