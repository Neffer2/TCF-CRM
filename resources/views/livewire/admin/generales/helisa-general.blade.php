<div>
    <div class="card">
        <div class="card-header p-0 px-3 mt-3 position-relative z-index-1 col-md-12">
            <div class="row">            
                <div class="col-md-12">
                    <h3 class="mb-0">Presupuestos</h3>
                    <p class="text-sm mb-0">Lista completa de presupuestos.</p>
                </div>
                <div class="form-group col-md-1">
                    <label for="comercial">Año:</label>
                    <select wire:model="año" class="form-control">
                        <option value="">Seleccionar</option>
                        @foreach ($años as $año)
                            <option value="{{ $año->id }}">{{ $año->description }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <label for="comercial">Buscar:</label> 
                    <input type="text" wire:model="centro" class="form-control" placeholder="Centro de costos">
                </div>
                <div class="form-group col-md-2">
                    <label for="comercial">Comercial:</label> 
                    <select wire:model="comercial" class="form-control">
                        <option value="">Seleccionar</option>
                        @foreach ($comerciales as $comercial)
                            <option value="{{ $comercial->id }}">{{ $comercial->name }}</option>
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
                    </tr>
                </thead>
                <tbody> 
                    @foreach ($registros_helisa as $registro_helisa)
                        <tr>
                            <td style="width: 16rem;">
                                <div class="d-flex px-2 py-1" title="{{ $registro_helisa->nom_centro_costos }}">
                                    <div>
                                        <img src="https://www.bullmarketing.com.co/wp-content/uploads/2022/04/cropped-favicon-bull-192x192.png" class="avatar avatar-sm me-3">
                                    </div>
                                    <div class="d-flex flex-column justify-content-center">
                                        <h6 class="mb-0 text-xs">{{ $registro_helisa->nom_tercero }}</h6>
                                        <p class="text-xs text-secondary mb-0">{{ $registro_helisa->nom_centro_costo }}</p>
                                    </div>
                                </div>
                            </td>                            
                            <td>
                                <p class="text-xs font-weight-bold mb-0">Fecha</p>
                                <p class="text-xs text-secondary mb-0">{{ $registro_helisa->fecha }}</p>
                            </td> 
                            <td>
                                <p class="text-xs font-weight-bold mb-0">Comercial</p>
                                <p class="text-xs text-secondary mb-0">{{ $registro_helisa->comercial_user->name }}</p>
                            </td>  
                            <td>
                                <p class="text-xs font-weight-bold mb-0">Centro de costos</p>
                                <textarea disabled rows="1" class="text-xs text-secondary mb-0">{{ $registro_helisa->centro }}</textarea>
                            </td>
                            <td>
                                <p class="text-xs font-weight-bold mb-0">Debito</p>
                                <p class="text-xs text-secondary mb-0">$ {{ number_format($registro_helisa->debito) }}</p>
                            </td> 
                            <td>
                                <p class="text-xs font-weight-bold mb-0">Cr&eacute;dito</p>
                                <p class="text-xs text-secondary mb-0">$ {{ number_format($registro_helisa->credito) }}</p>
                            </td>
                            <td>
                                <p class="text-xs font-weight-bold mb-0">Base factura</p>
                                <p class="text-xs text-secondary mb-0 font-weight-bold">$ {{ number_format($registro_helisa->base_factura) }}</p>
                            </td>
                        </tr> 
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="row p-2">
            <div class="col-md-6">
                @php
                    $registrosHelisaArray = $registros_helisa->toArray();
                    $registros_page = sizeof($registrosHelisaArray['data']);
                    $total = $registrosHelisaArray['total'];
                @endphp
                <span class="text-xs text-secondary mb-0">Mostrando {{ $registros_page }} registros de {{ $total }}.</span>        
            </div>
            <div class="col-md-12 table-responsive">
                {{ $registros_helisa->links() }}
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <button class="btn bg-gradient-warning m-2" wire:click="exportar">Exportar</button> 
            </div>
        </div>
    </div>
</div>
