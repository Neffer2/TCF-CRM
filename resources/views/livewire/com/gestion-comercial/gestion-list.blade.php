<div class="card">
    <div class="table-responsive"> 
        <table class="table">
            <thead> 
                <tr>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Datos personales</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Datos corporativos</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Celular</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Estado</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Siguiente estado</th>
                </tr>
            </thead>
            <tbody>  
                @foreach ($datos as $dato)
                    <tr>
                        <td>
                            <div class="d-flex px-2 py-1">
                                <div>
                                    <img src="https://www.bullmarketing.com.co/wp-content/uploads/2022/04/cropped-favicon-bull-192x192.png" class="avatar avatar-sm me-3">
                                </div>
                                <div class="d-flex flex-column justify-content-center">
                                    <h6 class="mb-0 text-xs">{{ $dato->nombre }} {{ $dato->apellido }}</h6>
                                    <p class="text-xs text-secondary mb-0">{{ $dato->correo }}</p>
                                </div>
                            </div>
                        </td>
                        <td>
                            <p class="text-xs font-weight-bold mb-0">{{ $dato->cargo }}</p>
                            <p class="text-xs text-secondary mb-0">{{ $dato->empresa }}</p>
                        </td>
                        <td>
                            <p class="text-xs text-secondary mt-3">{{ $dato->celular }}</p>
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
                        <td class="align-middle d-flex justify-content-center">
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
                                        <div :wire:key="'item-'.$dato->id">
                                            @livewire('com.gestion-comercial.forms.oportunidad-form', ['lead_id' => $dato->id, key('item-'.$dato->id)])   
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
                                        <div :wire:key="'item-'.$dato->id">
                                            @livewire('com.gestion-comercial.forms.cotizacion-form', ['lead_id' => $dato->id], key('item-'.$dato->id))   
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn bg-gradient-danger mb-0" data-bs-dismiss="modal">Cancelar</button>
                                    </div>
                                </div> 
                            </div>
                        </div>
                    @elseif($dato->id_estado == 3)
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
                                        {{-- formulario propuesta --}} 
                                        <div :wire:key="'item-'.$dato->id">
                                            @livewire('com.gestion-comercial.forms.propuesta-form', ['lead_id' => $dato->id], key('item-'.$dato->id))   
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn bg-gradient-danger mb-0" data-bs-dismiss="modal">Cancelar</button>
                                    </div>
                                </div> 
                            </div> 
                        </div>
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
                                        <div :wire:key="'item-'.$dato->id">
                                            @livewire('com.gestion-comercial.forms.descicion-form', ['lead_id' => $dato->id, key('item-'.$dato->id)])   
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
                                        <div :wire:key="'item-'.$dato->id">
                                            @livewire('com.gestion-comercial.forms.new-proyecto', ['lead_id' => $dato->id, key('item-'.$dato->id)])   
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
                <tr>
                    <td colspan="6" class="d-flex">{{ $datos->links() }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>