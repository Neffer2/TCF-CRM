<div>
    <div class="card">
        <div class="card-header p-0 mx-3 mt-3 position-relative z-index-1 col-md-3">
            <div class="row">
                <div class="col-md-12">
                    <h3 class="mb-0">Ordenes de compra</h3>
                    <p class="text-sm mb-0">Lista de ordenes de compra por revisar.</p>
                </div>
                <div class="col-md-6">  
                    <label for="comercial">Buscar:</label>
                    <input type="text" wire:model="cod_cc" class="form-control" placeholder="Centro de costos">
                </div>
                <div class="col-md-6">
                    <label for="filtro_fecha">Fecha:</label>
                    <select id="filtro_fecha" class="form-control" wire:model="fecha">
                        <option value="asc">Seleccionar</option>
                        <option value="asc">M&aacute;s antiguos</option>
                        <option value="desc">M&aacute;s recientes</option>
                    </select> 
                </div>
            </div>
        </div> 
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
                    @foreach ($ordenes as $orden)
                        <tr>
                            <td style="width: 16rem;">
                                <div class="d-flex px-2 py-1" title="{{ $orden->presupuesto->gestion->nom_proyecto_cot }}"> 
                                    <div>
                                        <img src="https://www.bullmarketing.com.co/wp-content/uploads/2022/04/cropped-favicon-bull-192x192.png" class="avatar avatar-sm me-3">
                                    </div>
                                    <div class="d-flex flex-column justify-content-center">                            
                                        @if (strlen($orden->presupuesto->gestion->nom_proyecto_cot) > 80)
                                            <h6 class="mb-0 text-xs" >{{ substr($orden->presupuesto->gestion->nom_proyecto_cot, 0, 80) }}...</h6>
                                        @else 
                                            <h6 class="mb-0 text-xs" >{{ substr($orden->presupuesto->gestion->nom_proyecto_cot, 0, 80) }}</h6>
                                        @endif
                                        <p class="text-xs text-secondary mb-0">{{ $orden->presupuesto->gestion->contacto->empresa }}</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <p class="text-xs font-weight-bold mb-0">Tipo</p> 
                                <span class="badge badge-sm badge-primary">{{ $orden->tipo->description }}</span>
                            </td>
                            <td>
                                <p class="text-xs font-weight-bold mb-0">Centro de costos</p>
                                <textarea disabled rows="1" class="text-xs text-secondary mb-0">{{ $orden->presupuesto->cod_cc }}</textarea>
                            </td>
                            <td>
                                <p class="text-xs font-weight-bold mb-0">Comercial</p>
                                <p class="text-xs text-secondary mb-0">{{ $orden->presupuesto->gestion->comercial->name }}</p>
                            </td>
                            <td> 
                                <p class="text-xs font-weight-bold mb-0">Productor</p>
                                <p class="text-xs text-secondary mb-0">{{ $orden->presupuesto->productor_info->name }}</p>
                            </td>
                            <td>
                                <p class="text-xs font-weight-bold mb-0">Estado</p>
                                <p class="text-xs text-secondary mb-0">Revision</p>
                            </td>
                            <td class="d-flex align-items-center justify-content-center">
                                <a class="btn bg-gradient-primary m-0 me-1 mb-1" href="{{ route('orden-juridica', ['orden' => $orden->id]) }}">Ver</a> 
                            </td>
                        </tr>  
                    @endforeach
                    <tr> 
                        @php
                            $ordenesArray = $ordenes->toArray();
                            $registros_page = sizeof($ordenesArray['data']);
                            $total = $ordenesArray['total'];
                        @endphp
                        <td colspan="1" class="d-flex text-xs text-secondary mb-0">Mostrando {{ $registros_page }} registros de {{ $total }}.</td>
                        <td colspan="5" class="d-flex pt-0">{{ $ordenes->links() }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
