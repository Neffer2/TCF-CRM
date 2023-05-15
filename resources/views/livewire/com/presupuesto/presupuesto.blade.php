<div x-data>
    <div class="card card-frame">
        <div class="row justify-content-md-center">
            <div class="col-md-3 m-2">
                <div class="card">
                    <div class="table-responsive">
                        <table class="table">
                            <tr>
                                <td class="font-weight-bold font-table">MARGEN GENERAL</td>
                                <td class="font-table">{{ $margenGeneral }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold font-table">VENTA PROYECTO</td>
                                <td class="font-table">0</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold font-table">COSTOS DEL PROYECTO</td>
                                <td class="font-table">{{ $ventaProyecto }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold font-table">MARGEN DEL PROYECTO</td>
                                <td class="font-table">0</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold font-table">MARGEN BRUTO (PESOS)</td>
                                <td class="font-table">0</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-3 m-2">
                <div class="card">
                    <div class="table-responsive">
                        <table class="table">
                            <tr>
                                <td class="font-weight-bold font-table">CONTACTO</td>
                                <td class="font-table">
                                    {{ $this->nombre }}
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold font-table">CLIENTE</td>
                                <td class="font-table">
                                    {{ $this->cliente }}
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold font-table">PROYECTO</td>
                                <td class="font-table">
                                    {{ $this->nomProyecto }}
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold font-table">CIUDAD</td>
                                <td class="font-table">
                                    {{ $this->ciudadContacto }}
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
                    <th class="font-weight-bold font-table bg-gradient-info text-white">REVISAR</th>
                    <th class="font-weight-bold font-table bg-gradient-info text-white">CONCEPTO</th>

                    <th class="font-weight-bold font-table bg-gradient-warning text-white">ITEM</th>
                    <th class="font-weight-bold font-table bg-gradient-warning text-white">CANTIDAD</th>
                    <th class="font-weight-bold font-table bg-gradient-warning text-white">DIA</th>
                    <th class="font-weight-bold font-table bg-gradient-warning text-white">OTROS</th>
                    <th class="font-weight-bold font-table bg-gradient-warning text-white">DESCRIPCION</th>
                    <th class="font-weight-bold font-table bg-gradient-warning text-white">V. UNITARIO</th>
                    <th class="font-weight-bold font-table bg-gradient-warning text-white">V. TOTAL</th>
                    <th class="font-weight-bold font-table bg-gradient-warning text-white">PROVEEDOR</th>
                    <th class="font-weight-bold font-table bg-gradient-warning text-white">UTILIDAD</th>

                    <th class="font-weight-bold font-table bg-gradient-success text-white">MES</th>
                    <th class="font-weight-bold font-table bg-gradient-success text-white">DIAS</th>
                    <th class="font-weight-bold font-table bg-gradient-success text-white">CIUDAD</th>

                    <th colspan="2" class="font-weight-bold font-table bg-gradient-primary text-white">ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $key => $item)
                    @if ($item->evento)
                        <tr>
                            <td colspan="15" class="font-weight-bold font-table text-center bg-gradient-info text-white">
                                {{ $item->descripcion }}
                            </td>
                            <td class="font-weight-bold font-table">
                                <button wire:click="deleteItem({{ $item->id }})">✖️</button>
                            </td>
                            <td class="font-weight-bold font-table">
                                <button wire:click="getDataEdit({{ $item->id }})">📝</button>
                            </td>
                        </tr>
                    @else
                        <tr> 
                            <td class="font-weight-bold font-table">
                                {{ $item->cod }}
                            </td>
                            <td class="font-weight-bold font-table">
                                {{ $item->revisar }}
                            </td>
                            <td class="font-weight-bold font-table">
                                {{ $item->concepto }}
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
                                <textarea name="" id="" cols="30" rows="1" readonly>{{ $item->descripcion }}</textarea>
                            </td>
                            <td class="font-weight-bold font-table">
                                {{ number_format($item->v_unitario) }}
                            </td>
                            <td class="font-weight-bold font-table">
                                {{ number_format($item->v_total) }}
                            </td>
                            <td class="font-weight-bold font-table">
                                {{ $item->proveedor }}
                            </td>
                            <td class="font-weight-bold font-table">
                                {{ $item->margen_utilidad }}
                            </td>

                            <td class="font-weight-bold font-table">
                                {{ $item->mes }}
                            </td>
                            <td class="font-weight-bold font-table">
                                {{ $item->dias }}
                            </td>
                            <td class="font-weight-bold font-table">
                                {{ $item->ciudad }}
                            </td>

                            <td class="font-weight-bold font-table">
                                <button wire:click="deleteItem({{ $item->id }})">✖️</button>
                            </td>
                            <td class="font-weight-bold font-table">
                                <button wire:click="getDataEdit({{ $item->id }})">📝</button>
                            </td>
                        </tr>
                    @endif           
                @endforeach
            </tbody>
        </table>
    </div>          

    <div class="row mt-2">
        <div class="col-md-12 p-2"> 
            <div class="row gy-0">
                <div class="col-md-1">
                    <div class="form-group mb-0">
                        <label for="cod">COD</label>
                        <input type="number" class="form-control @error('cod') is-invalid @elseif(strlen($cod) > 0) is-valid @enderror"
                        placeholder="Cod" required wire:model.lazy="cod"> 
                        @error('cod')
                            <div id="cod" class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="form-group mb-0">
                        <label for="revisar">REVISAR</label>
                        <input type="number" class="form-control @error('revisar') is-invalid @elseif(strlen($revisar) > 0) is-valid @enderror"
                        placeholder="Revisar" required wire:model.lazy="revisar"> 
                        @error('revisar')
                            <div id="revisar" class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="form-group mb-0">
                        <label for="concepto">CONCEPTO</label>
                        <input type="text" class="form-control @error('concepto') is-invalid @elseif(strlen($concepto) > 0) is-valid @enderror"
                        placeholder="Concepto" required wire:model.lazy="concepto"> 
                        @error('concepto')
                            <div id="concepto" class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="form-group mb-0">
                        <label for="cantidad">CANTIDAD</label>
                        <input type="number" class="form-control @error('cantidad') is-invalid @elseif(strlen($cantidad) > 0) is-valid @enderror"
                        placeholder="Cantidad" required wire:model.lazy="cantidad"> 
                        @error('cantidad')
                            <div id="cantidad" class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="form-group mb-0">
                        <label for="dia">D&Iacute;A</label>
                        <input type="number" class="form-control @error('dia') is-invalid @elseif(strlen($dia) > 0) is-valid @enderror"
                        placeholder="D&iacute;a" required wire:model.lazy="dia"> 
                        @error('dia')
                            <div id="dia" class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="form-group mb-0">
                        <label for="otros">OTROS</label>
                        <input type="number" class="form-control @error('otros') is-invalid @elseif(strlen($otros) > 0) is-valid @enderror"
                        placeholder="Otros" required wire:model.lazy="otros">
                        @error('otros')
                            <div id="otros" class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group mb-0">
                        <label for="descripcion">DESCRIPCI&Oacute;N</label>
                        <textarea id="descripcion" cols="30" rows="1" class="form-control @error('descripcion') is-invalid @elseif(strlen($descripcion) > 0) is-valid @enderror"
                            placeholder="Descripci&oacute;n" required wire:model.lazy="descripcion"></textarea>
                        @error('descripcion')
                            <div id="descripcion" class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group mb-0">
                        <label for="valor_unitario">V. UNITARIO</label>
                        <input type="text" class="form-control @error('valor_unitario') is-invalid @elseif(strlen($valor_unitario) > 0) is-valid @enderror"
                        placeholder="Valor unitario" required wire:model.lazy="valor_unitario" x-mask:dynamic="$money($input)">
                        @error('valor_unitario')
                            <div id="valor_unitario" class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group mb-0">
                        <label for="valor_total">V. TOTAL</label>
                        <input type="text" class="form-control @error('valor_total') is-invalid @elseif(strlen($valor_total) > 0) is-valid @enderror"
                        placeholder="Valor total" disabled required wire:model.lazy="valor_total" x-mask:dynamic="$money($input)">
                        @error('valor_total')
                            <div id="valor_total" class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group mb-0">
                        <label for="proveedor">PROVEEDOR</label>
                        <input type="text" class="form-control @error('proveedor') is-invalid @elseif(strlen($proveedor) > 0) is-valid @enderror"
                        placeholder="Proveedor" required wire:model.lazy="proveedor">
                        @error('proveedor')
                            <div id="proveedor" class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group mb-0">
                        <label for="utilidad">UTILIDAD</label>
                        <input type="text" class="form-control @error('utilidad') is-invalid @elseif(strlen($utilidad) > 0) is-valid @enderror"
                        placeholder="Utilidad" required wire:model.lazy="utilidad" x-mask:dynamic="$money($input)">
                        @error('utilidad')
                            <div id="utilidad" class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="form-group mb-0">
                        <label for="mes">MES</label>
                        <select class="form-control @error('mes') is-invalid @elseif(strlen($mes) > 0) is-valid @enderror"
                        placeholder="Mes" required wire:model.lazy="mes">
                            <option value="">Seleccionar</option>
                            @foreach ($meses as $mes)
                                <option value="{{ $mes->id }}">{{ $mes->description }}</option>
                            @endforeach
                        </select>
                        @error('mes')
                            <div id="mes" class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="form-group mb-0">
                        <label for="dias">DIAS</label>
                        <input type="number" class="form-control @error('dias') is-invalid @elseif(strlen($dias) > 0) is-valid @enderror"
                        placeholder="Dias" required wire:model.lazy="dias">
                        @error('dias')
                            <div id="dias" class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-2"> 
                    <div class="form-group mb-0">
                        <label for="ciudad">CIUDAD</label>
                        <select type="text" class="form-control @error('ciudad') is-invalid @elseif(strlen($ciudad) > 0) is-valid @enderror"
                        placeholder="Ciudad" required wire:model.lazy="ciudad">
                            <option selected value="">Seleccionar</option>
                            @foreach ($ciudades as $ciudad)
                                <option value="{{ $ciudad }}">{{ $ciudad }}</option>
                            @endforeach
                        </select>
                        @error('ciudad')
                            <div id="ciudad" class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
            </div>                
        </div>
        <div class="col-md-12 d-flex justify-content-left align-items-center p-2">
            <button wire:click="new_event" href="javascript:;" class="btn btn-icon btn-3 bg-gradient-info mb-0 me-1" type="button">
                <span class="btn-inner--icon"><i class="ni ni-fat-add"></i></span>
              <span class="btn-inner--text">Evento</span>
            </button>

            <button wire:click="new_item" href="javascript:;" class="btn btn-icon btn-3 bg-gradient-warning mb-0 me-1" type="button">
                <span class="btn-inner--icon"><i class="ni ni-fat-add"></i></span>
              <span class="btn-inner--text">Item</span>
            </button>       

            <button wire:click="actionEdit()" href="javascript:;" class="btn btn-icon btn-3 bg-gradient-primary mb-0" type="button">
                <span class="btn-inner--icon"><i class="ni ni-ruler-pencil"></i></span>
              <span class="btn-inner--text">Editar</span>
            </button>       
        </div>
    </div>
</div>
 