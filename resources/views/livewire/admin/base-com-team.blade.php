<div class="card">
    <div class="table-responsive">
        <table class="table align-items-center mb-0">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>Proyecto</th> 
                    <th>COD_CC</th>
                    <th>Valor</th> 
                    <th>Estado</th>
                    <th>Inicio</th>
                    <th>Fin</th>
                    <th></th> 
                </tr>
            </thead>
            <tbody>   
                @foreach ($list as $item) 
                    <tr>
                        <td>
                            <div class="d-flex px-2">
                                <div class="my-auto">
                                    <h6 class="mb-0 text-xs">{{ $item->fecha }}</h6>
                                </div>
                            </div>
                        </td>
                        <td>
                            <p class="text-xs font-weight-bold mb-0">{{ $item->nom_cliente }}</p>
                        </td>
                        <td>
                            <p class="text-xs font-weight-bold mb-0">{{ $item->nom_proyecto }}</p>
                        </td>
                        <td class="align-middle text-center">
                            <div class="d-flex align-items-center">
                                <span class="me-2 text-xs">{{ $item->cod_cc }}</span>
                            </div>
                        </td>
                        <td class="align-middle text-center">
                            <div class="d-flex align-items-center">
                                <span class="me-2 text-xs">{{ number_format($item->valor_proyecto) }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-dot me-4">
                                <i class="bg-info"></i>
                                <span class="text-dark text-xs">{{ $item->estado_cuenta->description }}</span>
                            </span>
                        </td>
                        <td class="align-middle text-center">
                            <div class="d-flex align-items-center">
                                <span class="me-2 text-xs">{{ $item->fecha_inicio }}</span>
                            </div>
                        </td>
                        <td>
                            <p class="text-xs font-weight-bold mb-0">{{ $item->dura_mes }}</p>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
