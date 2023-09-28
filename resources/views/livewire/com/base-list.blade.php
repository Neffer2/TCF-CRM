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
                    <th>Cliente</th>
                    <th>Proyecto</th> 
                    <th>COD_CC</th>
                    <th>Valor Original</th>
                    <th>Valor</th> 
                    <th>Estado</th> 
                    <th>Cuenta</th>
                    <th>Inicio</th>
                    <th>Fin</th>
                    <th>Editor</th>
                    <th>ACCIONES</th> 
                </tr> 
                </thead> 
                <tbody>   
                    @foreach ($list as $key => $item)
                    <tr> 
                        <td>{{ $key+=1 }}</td>
                        <td class="text-sm font-weight-normal">{{ $item->fecha }}</td>
                        <td class="text-sm font-weight-normal">
                            {{ $item->nom_cliente }}
                        </td>
                        <td class="text-sm font-weight-normal">
                            {{ $item->nom_proyecto }}
                        </td>
                        <td class="text-sm font-weight-normal">
                            {{ $item->cod_cc }}
                        </td> 
                        <td class="text-sm font-weight-normal">
                            {{ number_format($item->valor_original) }}
                        </td>
                        <td class="text-sm font-weight-normal">
                            {{ number_format($item->valor_proyecto) }}
                        </td>
                        <td class="text-sm font-weight-normal">
                            {{ $item->estado_cuenta->description }}
                        </td>
                        <td class="text-sm font-weight-normal">{{ $item->cuenta->description }}</td>
                        <td class="text-sm font-weight-normal">{{ $item->fecha_inicio }}</td>
                        <td class="text-sm font-weight-normal">{{ $item->dura_mes }}</td>
                        <td class="text-sm font-weight-normal">
                            @if ($item->id_asistente)
                                {{ $item->asistente->name }}
                            @endif                            
                        </td>
                        <td  colspan="2">
                            {{-- <button class="btn bg-gradient-danger btn-sm mb-0" data-bs-toggle="modal" data-bs-target="#modal{{ $item->id }}">Eliminar</button> --}}
                            <button class="btn bg-gradient-primary btn-sm mb-0" data-bs-toggle="modal" data-bs-target="#editmodal{{ $item->id }}"> Editar </button>
                        </td>
                    </tr> 
                    <div class="modal fade" id="editmodal{{ $item->id }}" tabindex="-1" aria-labelledby="editModal" aria-hidden="true">
                        <div class="modal-dialog"> 
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exampleModalLabel">Editar proyecto</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div :wire:key="'item-'.$item->id"> 
                                    @livewire('com.base.edit', ['proyecto_id' => $item->id, key('item-'.$item->id)])   
                                </div>
                            </div>
                        </div>
                    </div> 

                    <div class="modal fade" id="modal{{ $item->id }}" tabindex="-1" aria-labelledby="Modal" aria-hidden="true">
                        <div class="modal-dialog">
                            <form action="{{ route('delete-proyecto', $item->id) }}" method="POST">
                            @csrf
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exampleModalLabel">¿Estas seguro?</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                        Desea eliminar el proyeto: <b>{{ $item->nom_proyecto }}</b>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Cancelar</button>
                                        <button type="submit" class="btn bg-gradient-danger">Eliminar</button>
                                    </div>
                                </div> 
                            </div>
                        </form>
                    </div> 
                    @endforeach
                </tbody>
            </table>
        </div>

        <form action="{{ route('base-export', auth()->user()->id) }}" method="POST" class="d-flex justify-content-center"> 
            @csrf
            <button type="submit" class="btn bg-gradient-warning mt-3">
                <span class="btn-inner--icon"><i class="ni ni-cloud-download-95 me-1"></i></span>
                <span class="btn-inner--text">Descargar Base Comercial</span>
            </button>
        </form>
    </div>
</div>
