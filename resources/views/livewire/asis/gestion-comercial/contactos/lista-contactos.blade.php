<div class="card">
    <div class="table-responsive"> 
        <table class="table">
            <thead> 
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Informaci&oacute;n personal</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">CARGO</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">CONTACTO</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">ACCIONES</th>
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
                        <td>
                            <button class="btn bg-gradient-danger mb-0" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $contacto->id }}">Eliminar</button>
                        </td> 
                    </tr>
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