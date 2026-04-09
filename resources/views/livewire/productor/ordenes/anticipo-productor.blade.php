<div class="card {{ $anticipo_id ? 'p-3' : '' }}">
    <div class="card-header text-center font-weight-bold bg-gradient-info text-white p-0">
        SOLICITUD ANTICIPO PRODUCTOR
    </div>

    @if (Auth::user()->rol == 7 && ! $queriedAnticipo)
        <div class="row px-4 mt-3">
            <div class="col-md-12 border-bottom" style="border-color: #f3f3f3">
                <div class="form-group">
                    <label for="centro_costo">Centro de costos:</label>
                    <select name="centro_costo" id="centro_costo" class="form-control" wire:model="centro_costo">
                        <option value="">Seleccione un centro de costos</option>
                        @foreach ($centros_costo as $centro)
                            <option value="{{ $centro->id }}">{{ $centro->cod_cc }}</option>
                        @endforeach
                    </select>
                    @error('centro_costo')
                        <div id="centro_costo" class="text-invalid">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
        </div>
    @endif

    <div class="row font-table px-4">
        <div class="col-md-6 mt-3">
            <div class="table-responsive">
                <table class="table mb-1">
                    <tr style="height: 35px;">
                        <td class="font-weight-bold">Cliente:</td>
                        @if ($centro_costo)
                            <td>{{ $centros_costo->find($centro_costo)->gestion->contacto->empresa }}</td>
                        @endif
                    </tr>
                    <tr style="height: 35px;">
                        <td class="font-weight-bold">Proyecto:</td>
                        @if ($centro_costo)
                            <td>{{ $centros_costo->find($centro_costo)->gestion->nom_proyecto_cot }}</td>
                        @endif
                    </tr>
                    @if ($anticipo_id)
                        <tr style="height: 35px;">
                            <td class="font-weight-bold">Productor:</td>
                            <td>{{ $queriedAnticipo->productor_info->name }}</td>
                        </tr>
                    @endif
                </table>
            </div>
        </div>
        <div class="col-md-6 mt-3">
            <div class="table-responsive">
                <table class="table mb-1">
                    <tr style="height: 35px;">
                        <td class="font-weight-bold">Centro de Costos:</td>
                        @if ($centro_costo)
                            <td>{{ $centros_costo->find($centro_costo)->cod_cc }}</td>
                        @endif
                    </tr>
                    <tr style="height: 35px;">
                        <td class="font-weight-bold">Ciudad:</td>
                        @if ($centro_costo)
                            <td>{{ $centros_costo->find($centro_costo)->presupuestoItems[0]->ciudad }}</td>
                        @endif
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="row font-table px-4">
        <div class="col-md-12">
            <div class="card card-body table-responsive mb-3 rounded bg-whitem p-0">
                <table class="table mb-0">
                    <thead>
                    <tr>
                        <th class="font-weight-bold bg-gradient-info text-white">ITEM</th>
                        <th class="font-weight-bold bg-gradient-info text-white">CANT</th>
                        <th class="font-weight-bold bg-gradient-info text-white">DIAS</th>
                        <th class="font-weight-bold bg-gradient-info text-white">OTROS</th>
                        <th class="font-weight-bold bg-gradient-info text-white">CARACTERISTICAS</th>
                        <th class="font-weight-bold bg-gradient-info text-white">V. UNI</th>
                        <th class="font-weight-bold bg-gradient-info text-white">V. TOTAL</th>
                        <th class="font-weight-bold bg-gradient-info text-white">V. ANTICIPO</th>
                        <th class="font-weight-bold bg-gradient-info text-white">SALDO</th>
                        @if (Auth()->user()->rol == 7 && ( (!$queriedAnticipo) || ( $queriedAnticipo->estado_id == 11 || $queriedAnticipo->estado_id == 12 ) ))
                            <th class="font-weight-bold bg-gradient-info text-white">ACCIONES</th>
                        @endif
                    </tr>
                    </thead>
                    <tbody>
                        @if ($centro_costo)
                            @foreach ($items as $key => $item)
                                @php
                                    $key+=1;
                                @endphp
                                <tr>
                                    <td class="text-center font-weight-bold">{{ $item['display_item'] }}</td>
                                    <td class="text-center">{{ $item['cant'] }}</td>
                                    <td class="text-center">{{ $item['dias'] }}</td>
                                    <td class="text-center">{{ $item['otros'] }}</td>
                                    <td>
                                        <textarea disabled cols="30" rows="1">{{ $item['desc'] }}</textarea>
                                    </td>
                                    <td class="text-center">{{ number_format($item['valor_unitario']) }}</td>
                                    <td class="text-center">{{ number_format($item['valor_total']) }}</td>
                                    <td class="text-center" style="font-weight: 700; font-size: 0.7rem">{{ number_format($item['valor_anticipo']) }}</td>
                                    <td class="text-center" style="font-weight: 700; font-size: 0.7rem">{{ number_format($item['saldo']) }}</td>
                                    @if (Auth()->user()->rol == 7 && ( (!$queriedAnticipo) || ( $queriedAnticipo->estado_id == 11 || $queriedAnticipo->estado_id == 12 ) ))
                                        <td class="d-flex justify-content-center" style="padding: 0.2rem;">
                                            <button class="btn avatar border-1 rounded-circle bg-gradient-danger me-2 mb-0" style="width: 25px; height: 25px; font-size: 0.5rem; padding: 0.5rem"
                                                wire:click="deleteItem({{ $key-=1 }})" title="Eliminar item">
                                                <i class="fas fa-xmark text-white"></i>
                                            </button>
                                            <button class="btn avatar border-1 rounded-circle bg-gradient-warning mb-0" style="width: 25px; height: 25px; font-size: 0.5rem; padding: 0.5rem"
                                                wire:click="getItem({{ $key }})" title="Editar item">
                                                <i class="fa-solid fa-pen-to-square text-white"></i>
                                            </button>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                    @if (count($items) > 0)
                        <tfoot class="border-top-0">
                            <tr>
                                <td colspan="@if(Auth()->user()->rol == 7) 10 @else 9 @endif"
                                    class="bg-gradient-info text-white font-weight-bold" style="font-size: 1rem">
                                    TOTAL ANTICIPO: {{ number_format( $total_anticipo ) }}
                                </td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

    <div class="row px-4 mb-4">
        <div class="col-md-12">
            <div class="card card-body">
                {{-- Gestión por parte del productor --}}
                @if (Auth::user()->rol == 7)
                    @if (! $queriedAnticipo || ( $queriedAnticipo->estado_id == 11 || $queriedAnticipo->estado_id == 12 ))
                        @if ($queriedAnticipo)
                            <div class="row">
                                <div class="col-12">
                                    <p class="text-dark mt-3">
                                        @if ($queriedAnticipo->estado_id == 11)
                                            <span class="font-weight-bold">Observaciones de rechazo - Lider de producción</span> <br>
                                            {{ $queriedAnticipo->rechazo_revision_lider }}
                                        @elseif ($queriedAnticipo->estado_id == 12)
                                            <span class="font-weight-bold">Observaciones de rechazo - Gerencia</span> <br>
                                            {{ $queriedAnticipo->rechazo_revision_lider }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="item_presupuesto">Item:</label>
                                    <select name="item_presupuesto" id="item_presupuesto" class="form-control" wire:model="item_presupuesto"
                                        @if ($selected_item) disabled @endif>
                                        <option value="">Seleccione un item</option>
                                        @foreach ($items_presupuesto as $item)
                                            @php
                                                $disable = $items->contains('item_id', $item->id) || $items_anticipos_general->contains('item_id', $item->id);
                                            @endphp

                                            <option value="{{ $item->id }}"
                                                    @if ($disable) style="background-color: #e9ecef" disabled @endif>
                                                {{ $item->descripcion }} - ITEM {{ $item->displayItem() }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('item_presupuesto')
                                    <div id="invalud-item_presupuesto" class="text-invalid">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="cantidad">Cantidad</label>
                                    <input id="cantidad" type="number" class="form-control"
                                           wire:model="cantidad" placeholder="#" x-mask:dynamic="$money($input)">
                                    @error('cantidad')
                                    <div id="invalid-cantidad" class="text-invalid">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="dias">Dias</label>
                                    <input id="dias" type="number" class="form-control"
                                           wire:model="dias" placeholder="Dias" x-mask:dynamic="$money($input)" disabled>
                                    @error('dias')
                                    <div id="invalid-dias" class="text-invalid">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="otros">Otro</label>
                                    <input id="otros" type="number" class="form-control"
                                           wire:model="otros" placeholder="Otro" x-mask:dynamic="$money($input)" disabled>
                                    @error('otros')
                                    <div id="invalid-otros" class="text-invalid">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="valor_unitario">Valor unitario</label>
                                    <input id="valor_unitario" type="text" class="form-control"
                                           wire:model="valor_unitario" placeholder="$" x-mask:dynamic="$money($input)">
                                    @error('valor_unitario')
                                    <div id="invalid-valor_unitario" class="text-invalid">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="valor_total">Valor Total</label>
                                    <input id="valor_total" type="text" class="form-control"
                                           wire:model="valor_total" placeholder="$" x-mask:dynamic="$money($input)" disabled>
                                    @error('valor_total')
                                    <div id="invalid-valor_total" class="text-invalid">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="valor_anticipo">Valor Anticipo</label>
                                    <input id="valor_anticipo" class="form-control" wire:model="valor_anticipo">
                                    @error('valor_anticipo')
                                    <div id="-invalid-valor_anticipo" class="text-invalid">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="saldo">Saldo:</label>
                                    <input id="saldo" type="text" class="form-control" wire:model="saldo" disabled
                                           x-mask:dynamic="$money($input)">
                                    @error('saldo')
                                    <div id="invalid-saldo" class="text-invalid">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 border-bottom" style="border-color: #f3f3f3">
                                @if (is_null($selected_item))
                                    <button wire:click="newItem" x-on:mouseover="event.target.style.transform = 'rotate(360deg)'" x-on:mouseleave="event.target.style.transform = 'rotate(0deg)'"
                                            class="btn avatar border-1 rounded-circle bg-gradient-success" style="box-shadow: none;">
                                        <i class="fas fa-plus text-white"></i>
                                    </button>
                                @else
                                    <button wire:click="actionEdit" x-on:mouseover="event.target.style.transform = 'rotate(360deg)'" x-on:mouseleave="event.target.style.transform = 'rotate(0deg)'"
                                            class="btn avatar border-1 rounded-circle bg-gradient-warning" style="box-shadow: none;">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                @endif

                                @error('items-error')
                                <div class="text-invalid m-0">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            @if (! $queriedAnticipo)
                                <div class="col-12 pl-0 pe-0 pb-2 mb-3 mt-3 border-bottom" style="border-color: #f3f3f3">
                                    @if (! $firma_productor)
                                        <div class="row m-0">
                                            <div class="col-12 col-md-8">
                                                <h6>Firma: </h6>
                                                <canvas id="signature-pad" class="signature-pad" width="500" height="210"></canvas>
                                                <input id="firma_hidden" type="text" wire:model="firma" style="display: none">
                                            </div>
                                            <div class="col-12 col-md-4">
                                                <button id="save" class="btn bg-gradient-primary mt-md-2 mb-0">Guardar firma</button>
                                                <button id="clear" class="btn bg-gradient-secondary mt-md-2 mb-0">Borrar firma</button>
                                            </div>
                                        </div>
                                    @else
                                        <div class="row m-0">
                                            <div class="col-12 col-md-8">
                                                <h6>Firma: </h6>
                                                <a href="{{ asset(str_replace("public", "storage", $firma_productor)) }}" target="_blank">
                                                    <img src="{{ asset(str_replace("public", "storage", $firma_productor)) }}" height="150">
                                                </a>
                                            </div>
                                            <div class="col-12 col-md-4">
                                                <button wire:click="borrarFirmaProductor" class="btn bg-gradient-secondary mt-md-2 mb-0">Borrar firma</button>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endif
                            <div class="col-md-12">
                                @if (! $queriedAnticipo)
                                    <button id="enviar-anticipo" class="btn bg-gradient-info mb-0" @if (! $firma_productor) disabled @endif>Crear Anticipo</button>
                                @elseif ($queriedAnticipo->estado_id == 11 || $queriedAnticipo->estado_id == 12)
                                    <button wire:click="actualizarAnticipoProductor" class="btn bg-gradient-info mt-3 mb-0">Actualizar Anticipo</button>
                                @endif
                            </div>
                        </div>

                        <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
                        <script>
                            let saveButton = document.getElementById('save');
                            let cancelButton = document.getElementById('clear');
                            let enviarBtn = document.getElementById('enviar-anticipo');

                            enviarBtn.addEventListener('click', enviar);

                            signaturePad = new SignaturePad(document.getElementById('signature-pad'), {
                                backgroundColor: 'rgb(255, 255, 255)',
                                penColor: 'rgb(0, 0, 0)'
                            });

                            signaturePad.addEventListener('beginStroke', () => {
                                enviarBtn.disabled = true;
                            });

                            saveButton.addEventListener('click', function (event) {
                                data = signaturePad.toDataURL('image/png');
                                enviarBtn.disabled = false;
                                document.getElementById('firma_hidden').value = data;
                            });

                            cancelButton.addEventListener('click', function (event) {
                                signaturePad.clear();
                                enviarBtn.disabled = true;
                            });

                            function enviar(){
                                let firma = document.getElementById('firma_hidden')?.value ?? null;

                                Livewire.emit('store-anticipo', firma);
                            }
                        </script>
                    @elseif ($queriedAnticipo && $queriedAnticipo->estado_id == 7)
                        <div class="row m-0">
                            <div class="col-12">
                                <h4>Adjuntar evidencias</h4>
                                <p>Debes subir <strong style="font-size: 1.2rem">{{ count($items) }}</strong> evidencia(s) para continuar con el proceso de legalización.</p>
                            </div>

                            <div class="col-12 border-bottom mb-2" style="border-color: #f3f3f3">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th class="bg-gradient-success font-weight-bold text-white">ITEM</th>
                                                <th class="bg-gradient-success font-weight-bold text-white">Fecha</th>
                                                <th class="bg-gradient-success font-weight-bold text-white">Foto</th>
                                                <th class="bg-gradient-success font-weight-bold text-white">Observaciones</th>
                                                <th class="bg-gradient-success font-weight-bold text-white">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($evidencias as $key => $evidencia)
                                                <tr>
                                                    <td class="text-center">{{ $evidencia->itemPresupuesto->descripcion . " - ITEM " . $evidencia->itemPresupuesto->displayItem() }}</td>
                                                    <td class="text-center">{{ $evidencia->fecha_evidencia }}</td>
                                                    <td class="text-center">
                                                        <a href="{{ asset(str_replace("public", "storage", $evidencia->foto_evidencia)) }}" target="_blank">
                                                            <img src="{{ asset(str_replace("public", "storage", $evidencia->foto_evidencia)) }}" height="70">
                                                        </a>
                                                    </td>
                                                    <td class="text-center">{{ $evidencia->observacion_evidencia }}</td>
                                                    <td class="text-center">
                                                        <button wire:click="deleteEvidencia({{ $evidencia->id }})" x-on:mouseover="event.target.style.transform = 'rotate(360deg)'" x-on:mouseleave="event.target.style.transform = 'rotate(0deg)'"
                                                                class="btn avatar border-1 rounded-circle bg-gradient-danger mb-0" title="Eliminar evidencia" style="width: 30px; height: 30px; font-size: 0.6rem; padding: 0.6rem">
                                                            <i class="fas fa-trash-alt text-white"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            @if ($evidencias->count() < $items->count())
                                <div class="col-12">
                                    <div class="row m-0">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="item_evidencia">Item:</label>
                                                <select name="item_evidencia" id="item_evidencia" class="form-control" wire:model="item_evidencia">
                                                    <option value="">Seleccione un item</option>
                                                    @foreach ($items as $item)
                                                        <option value="{{ $item['item_id'] }}">
                                                            {{ $item['desc'] }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('item_evidencia')
                                                    <div id="invalid-item_evidencia" class="text-invalid">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="fecha_evidencia">Fecha evidencia</label>
                                                <input id="fecha_evidencia" type="date" class="form-control form-control"
                                                       wire:model.lazy="fecha_evidencia" placeholder="Fecha evidencia">
                                                @error('fecha_evidencia')
                                                    <div id="invalid-fecha_evidencia" class="text-invalid">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="foto_evidencia">Foto evidencia</label>
                                                <input type="file" class="form-control"
                                                       wire:model.change="foto_evidencia">
                                                @error('foto_evidencia')
                                                    <div id="invalid-foto_evidencia" class="text-invalid">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="observacion_evidencia">Observaciones: </label>
                                                <textarea id="observacion_evidencia" type="text" class="form-control form-control"
                                                          wire:model.lazy="observacion_evidencia" rows="2"></textarea>
                                                @error('observacion_evidencia')
                                                    <div id="invalid-observacion_evidencia" class="text-invalid">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-1 d-flex align-items-center">
                                            <button wire:click="newEvidencia" x-on:mouseover="event.target.style.transform = 'rotate(360deg)'" x-on:mouseleave="event.target.style.transform = 'rotate(0deg)'"
                                                    class="btn avatar border-1 rounded-circle bg-gradient-success mb-0" style="box-shadow: none;">
                                                <i class="fas fa-plus text-white"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @elseif ($evidencias->count() == $items->count())
                                <div class="col-12 mt-3">
                                    <button wire:click="enviarEvidencias" wire:loading.attr="disabled"
                                        class="btn bg-gradient-info mb-0">Enviar evidencias</button>
                                </div>
                                <div class="col-md-12">
                                    <div class="spinner-border text-warning ms-1" role="status" wire:loading>
                                        <span class="sr-only"></span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @elseif ($queriedAnticipo && ( $queriedAnticipo->estado_id == 8 || $queriedAnticipo->estado_id == 9 || $queriedAnticipo->estado_id == 10 ))
                        <div class="row m-0">
                            <div class="col-12">
                                <h4 class="text-center">
                                    @switch ($queriedAnticipo->estado_id)
                                        @case (8)
                                            {{ 'Tu anticipo está siendo validado por el lider de producción.' }}
                                            @break
                                        @case (9)
                                            {{ 'Tu anticipo está siendo validado por la gerencia.' }}
                                            @break
                                        @case (10)
                                            {{ 'Tu anticipo se encuentra en revisión de evidencias por el lider de producción.' }}
                                            @break
                                    @endswitch
                                </h4>
                                <div class="d-flex justify-content-center">
                                    <div class="spinner-grow text-primary" role="status">
                                        <span class="sr-only"></span>
                                    </div>
                                    <div class="spinner-grow text-success" role="status">
                                        <span class="sr-only"></span>
                                    </div>
                                    <div class="spinner-grow text-warning" role="status">
                                        <span class="sr-only"></span>
                                    </div>
                                    <div class="spinner-grow text-info" role="status">
                                        <span class="sr-only"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                {{-- Lider de producción --}}
                @elseif (Auth::user()->rol == 6)
                    {{-- Validación inicial --}}
                    @if ($queriedAnticipo->estado_id == 8)
                        <div class="row m-0">
                            <div class="col-12">
                                <div class="form-group">
                                    @php
                                        $firma = str_replace('public/', '', $queriedAnticipo->firma_productor);
                                    @endphp
                                    <a href="{{ asset("storage/$firma") }}" target="_blank" class="">
                                        <span class="btn-inner--icon"><i class="ni ni-single-copy-04"></i></span>
                                        <span class="btn-inner--text">Firma productor.</span>
                                    </a>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="observaciones_negociacion">Observaciones de aprobaci&oacute;n:</label>
                                    <textarea name="observaciones_revision_lider" id="observaciones_revision_lider" class="form-control" wire:model="observaciones_revision_lider" cols="100" rows="2"></textarea>
                                    @error('observaciones_revision_lider')
                                        <div id="invalid-observaciones_revision_lider" class="text-invalid">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <button wire:click="revisionAnticipoProductor(9)" wire:loading.attr="disabled" class="btn bg-gradient-warning">
                                    Aprobar
                                </button>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="rechazo_revision_lider">Justificaci&oacute;n de rechazo:</label>
                                    <textarea name="rechazo_revision_lider" id="rechazo_revision_lider" class="form-control" wire:model="rechazo_revision_lider" cols="100" rows="2"></textarea>
                                    @error('rechazo_revision_lider')
                                        <div id="invalid-rechazo_revision_lider" class="text-invalid">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <button wire:click="revisionAnticipoProductor(11)" wire:loading.attr="disabled" class="btn bg-gradient-danger">Rechazar</button>
                            </div>

                            <div class="col-md-12">
                                <div class="spinner-border text-warning ms-1" role="status" wire:loading>
                                    <span class="sr-only"></span>
                                </div>
                            </div>
                        </div>
                    {{-- Revisión de evidencias --}}
                    @elseif ($queriedAnticipo->estado_id == 10)
                        <div class="row m-0"></div>
                    @endif

                {{-- Gerencia --}}
                @elseif (Auth::user()->rol == 1 && ( Auth::user()->id == 8 || Auth::user()->id == 10 ))
                    {{-- Validación inicial --}}
                    @if ($queriedAnticipo->estado_id == 9)
                        <div class="row m-0">
                            <div class="col-12 mt-3">
                                <div class="form-group">
                                    @php
                                        $firma = str_replace('public/', '', $queriedAnticipo->firma_productor);
                                    @endphp
                                    <a href="{{ asset("storage/$firma") }}" target="_blank" class="">
                                        <span class="btn-inner--icon"><i class="ni ni-single-copy-04"></i></span>
                                        <span class="btn-inner--text">Firma productor.</span>
                                    </a>
                                </div>
                            </div>

                            <div class="col-12">
                                <p class="text-dark font-weight-bold mt-3">
                                    Observaciones lider de producción: {{ $orden_compra->observaciones_revision_lider }}
                                </p>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="observaciones_revision_gerencia">Observaciones de aprobaci&oacute;n:</label>
                                    <textarea name="observaciones_revision_gerencia" id="observaciones_revision_gerencia" class="form-control" wire:model="observaciones_revision_gerencia" cols="100" rows="2"></textarea>
                                    @error('observaciones_revision_gerencia')
                                        <div id="invalid-observaciones_revision_gerencia" class="text-invalid">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <button wire:click="revisionAnticipoProductor(7)" wire:loading.attr="disabled" class="btn bg-gradient-warning">
                                    Aprobar
                                </button>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="rechazo_revision_gerencia">Justificaci&oacute;n de rechazo:</label>
                                    <textarea name="rechazo_revision_gerencia" id="rechazo_revision_gerencia" class="form-control" wire:model="rechazo_revision_gerencia" cols="100" rows="2"></textarea>
                                    @error('rechazo_revision_gerencia')
                                        <div id="invalid-rechazo_revision_gerencia" class="text-invalid">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <button wire:click="revisionAnticipoProductor(12)" wire:loading.attr="disabled" class="btn bg-gradient-danger">Rechazar</button>
                            </div>

                            <div class="col-md-12">
                                <div class="spinner-border text-warning ms-1" role="status" wire:loading>
                                    <span class="sr-only"></span>
                                </div>
                            </div>
                        </div>
                    @endif

                {{-- Controller --}}
                @elseif (Auth::user()->rol == 2)
                    {{-- Legalización --}}
                    <div class="row m-0"></div>
                @endif
            </div>
        </div>
    </div>
</div>
