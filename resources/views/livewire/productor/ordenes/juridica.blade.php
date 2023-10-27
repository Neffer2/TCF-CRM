<div>
    <div class="card-body pt-0">
        <div class="card">
            <div class="card-header text-center font-weight-bold bg-gradient-primary text-white p-0">
                SOLICITUD ORDEN DE COMPRA JUR&Iacute;DICA  
            </div>
            <div class="row font-table px-4">
                <div class="col-md-6 mt-3">
                    <div class="table-responsive">
                        <table class="table">  
                            <tr style="height: 35px;">
                                <td class="font-weight-bold">Cliente:</td>
                                <td>{{ $presupuesto->gestion->contacto->empresa }}</td>
                            </tr> 
                            <tr style="height: 35px;">
                                <td class="font-weight-bold">Proyecto:</td>
                                <td>{{ $presupuesto->gestion->nom_proyecto_cot }}</td>
                            </tr>
                            <tr style="height: 35px;">
                                <td class="font-weight-bold">Centro de Costos:</td>
                                <td>{{ $presupuesto->cod_cc }}</td> 
                            </tr>
                            <tr style="height: 35px;">
                                <td class="font-weight-bold">Ciudad:</td>
                                <td>{{ $presupuesto->presupuestoItems[0]->ciudad }}</td>
                            </tr>
                            @if ($justificacion_rechazo && Auth::user()->rol != 1 && $orden_compra->estado_id == 3)
                                <tr style="height: 35px;">
                                    <td class="font-weight-bold">Jutificacion de rechazo:</td>
                                    <td>
                                        <textarea disabled cols="30" rows="2">{{ $justificacion_rechazo }}</textarea>
                                    </td>
                                </tr>                                
                            @endif
                        </table>
                    </div>
                </div> 
                <div class="col-md-6 mt-3">
                    <div class="table-responsive">
                        <table class="table">
                            <tr style="height: 35px;">
                                <td class="font-weight-bold">Proveedor:</td>
                                <td>
                                    <div class="form-group m-0">
                                        <input type="text" wire:model.lazy="proveedor" style="width: 80%;" @if (Auth::user()->rol == 1) disabled @endif>
                                        @error('proveedor')
                                            <div id="proveedor" class="text-danger">
                                                <strong>{{ $message }}</strong>
                                            </div>
                                        @enderror
                                    </div> 
                                </td>
                            </tr>
                            <tr style="height: 35px;">
                                <td class="font-weight-bold">NIT:</td>
                                <td>
                                    <div class="form-group m-0">
                                        <input type="text" wire:model.lazy="nit" style="width: 80%;" @if (Auth::user()->rol == 1) disabled @endif>
                                        @error('nit')
                                            <div id="nit" class="text-danger">
                                                <strong>{{ $message }}</strong>
                                            </div>
                                        @enderror
                                    </div>
                                </td>
                            </tr>
                            <tr style="height: 35px;">
                                <td class="font-weight-bold">Email Proveedor:</td>
                                <td>
                                    <div class="form-group m-0">
                                        <input type="email" wire:model.lazy="email" placeholder="alguien@ejemplo.com" style="width: 80%;" @if (Auth::user()->rol == 1) disabled @endif>
                                        @error('email')
                                            <div id="email" class="text-danger">
                                                <strong>{{ $message }}</strong>
                                            </div>
                                        @enderror
                                    </div>
                                </td>
                            </tr> 
                            <tr style="height: 35px;">
                                <td class="font-weight-bold">Contacto Proveedor:</td>
                                <td>
                                    <div class="form-group m-0">
                                        <input type="text" wire:model.lazy="contacto" style="width: 80%;" @if (Auth::user()->rol == 1) disabled @endif>
                                        @error('contacto')
                                            <div id="contacto" class="text-danger">
                                                <strong>{{ $message }}</strong>
                                            </div>
                                        @enderror
                                    </div>
                                </td>
                            </tr>
                            <tr style="height: 35px;">
                                <td class="font-weight-bold">Tel&eacute;fono Proveedor:</td>
                                <td>
                                    <div class="form-group m-0">
                                        <input type="text" wire:model.lazy="tel" style="width: 80%;" @if (Auth::user()->rol == 1) disabled @endif>
                                        @error('tel')
                                            <div id="tel" class="text-danger">
                                                <strong>{{ $message }}</strong>
                                            </div>
                                        @enderror
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row font-table px-4">
                <div class="col-md-12">
                    <div class="card card-body table-responsive mb-3 rounded bg-whitem p-0">
                        <table class="table m-0">
                            <thead> 
                                <tr>  
                                    <th class="font-weight-bold bg-gradient-primary text-white">No. ITEM</th>
                                    <th class="font-weight-bold bg-gradient-primary text-white">CANT</th>
                                    <th class="font-weight-bold bg-gradient-primary text-white">DIAS</th>
                                    <th class="font-weight-bold bg-gradient-primary text-white">OTROS</th>
                                    <th class="font-weight-bold bg-gradient-primary text-white">CARACTERISTICAS</th>
                                    <th class="font-weight-bold bg-gradient-primary text-white">V. UNI</th>
                                    <th class="font-weight-bold bg-gradient-primary text-white">V. TOTAL</th>
                                    @if (Auth::user()->rol != 1)
                                        <th colspan="2" class="font-weight-bold bg-gradient-primary text-white">ACCIONES</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ocItems as $item)
                                    <tr>
                                        <td class="text-center">{{ $item['displayItem'] }}</td>
                                        <td class="text-center">{{ $item['cant'] }}</td>
                                        <td class="text-center">{{ $item['dias'] }}</td>
                                        <td class="text-center">{{ $item['otros'] }}</td>
                                        <td>
                                            <textarea disabled cols="30" rows="1">{{ $item['desc'] }}</textarea>
                                        </td> 
                                        <td class="text-center">{{ number_format($item['vUnit']) }}</td>
                                        <td class="text-center">{{ number_format($item['vTotal']) }}</td>
                                        @if (Auth::user()->rol != 1)
                                            <td class="d-flex justify-content-center" style="padding: 11px;">
                                                <button class="me-2" wire:click="delete({{ $item['id'] }})">
                                                    ✖️
                                                </button>
                                                <button class="" wire:click="getSelectedItem({{ $item['id'] }})">
                                                    📝
                                                </button>
                                            </td>                                    
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>                    
            @if (Auth::user()->rol == 1 && $orden_compra->estado_id == 2)
                <div class="row px-4">
                    <div class="col-md-12">
                        <div class="form-group"> 
                            @php
                                $aux = str_replace('public/', '', $orden_compra->archivo_cot);
                            @endphp
                            <a href="{{ asset("storage/$aux") }}" target="_blank" class="">
                                <span class="btn-inner--icon"><i class="ni ni-single-copy-04"></i></span>
                                <span class="btn-inner--text">Cotizaci&oacute;n - {{ $presupuesto->gestion->nom_proyecto_cot }}</span>
                            </a>
                        </div>
                    </div>
                    <div class="row px-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="oc_helisa">Adjunta la orden de compra generada en Helisa:</label>
                                <input id="oc_helisa" wire:model="oc_helisa" type="file" class="form-control" accept=".pdf,.xls,.xlsx">
                                <div wire:loading wire:target="oc_helisa" class="py-2">
                                    <div class="spinner-border text-warning" role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                </div>
                                @error('oc_helisa')
                                    <div id="oc_helisa" class="text-invalid">
                                        {{ $message }}
                                    </div>
                                @enderror  
                            </div>
                        </div>
                        <div class="col-md-6"> 
                            <div class="form-group">
                                <label for="cod_oc">C&oacute;digo de orden de compra:</label>
                                <input name="cod_oc" id="cod_oc" class="form-control" wire:model="cod_oc">
                                @error('cod_oc')
                                    <div id="cod_oc" class="text-invalid">
                                        {{ $message }}
                                    </div>
                                @enderror 
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="justificacion_rechazo">Justificaci&oacute;n de rechazo:</label>
                                <textarea name="justificacion_rechazo" id="justificacion_rechazo" class="form-control" wire:model="justificacion_rechazo" cols="100" rows="2"></textarea>
                                @error('justificacion_rechazo')
                                    <div id="justificacion_rechazo" class="text-invalid">
                                        {{ $message }}
                                    </div>
                                @enderror 
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <button wire:click="cambioEstado(1)" class="btn bg-gradient-warning">Aprobar</button>
                        <button wire:click="cambioEstado(3)" class="btn bg-gradient-danger">Rechazar</button>
                    </div> 
                </div>
            @elseif(($orden_compra && $orden_compra->estado_id == 1) && Auth::user()->rol == 7)
                <div class="row px-4">
                    <div class="col-md-12">
                        <div class="form-group"> 
                            @php 
                                $aux = str_replace('public/', '', $orden_compra->archivo_orden_helisa);
                            @endphp
                            <a href="{{ asset("storage/$aux") }}" target="_blank" class="">
                                <span class="btn-inner--icon"><i class="ni ni-single-copy-04"></i></span>
                                <span class="btn-inner--text">Orden de compra</span>
                            </a>
                        </div>
                    </div> 
                    <div class="col-md-12">
                        <a class="btn btn-icon btn-3 bg-gradient-warning" type="button" href="{{ route('firmar-gr', $orden_compra) }}">
                            <span class="btn-inner--icon"><i class="fa-solid fa-file-signature"></i></span>
                            <span class="btn-inner--text">Firmar GR</span>
                        </a>
                    </div>
                </div>
            @elseif(($orden_compra && (($orden_compra->estado_id == 5) || (Auth::user()->rol == 1))))
                <div class="row px-4">
                    <div class="col-md-2">
                        <div class="form-group"> 
                            @php
                                $archivo_cot = str_replace('public/', '', $orden_compra->archivo_cot); 
                            @endphp
                            <a href="{{ asset("storage/$archivo_cot") }}" target="_blank" class="">
                                <span class="btn-inner--icon"><i class="ni ni-single-copy-04"></i></span>
                                <span class="btn-inner--text">Cotizaci&oacute;n.</span>
                            </a>
                        </div>
                    </div>            
                    <div class="col-md-2">
                        <div class="form-group"> 
                            @php
                                $archivo_orden_helisa = str_replace('public/', '', $orden_compra->archivo_orden_helisa); 
                            @endphp
                            <a href="{{ asset("storage/$archivo_orden_helisa") }}" target="_blank" class="">
                                <span class="btn-inner--icon"><i class="ni ni-single-copy-04"></i></span>
                                <span class="btn-inner--text">Orden de compra.</span>
                            </a>
                        </div>
                    </div>            
                    <div class="col-md-2">
                        <div class="form-group"> 
                            @php
                                $archivo_remision = str_replace('public/', '', $orden_compra->archivo_remision); 
                            @endphp
                            <a href="{{ asset("storage/$archivo_remision") }}" target="_blank" class="">
                                <span class="btn-inner--icon"><i class="ni ni-single-copy-04"></i></span>
                                <span class="btn-inner--text">Remisi&oacute;n.</span>
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <div class="row px-4">
                    <div class="col-md-1 d-flex justify-content-center align-items-center">
                        <button wire:click="newItem" x-on:mouseover="event.target.style.transform = 'rotate(360deg)'" x-on:mouseleave="event.target.style.transform = 'rotate(0deg)'"
                        class="btn avatar border-1 rounded-circle bg-gradient-primary" style="box-shadow: none;" >
                            @if (is_null($selectedItem)) <i class="fas fa-plus text-white" aria-hidden="true"></i> @else <i class="fa-solid fa-pen-to-square"></i> @endif
                        </button>
                    </div>
                    <div class="col-md-11 row">
                        <div class="col-md-2"> 
                            <div class="form-group">
                                <label for="">ITEM</label>
                                <select wire:model.lazy="item" class="form-control" @if (!is_null($selectedItem)) disabled @endif>
                                    <option value="">Seleccionar</option>
                                    @foreach ($presupuesto->presupuestoItems as $key => $presupuestoItem)
                                        @if (!$presupuestoItem->evento)
                                            <option value="{{ $presupuestoItem->id }}">{{ $key+1 }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="">CANT</label>
                                <input type="number" class="form-control @error('cant') is-invalid @elseif(strlen($cant) > 0) is-valid @enderror"
                                placeholder="Cantidad" required wire:model.lazy="cant"> 
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="">DIAS</label>
                                <input type="number" class="form-control" disabled placeholder="Dias" required wire:model.lazy="dias"> 
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="">OTROS</label>
                                <input type="number" class="form-control" disabled placeholder="Otros" required wire:model.lazy="otros"> 
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">DESCRIPCION</label>
                                <textarea class="form-control @error('desc') is-invalid @elseif(strlen($desc) > 0) is-valid @enderror"
                                    placeholder="Descripción" wire:model.lazy="desc" cols="30" rows="1"></textarea>
                            </div>
                        </div>                    
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">V. UNI</label>
                                <input type="text" class="form-control @error('vUnit') is-invalid @elseif(strlen($vUnit) > 0) is-valid @enderror"
                                placeholder="Valor unitario" required wire:model.lazy="vUnit" x-mask:dynamic="$money($input)"> 
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">V. TOTAL</label>
                                <input type="text" class="form-control @error('vTotal') is-invalid @elseif(strlen($vTotal) > 0) is-valid @enderror"
                                placeholder="Total" disabled required wire:model.lazy="vTotal" x-mask:dynamic="$money($input)"> 
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row px-4">
                    @error('vUnit')
                        <div id="vUnit" class="text-invalid"> 
                            {{ $message }}
                        </div>
                    @enderror
                    @error('cant')
                        <div id="cant" class="text-invalid"> 
                            {{ $message }}
                        </div>
                    @enderror
                    @error('vTotal')
                        <div id="vTotal" class="text-invalid"> 
                            {{ $message }}
                        </div>
                    @enderror
                    @error('desc')
                        <div id="desc" class="text-invalid">
                            {{ $message }}
                        </div>
                    @enderror
                    @error('customError')
                        <div id="customError" class="text-invalid">
                            {{ $message }}
                        </div>
                    @enderror
                    @error('file_cot')
                        <div id="file_cot" class="text-invalid">
                            {{ $message }}
                        </div>
                    @enderror
                    @error('oc_helisa')
                        <div id="oc_helisa" class="text-invalid">
                            {{ $message }}
                        </div>
                    @enderror       
                </div>
                <div class="row px-4">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="cotizacion">Adjunta tu cotizaci&oacute;n:</label>
                            <input id="cotizacion" wire:model="file_cot" type="file" class="form-control" accept=".pdf,.xls,.xlsx">
                            <div wire:loading wire:target="file_cot" class="py-2">
                                <div class="spinner-border text-warning" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row px-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <button wire:click="enviarAprobacion" class="btn bg-gradient-warning mt-2 mb-0">Enviar a aprobaci&oacute;n</button>                            
                            @if($orden_compra && $orden_compra->estado_id == 3)
                                <button wire:click="deleteOrden" class="btn btn-icon btn-3 btn bg-gradient-danger mt-2 mb-0" type="button">
                                    <span class="btn-inner--icon">
                                        <i class="fa-solid fa-trash"></i>
                                    </span>
                                    <span class="btn-inner--text">Eliminar</span>
                                </button>
                            @endif 
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @if (session('success'))
        <script>
            Swal.fire(
                'Hecho',
                `{{ session('success') }}`,
                'success'
            );

            let file = document.getElementById('cotizacion');
            file.value = "";
        </script>
    @endif
</div>
