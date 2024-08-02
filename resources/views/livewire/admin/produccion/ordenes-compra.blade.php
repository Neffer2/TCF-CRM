@if (Auth::user()->rol == 1)
    <div>
        <div class="card"> 
            <div class="card-header p-0 mx-3 mt-3 position-relative z-index-1 col-md-4">
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="mb-0">Ordenes de compra</h3>
                        <p class="text-sm mb-0">Lista de ordenes de compra por revisar.</p>
                    </div>
                    <div class="col-md-4">  
                        <label for="comercial">Buscar:</label>
                        <input type="text" wire:model="cod_cc" class="form-control" placeholder="Centro de costos">
                    </div>
                    <div class="col-md-4"> 
                        <label for="filtro_fecha">Fecha:</label>
                        <select id="filtro_fecha" class="form-control" wire:model="fecha">
                            <option value="asc">Seleccionar</option>
                            <option value="asc">M&aacute;s antiguos</option>
                            <option value="desc">M&aacute;s recientes</option>
                        </select>
                    </div> 
                    <div class="col-md-4">
                        <label for="estado">Estados:</label>
                        <select id="estado" class="form-control" wire:model="estado">
                            <option value="">Seleccionar</option>
                            @foreach ($estados as $estado)
                                <option value="{{ $estado->id }}">{{ $estado->description }}</option>
                            @endforeach 
                        </select> 
                    </div>
                </div>
            </div> 
            <div class="table-responsive">
                <table class="table align-items-center mb-0"> 
                    <thead>  
                        <tr> 
                            <th colspan="1" class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">DATOS DE PROYECTO</th>
                            <th colspan="6" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">M&eacute;tricas</th>
                            <th colspan="1" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($ordenes as $orden)
                            @if ($orden->tipo_oc == 1)
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
                                        <p class="text-xs font-weight-bold mb-0">Proveedor</p>
                                        <span class="text-xs text-secondary mb-0">{{ $orden->proveedor->tercero }}</span>
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
                                        <p class="text-xs text-secondary mb-0">
                                            @if ($orden->presupuesto->productor_info)
                                                {{ $orden->presupuesto->productor_info->name }}
                                            @else 
                                                NO ASIGNADO
                                            @endif
                                        </p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">Estado</p>
                                        <p class="text-xs text-secondary mb-0">{{ $orden->estado_oc->description }}</p>
                                    </td> 
                                    <td class="d-flex align-items-center justify-content-center">
                                        <a class="btn bg-gradient-primary m-0 me-1 mb-1" href="{{ route('orden-juridica', ['orden' => $orden->id]) }}">Ver</a> 
                                    </td>
                                </tr>  
                            @elseif($orden->tipo_oc == 2)
                                <tr>
                                    <td style="width: 16rem;">
                                        <div class="d-flex px-2 py-1" title="Orden #{{ $orden->id }}"> 
                                            <div>
                                                <img src="https://www.bullmarketing.com.co/wp-content/uploads/2022/04/cropped-favicon-bull-192x192.png" class="avatar avatar-sm me-3">
                                            </div>
                                            <div class="d-flex flex-column justify-content-center">                            
                                                <h6 class="mb-0 text-xs" >Orden #{{ $orden->id }}</h6>
                                                <p class="text-xs text-secondary mb-0">{{ $orden->id }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">Tipo</p> 
                                        <span class="badge badge-sm badge-info">{{ $orden->tipo->description }}</span>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">Tercero</p> 
                                        <span class="text-xs text-secondary mb-0">{{ $orden->naturalInfo->tercero->nombre }} {{ $orden->naturalInfo->tercero->apellido }}</span>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">Fecha</p>
                                        <p class="text-xs text-secondary mb-0">{{ $orden->created_at }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">Productor</p>
                                        <p class="text-xs text-secondary mb-0">{{ $orden->naturalInfo->productor->name }}</p>
                                    </td>
                                    <td colspan="2">
                                        <p class="text-xs font-weight-bold mb-0">Estado</p>
                                        <p class="text-xs text-secondary mb-0">{{ $orden->estado_oc->description }}</p>
                                    </td> 
                                    <td class="d-flex align-items-center justify-content-center">
                                        <a class="btn bg-gradient-primary m-0 me-1 mb-1" href="{{ route('orden-natural', ['orden_id' => $orden->id]) }}">Ver</a>  
                                    </td>
                                </tr>  
                            @endif
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
@elseif(Auth::user()->rol == 7)
    <div>
        <div class="card"> 
            <div class="card-header p-0 mx-3 mt-3 position-relative z-index-1 col-md-4">
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="mb-0">Ordenes de compra</h3>
                        <p class="text-sm mb-0">Lista de ordenes de compra.</p>
                    </div>
                    {{-- <div class="col-md-4">  
                        <label for="comercial">Buscar:</label>
                        <input type="text" wire:model="cod_cc" class="form-control" placeholder="Centro de costos">
                    </div> --}}
                    <div class="col-md-4">
                        <label for="filtro_fecha">Fecha:</label>
                        <select id="filtro_fecha" class="form-control" wire:model="fecha">
                            <option value="asc">Seleccionar</option>
                            <option value="asc">M&aacute;s antiguos</option>
                            <option value="desc">M&aacute;s recientes</option>
                        </select>
                    </div> 
                    <div class="col-md-4">
                        <label for="estado">Estados:</label>
                        <select id="estado" class="form-control" wire:model="estado">
                            <option value="">Seleccionar</option>
                            @foreach ($estados as $estado)
                                <option value="{{ $estado->id }}">{{ $estado->description }}</option>
                            @endforeach 
                        </select> 
                    </div>
                </div>
            </div> 
            <div class="table-responsive">
                <table class="table align-items-center mb-0"> 
                    <thead>  
                        <tr> 
                            <th colspan="6" class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">DATOS DE PROYECTO</th>
                            <th colspan="1" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Acciones</th>
                        </tr> 
                    </thead>
                    <tbody>
                        @foreach ($ordenes as $key => $orden) 
                            <tr>
                                <td style="width: 16rem;">
                                    <div class="d-flex px-2 py-1" title="Orden #{{ $orden->id }}"> 
                                        <div>
                                            <img src="https://www.bullmarketing.com.co/wp-content/uploads/2022/04/cropped-favicon-bull-192x192.png" class="avatar avatar-sm me-3">
                                        </div>
                                        <div class="d-flex flex-column justify-content-center">                            
                                            <h6 class="mb-0 text-xs" >Orden #{{ $orden->id }}</h6>
                                            <p class="text-xs text-secondary mb-0">{{ $orden->id }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <p class="text-xs font-weight-bold mb-0">Tipo</p> 
                                    <span class="badge badge-sm badge-info">{{ $orden->tipo->description }}</span>
                                </td>
                                <td>
                                    <p class="text-xs font-weight-bold mb-0">Tercero</p> 
                                    <span class="text-xs text-secondary mb-0">{{ $orden->naturalInfo->tercero->nombre }} {{ $orden->naturalInfo->tercero->apellido }}</span>
                                </td>
                                <td>
                                    <p class="text-xs font-weight-bold mb-0">Estado</p>
                                    <p class="text-xs text-secondary mb-0">{{ $orden->estado_oc->description }}</p>
                                </td> 
                                <td>
                                    <p class="text-xs font-weight-bold mb-0">Fecha</p>
                                    <p class="text-xs text-secondary mb-0">{{ $orden->created_at }}</p>
                                </td>
                                <td>
                                    <a class="btn btn-success m-0 me-1 mb-1"
                                    href="https://wa.me/{{ $orden->naturalInfo->tercero->telefono }}?text=¡Hola! Tu número de orden es: {{ $orden->id }}. Puedes seguir el estado de tu pago desde este enlace: {{ route('consulta-terceros') }}?orden={{ $orden->id }}. ¡Gracias por tus servicios y que tengas un día fabuloso! Bullmarketing.com.co"
                                    target="_blank">
                                        <i class="fa-brands fa-whatsapp"></i> 
                                    </a>
                                </td> 
                                <td class="d-flex align-items-center justify-content-center">
                                    <a class="btn bg-gradient-primary m-0 me-1 mb-1" href="{{ route('orden-natural-prod', ['orden_id' => $orden->id]) }}">Ver</a>  
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
@endif

