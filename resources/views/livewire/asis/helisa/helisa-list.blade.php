<div class="col-lg-12 col-12 mx-auto">
    <div class="card card-body mt-4">
        <!-- Card header --> 
        <div class="card-header">
            <h5 class="mb-0">Base comercial</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-flush" id="datatable-search">
                <thead class="thead-light">
                <tr> 
                    <th>#</th>
                    <th>Fecha</th>
                    <th>Tipo Doc</th>
                    <th>N&uacute;mero Doc</th>
                    <th>Identidad</th>
                    <th>Nombre del tercero</th>
                    <th>Centro costo</th>
                    <th>Nombre Centro de Costo</th>
                    <th>Debito</th>
                    <th>Credito</th>
                    <th>Comercial</th>
                    <th>Cuenta</th>
                    <th>Participaci&oacute;n</th>
                    <th>Base factura</th>
                    <th>Mes</th>
                    <th>Año</th>
                    <th>Comision</th> 
                    <th>Acciones</th> 
                </tr>  
                </thead> 
                <tbody>  
                    @foreach ($list as $key => $item)  
                        <tr>
                            <td class="text-sm font-weight-normal">{{ $key+=1 }}</td>
                            <td class="text-sm font-weight-normal">{{ $item->fecha }}</td>
                            <td class="text-sm font-weight-normal">{{ $item->tipo_doc }}</td>
                            <td class="text-sm font-weight-normal">{{ $item->num_doc }}</td>
                            <td class="text-sm font-weight-normal">{{ $item->identidad }}</td>
                            <td class="text-sm font-weight-normal">{{ $item->nom_tercero }}</td>
                            <td class="text-sm font-weight-normal">{{ $item->centro }}</td>
                            <td class="text-sm font-weight-normal">{{ $item->nom_centro_costo }}</td>
                            <td class="text-sm font-weight-normal">{{ $item->debito }}</td>
                            <td class="text-sm font-weight-normal">{{ $item->credito }}</td> 
                            <td class="text-sm font-weight-normal">{{ $item->comercial_user->name }}</td>
                            <td class="text-sm font-weight-normal">{{ $item->cuenta->description }}</td>
                            <td class="text-sm font-weight-normal">{{ $item->participacion }}</td>
                            <td class="text-sm font-weight-normal">{{ $item->base_factura }}</td>
                            <td class="text-sm font-weight-normal">{{ $item->mes }}</td>
                            <td class="text-sm font-weight-normal">{{ $item->año }}</td>
                            <td class="text-sm font-weight-normal">{{ $item->comision }}</td>
                            <td class="text-sm font-weight-normal">
                                <button class="btn bg-gradient-danger btn-sm mb-0" data-bs-toggle="modal" data-bs-target="#modal{{ $item->id }}">Eliminar</button>
                                <button class="btn bg-gradient-primary btn-sm mb-0" data-bs-toggle="modal" data-bs-target="#editmodal{{ $item->id }}"> Editar </button>
                            </td>
                        </tr>
                        {{-- edit modal --}}   
                        <div class="modal fade" id="editmodal{{ $item->id }}" tabindex="-1" aria-labelledby="editModal" aria-hidden="true">
                            <div class="modal-dialog"> 
                                @livewire('com.helisa.edit', ['id_helisa' => $item->id ])  
                            </div> 
                        </div>
                        {{-- delete modal --}}
                        <div class="modal fade" id="modal{{ $item->id }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <form action="{{ route('delete-registro', $item->centro) }}" method="POST">
                                @csrf 
                                <div class="modal-content">
                                    <div class="modal-header"> 
                                        <h1 class="modal-title fs-5" id="exampleModalLabel">¿Estas seguro?</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                            ¿Desea eliminar &eacute;ste registro?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Cancelar</button>
                                            <button type="submit" class="btn bg-gradient-warning">Eliminar</button>
                                        </div>
                                    </div> 
                                </div>
                            </form>
                        </div> 
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>