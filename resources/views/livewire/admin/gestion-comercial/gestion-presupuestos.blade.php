<div class="card">
    <div class="card-header p-0 px-2 mt-3">
        <div class="row">
            <div class="col-md-12">
                <h3 class="mb-0">Aprobar</h3>
                <p class="text-sm mb-0">Presupuestos por aprobar.</p>
            </div>
            <div class="form-group col-md-1 mb-0">
                <label for="estados">Estados:</label>
                <select id="estados" class="form-control" wire:model="filter">
                    <option selected value="0">Todos</option>                    
                    @foreach ($estados as $estado)
                        <option value="{{ $estado->id }}">{{ $estado->description }}</option>                    
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-2 mb-0">
                <label for="comercial">Margen:</label>
                <select id="comercial" class="form-control" wire:model="margen">
                    <option value="">Seleccionar</option>
                    <option value=">">Mayor a 35</option>
                    <option value="<">Menor a 35</option>
                </select>
            </div>
            <div class="form-group col-md-2 mb-0">
                <label for="comercial">Fecha:</label>
                <select id="comercial" class="form-control" wire:model="fecha">
                    <option value="asc">Seleccionar</option>
                    <option value="asc">M&aacute;s reciente</option>
                    <option value="desc">M&aacute;s antiguos</option>
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
                    @foreach ($presupuestos as $presupuesto)
                        <tr>
                            <td style="width: 16rem;">
                                <div class="d-flex px-2 py-1">
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
                                <p class="text-xs font-weight-bold mb-0">Venta Proyecto</p>
                                <p class="text-xs text-secondary mb-0">$ {{ number_format($presupuesto->venta_proy) }}</p>
                            </td>
                            <td> 
                                <p class="text-xs font-weight-bold mb-0">Costos Proyecto</p>
                                <p class="text-xs text-secondary mb-0">$ {{ number_format($presupuesto->costos_proy) }}</p>
                            </td>
                            <td>
                                <p class="text-xs font-weight-bold mb-0">Margen Proyecto</p>
                                <p class="text-xs text-secondary mb-0">$ {{ $presupuesto->margen_proy }} %</p>
                            </td>
                            <td class="d-flex align-items-start">
                                <a class="btn bg-gradient-primary m-0 me-1" href="{{ route('presupuesto', $presupuesto->id_gestion) }}">Ver</a>
                                <select @if($presupuesto->estado_id == 1) disabled @endif class="form-control mb-1" wire:change="cambioEstado({{ $presupuesto->id }}, event.currentTarget.value)" wire:loading.attr="disabled">
                                    @foreach ($estados as $estado) 
                                        @if ($presupuesto->estado_id == $estado->id)
                                            <option selected value="{{ $estado->id }}">{{ $estado->description }}</option>
                                        @else                                        
                                            @if ($estado->id == 1 && !is_null($presupuesto->cod_cc))                                        
                                                <option value="{{ $estado->id }}">{{ $estado->description }}</option>
                                            @endif
                                        @endif 
                                    @endforeach
                                    <option value="3">Rechazar</option>
                                </select>
                            </td>
                        </tr>  
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="row p-2 pt-0">
        <div class="col-md-6">
            @php
                $presupuestosArray = $presupuestos->toArray();
                $registros_page = sizeof($presupuestosArray['data']);
                $total = $presupuestosArray['total'];
            @endphp
            <span class="text-xs text-secondary mb-0">Mostrando {{ $registros_page }} registros de {{ $total }}.</span>        
        </div>
        <div class="col-md-12 table-responsive">
            {{ $presupuestos->links() }}
        </div>
    </div>
</div>
 