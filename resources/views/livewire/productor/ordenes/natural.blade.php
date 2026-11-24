<div class="card card-body mb-2" x-data>
    @php
        // Una orden existente conserva la visual correspondiente a su tipo guardado.
        $es_orden_trabajo = $queriedOrden ? (int) $queriedOrden->tipo_oc === 4 : $es_orden_trabajo;
    @endphp
    {{-- <button wire:click="sendMessage">Mensaje</button> --}}
    <div class="row">
        <div class="col-md-12 mb-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
            <h3 class="m-0">{{ $es_orden_trabajo ? 'Orden de empleado' : 'Orden de compra natural' }} @if($queriedOrden) #{{ $queriedOrden->id }} @endif</h3>
            <p class="text-sm m-0">{{ $es_orden_trabajo ? 'Asigna los ítems, guarda la orden y solicita el anticipo.' : 'Selecciona tu personal y asigna los ítems necesarios.' }}</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                @if ($es_orden_trabajo)
                    @if ((int) auth()->user()->rol === 1 && $queriedOrden)
                        @forelse ($queriedOrden->anticipos as $anticipo)
                            <div>
                            <button type="button" wire:key="descargar-anticipo-{{ $anticipo->id }}"
                                wire:click="exportarAnticipoPdf({{ $anticipo->id }})"
                                wire:loading.attr="disabled" wire:target="exportarAnticipoPdf"
                                class="btn btn-primary mb-0">
                                <span wire:loading.remove wire:target="exportarAnticipoPdf">
                                    Descargar Anticipo / Orden {{ $anticipo->cod_orden_anticipo ?? '#'.$anticipo->id }}
                                </span>
                                <span wire:loading wire:target="exportarAnticipoPdf">Generando PDF...</span>
                            </button>
                                <small class="d-block text-muted">Estado: {{ $anticipo->estado?->description ?? 'Sin estado registrado' }}</small>
                            </div>
                        @empty
                            <span class="text-muted text-sm">Esta OC no tiene anticipos asociados.</span>
                        @endforelse
                    @endif
                @else
                @include('partials.orden-compra-anticipos', [
                    'anticipos' => $queriedOrden?->anticipos?->where('estado_id', \App\Models\Anticipo::ESTADO_APROBADO) ?? collect(),
                    'claseBoton' => 'btn btn-sm btn-outline-primary mb-0',
                ])
                @endif
            </div>
        </div>
        @if ($es_orden_trabajo)
            <div class="col-12 mb-3">
                <label>Empleado solicitante</label>
                <input class="form-control" value="{{ $queriedOrden?->naturalInfo?->productor?->name ?? auth()->user()->name }}" readonly>
            </div>
        @else
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
                                <select @if (Auth()->user()->rol == 1) disabled @endif id="tercero" size="12" class="form-control" wire:model.change="tercero">
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
        @endif
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
                        <th class="font-weight-bold bg-gradient-primary text-white">NUM ITEM</th>
                        <th class="font-weight-bold bg-gradient-primary text-white">CANT</th>
                        <th class="font-weight-bold bg-gradient-primary text-white">DIAS</th>
                        <th class="font-weight-bold bg-gradient-primary text-white">OTROS</th>
                        <th class="font-weight-bold bg-gradient-primary text-white">V. UNI</th>
                        <th class="font-weight-bold bg-gradient-primary text-white">V. TOTAL</th>
                        <th class="font-weight-bold bg-gradient-primary text-white">SERVICIO</th>
                        <th class="font-weight-bold bg-gradient-primary text-white">CONTRATO</th>
                        @if (Auth()->user()->rol == 7 && ((!$queriedOrden) || ($queriedOrden && $queriedOrden->estado_id == 12) || ($queriedOrden && $queriedOrden->estado_id == 3)))
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
                            <td>{{ $item['item']['display_item'] }}</td>
                            <td>{{ $item['cant'] }}</td>
                            <td>{{ $item['dias'] }}</td>
                            <td>{{ $item['otros'] }}</td>
                            <td>{{ number_format($item['valor_unitario']) }}</td>
                            <td>{{ number_format($item['valor_total']) }}</td>
                            <td>{{ $item['tipo_servicio'] }}</td>
                            <td>{{ $item['tipo_contrato'] }}</td>
                            @if (Auth()->user()->rol == 7 && ((!$queriedOrden) || ($queriedOrden && $queriedOrden->estado_id == 3) || ($queriedOrden && $queriedOrden->estado_id == 12)))
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
    @if ((Auth()->user()->rol == 7) && ((!$queriedOrden)))
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
                            <option value="{{ $item_presupuesto->id }}">{{ $item_presupuesto->descripcion }} - ITEM {{ $item_presupuesto->num_item }}</option>
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
            @if (!$es_orden_trabajo && (int) Auth()->user()->rol === 7 && (!$queriedOrden || in_array((int) $queriedOrden->estado_id, [3, 12], true)))
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="porcentaje">NEGOCIACIÓN (%)</label>
                            <input id="porcentaje" type="number" min="0" max="100" step="0.01" class="form-control @error('porcentaje') is-invalid @enderror"
                                placeholder="Porcentaje de negociación" required wire:model.lazy="porcentaje">
                        @error('porcentaje')
                            <div class="text-invalid">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            @endif
            <div class="form-check mb-2">
                <input
                    type="checkbox"
                    id="es_orden_trabajo"
                    class="form-check-input"
                    wire:model="es_orden_trabajo"
                >
                <label class="form-check-label" for="es_orden_trabajo">
                    Marcar como Anticipo empleado
                </label>
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
        <div class="col-md-4">
            <div class="form-group">
                <label for="">JUSTIFICACION</label>
                <textarea class="form-control"
                        placeholder="Justificación" wire:model.lazy="justificacion_productor" cols="30" rows="3"></textarea>
                @error('justificacion_productor')
                    <span class="text-danger error">{{ $message }}</span> 
                @enderror
           </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                @if(!$orden_id)
                    <button wire:click="uploadOC" class="btn bg-gradient-warning mt-2 mb-0">GENERAR ORDEN DE TRABAJO</button>
                @endif
            </div>
        </div>
        
    @elseif ((Auth()->user()->rol == 7) && $queriedOrden && (int) $queriedOrden->estado_id === 3 && (int) $queriedOrden->tipo_oc === 2 && $queriedOrden->evidencias->isNotEmpty())
        @include('partials.orden-natural-revision-evidencias')
    @elseif ((Auth()->user()->rol == 7) && ($queriedOrden && ($queriedOrden->estado_id == 3)))
        @if ($queriedOrden->evidencias->isEmpty())
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
                                <option value="{{ $item_presupuesto->id }}">{{ $item_presupuesto->descripcion }} - ITEM {{ $item_presupuesto->num_item }}</option>
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
                @if (!$es_orden_trabajo && (int) Auth()->user()->rol === 7 && (!$queriedOrden || in_array((int) $queriedOrden->estado_id, [3, 12], true)))
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="porcentaje">NEGOCIACIÓN (%)</label>
                                <input id="porcentaje" type="number" min="0" max="100" step="0.01" class="form-control @error('porcentaje') is-invalid @enderror"
                                    placeholder="Porcentaje de negociación" required wire:model.lazy="porcentaje">
                            @error('porcentaje')
                                <div class="text-invalid">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                @endif
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
        @endif

        <div class="row">
            <div class="col-md-6">
                @if(!$orden_id)
                    <button wire:click="uploadOC" class="btn bg-gradient-warning mt-2 mb-0">GENERAR ORDEN DE TRABAJO</button>
                @else
                    @if ($es_orden_trabajo)
                        <button wire:click="updateOC" class="btn bg-gradient-success mt-2">Guardar orden de empleado</button>
                        <a href="{{ route('anticipo-prod', ['orden_id' => $queriedOrden->id, 'natural' => 1]) }}" class="btn btn-outline-primary mt-2">Solicitar anticipo</a>
                    @elseif ($queriedOrden->naturalInfo->terminos == 1)
                        <button class="btn bg-gradient-success mt-2 mb-0" data-bs-toggle="modal" data-bs-target="#confirmarInfo">CONFIRMAR INFORMACI&Oacute;N</button>
                            <div class="modal fade" id="confirmarInfo" tabindex="-1" aria-labelledby="confirmarInfoLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="exampleModalLabel">Confirmar información</h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            Al confirmar la informaci&oacute;n, se solicitarán evidencias al Tercero.
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                            <button type="button"wire:click="updateOC" data-bs-dismiss="modal" class="btn bg-gradient-success">Confirmar informaci&oacute;n</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    
                        @if (!($queriedOrden->evidencias->isEmpty()))
                            <button type="button" wire:click="toggleRechazo" @if($this->toggleRechazo) disabled @endif class="btn bg-gradient-danger mt-2 mb-0" data-bs-toggle="modal" data-bs-target="#rechazarEviedenciasModal"> RECHAZAR EVIDENCIAS </button>
                        @endif
                    @endif
                    
                    <button type="button" class="btn bg-gradient-danger mt-2 mb-0" data-bs-toggle="modal" data-bs-target="#exampleModal"> ELIMINAR </button>

                    <!-- Modal -->
                    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exampleModalLabel">Eliminar Orden</h1>
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
                @if ($this->toggleRechazo)
                    <div class="card mt-2">
                        <div class="card-body">
                            ¿Estas seguro de rechazar estas evidencias?
                            <div class="form-group mt-2">
                                <label for="justificacion_rechazo">Justificaci&oacute;n de Rechazo</label>
                                <textarea id="justificacion_rechazo" class="form-control @error('justificacion_rechazo') is-invalid @elseif(strlen($justificacion_rechazo) > 0) is-valid @enderror"
                                wire:model.lazy="justificacion_rechazo" cols="30" rows="5"></textarea>
                                @error('justificacion_rechazo')
                                    <div id="invalid-justificacion_rechazo" class="text-invalid">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div>
                                <button type="button" wire:click="validateEvidencia(7)"  class="btn bg-gradient-danger">Rechazar</button>
                                <button type="button" wire:click="toggleRechazo" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>    
    @elseif ((Auth()->user()->rol == 7) && ($queriedOrden && ($queriedOrden->estado_id == 12)))
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
                            <option value="{{ $item_presupuesto->id }}">{{ $item_presupuesto->descripcion }}ITEM {{ $item_presupuesto->num_item }}</option>
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
            <div class="col-12">
                <div class="form-group">
                    <label for="justificacion_rechazo_productor">Justificación de rechazo</label>
                    <textarea id="justificacion_rechazo_productor" class="form-control" cols="30" rows="3" readonly>{{ $queriedOrden->justificacion_rechazo }}</textarea>
                </div>
            </div>
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

        {{-- Botones de acción para el estado 12 (Incluye Enviar a Aprobación para cambiar a estado 8) --}}
        <div class="row">
            <div class="col-md-6">
                {{-- Botón para enviar a aprobación (Cambia a estado_id = 8, puedes cambiar "enviarAprobacion" por el nombre de tu método en Livewire) --}}
                <button type="button" wire:click="enviarAprobacion" class="btn bg-gradient-info mt-2 mb-0">ENVIAR A APROBACI&Oacute;N</button>

                <button type="button" class="btn bg-gradient-danger mt-2 mb-0" data-bs-toggle="modal" data-bs-target="#exampleModal12"> ELIMINAR </button>

                <!-- Modal Eliminar para Estado 12 -->
                <div class="modal fade" id="exampleModal12" tabindex="-1" aria-labelledby="exampleModal12Label" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="exampleModal12Label">Eliminar Orden</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                ¿Estas seguro de eliminar esta orden?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="button" wire:click="deleteOrden" class="btn bg-gradient-danger">Eliminar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @elseif ((Auth()->user()->rol == 1 || Auth()->user()->rol == 7) && ((!$queriedOrden) || ($queriedOrden && ($queriedOrden->estado_id == 7 || $queriedOrden->estado_id == 2))))
        <div>
            @if (Auth()->user()->rol == 1 && $queriedOrden->estado_id == 2)
                <div class="card mt-3">
                    <div class="card-header p-0 mx-3 mt-3 position-relative z-index-1 col-md-12">
                        <div class="row">
                            @if ($queriedOrden->observacion_causal)
                                <div class="col-md-12 mb-2">
                                    <h3 class="mb-0">Observaciones</h3>
                                    <p class="text-sm mb-0">
                                        <b>El equipo de contabilidad realizó las siguientes observaciones.
                                    </p>
                                    <div class="pe-5">
                                        <textarea class="form-control" disabled cols="15" rows="2">{{ $queriedOrden->observacion_causal }}</textarea>
                                    </div>
                                </div>
                            @endif
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
                            <div class="col-md-12 mb-4">
                                <h5 class="mb-2">Vista previa de la cuenta de cobro</h5>

                                {{-- wire:ignore evita que Livewire recargue el PDF en cada render --}}
                                <div class="d-none d-md-block w-100" wire:ignore>
                                    <embed src="{{ asset('storage/' . $queriedOrden->archivo_cuenta_cobro) }}" width="100%" height="800" type="application/pdf">
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
        <div>
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
                                    <a href="{{ asset(str_replace('public', 'storage', $queriedOrden->naturalInfo->tercero->art383)) }}" target="_blank">Art&iacute;culo 383</a><br>
                                    <a href="{{ asset(str_replace('public', 'storage', $queriedOrden->naturalInfo->contrato)) }}" target="_blank">Contrato</a><br>
                                    <a href="{{ asset(str_replace('public', 'storage', $queriedOrden->archivo_orden_helisa)) }}" target="_blank">Orden de compra</a><br>
                                    <a href="{{ asset(str_replace('public', 'storage', $queriedOrden->archivo_comprobante_pago)) }}" target="_blank">Comprobante de pago</a><br>
                                    <a href="{{ asset("storage/".($queriedOrden->archivo_cuenta_cobro)) }}" target="_blank">Ver cuenta de cobro</a>
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @if ($queriedOrden->evidencias)
                @include('partials.orden-natural-evidencias', ['evidencias' => $queriedOrden->evidencias])
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
