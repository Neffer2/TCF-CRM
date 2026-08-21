<div class="card">
    <div class="card-header p-0 px-3 mt-3 position-relative z-index-1 col-md-12">
        <div class="row">
            <div class="col-md-12">
                <h3 class="mb-0">Causaci&oacute;n</h3>
                <p class="text-sm mb-0">Digita el n&uacute;mero de causaci&oacute;n para este Anticipo.</p>
            </div>
        </div>
    </div>
    <div class="card-body pt-2">
        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <h6 style="margin: 0">Valor Orden de Compra:</h6>
                                    <input type="text" class="form-control" value="{{ number_format($orden->ordenItems->sum('vtotal_oc'), 2) }}" disabled>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <h6 style="margin: 0">% Anticipo:</h6>
                                    <input type="text" class="form-control" value="{{ number_format($anticipo->porcentaje_anticipo, 2) }}" disabled>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <h6 style="margin: 0">Valor Anticipo:</h6>
                                    <input type="text" class="form-control" value="{{ number_format($anticipo->total_anticipo, 2) }}" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <h6 style="margin: 0">N&uacute;mero de causaci&oacute;n:</h6>
                            <input id="causa_cod" wire:model="causa_cod" type="text" class="form-control">
                            @error('causa_cod')
                                <div id="causa_cod" class="text-invalid">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <h6 style="margin: 0">Observaciones (opcional):</h6>
                            <div style="font-size: 9px;">
                                Notif&iacute;ca si existe alguna novedad en el anticipo.
                            </div>
                            <textarea id="observacion_causacion" wire:model="observacion_causacion" class="form-control"></textarea>
                            @error('observacion_causacion')
                                <div id="observacion_causacion" class="text-invalid">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-12">
                        <button wire:loading.attr="disabled" class="btn bg-gradient-success" data-bs-toggle="modal" data-bs-target="#causado">Marcar como causado</button>
                        <button type="button" class="btn bg-gradient-danger" data-bs-toggle="modal" data-bs-target="#deleteModal"> Rechazar </button>

                        <!-- Modal -->
                        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="dangerModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="successModalLabel">Rechazar oden de compra</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        ¿Estas seguro de rechazar esta orden?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn bg-gradient-danger" data-bs-dismiss="modal" wire:click="rechazar">Rechazar</button>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal -->
                        <div class="modal fade" id="causado" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="successModalLabel">Marcar como causado</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        ¿Estas seguro de marcar como causada esta orden?
                                    </div>
                                    <div class="modal-footer">
                                        <button wire:click="store" wire:loading.attr="disabled"  data-bs-dismiss="modal" class="btn bg-gradient-success">Marcar como causado</button>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="spinner-border text-warning ms-1" role="status" wire:loading>
                            <span class="sr-only">Loading...</span>
                        </div>
                        @error('error')
                            <div id="error" class="text-invalid">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            @php
                                $archivo_orden_helisa = str_replace('public/', '', $orden->archivo_orden_helisa);
                            @endphp
                            <a href="{{ asset("storage/$archivo_orden_helisa") }}" target="_blank" class="">
                                <span class="btn-inner--icon"><i class="ni ni-single-copy-04"></i></span>
                                <span class="btn-inner--text">Orden de compra.</span>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            @if ($orden->tipo_oc == 1)
                                @php $archivo_cot = str_replace('public/', '', $orden->archivo_cot); @endphp
                                <a href="{{ asset("storage/$archivo_cot") }}" target="_blank" class="">
                                    <span class="btn-inner--icon"><i class="ni ni-single-copy-04"></i></span>
                                    <span class="btn-inner--text">Cotizaci&oacute;n.</span>
                                </a>                                
                            @endif
                        </div>
                    </div> 
                    <div class="col-md-12">
                        <div class="form-group">
                            <h6>Observaciones de negociaci&oacute;n:</h6>
                            <textarea name="observaciones_negociacion" class="form-control" disabled>{{ $orden->observaciones_negociacion }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
            @if($orden->tipo_oc == 2)
                @if ($orden->naturalInfo->contrato)
                    <div class="card card-body px-3 py-0 mt-3">
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
                                                {{ $orden->naturalInfo->tercero->nombre }} {{ $orden->naturalInfo->tercero->apellido }}
                                            </p>
                                        </td>
                                        <td>
                                            <p class="text-xs text-secondary mb-0">
                                                {{ $orden->naturalInfo->tercero->cedula }}
                                            </p>
                                        </td>
                                        <td>
                                            <p class="text-xs text-secondary mb-0">
                                                {{ $orden->naturalInfo->tercero->correo }}
                                            </p>
                                        </td>
                                        <td>
                                            <p class="text-xs text-secondary mb-0">
                                                {{ $orden->naturalInfo->tercero->telefono }}
                                            </p>
                                        </td>
                                        <td>
                                            <p class="text-xs text-secondary mb-0">
                                                <a href="{{ asset(str_replace('public', 'storage', $orden->naturalInfo->tercero->cert_bancaria)) }}" target="_blank">Certificaci&oacute;n Bancaria</a><br>
                                                <a href="{{ asset(str_replace('public', 'storage', $orden->naturalInfo->tercero->rut)) }}" target="_blank">RUT</a><br>
                                                <a href="{{ asset(str_replace('public', 'storage', $orden->naturalInfo->contrato)) }}" target="_blank">Contrato</a>
                                            </p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
