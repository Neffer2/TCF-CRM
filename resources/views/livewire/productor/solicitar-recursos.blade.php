<div>    
    <div class="table-responsive mt-2 rounded bg-whitem mb-2">
        <table class="table mb-0">
            <thead> 
                <tr>
                    <th class="font-weight-bold font-table bg-gradient-warning text-white">ITEM</th>
                    <th class="font-weight-bold font-table bg-gradient-warning text-white">DESCRIPCION</th>
                    <th class="font-weight-bold font-table bg-gradient-warning text-white">CANTIDAD</th>
                    <th class="font-weight-bold font-table bg-gradient-warning text-white">DIAS</th>
                    <th class="font-weight-bold font-table bg-gradient-warning text-white">OTROS</th>
                    <th class="font-weight-bold font-table bg-gradient-warning text-white">V. UNITARIO</th>
                    <th class="font-weight-bold font-table bg-gradient-warning text-white">V. TOTAL</th>
                    {{-- <th colspan="2" class="font-weight-bold font-table bg-gradient-primary text-white">ACCIONES</th> --}}
                </tr>
            </thead>
            <tbody>
                @foreach ($presupuestoItems as $key => $presupuestoItem)
                    <tr>
                        <td class="font-weight-bold font-table">{{ $key+=1 }}</td>
                        <td class="font-weight-bold font-table">
                            <textarea cols="30" rows="1" disabled>{{ $presupuestoItem->descripcion }}</textarea>
                        </td>
                        <td class="font-weight-bold font-table">{{ $presupuestoItem->cantidad }}</td>
                        <td class="font-weight-bold font-table">{{ $presupuestoItem->dia }}</td>
                        <td class="font-weight-bold font-table">{{ $presupuestoItem->otros }}</td>
                        <td class="font-weight-bold font-table">$ {{ number_format($presupuestoItem->v_unitario) }}</td>
                        <td class="font-weight-bold font-table">$ {{ number_format($presupuestoItem->v_total) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="border-0">
                <tr>
                    <th class="font-weight-bold font-table bg-gradient-warning text-white text-center" colspan="6">TOTAL COSTO INTERNO: </th>
                    <th class="font-weight-bold font-table bg-gradient-success text-white">$ {{ number_format($presupuesto->costos_proy) }} </th>
                </tr>
            </tfoot>
        </table>
    </div>

    <button class="btn btn-icon btn-3 bg-gradient-success mb-0 me-1" data-bs-toggle="modal" data-bs-target="#staticBackdrop{{ $id_presupuesto }}" type="button">
        <span class="btn-inner--icon"><i class="ni ni-single-copy-04"></i></span>
        <span class="btn-inner--text">Exportar</span>
    </button>

    <!-- Modal --> 
    <div class="modal fade" id="staticBackdrop{{ $id_presupuesto }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Exportar</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">                       
                    <h2 class="fs-5">Documentos Interno</h2>
                    <button wire:click="internoPdf" class="btn btn-icon btn-3 bg-gradient-warning mb-0 me-1" type="button" data-bs-dismiss="modal">
                        <span class="btn-inner--icon"><i class="ni ni-single-copy-04"></i></span>
                        <span class="btn-inner--text">Interno PDF</span>
                    </button>
                    
                    <button wire:click="internoExcel" class="btn btn-icon btn-3 bg-gradient-success mb-0 me-1" type="button" data-bs-dismiss="modal">
                        <span class="btn-inner--icon"><i class="ni ni-single-copy-04"></i></span>
                        <span class="btn-inner--text">Interno Excel</span>
                    </button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</div>