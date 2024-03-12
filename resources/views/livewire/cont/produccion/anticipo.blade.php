<div class="card">
    <div class="card-header p-0 px-3 mt-3 position-relative z-index-1 col-md-12">
        <div class="row">            
            <div class="col-md-12">
                <h3 class="mb-0">Causaci&oacute;n</h3>
                <p class="text-sm mb-0">Digita el n&uacute;mero de causaci&oacute;n para este anticipo.</p>
            </div> 
        </div> 
    </div>
    <div class="card-body pt-2">
        <div class="row">
            <div class="col-md-6">
                <div class="row"> 
                    <div class="col-md-12">
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
                        <button wire:click="store"wire:loading.attr="disabled" class="btn bg-gradient-warning mb-1" >Marcar como causado</button>
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
                            @php
                                $archivo_cot = str_replace('public/', '', $orden->archivo_cot); 
                            @endphp
                            <a href="{{ asset("storage/$archivo_cot") }}" target="_blank" class="">
                                <span class="btn-inner--icon"><i class="ni ni-single-copy-04"></i></span>
                                <span class="btn-inner--text">Cotizaci&oacute;n.</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
