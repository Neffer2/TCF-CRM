<div x-data>
    <div class="card card-frame p-2">
        <div class="row justify-content-md-center"> 
            <div class="col-md-3">
                <div class="card">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <tr> 
                                <td class="font-weight-bold font-table">MARGEN GENERAL</td>
                                <td class="font-table">{{ number_format($presupuesto->margen_general, 4) }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold font-table">VENTA PROYECTO</td>
                                <td class="font-table">{{ number_format($presupuesto->margen_proy) }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold font-table">COSTOS DEL PROYECTO</td>
                                <td class="font-table">{{ number_format($presupuesto->costos_proy) }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold font-table">MARGEN DEL PROYECTO</td>
                                <td class="font-table">{{ number_format($presupuesto->margen_proy, 2) }} %</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold font-table">MARGEN BRUTO (PESOS)</td>
                                <td class="font-table">{{ number_format($presupuesto->margen_bruto) }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div> 
            <div class="col-md-3">
                <div class="card"> 
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <tr>
                                <td class="font-weight-bold font-table">CONTACTO</td>
                                <td class="font-table">
                                    {{ $presupuesto->gestion->contacto->nombre }} {{ $presupuesto->gestion->contacto->apellido }} 
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold font-table">CLIENTE</td>
                                <td class="font-table">
                                    {{ $presupuesto->gestion->contacto->empresa }}
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold font-table">PROYECTO</td>
                                <td class="font-table">
                                    {{ $presupuesto->gestion->nom_proyecto_cot }}
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold font-table">CIUDAD</td>
                                <td class="font-table">
                                    {{ $presupuesto->gestion->contacto->ciudad }} 
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <tr>
                                <td class="font-weight-bold font-table">IMPREVISTOS</td>
                                <td class="font-table">
                                    <input type="text" disabled value="{{ $presupuesto->imprevistos }}">
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold font-table">ADMINISTRACI&Oacute;N</td>
                                <td class="font-table">
                                    <input type="text" disabled value="{{ $presupuesto->administracion }}">
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold font-table">FEE AGENCIA</td>
                                <td class="font-table">
                                    <input type="text" disabled value="{{ $presupuesto->fee }}">
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold font-table">TIEMPO</td>
                                <td class="font-table">
                                    <input type="text" disabled value="{{ $presupuesto->tiempo_factura }}">
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="table-responsive"> 
                        <table class="table mb-0"> 
                            <tr>
                                <td class="font-weight-bold font-table">NOTAS</td>
                                <td class="font-table">
                                    <textarea wire:model.lazy="notas" cols="55" rows="8" disabled>{{ $presupuesto->notas }}</textarea>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div> 
    </div>             
    
    <div class="table-responsive mt-2 rounded bg-white">
        <table class="table">
            <thead>
                <tr>
                    <th class="font-weight-bold font-table bg-gradient-info text-white" >COD</th>

                    <th class="font-weight-bold font-table bg-gradient-warning text-white">ITEM</th>
                    <th class="font-weight-bold font-table bg-gradient-warning text-white">CANTIDAD</th>
                    <th class="font-weight-bold font-table bg-gradient-warning text-white">DIA</th>
                    <th class="font-weight-bold font-table bg-gradient-warning text-white">OTROS</th>
                    <th class="font-weight-bold font-table bg-gradient-warning text-white">DESCRIPCION</th>
                    <th class="font-weight-bold font-table bg-gradient-warning text-white">V. UNITARIO</th>
                    <th class="font-weight-bold font-table bg-gradient-warning text-white">V. TOTAL</th>
                    <th class="font-weight-bold font-table bg-gradient-warning text-white">PROVEEDOR</th>
                    {{-- <th class="font-weight-bold font-table bg-gradient-warning text-white">UTILIDAD</th> --}}

                    {{-- <th class="font-weight-bold font-table bg-gradient-success text-white">MES</th> --}}
                    <th class="font-weight-bold font-table bg-gradient-success text-white">DIAS</th>
                    <th class="font-weight-bold font-table bg-gradient-success text-white">CIUDAD</th>

                    <th colspan="2" class="font-weight-bold font-table bg-gradient-primary text-white">ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($presupuesto->presupuestoItems as $key => $item)
                    @php
                        if (count($item->consumidos) > 0){ 
                            $cont_cant_oc = 0;
                            $cont_dias_oc = 0;
                            $cont_otros_oc = 0;
                            $acum_total_oc = 0;

                            $item->consumidos->map(function ($item) use (&$cont_cant_oc, &$cont_dias_oc, &$cont_otros_oc, &$acum_total_oc){
                                if (!($item->OrdenCompra->estado_id == 6)){
                                    $cont_cant_oc += $item->cant_oc;
                                    $cont_dias_oc += $item->dias_oc;
                                    $cont_otros_oc += $item->otros_oc;
                                    $acum_total_oc += $item->vtotal_oc;
                                }
                            });
                        }
                    @endphp
                    @if ($item->evento)
                        <tr>
                            <td colspan="13" class="font-weight-bold font-table text-center bg-gradient-info text-white">
                                {{ $item->descripcion }}
                            </td>
                            @if (Auth::user()->rol != 1)
                                <td class="font-weight-bold font-table">
                                    <button wire:click="deleteItem({{ $item->id }})">✖️</button>
                                </td>
                            @endif
                            @if (Auth::user()->rol != 1)
                                <td class="font-weight-bold font-table">
                                    <button wire:click="getDataEdit({{ $item->id }})">📝</button>
                                </td>
                            @endif
                        </tr>
                    @else
                        <tr @if (count($item->consumidos) > 0 && (($item->cantidad - $cont_cant_oc == 0) || ($item->v_total - $acum_total_oc == 0)))
                                style="background-color: #f5365c; color: white;"
                            @endif> 
                            <td class="font-weight-bold font-table">
                                {{ $item->cod }}
                            </td>
                            <td class="font-weight-bold font-table">
                                {{ $key+=1 }}
                            </td> 
                            <td class="font-weight-bold font-table">
                                {{ $item->cantidad }}
                            </td>
                            <td class="font-weight-bold font-table">
                                {{ $item->dia }}
                            </td>
                            <td class="font-weight-bold font-table">
                                {{ $item->otros }}
                            </td>
                            <td class="font-weight-bold font-table">
                                <textarea cols="30" rows="1" readonly>{{ $item->descripcion }}</textarea>
                            </td>
                            <td class="font-weight-bold font-table">
                                $ {{ number_format($item->v_unitario) }}
                            </td>
                            <td class="font-weight-bold font-table">
                                $ {{ number_format($item->v_total) }}
                            </td>
                            <td class="font-weight-bold font-table">
                                @if ($proveedores_item = @unserialize($item->proveedor))
                                    @foreach ($proveedores_item as $proveedor) 
                                        {{ $proveedores->find($proveedor)->tercero }} <br>
                                    @endforeach 
                                @else  
                                    @if ($proveedores->find($item->proveedor))
                                        {{ $proveedores->find($item->proveedor)->tercero }}
                                    @else   
                                        {{ $item->proveedor }}
                                    @endif
                                @endif
                            </td>
                            <td class="font-weight-bold font-table">
                                {{ $item->dias }}
                            </td>
                            <td class="font-weight-bold font-table">
                                {{ $item->ciudad }}
                            </td>
                            @if (count($item->consumidos) > 0)
                                <td class="font-weight-bold font-table">
                                    <div data-bs-toggle="collapse" href="#collapseOrden{{ $key }}" role="button" aria-expanded="false"
                                        aria-controls="collapseOrden" class="m-0 p-0 d-flex justify-content-center" style="width: 100%;">
                                        <i class="fa-solid fa-caret-down"></i>
                                    </div>
                                </td>
                                <tr class="collapse" id="collapseOrden{{ $key }}">
                                    <td colspan="11" class="m-0 p-0">
                                        <div class="table-responsive px-5 py-1 rounded bg-white">
                                            <table class="table font-table">
                                                <tr> 
                                                    <th class="font-weight-bold bg-gradient-primary text-white">No. ITEM</th>
                                                    <th class="font-weight-bold bg-gradient-primary text-white">CANT</th>
                                                    <th class="font-weight-bold bg-gradient-primary text-white">DIAS</th>
                                                    <th class="font-weight-bold bg-gradient-primary text-white">OTROS</th>
                                                    <th class="font-weight-bold bg-gradient-primary text-white">CARACTERISTICAS</th>
                                                    <th class="font-weight-bold bg-gradient-primary text-white">V. UNI</th>
                                                    <th class="font-weight-bold bg-gradient-primary text-white">V. TOTAL</th>
                                                    <th class="font-weight-bold bg-gradient-primary text-white">ESTADO</th>
                                                    <th class="font-weight-bold bg-gradient-primary text-white">PROVEEDOR</th>
                                                    <th class="font-weight-bold bg-gradient-primary text-white">ORDEN DE COMPRA</th>
                                                </tr>
                                                @foreach ($item->consumidos as $ordenItem)
                                                    <tr @if ($ordenItem->OrdenCompra->estado_id == 6) style="text-decoration-line: line-through;" @endif>
                                                        <td class="font-weight-bold font-table">
                                                            {{ $key}}
                                                        </td>
                                                        <td class="font-weight-bold font-table">
                                                            {{ $ordenItem->cant_oc }}
                                                        </td>
                                                        <td class="font-weight-bold font-table">
                                                            {{ $ordenItem->dias_oc }}
                                                        </td>
                                                        <td class="font-weight-bold font-table">
                                                            {{ $ordenItem->otros_oc }}
                                                        </td>
                                                        <td class="font-weight-bold font-table" style="width: 1rem;">
                                                            <textarea cols="30" rows="1" readonly>{{ $ordenItem->desc_oc }}</textarea>                                                    
                                                        </td>
                                                        <td class="font-weight-bold font-table">
                                                            $ {{ number_format($ordenItem->vunit_oc) }}
                                                        </td>
                                                        <td class="font-weight-bold font-table">
                                                            $ {{ number_format($ordenItem->vtotal_oc) }}
                                                        </td>
                                                        <td class="font-weight-bold font-table">
                                                            {{ $ordenItem->OrdenCompra->estado_oc->description }}
                                                        </td>
                                                        <td class="font-weight-bold font-table">
                                                            {{ $ordenItem->OrdenCompra->proveedor->tercero }} - {{ $ordenItem->OrdenCompra->proveedor->documento }}
                                                        </td>
                                                        <td class="font-weight-bold font-table">
                                                            <a href="{{ route('orden-juridica', $ordenItem->OrdenCompra->id) }}" target="_blank">Orden de compra</a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </table>
                                        </div>
                                    </td>
                                </tr>                                
                            @else
                                <td class="font-weight-bold font-table">
                                    <div class="m-0 p-0 d-flex justify-content-center" style="width: 100%; color: #825ee4;">
                                        <i class="fa-solid fa-ban"></i>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @endif           
                @endforeach
            </tbody>
        </table>
    </div>
</div>
 