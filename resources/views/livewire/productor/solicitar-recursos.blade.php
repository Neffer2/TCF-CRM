<div>     
    @if ($verifyPresupuesto)
        <div class="table-responsive mt-2 rounded bg-whitem mb-2" x-data="showOC">
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
                        <th colspan="2" class="font-weight-bold font-table bg-gradient-primary text-white">ACCIONES</th>
                    </tr>
                </thead>
                <tbody> 
                    @foreach ($presupuestoItems as $key => $presupuestoItem)
                        <div>                                                    
                            <tr>
                                <td class="font-weight-bold font-table">{{ $key+1 }}</td>
                                <td class="font-weight-bold font-table">
                                    <textarea cols="30" rows="1" disabled>{{ $presupuestoItem->descripcion }}</textarea>
                                </td>
                                <td class="font-weight-bold font-table">{{ $presupuestoItem->cantidad }}</td>
                                <td class="font-weight-bold font-table">{{ $presupuestoItem->dia }}</td>
                                <td class="font-weight-bold font-table">{{ $presupuestoItem->otros }}</td>
                                <td class="font-weight-bold font-table">$ {{ number_format($presupuestoItem->v_unitario) }}</td>
                                <td class="font-weight-bold font-table">$ {{ number_format($presupuestoItem->v_total) }}</td>
                                <td class="font-weight-bold font-table text-center">
                                    <button x-on:click="Open({{ $presupuestoItem->id }}, event)" class="btn btn-primary 4mb-0 px-3 py-1">
                                        🔽
                                    </button>
                                </td>

                                <tr id="{{ $presupuestoItem->id }}" x-show="false">
                                    <td colspan="9">
                                        <div class="">
                                            <div class="card-body">
                                                <select class="form-control">
                                                    <option value="">Seleccionar</option>
                                                    <option value="">ORDEN DE COMPRA JUR&Iacute;DICA</option>
                                                    <option value="">ORDEN DE COMPRA NATURAL</option>
                                                </select>
                                            </div>
                                            
                                            <div class="card-body pt-0">
                                                <div class="card">
                                                    <div class="card-header text-center font-weight-bold bg-gradient-primary text-white p-0">
                                                        SOLICITUD ORDEN DE COMPRA
                                                    </div>
                                                    <div class="row font-table px-4">
                                                        <div class="col-md-6 mt-3">
                                                            <table class="card card-body table">
                                                                <tr>
                                                                    <td class="font-weight-bold">Cliente:</td>
                                                                    <td>PEPSICO.</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="font-weight-bold">Proyecto:</td>
                                                                    <td>EVENTO DEMO FARM COLOMBIA pago terceros.</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="font-weight-bold">Centro de Costos:</td>
                                                                    <td>C3230907.</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="font-weight-bold">Ciudad:</td>
                                                                    <td>BOGOT&Aacute;.</td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                        <div class="col-md-6 mt-3">
                                                            <table class="card card-body table">
                                                                <tr>
                                                                    <td class="font-weight-bold">Proveedor:</td>
                                                                    <td>A&F.</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="font-weight-bold">Email Proveedor:</td>
                                                                    <td>Leduardo.caipa@ayf-solution.com.co</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="font-weight-bold">Contacto Proveedor:</td>
                                                                    <td>Andrea Sanchez.</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="font-weight-bold">Tel&eacute;fono Proveedor:</td>
                                                                    <td>3124096157</td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="row font-table px-4">
                                                        <div class="col-md-12">
                                                            <div class="card card-body table-responsive mb-3 rounded bg-whitem p-0">
                                                                <table class="table">
                                                                    <thead> 
                                                                        <tr> 
                                                                            <th class="font-weight-bold bg-gradient-primary text-white">No. ITEM</th>
                                                                            <th class="font-weight-bold bg-gradient-primary text-white">PIEZA - CARACTERISTICAS</th>
                                                                            <th class="font-weight-bold bg-gradient-primary text-white">CANT</th>
                                                                            <th class="font-weight-bold bg-gradient-primary text-white">V. UNI</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            <td class="font-weight-bold">1</td>
                                                                            <td class="font-weight-bold">
                                                                                <textarea cols="30" rows="1">iPad de 10.2" Pulgadas 64 GB Wifi 9na Gen Gris Espacial</textarea>
                                                                            </td>
                                                                            <td class="font-weight-bold">
                                                                                <input type="text" value="8">
                                                                            </td>
                                                                            <td>
                                                                                <input type="text" value="$ 1'960.000">
                                                                            </td>
                                                                        </tr>
    
                                                                        <tr>
                                                                            <td class="font-weight-bold">1</td>
                                                                            <td class="font-weight-bold">
                                                                                <textarea cols="30" rows="1">iPad de 10.2" Pulgadas 64 GB Wifi 9na Gen Gris Espacial</textarea>
                                                                            </td>
                                                                            <td class="font-weight-bold">
                                                                                <input type="text" value="8">
                                                                            </td>
                                                                            <td>
                                                                                <input type="text" value="$ 1'960.000">
                                                                            </td>
                                                                        </tr>
    
                                                                        <tr>
                                                                            <td class="font-weight-bold">1</td>
                                                                            <td class="font-weight-bold">
                                                                                <textarea cols="30" rows="1">iPad de 10.2" Pulgadas 64 GB Wifi 9na Gen Gris Espacial</textarea>
                                                                            </td>
                                                                            <td class="font-weight-bold">
                                                                                <input type="text" value="8">
                                                                            </td>
                                                                            <td>
                                                                                <input type="text" value="$ 1'960.000">
                                                                            </td>
                                                                        </tr>                                                                    
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div> 
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>            
                            </tr>
                        </div>
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

        <div>
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
    @else 
        <div class="card card-frame p-5">
            <h3 class="text-center">Tu presupuesto fu&eacute; modificado y está siendo validado.</h3>
            <div class="d-flex justify-content-center">
                <div class="spinner-grow text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <div class="spinner-grow text-success" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <div class="spinner-grow text-warning" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <div class="spinner-grow text-info" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
        </div>
    @endif
</div>
    <script>
        function showOC (){ 
            return {
                Open(id, event){
                    btn = event.target
                    elem = document.getElementById(id);

                    if (elem.style.display === 'table-row'){
                        elem.style.display = 'none';
                        btn.innerHTML = "🔽";
                    }else {
                        elem.style.display = 'table-row';
                        btn.innerHTML = "🔼";
                    }
                }
            }
        }
    </script>