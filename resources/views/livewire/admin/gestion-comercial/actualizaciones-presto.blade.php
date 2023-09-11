<div>
    <div class="card">
        <div class="card-header p-0 mx-3 mt-3 position-relative z-index-1 col-md-3">
            <div class="row">
                <div class="col-md-12">
                    <h3 class="mb-0">Actualizaciones</h3>
                    <p class="text-sm mb-0">Actualizaciones por aprobar.</p>
                </div>
                <div class="col-md-6">  
                    <label for="comercial">Buscar:</label>
                    <input type="text" wire:model="cod_cc" class="form-control">
                </div>
                <div class="col-md-6">
                    <label for="comercial">Fecha:</label>
                    <select id="comercial" class="form-control" wire:model="fecha">
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
                        <th colspan="2" class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">DATOS DE PROYECTO</th>
                        <th colspan="4" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">M&eacute;tricas</th>
                        <th colspan="1" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($presupuestos as $presupuesto)
                        <tr>
                            <td>
                                <div class="d-flex px-2 py-1">
                                    <div>
                                        <img src="https://www.bullmarketing.com.co/wp-content/uploads/2022/04/cropped-favicon-bull-192x192.png" class="avatar avatar-sm me-3">
                                    </div>
                                    <div class="d-flex flex-column justify-content-center">                                        
                                        @if (strlen($presupuesto->gestion->nom_proyecto_cot) > 30) 
                                            <h6 class="mb-0 text-xs" >{{ substr($presupuesto->gestion->nom_proyecto_cot, 0, -23) }}...</h6>
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
                            @if (Auth::user()->rol == 1)
                                <td class="d-flex align-items-start">
                                    <a class="btn bg-gradient-primary m-0 me-1" href="{{ route('presupuesto', $presupuesto->id_gestion) }}">Ver</a>
                                    <select @if($presupuesto->estado_id == 1) disabled @endif class="form-control mb-1" wire:change="cambioEstado({{ $presupuesto->id }}, event.currentTarget.value)">
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
                            @else
                                <td class="d-flex align-items-start"> 
                                    <a class="btn bg-gradient-warning" href="{{ route('presupuesto', $presupuesto->id_gestion) }}" target="_blank">Presupuesto</a>
                                </td>
                            @endif
                        </tr> 
                    @endforeach
                    <tr> 
                        @php
                            $presupuestosArray = $presupuestos->toArray();
                            $registros_page = sizeof($presupuestosArray['data']);
                            $total = $presupuestosArray['total'];
                        @endphp
                        <td colspan="1" class="d-flex text-xs text-secondary mb-0">Mostrando {{ $registros_page }} registros de {{ $total }}.</td>
                        <td colspan="5" class="d-flex pt-0">{{ $presupuestos->links() }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
