<div>
    <div class="card">
        <div class="card-header p-0 px-3 mt-3 position-relative z-index-1 col-md-12">
            <div class="row">            
                <div class="col-md-12">
                    <h3 class="mb-0">Base comercial general</h3> 
                    <p class="text-sm mb-0">Aqu&iacute; encontrar&aacute;s toda la base comercial almacenada en sistema.</p>
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
                <div class="form-group col-md-1">
                    <label for="comercial">Mes:</label>
                    <select wire:model="mes" class="form-control"> 
                        <option value="">Seleccionar</option>
                        @foreach ($yearInfo->Meses as $mes) 
                            <option value="{{ $mes->id }}">{{ $mes->description }}</option>
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
                <div class="form-group col-md-2">
                    <label for="comercial">Estado:</label> 
                    <select wire:model="estado" class="form-control">
                        <option value="">Seleccionar</option>
                        @foreach ($estados as $estado)
                            <option value="{{ $estado->id }}">{{ $estado->description }}</option>
                        @endforeach
                    </select>
                </div>
            </div> 
        </div>
        <div class="card-body">
            <div class="table-responsive"> 
                <table class="table font-table align-items-center table-striped mb-0">
                    <thead class="font-weight-bold bg-gradient-warning text-white">
                        <tr> 
                            <th>FECHA</th>
                            <th class="text-center">CLIENTE</th>
                            <th class="text-center">PROYECTO</th> 
                            <th>CENTRO DE COSTOS</th>
                            <th>VALOR</th> 
                            <th>ESTADO</th> 
                            <th class="text-center">INICIO</th>
                            <th class="text-center">FIN</th>
                            <th class="text-center">COMERCIAL</th>
                        </tr> 
                    </thead>
                    <tbody> 
                        @foreach ($baseComerciales as $key => $baseComercial)
                            <tr> 
                                <td class="font-weight-bold">{{ $baseComercial->fecha }}</td>
                                <td class="font-weight-bold">{{ $baseComercial->nom_cliente }}</td>
                                <td class="font-weight-bold">{{ $baseComercial->nom_proyecto }}</td>
                                <td class="font-weight-bold">{{ $baseComercial->cod_cc }}</td>
                                <td class="font-weight-bold">{{ number_format($baseComercial->valor_proyecto) }}</td>
                                <td class="font-weight-bold">{{ $baseComercial->estado_cuenta->description }}</td>
                                <td class="font-weight-bold">{{ $baseComercial->fecha_inicio }}</td>
                                <td class="font-weight-bold">{{ $baseComercial->dura_mes }}</td>
                                <td class="font-weight-bold">{{ $baseComercial->comercial->name }}</td>
                            </tr>  
                        @endforeach
                    </tbody>
                    <tfoot class="font-weight-bold bg-gradient-warning text-white">
                        <tr>
                            <th colspan="4" class="text-center">
                                <span class="font-weight-bold">Total:</span>
                            </th>
                            <th colspan="1">
                                <span class="font-weight-bold">{{ number_format($valorTotal) }}</span>
                            </th>
                            <th colspan="4"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="row p-2">
                <div class="col-md-6">
                    @php
                        $baseComercialesArray = $baseComerciales->toArray();
                        $registros_page = sizeof($baseComercialesArray['data']);
                        $total = $baseComercialesArray['total'];
                    @endphp
                    <span class="text-xs text-secondary mb-0">Mostrando {{ $registros_page }} registros de {{ $total }}.</span>        
                </div>
                <div class="col-md-12 table-responsive">
                    {{ $baseComerciales->links() }}
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <button class="btn bg-gradient-warning m-2" wire:click="exportar">Exportar</button> 
                </div>
            </div>
        </div>
    </div>
</div>
