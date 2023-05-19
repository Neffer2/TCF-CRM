<div>
    <div class="card">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <th class="bg-gradient-warning text-white">Nom Proyecto</th>
                    <th class="bg-gradient-warning text-white">Empresa</th>
                    <th class="bg-gradient-warning text-white">Comercial</th>
                    <th class="bg-gradient-warning text-white">Venta Proyecto</th>
                    <th class="bg-gradient-warning text-white">Costos Proyecto</th>
                    <th class="bg-gradient-warning text-white">Margen Proyecto</th>
                    <th class="bg-gradient-warning text-white">Acciones</th>
                </thead>
                <tbody>
                    @foreach ($presupuestos as $presupuesto)
                        <tr>
                            <td>{{ $presupuesto->gestion->nom_proyecto_cot }}</td>
                            <td>{{ $presupuesto->gestion->contacto->empresa }}</td>
                            <td>{{ $presupuesto->gestion->comercial->name }}</td>
                            <td>$ {{ number_format($presupuesto->venta_proy) }}</td>
                            <td>$ {{ number_format($presupuesto->costos_proy) }}</td>
                            <td>$ {{ $presupuesto->margen_proy }} %</td>
                            <td>
                                <a class="btn bg-gradient-primary mb-0" href="{{ route('presupuesto', $presupuesto->id_gestion) }}" target="_blank">Ver mas</a>
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
 