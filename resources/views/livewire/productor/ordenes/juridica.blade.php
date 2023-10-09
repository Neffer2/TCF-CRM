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
                                        <input type="text" wire:model.lazy="proveedor" style="width: 80%;">
                                        @error('proveedor')
                                            <div id="proveedor" class="text-danger">
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
                                        <input type="email" wire:model.lazy="email" placeholder="alguien@ejemplo.com" style="width: 80%;">
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
                                        <input type="text" wire:model.lazy="contacto" style="width: 80%;">
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
                                        <input type="text" wire:model.lazy="tel" style="width: 80%;">
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
                                    <th class="font-weight-bold bg-gradient-primary text-white">PIEZA - CARACTERISTICAS</th>
                                    <th class="font-weight-bold bg-gradient-primary text-white">CANT</th>
                                    <th class="font-weight-bold bg-gradient-primary text-white">V. UNI</th>
                                    <th class="font-weight-bold bg-gradient-primary text-white">V. TOTAL</th>
                                    <th colspan="2" class="font-weight-bold bg-gradient-primary text-white">ACCIONES</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ocItems as $item)
                                    <tr>
                                        <td class="text-center">{{ $item['displayItem'] }}</td>
                                        <td>
                                            <textarea disabled cols="30" rows="1">{{ $item['desc'] }}</textarea>
                                        </td> 
                                        <td class="text-center">{{ $item['cant'] }}</td>
                                        <td class="text-center">{{ number_format($item['vUnit']) }}</td>
                                        <td class="text-center">{{ number_format($item['vTotal']) }}</td>
                                        <td class="d-flex justify-content-center" style="padding: 11px;">
                                            <button class="me-2" wire:click="delete({{ $item['id'] }})">
                                                ✖️
                                            </button>
                                            <button class="" wire:click="getSelectedItem({{ $item['id'] }})">
                                                📝
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row px-4">
                <div class="col-md-1 d-flex justify-content-center align-items-end">
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
                                    <option value="{{ $presupuestoItem->id }}">{{ $key+1 }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="">DESCRIPCION</label>
                            <textarea class="form-control @error('desc') is-invalid @elseif(strlen($desc) > 0) is-valid @enderror"
                                placeholder="Descripción" wire:model.lazy="desc" cols="30" rows="1"></textarea>
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
                            <label for="">V. UNI</label>
                            <input type="text" class="form-control @error('vUnit') is-invalid @elseif(strlen($vUnit) > 0) is-valid @enderror"
                            placeholder="Valor unitario" required wire:model.lazy="vUnit" x-mask:dynamic="$money($input)"> 
                        </div>
                    </div>
                    <div class="col-md-2">
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
            </div>
            <div class="row px-4">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="cotizacion">Adjunta tu cotizaci&oacute;n:</label>
                        <input id="cotizacion" @if (session('success')) value="" @endif wire:model="file_cot" type="file" class="form-control" accept=".pdf,.xls,.xlsx">
                        <div wire:loading wire:target="file_cot" class="py-2">
                            <div class="spinner-border text-warning" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button wire:click="enviarAprobacion" class="btn bg-gradient-warning mt-2 mb-0">Enviar a aprobaci&oacute;n</button>
    </div>
    @if($errors->has('customError')) 
        <script>
            // Swal.fire(
            //     '!Oppss tenemos un problema',
            //     `@foreach($errors->all() as $error)
            //         {{ $error }} 
            //     @endforeach`,
            //     'error'
            // );
        </script>
    @endif 
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
