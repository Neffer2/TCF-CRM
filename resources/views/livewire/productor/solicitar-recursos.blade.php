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
                        <th class="font-weight-bold font-table bg-gradient-warning text-white">PROVEEDOR</th>
                        <th class="font-weight-bold font-table bg-gradient-warning text-white">V. UNITARIO</th>
                        <th class="font-weight-bold font-table bg-gradient-warning text-white">V. TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($presupuesto->presupuestoItems as $key => $presupuestoItem)
                        <div>
                            @if($presupuestoItem->evento)
                                <tr>
                                    <td colspan="8" class="font-weight-bold font-table text-center bg-gradient-info text-white">
                                        {{ $presupuestoItem->descripcion }}
                                    </td>
                                </tr>
                            @else
                                {{-- if ($proveedores_item = @unserialize($presupuestoItem->proveedor)) --}}
                                {{-- @php $validator_cuenta_cobro = false; /* Valida que el item sea jurídico */ @endphp --}}
                                {{-- @foreach ($proveedores_item as $proveedor) --}}
                                    {{-- 3 = cuenta de cobro --}}

                                    {{-- @if($proveedor == 3) @php $validator_cuenta_cobro = true; @endphp @endif --}}
                                {{-- @endforeach --}}

                                {{-- Solo muestra items jurídicos --}}
                                {{-- @if (!$validator_cuenta_cobro) @endif --}}
                                <tr @if (!($presupuestoItem->disponible)) style="text-decoration: line-through;" @endif>
                                    <td class="font-weight-bold font-table">{{ $key+1 }}</td>
                                    <td class="font-weight-bold font-table">
                                        <textarea @if (!($presupuestoItem->disponible)) style="text-decoration: line-through;" @endif
                                            cols="70" rows="1" disabled>{{ $presupuestoItem->descripcion }}</textarea>
                                    </td>
                                    <td class="font-weight-bold font-table">{{ $presupuestoItem->cantidad }}</td>
                                    <td class="font-weight-bold font-table">{{ $presupuestoItem->dia }}</td>
                                    <td class="font-weight-bold font-table">{{ $presupuestoItem->otros }}</td>
                                    <td class="font-weight-bold font-table">
                                        @if ($proveedores_item = @unserialize($presupuestoItem->proveedor))
                                        @foreach ($proveedores_item as $proveedor)
                                            {{ @$proveedores->find($proveedor)->tercero }} <br>
                                        @endforeach
                                        @else
                                            @if ($proveedores->find($presupuestoItem->proveedor))
                                                {{ $proveedores->find($presupuestoItem->proveedor)->tercero }}
                                            @else
                                                {{ $presupuestoItem->proveedor }}
                                            @endif
                                        @endif
                                    </td>
                                    <td class="font-weight-bold font-table">$ {{ number_format($presupuestoItem->v_unitario) }}</td>
                                    <td class="font-weight-bold font-table">$ {{ number_format($presupuestoItem->v_total) }}</td>
                                </tr>
                            @endif
                        </div>
                    @endforeach
                </tbody>
                <tfoot class="border-0">
                    <tr>
                        <th class="font-weight-bold font-table bg-gradient-warning text-white text-center" colspan="7">TOTAL COSTO INTERNO: </th>
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
                            <select class="form-control" x-model="selectOc" disabled>
                                <option selected value="1">ORDEN DE COMPRA JUR&Iacute;DICA</option>
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
            </div>

            <div id="generadas" x-show="!show" x-transition>
                <div class="row" style="font-size: 12px;">
                    @foreach ($this->presupuesto->ordenesCompra as $orden)
                        <div class="col-md-12 my-1">
                            <div class="card" style="border-top: 3px solid #825ee4; border-radius: 2px; box-shadow: none;">
                                <div class="card-body px-1 py-1" style="background-color: white">
                                    <div class="row align-items-center">
                                        <div class="col-sm-3">
                                            <span class="font-weight-bold me-1">Proveedor: <br></span>{{ $orden->proveedor->tercero }}.
                                        </div>
                                        <div class="col-sm-3">
                                            <span class="font-weight-bold me-1">Contacto: <br></span>{{ $orden->proveedor->contacto }}.
                                        </div>
                                        <div class="col-sm-3">
                                            <span class="font-weight-bold me-1">Tel&eacute;fono/Correo: <br></span>{{ $orden->proveedor->celular }}<br>{{ $orden->proveedor->correo }}.
                                        </div>
                                        <div class="col-sm-2">
                                            <span class="font-weight-bold me-1">Estado: <br></span>{{ $orden->estado_oc->description }}.
                                        </div>
                                        <div class="col-sm-1">
                                            <div @if ($orden->estado_id != 2) x-on:click="collapseOC(event.delegateTarget)" data-bs-toggle="collapse" href="#collapseOrden{{ $orden->id }}" role="button" aria-expanded="false"
                                                aria-controls="collapseOrden" @endif class="m-0 p-0 d-flex justify-content-center"
                                                style="width: 100%; color: #825ee4;">
                                                @if ($orden->estado_id == 2)
                                                    <i class="fa-solid fa-ban"></i>
                                                @else
                                                    <i class="fa-solid fa-caret-down"></i>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="collapse mt-2" id="collapseOrden{{ $orden->id }}">
                                    @livewire('productor.ordenes.juridica', ['presupuesto' => $presupuesto, 'orden_compra' => $orden], key("juridica{{ $presupuesto->id }}".$orden->id))
                                </div>
                            </div>
                        </div>
                    @endforeach
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
                currentTab: 1,

                showJuridica: false,
                selectOc: false,

                toggleMain(id){
                    if (this.currentTab != id){
                        this.show = !this.show;
                        this.currentTab = id;
                    }
                },
                toggleOC(){
                    this.showJuridica = !this.showJuridica;
                },
                collapseOC(target){
                    let elem = target;
                    if (target.classList.contains('collapsed')){
                        elem.innerHTML = "<i class='fa-solid fa-caret-down'></i>";
                    }else{
                        elem.innerHTML = "<i class='fa-solid fa-caret-up'></i>";
                    }
                }
            }
        }
    </script>
