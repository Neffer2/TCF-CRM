<div>
    <div class="table-responsive mt-2 rounded bg-white">
        <table class="table">
            <thead>
                <tr>
                    <th class="font-weight-bold font-table bg-gradient-info text-white small" >COD</th>
                    <th class="font-weight-bold font-table bg-gradient-info text-white small">REVISAR</th>
                    <th class="font-weight-bold font-table bg-gradient-info text-white small">CONCEPTO</th>

                    <th class="font-weight-bold font-table bg-gradient-warning text-white small">ITEM</th>
                    <th class="font-weight-bold font-table bg-gradient-warning text-white small">CANTIDAD</th>
                    <th class="font-weight-bold font-table bg-gradient-warning text-white small">DIA</th>
                    <th class="font-weight-bold font-table bg-gradient-warning text-white small">OTROS</th>
                    <th class="font-weight-bold font-table bg-gradient-warning text-white">DESCRIPCION</th>
                    <th class="font-weight-bold font-table bg-gradient-warning text-white">V. UNITARIO</th>
                    <th class="font-weight-bold font-table bg-gradient-warning text-white">V. TOTAL</th>
                    <th class="font-weight-bold font-table bg-gradient-warning text-white">PROVEEDOR</th>
                    <th class="font-weight-bold font-table bg-gradient-warning text-white">UTILIDAD</th>

                    <th class="font-weight-bold font-table bg-gradient-success text-white small">MES</th>
                    <th class="font-weight-bold font-table bg-gradient-success text-white small">DIAS</th>
                    <th class="font-weight-bold font-table bg-gradient-success text-white small">CIUDAD</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $key => $item)
                    <tr> 
                        <td class="font-weight-bold font-table">
                            <input type="text" class="small" value="{{ $item->cod }}"> 
                        </td>
                        <td class="font-weight-bold font-table">
                            <input type="text" class="small" value="{{ $item->revisar }}"> 
                        </td>
                        <td class="font-weight-bold font-table">
                            <input type="text" class="small" value="{{ $item->concepto }}">
                        </td>

                        <td class="font-weight-bold font-table">
                            <input type="text" class="small" disabled value="{{ $key+=1 }}">
                        </td>
                        <td class="font-weight-bold font-table">
                            <input type="text" class="small" value="{{ $item->cantidad }}">
                        </td>
                        <td class="font-weight-bold font-table">
                            <input type="text" class="small" value="{{ $item->dia }}">
                        </td>
                        <td class="font-weight-bold font-table">
                            <input type="text" class="small" value="{{ $item->otros }}">
                        </td>
                        <td class="font-weight-bold font-table">
                            <textarea name="" id="" cols="30" rows="1">
                                {!! $item->descripcion !!}
                            </textarea>
                        </td>
                        <td class="font-weight-bold font-table">
                            <input type="text" value="{{ $item->v_unitario }}">
                        </td>
                        <td class="font-weight-bold font-table">
                            <input type="text" value="{{ $item->v_total }}">
                        </td>
                        <td class="font-weight-bold font-table">
                            <input type="text" value="{{ $item->proveedor }}">
                        </td>
                        <td class="font-weight-bold font-table">
                            <input type="text" value="{{ $item->margen_utilidad }}">
                        </td>

                        <td class="font-weight-bold font-table">
                            <input type="text" class="small" value="{{ $item->mes }}">
                        </td>
                        <td class="font-weight-bold font-table">
                            <input type="text" class="small" value="{{ $item->dias }}">
                        </td>
                        <td class="font-weight-bold font-table">
                            <input type="text" class="small" value="{{ $item->ciudad }}">
                        </td>
                    </tr>                    
                @endforeach
            </tbody>
        </table>
    </div>          

    <div class="row mt-2">
        <div class="col-md-1 d-flex justify-content-center align-items-center">
            <a wire:click="new_item" href="javascript:;" class="avatar border-1 rounded-circle bg-gradient-warning">
                <i class="fas fa-plus text-white" aria-hidden="true"></i>
            </a>
        </div>
        <div class="col-md-11 p-0">
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
                        placeholder="Valor unitario" required wire:model.lazy="valor_unitario">
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
                        placeholder="Valor total" required wire:model.lazy="valor_total">
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
                        placeholder="Utilidad" required wire:model.lazy="utilidad">
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
                        <input type="number" class="form-control @error('mes') is-invalid @elseif(strlen($mes) > 0) is-valid @enderror"
                        placeholder="Mes" required wire:model.lazy="mes">
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
                        <input type="text" class="form-control @error('ciudad') is-invalid @elseif(strlen($ciudad) > 0) is-valid @enderror"
                        placeholder="Ciudad" required wire:model.lazy="ciudad">
                        @error('ciudad')
                            <div id="ciudad" class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
            </div>                
        </div>
    </div>
</div>
 