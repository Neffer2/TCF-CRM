<div class="card card-body mb-2" x-data>
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
                                <label for="nombre_filtro">Nombre</label>
                                <input id="nombre_filtro" type="text" class="form-control"
                                wire:model.change="search_nombre" placeholder="Nombre">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="cedula_filtro">C&eacute;dula</label>
                                <input id="cedula_filtro" type="text" class="form-control"
                                wire:model.change="search_cedula" placeholder="C&eacute;dula">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="telefono_filtro">Tel&eacute;fono</label>
                                <input id="telefono_filtro" type="text" class="form-control"
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
                                @error('tercero')
                                    <div id="invalid-tercero" class="text-invalid">
                                        {{ $message }}
                                    </div>
                                @enderror
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
                        <label for="nombre">Nombres: <span class="text-danger">*</span></label>
                        <input id="nombre" type="text" class="form-control form-control @error('nombre') is-invalid @elseif(strlen($nombre) > 0) is-valid @enderror"
                        wire:model.lazy="nombre" placeholder="Nombre">
                        @error('nombre')
                            <div id="nombre" class="text-invalid">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="apellido">Apellidos: <span class="text-danger">*</span></label>
                        <input id="apellido" type="text" class="form-control @error('apellido') is-invalid @elseif(strlen($apellido) > 0) is-valid @enderror"
                        wire:model.change="apellido" placeholder="Apellido">
                        @error('apellido')
                            <div id="apellido" class="text-invalid">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="cedula">C&eacute;dula: <span class="text-danger">*</span></label>
                        <input id="cedula" type="text" class="form-control @error('apellido') is-invalid @elseif(strlen($apellido) > 0) is-valid @enderror"
                        wire:model.change="cedula" placeholder="C.C">
                        @error('cedula')
                            <div id="cedula" class="text-invalid">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="email">Correo: <span class="text-danger">*</span></label>
                        <input id="email" type="email" class="form-control @error('correo') is-invalid @elseif(strlen($correo) > 0) is-valid @enderror"
                        wire:model.change="correo" placeholder="alguien@ejemplo.com">
                        @error('correo')
                            <div id="correo" class="text-invalid">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="telefono">Tel&eacute;fono: <span class="text-danger">*</span></label>
                        <input id="telefono" type="text" class="form-control @error('telefono') is-invalid @elseif(strlen($telefono) > 0) is-valid @enderror"
                        wire:model.change="telefono" placeholder="Telefono">
                        @error('telefono')
                            <div id="telefono" class="text-invalid">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="ciudad">Ciudad: <span class="text-danger">*</span></label>
                        <select id="ciudad" class="form-control @error('ciudad') is-invalid @elseif(strlen($ciudad) > 0) is-valid @enderror"
                        wire:model.change="ciudad">
                            <option value="">Seleccionar</option>
                            @foreach ($ciudades as $ciudad)
                                <option value="{{ $ciudad }}">{{ $ciudad }}</option>
                            @endforeach
                        </select>
                        @error('ciudad')
                            <div id="ciudad" class="text-invalid">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="banco">Banco:</label>
                        <select id="banco" class="form-control @error('banco') is-invalid @elseif(strlen($banco) > 0) is-valid @enderror"
                            wire:model.change="banco">
                            <option value="">Seleccionar</option>
                            @foreach ($bancos as $item)
                                <option value="{{ $item }}">{{ $item }}</option>
                            @endforeach
                        </select>
                        @error('banco')
                            <div id="banco" class="text-invalid">
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
                        <th class="font-weight-bold bg-gradient-primary text-white text-center">#</th>
                        <th class="font-weight-bold bg-gradient-primary text-white">PROYECTO</th>
                        <th class="font-weight-bold bg-gradient-primary text-white">CENTRO DE COSTOS</th>
                        <th class="font-weight-bold bg-gradient-primary text-white">ITEM</th>
                        <th class="font-weight-bold bg-gradient-primary text-white">NOMBRE ITEM</th>
                        <th class="font-weight-bold bg-gradient-primary text-white">CANT</th>
                        <th class="font-weight-bold bg-gradient-primary text-white">DIAS</th>
                        <th class="font-weight-bold bg-gradient-primary text-white">OTROS</th>
                        <th class="font-weight-bold bg-gradient-primary text-white">V. UNI</th>
                        <th class="font-weight-bold bg-gradient-primary text-white">V. TOTAL</th>
                        <th class="font-weight-bold bg-gradient-primary text-white">SERVICIO</th>
                        <th class="font-weight-bold bg-gradient-primary text-white">CONTRATO</th>
                        @if (Auth()->user()->rol == 7 && ((!$queriedOrden) || ($queriedOrden && $queriedOrden->estado_id == 3)))
                            <th colspan="2" class="font-weight-bold bg-gradient-primary text-white">ACCIONES</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $key => $item)
                        <tr>
                            <td class="text-center">{{ $key+=1 }}</td>
                            <td>{{ $item['proyecto']['nombre'] }}</td>
                            <td>{{ $item['proyecto']['cod_cc'] }}</td>
                            <td>{{ $item['item']['nombre'] }}</td>
                            <td>{{ $item['item']['cod_cc'] }}</td>
                            <td>{{ $item['cant'] }}</td>
                            <td>{{ $item['dias'] }}</td>
                            <td>{{ $item['otros'] }}</td>
                            <td>{{ number_format($item['valor_unitario']) }}</td>
                            <td>{{ number_format($item['valor_total']) }}</td>
                            <td>{{ $item['tipo_servicio'] }}</td>
                            <td>{{ $item['tipo_contrato'] }}</td>
                            @if (Auth()->user()->rol == 7 && ((!$queriedOrden) || ($queriedOrden && $queriedOrden->estado_id == 3)))
                                <td class="d-flex justify-content-center" style="padding: 11px;">
                                    <button class="me-2" wire:click="deleteItem({{ $key-=1 }})">
                                        ✖️
                                    </button>
                                    <button class="" wire:click="getItem({{ $key }})">
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

    @if ((Auth()->user()->rol == 7) && ((!$queriedOrden) || ($queriedOrden && $queriedOrden->estado_id == 3)))
        <div class="row">
            <div class="col-lg-2">
                <div class="form-group">
                    <label for="proyecto">Proyecto</label>
                    <select id="proyecto" class="form-control" wire:model.change="presupuesto">
                        <option value="">Seleccionar</option>
                        @foreach ($presupuestos as $presupuesto)
                            <option value="{{ $presupuesto->id }}">{{ $presupuesto->cod_cc }}</option>
                        @endforeach
                    </select>
                    @error('presupuesto')
                        <div id="invalid-presupuesto" class="text-invalid">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group">
                    <label for="item_presupuesto">Item</label>
                    <select id="item_presupuesto" class="form-control" wire:model.change="item_presupuesto">
                        <option value="">Seleccionar</option>
                        @foreach ($items_presupuesto as $item_presupuesto)
                            <option value="{{ $item_presupuesto->id }}">{{ $item_presupuesto->descripcion }}</option>
                        @endforeach
                    </select>
                    @error('item_presupuesto')
                        <div id="invalid-item_presupuesto" class="text-invalid">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="col-lg-1">
                <div class="form-group">
                    <label for="cantidad">Cantidad</label>
                    <input id="cantidad" type="number" class="form-control"
                    wire:model.lazy="cantidad" placeholder="#" x-mask:dynamic="$money($input)">
                    @error('cantidad')
                        <div id="invalid-cantidad" class="text-invalid">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="col-lg-1">
                <div class="form-group">
                    <label for="dias">Dias</label>
                    <input id="dias" type="number" class="form-control"
                    wire:model.lazy="dias" placeholder="Dias" x-mask:dynamic="$money($input)" disabled>
                    @error('dias')
                        <div id="invalid-dias" class="text-invalid">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="col-lg-1">
                <div class="form-group">
                    <label for="otros">Otro</label>
                    <input id="otros" type="number" class="form-control"
                    wire:model.lazy="otros" placeholder="Otro" x-mask:dynamic="$money($input)" disabled>
                    @error('otros')
                        <div id="invalid-otros" class="text-invalid">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="col-lg-2">
                <div class="form-group">
                    <label for="valor_unitario">Valor unitario</label>
                    <input id="valor_unitario" type="text" class="form-control"
                    wire:model.lazy="valor_unitario" placeholder="$" x-mask:dynamic="$money($input)">
                    @error('valor_unitario')
                        <div id="invalid-valor_unitario" class="text-invalid">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="col-lg-2">
                <div class="form-group">
                    <label for="valor_total">Valor Total</label>
                    <input id="valor_total" type="text" class="form-control"
                    wire:model.lazy="valor_total" placeholder="$" x-mask:dynamic="$money($input)" disabled>
                    @error('valor_total')
                        <div id="invalid-valor_total" class="text-invalid">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="col-lg-1">
                <div class="form-group">
                    <label for="tipo_servicio">Tipo de servicio</label>
                    <select id="tipo_servicio" class="form-control" wire:model.change="tipo_servicio">
                        <option value="">Seleccionar</option>
                        @foreach ($servicios as $item)
                            <option value="{{ $item }}">{{ $item }}</option>
                        @endforeach
                    </select>
                    @error('tipo_servicio')
                        <div id="invalid-cantidad" class="text-invalid">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="col-lg-2">
                <div class="form-group">
                    <label for="tipo_contrato">Tipo de contrato</label>
                    <select id="tipo_contrato" class="form-control" wire:model.change="tipo_contrato">
                        <option value="">Seleccionar</option>
                        <option value="Contrato de prestación de servicios">Contrato de prestación de servicios</option>
                    </select>
                    @error('tipo_contrato')
                        <div id="invalid-cantidad" class="text-invalid">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            @error('items-error')
                <div class="text-invalid m-0">
                    {{ $message }}
                </div>
            @enderror
        </div>
        <div class="row">
            <div class="col-lg-2">
                <div class="form-group">
                    @if (is_null($selected_item))
                        <button wire:click="newItem" x-on:mouseover="event.target.style.transform = 'rotate(360deg)'" x-on:mouseleave="event.target.style.transform = 'rotate(0deg)'"
                        class="btn avatar border-1 rounded-circle bg-gradient-primary" style="box-shadow: none;" >
                            <i class="fas fa-plus text-white"></i>
                        </button>
                    @else
                        <button wire:click="actionEdit" x-on:mouseover="event.target.style.transform = 'rotate(360deg)'" x-on:mouseleave="event.target.style.transform = 'rotate(0deg)'"
                        class="btn avatar border-1 rounded-circle bg-gradient-primary" style="box-shadow: none;" >
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                    @endif
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                @if(!$orden_id)
                    <button wire:click="uploadOC" class="btn bg-gradient-warning mt-2 mb-0">GENERAR ORDEN</button>
                @else
                    <!-- Button trigger modal -->
                    @if ($queriedOrden->naturalInfo->terminos == 1)
                        <button wire:click="updateOC" class="btn bg-gradient-success mt-2 mb-0">CONFIRMAR INFORMACI&Oacute;N</button>
                    @endif

                    <button type="button" class="btn bg-gradient-danger mt-2 mb-0" data-bs-toggle="modal" data-bs-target="#exampleModal"> ELIMINAR </button>

                    <!-- Modal -->
                    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exampleModalLabel">Elininar Orden</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    ¿Estas seguro de eliminar esta orden?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="button" wire:click="deleteOrden"  class="btn bg-gradient-danger">Eliminar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

    @elseif ((Auth()->user()->rol == 1 || Auth()->user()->rol == 7) && ((!$queriedOrden) || ($queriedOrden && ($queriedOrden->estado_id == 7 || $queriedOrden->estado_id == 2))))
        <div>
            @if (Auth()->user()->rol == 1 && $queriedOrden->estado_id == 2)
                <div class="card mt-3">
                    <div class="card-header p-0 mx-3 mt-3 position-relative z-index-1 col-md-4">
                        <div class="row">
                            <div class="col-md-12">
                                <h3 class="mb-0">Evidencias</h3>
                                <p class="text-sm mb-0">
                                    <b>{{ $queriedOrden->naturalInfo->tercero->nombre }} {{ $queriedOrden->naturalInfo->tercero->apellido }}</b> ha enviado las siguientes evidencias.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table align-items-center mb-0">
                                        <thead>
                                            <tr>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Fecha</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Foto</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Observaciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($queriedOrden->evidencias as $evidencia)
                                                <tr>
                                                    <td>
                                                        <p class="text-xs text-secondary mb-0">
                                                            {{ $evidencia->fecha_evidencia }}
                                                        </p>
                                                    </td>
                                                    <td>
                                                        <p class="text-xs text-secondary mb-0">
                                                            <a href="{{ asset(str_replace("public", "storage", $evidencia->foto_evidencia)) }}" target="_blank">
                                                                <img src="{{ asset(str_replace("public", "storage", $evidencia->foto_evidencia)) }}" height="40">
                                                            </a>
                                                        </p>
                                                    </td>
                                                    <td>
                                                        <p class="text-xs text-secondary mb-0">
                                                            {{ $evidencia->observacion_evidencia }}
                                                        </p>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="cod_oc">C&oacute;digo de orden de compra</label>
                                    <input id="cod_oc" type="text" class="form-control @error('cod_oc') is-invalid @elseif(strlen($cod_oc) > 0) is-valid @enderror"
                                    wire:model.change="cod_oc" placeholder="C&oacute;digo de orden de compra">
                                    @error('cod_oc')
                                        <div id="cod_oc" class="text-invalid">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="oc_helisa">Orden de compra Helisa</label>
                                    <input type="file" class="form-control @error('oc_helisa') is-invalid @elseif(strlen($oc_helisa) > 0) is-valid @enderror"
                                    wire:model.change="oc_helisa">
                                    @error('oc_helisa')
                                        <div id="oc_helisa" class="text-invalid">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12 mt-2">
                                <button type="button" class="btn bg-gradient-success mt-2 mb-0" data-bs-toggle="modal" data-bs-target="#successModal"> Aprobar </button>
                                <button type="button" class="btn bg-gradient-danger mt-2 mb-0" data-bs-toggle="modal" data-bs-target="#deleteModal"> Rechazar </button>

                                <!-- Modal -->
                                <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="successModalLabel">Aprobar evidencias</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                ¿Estas seguro de eliminar esta orden?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn bg-gradient-success" data-bs-dismiss="modal" wire:click="validateEvidencia(5)">Aprobar</button>
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal -->
                                <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="deleteModalLabel">Elininar Orden</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                ¿Estas seguro de rechazar esta orden?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn bg-gradient-danger" data-bs-dismiss="modal" wire:click="validateEvidencia(7)">Rechazar</button>
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        <div class="container">
            <div class="card-frame p-5">
                <h3 class="text-center">
                    @if ($queriedOrden->estado_id == 7)
                        El tercero est&aacute; subiendo sus evidencias.
                    @elseif (Auth()->user()->rol == 7 && $queriedOrden->estado_id == 2)
                        Orden de compra en revisi&oacute;n.
                    @endif
                </h3>

                @if (($queriedOrden->estado_id == 7) || (Auth()->user()->rol == 7 && $queriedOrden->estado_id == 2))
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
                @endif
            </div>
        </div>
    @elseif ($queriedOrden->estado_id == 5)
        <div class="card card-body px-3 py-0">
            <div class="table-responsive">
                <table class="table align-items-center mb-0">
                    <thead>
                        <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nombre</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Cédula</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Correo</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tel&eacute;fono</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Documentos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="1">
                                <p class="text-xs text-secondary mb-0">
                                    {{ $queriedOrden->naturalInfo->tercero->nombre }} {{ $queriedOrden->naturalInfo->tercero->apellido }}
                                </p>
                            </td>
                            <td>
                                <p class="text-xs text-secondary mb-0">
                                    {{ $queriedOrden->naturalInfo->tercero->cedula }}
                                </p>
                            </td>
                            <td>
                                <p class="text-xs text-secondary mb-0">
                                    {{ $queriedOrden->naturalInfo->tercero->correo }}
                                </p>
                            </td>
                            <td>
                                <p class="text-xs text-secondary mb-0">
                                    {{ $queriedOrden->naturalInfo->tercero->telefono }}
                                </p>
                            </td>
                            <td>
                                <p class="text-xs text-secondary mb-0">
                                    <a href="{{ asset(str_replace('public', 'storage', $queriedOrden->naturalInfo->tercero->cert_bancaria)) }}" target="_blank">Certificaci&oacute;n Bancaria</a><br>
                                    <a href="{{ asset(str_replace('public', 'storage', $queriedOrden->naturalInfo->tercero->rut)) }}" target="_blank">RUT</a><br>
                                    <a href="{{ asset(str_replace('public', 'storage', $queriedOrden->naturalInfo->contrato)) }}" target="_blank">Contrato</a>
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @if ($queriedOrden->evidencias)
                <div class="table-responsive">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Fecha</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Foto</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Observaciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($queriedOrden->evidencias as $evidencia)
                                <tr>
                                    <td>
                                        <p class="text-xs text-secondary mb-0">
                                            {{ $evidencia->fecha_evidencia }}
                                        </p>
                                    </td>
                                    <td>
                                        <p class="text-xs text-secondary mb-0">
                                            <a href="{{ asset(str_replace("public", "storage", $evidencia->foto_evidencia)) }}" target="_blank">
                                                <img src="{{ asset(str_replace("public", "storage", $evidencia->foto_evidencia)) }}" height="40">
                                            </a>
                                        </p>
                                    </td>
                                    <td>
                                        <p class="text-xs text-secondary mb-0">
                                            {{ $evidencia->observacion_evidencia }}
                                        </p>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif
    {{-- @if (Auth()->user()->rol == 1)
        <button></button>
    @endif --}}
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
