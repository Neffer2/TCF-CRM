<div class="col-lg-12 col-12 mx-auto">
    <div class="card card-body mt-4">
        <!-- Card header --> 
        <div class="card-header"> 
            <h5 class="mb-0">Base comercial</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-flush" id="datatable-search">
                <thead class="thead-light">
                <tr> 
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>Proyecto</th> 
                    <th>COD_CC</th>
                    <th>Valor</th> 
                    <th>Estado</th>
                    <th>Inicio</th>
                    <th>Fin</th>
                    <th>comercial</th>
                </tr> 
                </thead> 
                <tbody>  
                    @foreach ($baseComercial as $item)
                    <tr>
                        <td class="text-sm font-weight-normal">{{ $item->fecha }}</td>
                        <td class="text-sm font-weight-normal">{{ $item->nom_cliente }}</td>
                        <td class="text-sm font-weight-normal">{{ $item->nom_proyecto }}</td>
                        <td class="text-sm font-weight-normal">{{ $item->cod_cc }}</td>
                        <td class="text-sm font-weight-normal">{{ number_format($item->valor_proyecto) }}</td>
                        <td class="text-sm font-weight-normal">{{ $item->estado_cuenta->description }}</td>
                        <td class="text-sm font-weight-normal">{{ $item->fecha_inicio }}</td>
                        <td class="text-sm font-weight-normal">{{ $item->dura_mes }}</td>
                        <td class="text-sm font-weight-normal">{{ $item->comercial->name }}</td>
                    </tr>
                    @endforeach
                </tbody> 
            </table>
        </div>
    </div>
</div>
