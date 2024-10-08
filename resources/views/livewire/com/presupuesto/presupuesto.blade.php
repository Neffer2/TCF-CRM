<div x-data="">
    @if ($estadoValidator != 2 || Auth::user()->rol == 1)
        <div class="card card-frame p-2">
            <div class="row justify-content-md-center">
                <div class="col-md-3">
                    <div class="card">
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <tr>
                                    <td class="font-weight-bold font-table">MARGEN GENERAL</td>
                                    <td class="font-table">{{ number_format($margenGeneral, 4) }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold font-table">VENTA PROYECTO</td>
                                    <td class="font-table">{{ number_format($ventaProyecto) }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold font-table">COSTOS DEL PROYECTO</td>
                                    <td class="font-table">{{ number_format($costosProyecto) }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold font-table">MARGEN DEL PROYECTO</td>
                                    <td class="font-table">{{ number_format($margenProyecto, 2) }} %</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold font-table">MARGEN BRUTO (PESOS)</td>
                                    <td class="font-table">{{ number_format($margenBruto) }}</td>
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
                                        {{$presupuesto->gestion->contacto->nombre}}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold font-table">CLIENTE</td>
                                    <td class="font-table">
                                        {{$presupuesto->gestion->contacto->empresa}}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold font-table">PROYECTO</td>
                                    <td class="font-table">
                                        {{$presupuesto->gestion->nom_proyecto_cot}}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold font-table">CENTRO DE COSTOS</td>
                                    <td class="font-table">
                                        @if ($presupuesto->cod_cc)
                                            {{ $presupuesto->cod_cc }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold font-table">CIUDAD</td>
                                    <td class="font-table">
                                        {{$presupuesto->gestion->contacto->ciudad}}
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
                                        <input type="text" wire:model.lazy="imprevistos" placeholder="%" @if (Auth::user()->rol == 1) disabled @endif
                                        class="@error('imprevistos') invalid-input @enderror">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold font-table">ADMINISTRACI&Oacute;N</td>
                                    <td class="font-table">
                                        <input type="text" wire:model.lazy="administracion" placeholder="%" @if (Auth::user()->rol == 1) disabled @endif
                                        class="@error('administracion') invalid-input @enderror">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold font-table">FEE AGENCIA</td>
                                    <td class="font-table">
                                        <input type="text" wire:model.lazy="fee" placeholder="%" @if (Auth::user()->rol == 1) disabled @endif
                                        class="@error('fee') invalid-input @enderror">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold font-table">TIEMPO</td>
                                    <td class="font-table">
                                        <input type="text" wire:model.lazy="tiempoFactura" placeholder="" @if (Auth::user()->rol == 1) disabled @endif
                                        class="@error('tiempoFactura') invalid-input @enderror">
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
                                        <textarea wire:model.lazy="notas" cols="55" rows="8" @if (Auth::user()->rol == 1) disabled @endif></textarea>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Actualizacion --}}
            @if (Auth::user()->rol == 1)
                <div class="row mt-2">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header p-0 mt-3 col-md-12">
                                <div class="row px-3">
                                    <div class="col-md-12">
                                        <h3 class="mb-0">Justificaci&oacute;n comercial</h3>
                                        <p class="text-sm mb-0">Revisa la justificaci&oacute;n que el comercial escribi&oacute; para t&iacute;.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-2">
                                <div class="form-group">
                                    <textarea name="justificacion" @if(Auth::user()->rol == 1) disabled @endif id="justificacion" cols="10" rows="2" class="form-control" wire:model="justificacion" class="form-control @error('justificacion') is-invalid @elseif(strlen($justificacion) > 0) is-valid @enderror"></textarea>
                                    @error('justificacion')
                                        <small id="justificacion" class="text-danger bold">
                                            {{ $message }}
                                        </small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header p-0 mt-3 col-md-12">
                                <div class="row px-3">
                                    <div class="col-md-12">
                                        <h3 class="mb-0">Justificaci&oacute;n compras</h3>
                                        <p class="text-sm mb-0">Expl&iacute;cale al comercial porqu&eacute; ha sido rechazado el presupuesto.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-2">
                                <div class="form-group">
                                    <textarea name="justificacion_compras" @if(Auth::user()->rol != 1) disabled @endif id="justificacion_compras" cols="10" rows="2" class="form-control" wire:model="justificacion_compras" class="form-control @error('justificacion_compras') is-invalid @elseif(strlen($justificacion_compras) > 0) is-valid @enderror"></textarea>
                                    @error('justificacion_compras')
                                        <small id="justificacion_compras" class="text-danger bold">
                                            {{ $message }}
                                        </small>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <button class="btn bg-gradient-warning m-0" wire:click="rechazar" wire:loading.attr="disabled">Rechazar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            @if ($justificacion_compras && Auth::user()->rol != 1)
                <div class="row mt-2">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header p-0 mt-3 col-md-12">
                                <div class="row px-3">
                                    <div class="col-md-12">
                                        <h3 class="mb-0">Justificaci&oacute;n compras</h3>
                                        <p class="text-sm mb-0">Revisa el poqu&eacute; fu&eacute; rechazado tu presupuesto.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-2">
                                <div class="form-group">
                                    <textarea name="justificacion_compras" @if(Auth::user()->rol != 1) disabled @endif id="justificacion_compras" cols="10" rows="1" class="form-control" wire:model="justificacion_compras" class="form-control @error('justificacion_compras') is-invalid @elseif(strlen($justificacion_compras) > 0) is-valid @enderror"></textarea>
                                    @error('justificacion_compras')
                                        <small id="justificacion_compras" class="text-danger bold">
                                            {{ $message }}
                                        </small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
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
                        @if ($presupuesto->gestion->claro)
                            <th class="font-weight-bold font-table bg-gradient-warning text-white">V. TOTAL CLIENTE</th>
                        @endif
                        <th class="font-weight-bold font-table bg-gradient-warning text-white">PROVEEDOR</th>
                        <th class="font-weight-bold font-table bg-gradient-warning text-white">UTILIDAD</th>

                        <th class="font-weight-bold font-table bg-gradient-success text-white">MES</th>
                        <th class="font-weight-bold font-table bg-gradient-success text-white">DIAS</th>
                        <th class="font-weight-bold font-table bg-gradient-success text-white">CIUDAD</th>

                        @if ($rentabilidadView)
                            <th class="font-weight-bold font-table bg-rentabilidad text-white">V. UNITARIO</th>
                            <th class="font-weight-bold font-table bg-rentabilidad text-white">V. TOTAL</th>
                            <th class="font-weight-bold font-table bg-rentabilidad text-white">RENTABILIDAD</th>
                        @endif

                        @if (Auth::user()->rol != 1)
                            <th colspan="3" class="font-weight-bold font-table bg-gradient-primary text-white">ACCIONES</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $key => $item)
                        @if ($item->evento)
                            <tr class="font-weight-bold font-table bg-gradient-info text-white">
                                <td colspan="@if ($rentabilidadView) 16 @else 13 @endif" class="text-center">
                                    {{ $item->descripcion }}
                                </td>
                                @if (Auth::user()->rol != 1)
                                    <td>
                                        <button wire:click="deleteItem({{ $item->id }})">✖️</button>
                                    </td>
                                @endif
                                @if (Auth::user()->rol != 1)
                                    <td>
                                        <button wire:click="getDataEdit({{ $item->id }})">📝</button>
                                    </td>
                                @endif
                            </tr>
                        @else
                            <tr @if ($item->actualizado) style="background-color: #ffbb17" class="text-white" @endif>
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
                                    <textarea name="" id="" cols="30" rows="1" readonly>{{ $item->descripcion }}</textarea>
                                </td>
                                <td class="font-weight-bold font-table">
                                    $ {{ number_format($item->v_unitario) }}
                                </td>
                                <td class="font-weight-bold font-table">
                                    $ {{ number_format($item->v_total) }}
                                </td>
                                @if ($presupuesto->gestion->claro)
                                    <td class="font-weight-bold font-table">
                                        $ {{ number_format($item->v_total_cliente) }}
                                    </td>
                                @endif
                                <td class="font-weight-bold font-table">
                                    @if ($proveedores_item = @unserialize($item->proveedor))
                                        @foreach ($proveedores_item as $proveedor)
                                            {{ @$proveedores->find($proveedor)->tercero }} <br>
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
                                    {{ $item->margen_utilidad }}
                                </td>

                                <td class="font-weight-bold font-table">
                                    @if ($item->mesDescription)
                                        {{ $item->mesDescription->description }}
                                    @endif
                                </td>
                                <td class="font-weight-bold font-table">
                                    {{ $item->dias }}
                                </td>
                                <td class="font-weight-bold font-table">
                                    {{ $item->ciudad }}
                                </td>

                                @if ($rentabilidadView)
                                    <td class="font-weight-bold font-table">
                                        $ {{ number_format($item->v_unitario_cot) }}
                                    </td>
                                    <td class="font-weight-bold font-table">
                                        $ {{ number_format($item->v_total_cot) }}
                                    </td>
                                    <td class="font-weight-bold font-table">
                                        $ {{ number_format($item->rentabilidad) }}
                                    </td>
                                @endif
                                @if (Auth::user()->rol == 2)
                                    <td class="font-weight-bold">
                                        <div class="form-check">
                                            <input wire:change="changeDisponibilidad({{ $item->id }})"
                                                class="form-check-input" type="checkbox" @if ($item->disponible) checked @endif>
                                        </div>
                                    </td>
                                    <td class="font-weight-bold font-table">
                                        @if (!$presupuesto->cod_cc)
                                            <button wire:click="deleteItem({{ $item->id }})">✖️</button>
                                        @endif
                                    </td>
                                @endif
                                @if (Auth::user()->rol == 2)
                                    <td class="font-weight-bold font-table">
                                        <button wire:click="getDataEdit({{ $item->id }})">📝</button>
                                    </td>
                                @endif
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>

    <div class="row mt-2">
        @if (Auth::user()->rol == 2 || Auth::user()->rol == 5)
            <div class="col-md-12 p-2">
                <div class="row gy-0 mb-3">
                    <div class="col-md-1">
                        <div class="form-group mb-0">
                            <label for="cod">COD</label>
                            <select type="number" class="form-control @error('cod') is-invalid @elseif(strlen($cod) > 0) is-valid @enderror"
                            placeholder="Cod" required wire:model.lazy="cod">
                                <option value="">Seleccionar</option>
                                <option value="0">---- Sin tarifario ----</option>
                                @foreach ($tarifario as $item)
                                    <option value="{{ $item->id }}">{{ $item->concepto }} {{ $item->caso }} - {{ number_format($item->v_unidad) }}</option>
                                @endforeach
                            </select>
                            @error('cod')
                                <div id="cod" class="invalid-feedback">
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
                                    {!! $message !!}
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
                                    {!! $message !!}
                                </div>
                            @enderror
                        </div>
                    </div>
                    @if ($presupuesto->gestion->claro)
                        <div class="col-md-2">
                            <div class="form-group mb-0">
                                <label for="valor_total_cliente">V. TOTAL CLIENTE (CLARO)</label>
                                <input id="valor_total_cliente" type="text" class="form-control @error('valor_total_cliente') is-invalid @elseif(strlen($valor_total_cliente) > 0) is-valid @enderror"
                                placeholder="Valor total cliente (claro)" required wire:model.lazy="valor_total_cliente" x-mask:dynamic="$money($input)">
                                @error('valor_total_cliente')
                                    <div id="valor_total_cliente" class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    @endif
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
                            <select class="form-control @error('mes') is-invalid @elseif(strlen($mes) > 0) is-valid @enderror"
                            placeholder="Mes" required wire:model.lazy="mes" required>
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
                            <label for="dias">D&Iacute;AS</label>
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
                    <div class="col-md-4">
                        <div class="form-group mb-0">
                            <label for="proveedor">PROVEEDOR</label>
                            {{-- <input type="text" class="form-control @error('proveedor') is-invalid @elseif(strlen($proveedor) > 0) is-valid @enderror"
                            placeholder="Proveedor" required wire:model.lazy="proveedor"> --}}
                            <select class="form-control select-multiple" @error('proveedor') is-invalid @enderror
                                placeholder="Proveedor" required wire:model.lazy="proveedor" multiple>
                                @foreach ($categorias_proveedor as $categoria)
                                    <optgroup label="{{ $categoria->description }}">
                                        @foreach ($categoria->proveedores as $proveedor)
                                            <option value="{{ $proveedor->id }}">{{ $proveedor->tercero }} - {{ $proveedor->categoria->description }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                            @error('proveedor')
                                <div id="proveedor" class="text-invalid">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="justificacion">JUSTIFICACI&Oacute;N</label>
                            <textarea name="justificacion" @if(Auth::user()->rol == 1) disabled @endif id="justificacion" cols="5" rows="2" class="form-control"
                                wire:model="justificacion" class="form-control @error('justificacion') is-invalid @elseif(strlen($justificacion) > 0) is-valid @enderror"
                                @if($presupuesto->cod_cc) placeholder="Explícale a compras tu presupuesto." @else placeholder="Si es necesario, explícale a compras tu presupuesto." @endif></textarea>
                            @error('justificacion')
                                <small id="justificacion" class="text-danger">
                                    {{ $message }}
                                </small>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8 d-flex p-2">
                <button wire:click="new_item" class="btn btn-icon btn-3 bg-gradient-warning mb-0 me-1" type="button">
                    <span class="btn-inner--icon"><i class="ni ni-fat-add"></i></span>
                <span class="btn-inner--text">Item</span>
                </button>

                <button wire:click="new_event" class="btn btn-icon btn-3 bg-gradient-info mb-0 me-1" type="button">
                    <span class="btn-inner--icon"><i class="ni ni-fat-add"></i></span>
                <span class="btn-inner--text">Evento</span>
                </button>

                <button wire:click="actionEdit()" class="btn btn-icon btn-3 bg-gradient-primary mb-0 me-1" type="button">
                    <span class="btn-inner--icon"><i class="ni ni-ruler-pencil"></i></span>
                <span class="btn-inner--text">Editar</span>
                </button>

                <button class="btn btn-icon btn-3 bg-gradient-success mb-0 me-1" data-bs-toggle="modal" data-bs-target="#staticBackdrop" type="button">
                    <span class="btn-inner--icon"><i class="ni ni-single-copy-04"></i></span>
                    <span class="btn-inner--text">Exportar</span>
                </button>

                <!-- Modal -->
                <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="staticBackdropLabel">Exportar</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <h2 class="fs-5">Documentos Cliente</h2>
                                    <button wire:click="cotizacionPdf" class="btn btn-icon btn-3 bg-gradient-warning mb-0 me-1" type="button" data-bs-dismiss="modal">
                                        <span class="btn-inner--icon"><i class="ni ni-single-copy-04"></i></span>
                                        <span class="btn-inner--text">Cotizaci&oacute;n PDF</span>
                                    </button>

                                    <button wire:click="cotizacionExcel" class="btn btn-icon btn-3 bg-gradient-success mb-0 me-1" type="button" data-bs-dismiss="modal">
                                        <span class="btn-inner--icon"><i class="ni ni-single-copy-04"></i></span>
                                        <span class="btn-inner--text">Cotizaci&oacute;n Excel</span>
                                    </button>
                                <hr class="horizontal dark">
                                <h2 class="fs-5">Documentos Interno</h2>
                                @if ($presupuesto->cod_cc)
                                    <button wire:click="internoPdf" class="btn btn-icon btn-3 bg-gradient-warning mb-0 me-1" type="button" data-bs-dismiss="modal">
                                        <span class="btn-inner--icon"><i class="ni ni-single-copy-04"></i></span>
                                        <span class="btn-inner--text">Interno PDF</span>
                                    </button>

                                    <button wire:click="internoExcel" class="btn btn-icon btn-3 bg-gradient-success mb-0 me-1" type="button" data-bs-dismiss="modal">
                                        <span class="btn-inner--icon"><i class="ni ni-single-copy-04"></i></span>
                                        <span class="btn-inner--text">Cotizaci&oacute;n Excel</span>
                                    </button>
                                @endif
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-check form-switch me-1">
                    <input wire:click="toggelRentabilidad" class="form-check-input" type="checkbox" id="flexSwitchCheckDefault">
                    <label class="form-check-label" for="flexSwitchCheckDefault">Vista rentabilidad</label>
                </div>
            </div>

            <div class="col-md-4 d-flex justify-content-end p-2">
                <button class="btn btn-icon btn-3 bg-gradient-warning mb-0 me-1" type="button" data-bs-toggle="modal" data-bs-target="#exampleModal" wire:loading.attr="disabled">
                    <span class="btn-inner--icon"><i class="ni ni-check-bold"></i></i></span>
                <span class="btn-inner--text">Enviar a aprobaci&oacute;n</span>
                </button>

                <!-- Modal -->
                <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h3 class="modal-title" id="exampleModalLabel">¿Estas seguro?</h3>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                Verifica que tu presupuesto est&eacute; completo antes de enviarlo.
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button wire:click="aprobacion" type="button" class="btn bg-gradient-warning" data-bs-dismiss="modal">Enviar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @elseif (Auth::user()->rol == 1)
            <div class="col-md-12 p-2">
                <div class="row gy-0">
                    <div class="col-md-3">
                        <div class="form-group mb-0">
                            <label for="centroCostos">CENTRO DE COSTOS</label>
                            <input type="text" class="form-control @error('centroCostos') is-invalid @elseif(strlen($centroCostos) > 0) is-valid @enderror"
                            placeholder="Centro de costos" required wire:model.lazy="centroCostos">
                            {{-- @if($this->presupuesto->cod_cc) disabled @endif --}}
                            @error('centroCostos')
                                <div id="centroCostos" class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                            <button wire:click="updateCentro" wire:loading.attr="disabled" class="btn btn-icon btn-3 bg-gradient-warning mb-0 mt-1" type="button">
                                <span class="btn-inner--icon"><i class="ni ni-ruler-pencil"></i></span>
                                <span class="btn-inner--text">Guardar</span>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6 py-1">
                        <div class="form-check form-switch me-1">
                            <input wire:click="toggelRentabilidad" class="form-check-input" type="checkbox" id="flexSwitchCheckDefault">
                            <label class="form-check-label" for="flexSwitchCheckDefault">Vista rentabilidad</label>
                        </div>
                        <button class="btn btn-icon btn-3 bg-gradient-success mb-0 me-1" data-bs-toggle="modal" data-bs-target="#staticBackdrop" type="button">
                            <span class="btn-inner--icon"><i class="ni ni-single-copy-04"></i></span>
                            <span class="btn-inner--text">Exportar</span>
                        </button>

                        <!-- Modal -->
                        <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Exportar</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="modal-body">
                                            <h2 class="fs-5">Documentos Cliente</h2>
                                                <button wire:click="cotizacionPdf" class="btn btn-icon btn-3 bg-gradient-warning mb-0 me-1" type="button" data-bs-dismiss="modal">
                                                    <span class="btn-inner--icon"><i class="ni ni-single-copy-04"></i></span>
                                                    <span class="btn-inner--text">Cotizaci&oacute;n PDF</span>
                                                </button>

                                                <button wire:click="cotizacionExcel" class="btn btn-icon btn-3 bg-gradient-success mb-0 me-1" type="button" data-bs-dismiss="modal">
                                                    <span class="btn-inner--icon"><i class="ni ni-single-copy-04"></i></span>
                                                    <span class="btn-inner--text">Cotizaci&oacute;n Excel</span>
                                                </button>
                                            <hr class="horizontal dark">
                                            <h2 class="fs-5">Documentos Interno</h2>
                                            @if ($presupuesto->cod_cc)
                                                <button wire:click="internoPdf" class="btn btn-icon btn-3 bg-gradient-warning mb-0 me-1" type="button" data-bs-dismiss="modal">
                                                    <span class="btn-inner--icon"><i class="ni ni-single-copy-04"></i></span>
                                                    <span class="btn-inner--text">Interno PDF</span>
                                                </button>

                                                <button wire:click="internoExcel" class="btn btn-icon btn-3 bg-gradient-success mb-0 me-1" type="button" data-bs-dismiss="modal">
                                                    <span class="btn-inner--icon"><i class="ni ni-single-copy-04"></i></span>
                                                    <span class="btn-inner--text">Cotizaci&oacute;n Excel</span>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    @elseif($estadoValidator == 2)
        <div class="card card-frame p-5">
            <h3 class="text-center">Tu presupuesto est&aacute; siendo validado.</h3>
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
