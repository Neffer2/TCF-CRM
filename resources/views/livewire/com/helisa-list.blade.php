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
                    <th>Tipo Doc</th>
                    <th>N&uacute;mero Doc</th>
                    <th>Concepto</th>
                    <th>Identidad</th>
                    <th>Nombre del tercero</th>
                    <th>Centro costo</th>
                    <th>Nombre Centro de Costo</th>
                    <th>Debito</th>
                    <th>Credito	Comercial</th>
                    <th>Participaci&oacute;n</th>
                    <th>Base factura</th>
                    <th>Mes</th>
                    <th>Año</th>
                    <th>Comision</th> 
                </tr> 
                </thead> 
                <tbody>  
                    @foreach ($list as $item)
                    <tr>
                        <td class="text-sm font-weight-normal">{{ $item->fecha }}</td>
                        <td class="text-sm font-weight-normal">{{ $item->tipo_doc }}</td>
                        <td class="text-sm font-weight-normal">{{ $item->num_doc }}</td>
                        <td class="text-sm font-weight-normal">{{ $item->concepto }}</td>
                        <td class="text-sm font-weight-normal">{{ $item->identidad }}</td>
                        <td class="text-sm font-weight-normal">{{ $item->nom_tercero }}</td>
                        <td class="text-sm font-weight-normal">{{ $item->centro }}</td>
                        <td class="text-sm font-weight-normal">{{ $item->debito }}</td>
                        <td class="text-sm font-weight-normal">{{ $item->credito }}</td> 
                        <td class="text-sm font-weight-normal">{{ $item->comercial_user->name }}</td>
                        <td class="text-sm font-weight-normal">{{ $item->participacion }}</td>
                        <td class="text-sm font-weight-normal">{{ $item->base_factura }}</td>
                        <td class="text-sm font-weight-normal">{{ $item->mes }}</td>
                        <td class="text-sm font-weight-normal">{{ $item->año }}</td>
                        <td class="text-sm font-weight-normal">{{ $item->comision }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>