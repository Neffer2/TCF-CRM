<div>
    <div class="row">
        <div class="col-md-12 d-flex justify-content-center">
            @php
                $archivo_cot_helisa = str_replace('public/', '', $storedOrden->archivo_cot_helisa); 
            @endphp
            <object data="{{ asset("storage/$archivo_cot_helisa") }}" type="application/pdf" width="500" height="500">
                <p>Su navegador no admite la visualización de PDF.</p>
            </object>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label for="remision">Remisi&oacute;n:</label>
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
        <div class="col-md-12 d-flex justify-content-center">
            <label for="signature-pad">Firma: </label>
        </div>
        <div class="col-md-12 d-flex justify-content-center">
            <canvas id="signature-pad" class="signature-pad" width="400" height="200"></canvas>
            <input id="firma_hidden" type="text" wire:model="firma" style="display: none">
        </div>
        <div class="col-md-12 d-flex justify-content-center mt-1">
            <div>
                <button id="save" class="btn bg-gradient-primary">Guardar</button>
                <button id="clear" class="btn bg-gradient-secondary">Borrar</button>
            </div>
        </div>
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
