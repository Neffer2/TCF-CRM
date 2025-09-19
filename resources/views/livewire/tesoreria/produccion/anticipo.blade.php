<div class="card">
    <div class="card-header p-0 px-3 mt-3 position-relative z-index-1 col-md-12">
        <div class="row">
            <div class="col-md-12">
                <h3 class="mb-0">Pago Anticipo {{ $orden->proveedor->tercero }}</h3>
                <p class="text-sm mb-0">Adjunta el comprobante de pago para el anticipo.</p>
            </div>
        </div>
    </div>
    <div class="card-body pt-2">
        <div class="row">
            <div class="col-md-6">
                <div class="row">
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

                    <div class="col-md-12">
                        <div class="form-group">
                            <h6 style="margin: 0">N&uacute;mero de causaci&oacute;n:</h6>
                            <input value="{{ $anticipo->cod_causal }}" class="form-control" disabled>
                        </div> 
                        @if ($orden->observacion_causal)
                            <div class="form-group">
                                <h6 style="margin: 0">Observaciones contabilidad:</h6>
                                <textarea class="form-control" disabled>{{ $anticipo->observacion_causal }}</textarea>
                            </div>
                        @endif
                        <div class="form-group">
                            <h6 style="margin: 0">Comprobante:</h6>
                            <div style="font-size: 9px;">
                                Adjunta el comprobante de pago para el proveedor {{ $orden->proveedor->tercero }}.
                            </div>
                            <input id="comprobante" wire:model="comprobante" type="file" class="form-control" accept=".pdf,.xls,.xlsx">
                            @error('comprobante')
                                <div id="comprobante" class="text-invalid">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <h6 style="margin: 0">Observaciones (opcional):</h6>
                            <div style="font-size: 9px;">
                                Notif&iacute;ca si existe alguna novedad en el pago.
                            </div>
                            <textarea id="observacion_anticipo" wire:model="observacion_anticipo" class="form-control"></textarea>
                            @error('observacion_anticipo')
                                <div id="observacion_anticipo" class="text-invalid">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-12">
                        <button wire:click="store" wire:loading.attr="disabled" class="btn bg-gradient-warning mb-1" >Marcar como pagado</button>
                        <div class="spinner-border text-warning ms-1" role="status" wire:loading>
                            <span class="sr-only">Loading...</span>
                        </div>
                        @error('error')
                            <div id="error" class="text-invalid">
                                {{ $message }}
                            </div>
                        @enderror
                        <div style="font-size: 9px;">
                            Se enviar&aacute; un correo al proveedor y productor del proyecto con el comprobante y las observaciones del pago del anticipo.
                        </div>
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
                            <br>
                            @if ($orden->tipo_oc == 2)
                                @if ($orden->naturalInfo->tercero->art383)
                                    <a href="{{ asset(str_replace('public', 'storage', $orden->naturalInfo->tercero->art383)) }}" target="_blank">
                                        <span class="btn-inner--icon"><i class="ni ni-single-copy-04"></i></span>
                                        <span class="btn-inner--text">Certificación Art. 383.</span>
                                    </a>
                                    <br>
                                @endif
                                @if ($orden->naturalInfo->tercero->planilla_aportes)
                                    <a href="{{ asset(str_replace('public', 'storage', $orden->naturalInfo->tercero->planilla_aportes)) }}" target="_blank">
                                        <span class="btn-inner--icon"><i class="ni ni-single-copy-04"></i></span>
                                        <span class="btn-inner--text">Planilla de Aportes.</span>
                                    </a>
                                    <br>
                                @endif
                                    <a href="{{ asset(str_replace('public', 'storage', $orden->naturalInfo->tercero->cert_bancaria)) }}" target="_blank">
                                        <span class="btn-inner--icon"><i class="ni ni-single-copy-04"></i></span>
                                        <span class="btn-inner--text">Certificación bancaria.</span>
                                    </a>
                                <p>
                                    <br>
                                    <strong>Banco:</strong> {{ $orden->naturalInfo->tercero->banco }}<br>
                                    <strong>Tipo de cuenta:</strong> {{ $orden->naturalInfo->tercero->tipo_cuenta }}<br>
                                    <strong>N&uacute;mero de cuenta:</strong> {{ $orden->naturalInfo->tercero->num_cuenta }}<br>
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
