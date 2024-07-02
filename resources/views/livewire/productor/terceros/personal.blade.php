<div>
    <div class="card">
        <div class="card-header p-0 px-3 mt-3">
            <div class="d-flex align-items-baseline" x-data="{ collapse: true}" x-cloak>
              <button class="btn bg-gradient-warning me-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample" x-on:click="collapse = !collapse">
                <i class="fas fa-plus text-white" aria-hidden="true" x-show="collapse"></i>
                <i class="fa-solid fa-minus text-white" aria-hidden="true" x-cloak x-show="!collapse"></i>
              </button> 
              <h5><b>Registrar personal</b></h5>
            </div>
            <div class="collapse" id="collapseExample">
                <div class="card card-body mb-2">
                    @livewire('productor.terceros.nuevo-personal')
              </div>          
            </div>

            <div class="row gy-1">            
                <div class="col-md-12">
                    <h3 class="mb-0">Personal</h3>
                    <p class="text-sm mb-0">Lista completa de personal disponible.</p>
                </div>    
                <div class="form-group col-md-2 mb-0">
                    <label for="comercial">Buscar:</label> 
                    <input type="text" wire:model="cod_cc" class="form-control" placeholder="C&eacute;dula">
                </div> 
                <div class="form-group col-md-2 mb-0">
                    <label for="comercial">Buscar:</label> 
                    <input type="text" wire:model="nom_proyecto" class="form-control" placeholder="Nombre">
                </div> 
                <div class="form-group col-md-2 mb-0">
                    <label for="comercial">Estado:</label> 
                    <select wire:model="comercial" class="form-control">
                        <option value="">Seleccionar</option>
                        {{-- @foreach ($comerciales as $comercial)
                            <option value="{{ $comercial->id }}">{{ $comercial->name }}</option>                            
                        @endforeach --}}
                    </select>
                </div> 
            </div>  
        </div>
        <div class="card-body p-0 pt-1">
            <div class="table-responsive"> 
                <table class="table align-items-center mb-0">
                    <thead> 
                        <tr>
                            <th colspan="1" class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">DATOS DE PROYECTO</th>
                            <th colspan="5" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">M&eacute;tricas</th>
                            <th colspan="1" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Acciones</th>
                        </tr>
                    </thead> 
                    <tbody> 
                        <tr>
                            <td style="width: 16rem;">
                                {{-- <div class="d-flex px-2 py-1" title="{{ $presupuesto->gestion->nom_proyecto_cot }}"> --}}
                                <div class="d-flex px-2 py-1" title="1">
                                    <div>
                                        <img src="https://www.bullmarketing.com.co/wp-content/uploads/2022/04/cropped-favicon-bull-192x192.png" class="avatar avatar-sm me-3">
                                    </div>
                                    <div class="d-flex flex-column justify-content-center">                                        
                                        <h6 class="mb-0 text-xs">2</h6>
                                        <p class="text-xs text-secondary mb-0">1</p>
                                    </div>
                                </div>
                            </td>                            
                            <td>
                                <p class="text-xs font-weight-bold mb-0">Fecha</p>
                                <p class="text-xs text-secondary mb-0">1</p>
                            </td> 
                            <td>
                                <p class="text-xs font-weight-bold mb-0">Comercial</p>
                                <p class="text-xs text-secondary mb-0">1</p>
                            </td>  
                            <td>
                                <p class="text-xs font-weight-bold mb-0">Centro de costos</p>
                                <textarea disabled rows="1" class="text-xs text-secondary mb-0">1</textarea>
                            </td>
                            <td>
                                <p class="text-xs font-weight-bold mb-0">Estado</p>
                                <p class="text-xs text-secondary mb-0">1</p>
                            </td>
                            <td>
                                <p class="text-xs font-weight-bold mb-0">Margen Proyecto</p>
                                <p class="text-xs text-secondary mb-0">$ 1 %</p>
                            </td> 
                            <td class="d-flex align-items-center justify-content-center">
                                {{-- <a class="btn bg-gradient-primary m-0 me-1 mb-2" target="_blank" href="{{ route('presupuesto', $presupuesto->id_gestion) }}">1</a> --}}
                                <a class="btn bg-gradient-primary m-0 me-1 mb-2" target="_blank" href="">1</a>
                            </td>
                        </tr> 
                        {{-- @foreach ($presupuestos as $presupuesto)
                            <tr>
                                <td style="width: 16rem;">
                                    <div class="d-flex px-2 py-1" title="{{ $presupuesto->gestion->nom_proyecto_cot }}">
                                        <div>
                                            <img src="https://www.bullmarketing.com.co/wp-content/uploads/2022/04/cropped-favicon-bull-192x192.png" class="avatar avatar-sm me-3">
                                        </div>
                                        <div class="d-flex flex-column justify-content-center">                                        
                                            @if (strlen($presupuesto->gestion->nom_proyecto_cot) > 80)
                                                <h6 class="mb-0 text-xs" >{{ substr($presupuesto->gestion->nom_proyecto_cot, 0, 80) }}...</h6>
                                            @else
                                                <h6 class="mb-0 text-xs" >{{ $presupuesto->gestion->nom_proyecto_cot }}</h6>
                                            @endif
                                            <p class="text-xs text-secondary mb-0">{{ $presupuesto->gestion->contacto->empresa }}</p>
                                        </div>
                                    </div>
                                </td>                            
                                <td>
                                    <p class="text-xs font-weight-bold mb-0">Fecha</p>
                                    <p class="text-xs text-secondary mb-0">{{ $presupuesto->created_at }}</p>
                                </td> 
                                <td>
                                    <p class="text-xs font-weight-bold mb-0">Comercial</p>
                                    <p class="text-xs text-secondary mb-0">{{ $presupuesto->gestion->comercial->name }}</p>
                                </td>  
                                <td>
                                    <p class="text-xs font-weight-bold mb-0">Centro de costos</p>
                                    <textarea disabled rows="1" class="text-xs text-secondary mb-0">{{ $presupuesto->cod_cc }}</textarea>
                                </td>
                                <td>
                                    <p class="text-xs font-weight-bold mb-0">Estado</p>
                                    <p class="text-xs text-secondary mb-0">{{ $presupuesto->estado->description }}</p>
                                </td>
                                <td>
                                    <p class="text-xs font-weight-bold mb-0">Margen Proyecto</p>
                                    <p class="text-xs text-secondary mb-0">$ {{ $presupuesto->margen_proy }} %</p>
                                </td> 
                                <td class="d-flex align-items-center justify-content-center">
                                    <a class="btn bg-gradient-primary m-0 me-1 mb-2" target="_blank" href="{{ route('presupuesto', $presupuesto->id_gestion) }}">Ver</a>
                                </td>
                            </tr> 
                        @endforeach --}}
                    </tbody>
                </table>
            </div>
            <div class="row p-2">
                <div class="col-md-6">
                    {{-- @php
                        $presupuestosArray = $presupuestos->toArray();
                        $registros_page = sizeof($presupuestosArray['data']);
                        $total = $presupuestosArray['total'];
                    @endphp
                    <span class="text-xs text-secondary mb-0">Mostrando {{ $registros_page }} registros de {{ $total }}.</span>         --}}
                </div>
                <div class="col-md-12 table-responsive">
                    {{-- {{ $presupuestos->links() }} --}}
                </div>
            </div>
        </div>
    </div>
</div>