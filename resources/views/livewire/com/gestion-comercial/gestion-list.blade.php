<div class="card">
    <div class="card-header p-0 px-3 mt-3">
        <div class="row">            
            <div class="col-md-12">
                <h3 class="mb-0">Gesti&oacute;n comercial</h3>
                <p class="text-sm mb-0">Lista completa de gestiones comerciales.</p>
            </div> 
            <div class="form-group col-md-1 mb-0"> 
                <label for="año">Año:</label> 
                <select id="año" wire:model="año" class="form-control">
                    <option value="">Seleccionar</option>
                    @foreach ($años as $año)
                        <option value="{{ $año->id }}">{{ $año->description }}</option>   
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-2 mb-0">  
                <label for="filtro_buscar">Buscar:</label> 
                <input id="filtro_buscar" type="text" wire:model="nomProyecto" class="form-control" placeholder="Nombre proyecto">
            </div>
            <div class="form-group col-md-2 mb-0"> 
                <label for="contacto">Contacto:</label> 
                <select id="contacto" wire:model="contacto" class="form-control">
                    <option value="">Seleccionar</option>
                    @foreach ($contactos as $contacto)
                        <option value="{{ $contacto->id }}">{{ $contacto->nombre }} {{ $contacto->apellido }} - {{ $contacto->empresa }}</option>   
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-2 mb-0"> 
                <label for="filtro_estado">Estado:</label> 
                <select id="filtro_estado" wire:model="estado" class="form-control">
                    <option value="">Seleccionar</option>
                    @foreach ($estados as $estado)
                        <option value="{{ $estado->id }}">{{ $estado->description }}</option>   
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-2 mb-0"> 
                <label for="filtro_fecha">Fecha:</label> 
                <select id="filtro_fecha" class="form-control" wire:model="order">
                    <option value="asc">Seleccionar</option>
                    <option value="asc">M&aacute;s antiguos</option>
                    <option value="desc">M&aacute;s recientes</option>
                </select> 
            </div> 
        </div> 
    </div>
    <div class="card-body p-0 pt-1">
        <div class="table-responsive">  
            <table class="table mb-0">
                <thead> 
                    <tr>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Datos de contacto</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2" colspan="2">Proyecto</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Estado</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Siguiente estado</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Acciones</th>
                    </tr> 
                </thead>
                <tbody>    
                    @foreach ($datos as $dato)
                        <tr> 
                            <td>
                                <div class="d-flex px-2 py-1" title="{{ $dato->nom_proyecto_cot }}">
                                    <div>
                                        <img src="https://www.bullmarketing.com.co/wp-content/uploads/2022/04/cropped-favicon-bull-192x192.png" class="avatar avatar-sm me-3">
                                    </div>
                                    <div class="d-flex flex-column justify-content-center">
                                        @if (strlen($dato->nom_proyecto_cot) > 50)
                                            <h6 class="mb-0 text-xs">{{ substr($dato->nom_proyecto_cot, 0, 50) }}...</h6>
                                        @else 
                                            <h6 class="mb-0 text-xs">{{ substr($dato->nom_proyecto_cot, 0, 50) }}</h6>
                                        @endif
                                        <p class="text-xs text-secondary mb-0">{{ $dato->contacto->empresa }}</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <p class="text-xs font-weight-bold mb-0">Valor proyecto</p>
                                @if ($dato->presupuesto)
                                    <p class="text-xs text-secondary mb-0">{{ number_format($dato->presupuesto->venta_proy) }} $</p>                                
                                @else 
                                    <p class="text-xs text-secondary mb-0">{{ number_format($dato->presto_cot) }} $</p>
                                @endif
                            </td>
                            <td>
                                <p class="text-xs font-weight-bold mb-0">{{ $dato->contacto->nombre }} {{ $dato->contacto->apellido }}</p>
                                <p class="text-xs text-secondary mb-0">{{ $dato->contacto->correo }}</p>
                            </td>
                            <td> 
                                <select name="" id="" class="form-control" disabled>
                                    @foreach ($estados as $estado)
                                        @if ($estado->id == $dato->id_estado)
                                            <option selected value="{{ $estado->id }}">{{ $estado->description }}</option>
                                        @else
                                            <option value="{{ $estado->id }}">{{ $estado->description }}</option>   
                                        @endif
                                    @endforeach
                                </select> 
                            </td>
                            <td class="align-middle" style="width: 10%">
                                @if($dato->id_estado == 5)
                                    <button class="btn bg-gradient-warning" data-bs-toggle="modal" data-bs-target="#Modal{{ $dato->id }}">
                                        Vendido
                                    </button>
                                @elseif ($dato->id_estado == 6)
                                    <button class="btn bg-gradient-warning" data-bs-toggle="modal" data-bs-target="#Modal{{ $dato->id }}">
                                        Perdido
                                    </button>
                                @elseif ($dato->id_estado == 7)
                                    <a class="btn bg-gradient-warning" href="{{ route('presupuesto', $dato->id) }}" target="_blank">
                                        Presupuesto @if ($dato->presupuesto) - {{ $dato->presupuesto->estado->description }} @endif
                                    </a>
                                @else 
                                    @if($dato->id_estado != 4)
                                        <button class="btn bg-gradient-warning" data-bs-toggle="modal" data-bs-target="#Modal{{ $dato->id }}">
                                            <i class="fa-solid fa-arrows-spin fa-spin fa-lg"></i>
                                        </button>
                                    @else 
                                        <button class="btn bg-gradient-warning me-3" data-bs-toggle="modal" data-bs-target="#Modalventa{{ $dato->id }}">
                                            Venta
                                        </button>
                                        <button class="btn bg-gradient-danger" data-bs-toggle="modal" data-bs-target="#ModalPerdido{{ $dato->id }}">
                                            Perdido
                                        </button>
                                    @endif
                                @endif  
                            </td> 
                            <td>
                                @if ($dato->id_estado != 5)
                                    <a class="btn bg-gradient-primary" href="{{ route('update-gestion-comercial', $dato->id) }}"> 
                                        Editar
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @if($dato->id_estado == 1)
                            <div class="modal fade" id="Modal{{ $dato->id }}" tabindex="-1" role="dialog" aria-labelledby="Modal{{ $dato->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">
                                                <b>CAMBIO DE ESTADO</b>
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div> 
                                        <div class="modal-body">
                                            {{-- formulario oportunidad --}} 
                                            <div :wire:key="'item-1'.$dato->id"> 
                                                @livewire('com.gestion-comercial.forms.oportunidad-form', ['lead_id' => $dato->id, key('item-1'.$dato->id)])   
                                            </div>
                                        </div> 
                                        <div class="modal-footer">
                                            <button type="button" class="btn bg-gradient-danger mb-0" data-bs-dismiss="modal">Cancelar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @elseif($dato->id_estado == 2)
                            <div class="modal fade" id="Modal{{ $dato->id }}" tabindex="-1" role="dialog" aria-labelledby="Modal{{ $dato->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">
                                                <b>CAMBIO DE ESTADO</b>
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            {{-- formulario Cotizacion --}} 
                                            <div :wire:key="'item-2'.$dato->id"> 
                                                @livewire('com.gestion-comercial.forms.cotizacion-form', ['lead_id' => $dato->id], key('item-2'.$dato->id))   
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn bg-gradient-danger mb-0" data-bs-dismiss="modal">Cancelar</button>
                                        </div>
                                    </div> 
                                </div>
                            </div>
                        {{-- @elseif($dato->id_estado == 3)
                            <div class="modal fade" id="Modal{{ $dato->id }}" tabindex="-1" role="dialog" aria-labelledby="Modal{{ $dato->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">
                                                <b>CAMBIO DE ESTADO</b>
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- formulario propuesta -->
                                            <div :wire:key="'item-'.$dato->id">
                                                @livewire('com.gestion-comercial.forms.propuesta-form', ['lead_id' => $dato->id], key('item-'.$dato->id))   
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn bg-gradient-danger mb-0" data-bs-dismiss="modal">Cancelar</button>
                                        </div>
                                    </div> 
                                </div> 
                            </div> --}} 
                        @elseif($dato->id_estado == 4)
                            <div class="modal fade" id="ModalPerdido{{ $dato->id }}" tabindex="-1" role="dialog" aria-labelledby="Modal{{ $dato->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">
                                                <b>PERDIDO</b>
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button> 
                                        </div>
                                        <div class="modal-body">
                                        {{-- formulario perdido --}} 
                                            <div :wire:key="'item-3'.$dato->id">
                                                @livewire('com.gestion-comercial.forms.descicion-form', ['lead_id' => $dato->id, key('item-3'.$dato->id)])   
                                            </div>
                                        </div>
                                        <div class="modal-footer"> 
                                            <button type="button" class="btn bg-gradient-danger mb-0" data-bs-dismiss="modal">Cancelar</button>
                                        </div>
                                    </div> 
                                </div>
                            </div>

                            <div class="modal fade" id="Modalventa{{ $dato->id }}" tabindex="-1" role="dialog" aria-labelledby="Modal{{ $dato->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div div class="modal-content">
                                        <div class="modal-header">
                                            <h6 class="modal-title" id="exampleModalLabel">
                                                <b>VENTA</b>
                                            </h6>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>  
                                        </div>
                                        <div class="modal-body">
                                            {{-- formulario venta --}} 
                                            <div :wire:key="'item-4'.$dato->id">
                                                @livewire('com.gestion-comercial.forms.new-proyecto', ['lead_id' => $dato->id, key('item-4'.$dato->id)])   
                                            </div>
                                        </div>
                                        <div class="modal-footer"> 
                                            <button type="button" class="btn bg-gradient-danger mb-0" data-bs-dismiss="modal">Cancelar</button>
                                        </div>
                                    </div> 
                                </div>
                            </div>
                        @endif 
                    @endforeach
                </tbody>
            </table>
        </div> 
        <div class="row p-2 pt-0">
            <div class="col-md-6">
                @php
                    $gestionesArray = $datos->toArray();
                    $registros_page = sizeof($gestionesArray['data']);
                    $total = $gestionesArray['total'];
                @endphp
                <span class="text-xs text-secondary mb-0">Mostrando {{ $registros_page }} registros de {{ $total }}.</span>        
            </div>
            <div class="col-md-12 table-responsive">
                {{ $datos->links() }}
            </div>
        </div>
    </div>
</div>