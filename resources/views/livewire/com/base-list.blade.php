<div class="col-lg-12 col-12 mx-auto">
    <div class="card">
        <div class="card-header p-0 px-3 mt-3 position-relative z-index-1 col-md-12">
            <div class="row gy-2">            
                <div class="col-md-12">
                    <h3 class="mb-0">Base comercial</h3> 
                    <p class="text-sm mb-0">Aqu&iacute; puedes gestionar los estados de tu base comercial.</p>
                </div>
                <div class="col-md-1 form-group">
                    <label for="comercial">Año:</label>
                    <select wire:model="año" class="form-control">
                        <option value="">Seleccionar</option>
                        @foreach ($años as $año)
                            <option value="{{ $año->id }}">{{ $año->description }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <label for="comercial">Buscar centro de costos:</label> 
                    <input type="text" wire:model="centro" class="form-control" placeholder="Centro de costos">
                </div>
                <div class="form-group col-md-2">
                    <label for="comercial">Buscar nombre del proyecto:</label> 
                    <input type="text" wire:model="nomProyecto" class="form-control" placeholder="Nombre del proyecto">
                </div>
                <div class="form-group col-md-2">
                    <label for="comercial">Estado:</label> 
                    <select wire:model="estado" class="form-control">
                        <option value="">Seleccionar</option>
                        @foreach ($estados as $estado)
                            <option value="{{ $estado->id }}">{{ $estado->description }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <label for="comercial">Fecha:</label> 
                    <select wire:model="orderBy" class="form-control">
                        <option value="DESC">Seleccionar</option>
                        <option value="ASC">Mas antiguos</option>
                        <option value="DESC">Mas recientes</option>
                    </select>
                </div>
            </div> 
        </div>
        <div class="card-body pt-1">
            <div class="table-responsive">
                <table class="table font-table align-items-center table-striped mb-0">
                    <thead class="font-weight-bold bg-gradient-warning text-white">
                        <tr class="text-center"> 
                            <th>FECHA</th>
                            <th>CLIENTE</th>
                            <th>PROYECTO</th> 
                            <th>COD_CC</th>
                            <th>VALOR ORIGINAL</th>
                            <th>VALOR</th> 
                            <th>ESTADO</th> 
                            <th>CUENTA</th>
                            <th>INICIO</th>
                            <th>FIN</th>
                            <th>EDITOR</th>
                            <th>ACCIONES</th>  
                        </tr> 
                    </thead>
                    <tbody>    
                        @foreach ($proyectos as $key => $proyecto)
                            <tr> 
                                <td class="text-center font-weight-bold">{{ $proyecto->fecha }}</td>
                                <td class="text-center font-weight-bold">
                                    {{ $proyecto->nom_cliente }}
                                </td>
                                <td class="font-weight-bold">
                                    {{ $proyecto->nom_proyecto }}
                                </td>
                                <td class="text-center font-weight-bold">
                                    {{ $proyecto->cod_cc }}
                                </td> 
                                <td class="text-center font-weight-bold">
                                    $ {{ number_format($proyecto->valor_original) }}
                                </td>
                                <td class="text-center font-weight-bold">
                                    $ {{ number_format($proyecto->valor_proyecto) }}
                                </td>
                                <td class="font-weight-bold text-center">
                                    {{ $proyecto->estado_cuenta->description }}
                                </td>
                                <td class="font-weight-bold text-center">{{ $proyecto->cuenta->description }}</td>
                                <td class="font-weight-bold text-center">{{ $proyecto->fecha_inicio }}</td>
                                <td class="font-weight-bold text-center">{{ $proyecto->dura_mes }}</td>
                                <td class="font-weight-bold text-center">
                                    @if ($proyecto->id_asistente)
                                        {{ $proyecto->asistente->name }}
                                    @endif                            
                                </td>
                                <td class="text-center" colspan="2">
                                    {{-- <button class="btn bg-gradient-danger btn-sm mb-0" data-bs-toggle="modal" data-bs-target="#modal{{ $item->id }}">Eliminar</button> --}}
                                    <button class="btn bg-gradient-primary btn-sm mb-0" data-bs-toggle="modal" data-bs-target="#editmodal{{ $proyecto->id }}"> Editar </button>
                                </td>
                            </tr> 
                            <div class="modal fade" id="editmodal{{ $proyecto->id }}" tabindex="-1" aria-labelledby="editModal" aria-hidden="true">
                                <div class="modal-dialog"> 
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="exampleModalLabel">Editar proyecto</h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div :wire:key="'proyecto-'.$proyecto->id"> 
                                            @livewire('com.base.edit', ['proyecto_id' => $proyecto->id, key('proyecto-'.$proyecto->id)])   
                                        </div>
                                    </div>
                                </div>
                            </div> 
        
                            <div class="modal fade" id="modal{{ $proyecto->id }}" tabindex="-1" aria-labelledby="Modal" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form action="{{ route('delete-proyecto', $proyecto->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="exampleModalLabel">¿Estas seguro?</h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                                Desea eliminar el proyeto: <b>{{ $proyecto->nom_proyecto }}</b>
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
            <div class="row p-2">
                <div class="col-md-6">
                    @php
                        $proyectosArray = $proyectos->toArray();
                        $registros_page = sizeof($proyectosArray['data']);
                        $total = $proyectosArray['total'];
                    @endphp
                    <span class="text-xs text-secondary mb-0">Mostrando {{ $registros_page }} registros de {{ $total }}.</span>        
                </div>
                <div class="col-md-12 table-responsive">
                    {{ $proyectos->links() }}
                </div>
            </div>
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
