<div>
    <div class="card-body pt-0">
        <div class="card">
            <div class="card-header text-center font-weight-bold bg-gradient-primary text-white p-0">
                SOLICITUD ORDEN DE COMPRA JUR&Iacute;DICA
            </div>
            <div class="row font-table px-4">
                <div class="col-md-6 mt-3">
                    <div class="table-responsive">
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
                </div>
                <div class="col-md-6 mt-3">
                    <div class="table-responsive">
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
                                        <td class="text-center">{{ $item['item'] }}</td>
                                        <td>
                                            <textarea disabled cols="30" rows="1">{{ $item['desc'] }}</textarea>
                                        </td>
                                        <td class="text-center">{{ $item['cant'] }}</td>
                                        <td class="text-center">{{ $item['vUnit'] }}</td>
                                        <td class="text-center">{{ $item['vTotal'] }}</td>
                                        <td class="d-flex justify-content-center" style="padding: 11px;">
                                            <button class="me-2" wire:click="delete({{ $item['id'] }})">
                                                ✖️
                                            </button>
                                            <button class="" wire:click="getItem({{ $item['id'] }})">
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
                        <i class="fas fa-plus text-white" aria-hidden="true"></i>
                        {{-- <i class="fa-solid fa-pen text-white" aria-hidden="true"></i> --}}
                    </button>
                </div>
                <div class="col-md-11 row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="">ITEM</label>
                            <select wire:model.lazy="item" class="form-control">
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
                            @error('desc')
                                <div id="desc" class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="">CANT</label>
                            <input type="number" class="form-control @error('cant') is-invalid @elseif(strlen($cant) > 0) is-valid @enderror"
                            placeholder="Cantidad" required wire:model.lazy="cant"> 
                            @error('cant')
                                <div id="cant" class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="">V. UNI</label>
                            <input type="number" class="form-control @error('vUnit') is-invalid @elseif(strlen($vUnit) > 0) is-valid @enderror"
                            placeholder="Valor unitario" required wire:model.lazy="vUnit"> 
                            @error('vUnit')
                                <div id="vUnit" class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="">V. TOTAL</label>
                            <input type="number" class="form-control @error('vTotal') is-invalid @elseif(strlen($vTotal) > 0) is-valid @enderror"
                            placeholder="Total" disabled required wire:model.lazy="vTotal"> 
                            @error('vTotal')
                                <div id="vTotal" class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button class="btn bg-gradient-warning mt-2 mb-0">Enviar a aprobaci&oacute;n</button>
    </div>
</div>
