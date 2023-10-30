<div class="card">
    <div class="row">
        <div class="col-md-2">
            <div class="form-group">
                @php
                    $archivo_orden_helisa = str_replace('public/', '', $storedOrden->archivo_orden_helisa); 
                @endphp 
                <h6 style="margin: 0">Orden de compra:</h6>
                <a href="{{ asset("storage/$archivo_orden_helisa") }}" class="btn btn-icon btn-3 bg-gradient-success mb-0 me-1" target="_blank" style="width: 100%">
                    <span class="btn-inner--icon"><i class="ni ni-single-copy-04"></i></span>
                    <span class="btn-inner--text">Exportar</span>
                </a>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group"> 
                <h6 style="margin: 0">Remisi&oacute;n:</h6>
                <div style="font-size: 9px;">
                    Adjunta la remisi&iacute; que te envi&oacute; el proveedor.
                </div>
                <input id="remision" wire:model="remision" type="file" class="form-control" accept=".pdf,.xls,.xlsx">
                <div wire:loading wire:target="remision" class="py-2">
                    <div class="spinner-border text-warning" role="status">
                        <span class="sr-only">Loading...</span>
                    </div> 
                </div>
                @error('remision')
                    <div id="remision" class="text-invalid">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div> 
        <div class="col-md-12">
            <div class="form-group">
                <h6 style="margin: 0">Good receive:</h6>
                <div style="font-size: 9px;">
                    Indica el c&oacute;digo del Good Receive.
                </div>
                <input id="gr" wire:model.lazy="gr" type="text" class="form-control">
                @error('gr')
                    <div id="gr" class="text-invalid">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <h6 style="margin: 0">Verifica el correo del proveedor:</h6>
                <div style="font-size: 9px;">
                    Se enviar&aacute; un correo al proveedor con la orden de compra y el Good Receive.
                </div>
                <input wire:model="email_prov" type="email" class="form-control" placeholder="alguien@example.com">
                @error('email_prov')
                    <div id="email_prov" class="text-invalid">
                        {{ $message }}
                    </div>
                @enderror 
            </div>
        </div>
        <div class="col-md-4 d-flex flex-column">
            <h6>Firma: </h6>
            <canvas id="signature-pad" class="signature-pad" width="400" height="200"></canvas>
            <input id="firma_hidden" type="text" wire:model="firma" style="display: none">
        </div>
        <div class="col-md-12 d-flex mt-1">
            <div>
                <button id="save" class="btn bg-gradient-primary">Guardar</button>
                <button id="clear" class="btn bg-gradient-secondary">Borrar</button>
            </div>
        </div>
    </div> 
    <div class="row">
        <div class="col-md-12">
            <button id="enviar-btn" class="btn bg-gradient-warning" disabled>Enviar GR</button>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
    <script>
        let saveButton = document.getElementById('save');
        let cancelButton = document.getElementById('clear');
        let enviarBtn = document.getElementById('enviar-btn');
        
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
        });

        function enviar(){
            Livewire.emit('store-signal', firma_hidden.value);
        }
    </script>
</div>
