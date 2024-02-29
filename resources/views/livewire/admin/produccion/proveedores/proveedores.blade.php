<div>
    <div class="card">
        <div class="card-header p-0 px-3 mt-3 col-md-12">
            <div class="row">
                <div class="col-md-12">
                    <h3 class="mb-0">Proveedores</h3> 
                    <p class="text-sm mb-0">Lista de proveedores.</p>
                </div>
                <div class="col-md-2">  
                    <label for="comercial">Nombre:</label>
                    <input type="text" wire:model="contacto" class="form-control" placeholder="Nombre contacto">
                </div>
                <div class="col-md-2">  
                    <label for="comercial">Tercero:</label>
                    <input type="text" wire:model="tercero" class="form-control" placeholder="Nombre tercero">
                </div> 
                <div class="col-md-2">
                    <label for="filtro_fecha">Categor&iacute;a:</label>
                    <select id="filtro_fecha" class="form-control" wire:model="categoria">
                        <option value="">Seleccionar</option>
                        @foreach ($categorias as $categoria)
                            <option value="{{ $categoria->id }}">{{ $categoria->description }}</option>
                        @endforeach
                    </select> 
                </div> 
                <div class="col-md-2">
                    <label for="estado">Ciudad:</label>
                    <select id="estado" class="form-control" wire:model="ciudad">
                        <option value="">Seleccionar</option>
                        @foreach ($ciudades as $ciudad)
                            <option value="{{ $ciudad }}">{{ $ciudad }}</option>
                        @endforeach
                    </select> 
                </div>
                @if (Auth::user()->rol == 1)
                    <div class="col-md-2">
                        <label for="estado">Estado:</label>
                        <select id="estado" class="form-control" wire:model="estado">
                            <option value="">Seleccionar</option>
                            <option value="CONFIRMADO">CONFIRMADO</option>
                            <option value="CONFIRMADO - COMUNICADO">CONFIRMADO - COMUNICADO</option>
                            <option value="NO APLICA">NO APLICA</option>
                            <option value="SE CANCELO ACUERDO">SE CANCELO ACUERDO</option>
                        </select> 
                    </div>
                @endif
            </div>
        </div>
        <div class="card-body p-0 pt-1">
            <div class="table-responsive">
                <table class="table">
                    <thead> 
                        <th colspan="6" class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Informaci&oacute;n proveedores</th>
                    </thead>  
                    <tbody>  
                        @foreach ($proveedores as $proveedor)
                            <tr> 
                                <td>
                                    <div class="d-flex px-2 py-1">
                                        <div>
                                            <img src="https://www.bullmarketing.com.co/wp-content/uploads/2022/04/cropped-favicon-bull-192x192.png" class="avatar avatar-sm me-3">
                                        </div>
                                        <div class="d-flex flex-column justify-content-center">
                                            <h6 class="mb-0 text-xs">{{ $proveedor->tercero }}</h6>
                                            <p class="text-xs text-secondary mb-0">{{ $proveedor->contacto }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <p class="text-xs font-weight-bold mb-0">Tipo</p> 
                                    <span class="badge badge-sm badge-primary">{{ $proveedor->tipo }}</span>
                                </td>
                                <td>
                                    <p class="text-xs font-weight-bold mb-0">{{ $proveedor->tipo_doc }}</p>
                                    <p class="text-xs text-secondary mb-0">{{ $proveedor->documento }}</p>
                                </td>
                                <td>
                                    <p class="text-xs font-weight-bold mb-0">{{ $proveedor->correo }}</p>
                                    <p class="text-xs text-secondary">{{ $proveedor->celular }}</p>
                                </td>
                                <td>
                                    <p class="text-xs font-weight-bold mb-0">{{ $proveedor->departamento }}</p>
                                    <p class="text-xs text-secondary">{{ $proveedor->ciudad }}</p>
                                </td>
                                <td colspan="2"> 
                                    <button @if (!(Auth::user()->rol == 1)) disabled @endif class="btn bg-gradient-primary mb-0" data-bs-toggle="modal" data-bs-target="#editModal{{ $proveedor->id }}">Editar</button>
                                    <button @if (!(Auth::user()->rol == 1)) disabled @endif class="btn bg-gradient-danger mb-0" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $proveedor->id }}">Eliminar</button>
                                </td>
                            </tr>
                            <!-- Edit Modal --> 
                            <div class="modal fade" id="editModal{{ $proveedor->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header"> 
                                            <h5 class="modal-title" id="exampleModalLabel">{{ $proveedor->contacto }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            @livewire('admin.produccion.proveedores.nuevo-proveedor', ['proveedor_id' => $proveedor->id], key($proveedor->id))
                                        </div>
                                    </div>
                                </div> 
                            </div>
                            <!-- Delete Modal --> 
                            <div class="modal fade" id="deleteModal{{ $proveedor->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header"> 
                                            <h5 class="modal-title" id="exampleModalLabel">{{ $proveedor->contacto }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            ¿Deseas eliminar al proveedor: <b>{{ $proveedor->contacto }}</b>?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn bg-gradient-danger my-0" data-bs-dismiss="modal" wire:click="deleteProveedor({{ $proveedor->id }})">Eliminar</button>
                                            <button type="button" class="btn bg-gradient-secondary my-0" data-bs-dismiss="modal">Cerrar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
                {{ $proveedores->links() }}
            </div>
        </div>
    </div>
    <div class="alerts"> 
        @if (session('success'))
            <script>
                Swal.fire(
                'Hecho',
                    `{{ session('success') }}`,
                'success'
                );
            </script>
        @endif
    </div> 
</div>
