<div class="card card-body mb-2">
    <div class="row">
        <div class="col-md-12 mb-3">
            <h3 class="m-0">Orden de compra natural</h3>
            <p class="text-sm m-0">Selecciona tu personal y asigna los items nesesarios.</p>
        </div>     
        <div class="col-md-6" style="border-right: 1px solid #eee;">        
            <div class="row">
                <div class="col-md-4">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Nombre</label>
                                <input type="text" class="form-control"
                                wire:model.change="search_nombre" placeholder="Nombre">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">C&eacute;dula</label>
                                <input type="text" class="form-control"
                                wire:model.change="search_cedula" placeholder="C&eacute;dula">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Tel&eacute;fono</label>
                                <input type="text" class="form-control"
                                wire:model.change="search_telefono" placeholder="Tel&eacute;fono">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="tercero">Tercero: <span class="text-danger">*</span></label>
                                <select id="tercero" size="12" class="form-control" wire:model.change="tercero">
                                    @foreach ($terceros as $tercero)
                                        <option value="{{ $tercero->id }}">{{ $tercero->nombre }} {{ $tercero->apellido }} - {{ $tercero->cedula }}</option>
                                    @endforeach
                                </select>
                            </div> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">     
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="">Nombres: <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control @error('nombre') is-invalid @elseif(strlen($nombre) > 0) is-valid @enderror" 
                        wire:model.lazy="nombre" placeholder="Nombre">
                        @error('nombre')
                            <div id="nombre" class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="">Apellidos: <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('apellido') is-invalid @elseif(strlen($apellido) > 0) is-valid @enderror"
                        wire:model.change="apellido" placeholder="Apellido">
                        @error('apellido')
                            <div id="apellido" class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="">C&eacute;dula: <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('apellido') is-invalid @elseif(strlen($apellido) > 0) is-valid @enderror"
                        wire:model.change="cedula" placeholder="C.C">
                        @error('cedula')
                            <div id="cedula" class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="">Correo: <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('correo') is-invalid @elseif(strlen($correo) > 0) is-valid @enderror"
                        wire:model.change="correo" placeholder="alguien@ejemplo.com">
                        @error('correo')
                            <div id="correo" class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="">Tel&eacute;fono: <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('telefono') is-invalid @elseif(strlen($telefono) > 0) is-valid @enderror"
                        wire:model.change="telefono" placeholder="Telefono">
                        @error('telefono')
                            <div id="telefono" class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">                    
                        <label for="">Ciudad: <span class="text-danger">*</span></label>
                        <select id="" class="form-control @error('ciudad') is-invalid @elseif(strlen($ciudad) > 0) is-valid @enderror"
                        wire:model.change="ciudad">
                            <option value="">Seleccionar</option>
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
                <div class="col-md-4">
                    <div class="form-group">                    
                        <label for="">Banco:</label>
                        <select id="" class="form-control @error('banco') is-invalid @elseif(strlen($banco) > 0) is-valid @enderror"
                            wire:model.change="banco">
                            <option value="">Seleccionar</option>
                            <option value="Banco 1">Banco 2</option>
                        </select>
                        @error('banco')
                            <div id="banco" class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>        
    </div>
    <div class="row font-table py-2">
        <div class="col-md-12 table-responsive">
            <table class="table">
                <thead> 
                    <tr>  
                        <th class="font-weight-bold bg-gradient-primary text-white">PROYECTO</th>
                        <th class="font-weight-bold bg-gradient-primary text-white">CENTRO DE COSTOS</th>
                        <th class="font-weight-bold bg-gradient-primary text-white">ITEM</th>
                        <th class="font-weight-bold bg-gradient-primary text-white">NOMBRE ITEM</th>
                        <th class="font-weight-bold bg-gradient-primary text-white">CANT</th>
                        <th class="font-weight-bold bg-gradient-primary text-white">DIAS</th>
                        <th class="font-weight-bold bg-gradient-primary text-white">OTROS</th>
                        <th class="font-weight-bold bg-gradient-primary text-white">V. UNI</th>
                        <th class="font-weight-bold bg-gradient-primary text-white">V. TOTAL</th>
                        <th colspan="2" class="font-weight-bold bg-gradient-primary text-white">ACCIONES</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $item)
                        <tr>
                            <td>{{ $item['proyecto']['nombre'] }}</td>
                            <td>{{ $item['proyecto']['cod_cc'] }}</td>
                            <td>{{ $item['item']['nombre'] }}</td>
                            <td>{{ $item['item']['cod_cc'] }}</td>
                            <td>{{ $item['cant'] }}</td>
                            <td>{{ $item['dias'] }}</td>
                            <td>{{ $item['otros'] }}</td>
                            <td>{{ $item['valor_unitario'] }}</td>
                            <td>{{ $item['valor_total'] }}</td>
                            <td class="d-flex justify-content-center" style="padding: 11px;">
                                <button class="me-2">
                                    ✖️
                                </button>
                                <button class="">
                                    📝
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label for="">Proyecto</label>
                <select class="form-control" wire:model.change="presupuesto">
                    <option value="">Seleccionar</option> 
                    @foreach ($presupuestos as $presupuesto)
                        <option value="{{ $presupuesto->id }}">{{ $presupuesto->cod_cc }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="">Item</label>
                <select class="form-control" wire:model.change="item_presupuesto">
                    <option value="">Seleccionar</option> 
                    @foreach ($items_presupuesto as $item_presupuesto)
                        <option value="{{ $item_presupuesto->id }}">{{ $item_presupuesto->descripcion }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-1">
            <div class="form-group">
                <label for="">Cantidad</label>
                <input type="number" class="form-control"
                wire:model.lazy="cantidad" placeholder="Nombre">
            </div>
        </div>
        <div class="col-md-1">
            <div class="form-group">
                <label for="">Dias</label>
                <input type="number" class="form-control"
                wire:model.lazy="dias" placeholder="Nombre">
            </div>
        </div>
        <div class="col-md-1">
            <div class="form-group">
                <label for="">Otro</label>
                <input type="number" class="form-control"
                wire:model.lazy="otros" placeholder="Nombre">
            </div>
        </div>
        <div class="col-md-1">
            <div class="form-group">
                <label for="">Valor unitario</label>
                <input type="text" class="form-control"
                wire:model.lazy="valor_unitario" placeholder="Nombre">
            </div>
        </div>
        <div class="col-md-1">
            <div class="form-group">
                <label for="">Valor Total</label>
                <input type="text" class="form-control"
                wire:model.lazy="valor_total" placeholder="Nombre">
            </div>
        </div>
        <div class="col-md-1 justify-content-center align-content-end">
            <div class="form-group">
                <button wire:click="newItem" class="btn bg-gradient-primary m-0 ">AGREGAR</button>
            </div>
        </div>
    </div>
    <hr class="ct-docs-hr">
    @if (session('success'))
        <script>
            Swal.fire(
                'Hecho',
                `{{ session('success') }}`,
                'success'
            );
        </script>
    @endif 
</div>