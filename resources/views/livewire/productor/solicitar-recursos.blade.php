<div>     
    @if ($verifyPresupuesto)
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
                    </tr>
                </thead>
                <tbody> 
                    @foreach ($presupuesto->presupuestoItems as $key => $presupuestoItem)
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
        
        <div x-data="navControl" class="mt-4">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="#" x-on:click.prevent="toggleMain(1)" x-bind:class="show ? 'active' : ''"><h6>NUEVA OC</h6></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" x-on:click.prevent="toggleMain(2)" x-bind:class="!show ? 'active' : ''"><h6>GENERADAS</h6></a>
                </li>
            </ul>
            
            <div id="nueva-oc" x-show="show" x-transition>
                <div class="card-body py-2">
                    <div class="row">
                        <div class="col-6">
                            <select class="form-control" x-model="selectOc">
                                <option value="">Seleccionar</option>
                                <option value="1">ORDEN DE COMPRA JUR&Iacute;DICA</option>
                                <option value="2">ORDEN DE COMPRA NATURAL</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <button class="btn bg-gradient-warning" x-on:click="toggleOC">GENERAR</button>
                        </div>
                    </div>
                </div>

                <div id="juridica" x-show="showJuridica">
                    @livewire('productor.ordenes.juridica', ['presupuesto' => $presupuesto], key("juridica".$presupuesto->id))
                </div>

                {{-- <div id="natural" x-show="showNatural">
                    <div class="card-body pt-0">
                        <div class="card">
                            <div class="card-header text-center font-weight-bold bg-gradient-info text-white p-0">
                                SOLICITUD ORDEN DE COMPRA NATURAL
                            </div>
                            <div class="row font-table px-4">
                                <div class="col-md-6 mt-3">
                                    <table class="table">
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
                                    <table class="table">
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
                                                    <th class="font-weight-bold bg-gradient-info text-white">No. ITEM</th>
                                                    <th class="font-weight-bold bg-gradient-info text-white">PIEZA - CARACTERISTICAS</th>
                                                    <th class="font-weight-bold bg-gradient-info text-white">CANT</th>
                                                    <th class="font-weight-bold bg-gradient-info text-white">V. UNI</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="font-weight-bold">
                                                        <select class="form-control">
                                                            <option value="">Seleccionar</option>
                                                            @foreach ($presupuesto->presupuestoItems as $key => $presupuestoItem)
                                                                <option value="{{ $presupuestoItem->id }}">{{ $key+1 }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td class="font-weight-bold">
                                                        <textarea cols="30" rows="2">iPad de 10.2" Pulgadas 64 GB Wifi 9na Gen Gris Espacial</textarea>
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
                            <div class="row px-4">
                                <div class="col-md-4">
                                    <button x-on:mouseover="event.target.style.transform = 'rotate(360deg)'" x-on:mouseleave="event.target.style.transform = 'rotate(0deg)'"
                                    class="btn avatar border-1 rounded-circle bg-gradient-info" style="box-shadow: none;">
                                        <i class="fas fa-plus text-white" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <button class="btn bg-gradient-warning mt-2 mb-0">Enviar a aprobaci&oacute;n</button>
                    </div>
                </div> --}}
            </div>

            <div id="generadas" x-show="!show" x-transition>
                <div class="card-body py-2">
                    <div class="row">
                        <div class="col-6">
                            <select class="form-control">
                                <option value="">Seleccionar</option>
                                <option value="">ORDEN DE COMPRA JUR&Iacute;DICA</option>
                                <option value="">ORDEN DE COMPRA NATURAL</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <button class="btn bg-gradient-primary">JIJIJI</button>
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
        function navControl (){ 
            return {
                show: true,
                currentTab: null,

                showJuridica: false,
                showNatural: false,
                selectOc: null,

                toggleMain(id){                    
                    if (this.currentTab != id){
                        this.show = !this.show;                    
                        this.currentTab = id;
                    }
                },
                toggleOC(){
                    if (this.selectOc == 1){
                        this.showJuridica = true;
                        this.showNatural = false;
                    }else if(this.selectOc == 2) {
                        this.showJuridica = false;
                        this.showNatural = true;
                    }else {
                        this.showJuridica = false;
                        this.showNatural = false;
                    }
                }
            }
        }
    </script>