<div>
    <div class="card">
        <div class="card-header p-0 mx-3 mt-3 position-relative z-index-1 col-md-1">
            <select class="form-control" wire:model="filter">
                <option selected value="0">Todos</option>                    
                @foreach ($estados as $estado)
                    <option value="{{ $estado->id }}">{{ $estado->description }}</option>                    
                @endforeach
            </select>
        </div>
        <div class="table-responsive">
            <table class="table align-items-center mb-0">
                <thead> 
                    <tr>
                        <th colspan="2" class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">DATOS DE PROYECTO</th>
                        <th colspan="3" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">M&eacute;tricas</th>
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
                                <a class="btn bg-gradient-primary m-0 me-1" href="{{ route('presupuesto', $presupuesto->id_gestion) }}" target="_blank">Ver</a>
                                <select class="form-control mb-1" wire:change="cambioEstado({{ $presupuesto->id }}, event.currentTarget.value)">
                                    <option value="">Seleccionar</option>
                                    @foreach ($estados as $estado)
                                        @if ($presupuesto->estado_id == $estado->id)
                                            <option selected value="{{ $estado->id }}">{{ $estado->description }}</option>
                                        @else
                                            <option value="{{ $estado->id }}">{{ $estado->description }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </td>
                        </tr> 
                    @endforeach
                    <tr>
                        <td colspan="6" class="d-flex">{{ $presupuestos->links() }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
 