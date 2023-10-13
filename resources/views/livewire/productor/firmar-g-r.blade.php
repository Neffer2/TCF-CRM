<div>
    <div class="row">
        <div class="col-md-12 d-flex justify-content-center">
            <canvas id="signature-pad" class="signature-pad" width="400" height="200"></canvas>
        </div>
        <div class="col-md-12 d-flex justify-content-center mt-1">
            <div>
                <button id="save" class="btn bg-gradient-warning">Guardar</button>
                <button id="clear" class="btn bg-gradient-secondary">Borrar</button>
            </div>
        </div>
    </div> 
    
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
    <script>
        var signaturePad = new SignaturePad(document.getElementById('signature-pad'), {
            backgroundColor: 'rgba(255, 255, 255, 0)',
            penColor: 'rgb(0, 0, 0)'
        });

        var saveButton = document.getElementById('save');
        var cancelButton = document.getElementById('clear');

        saveButton.addEventListener('click', function (event) {
        var data = signaturePad.toDataURL('image/png');
            // Send data to server instead...
            console.log(data);
            // window.open(data);
        });

        cancelButton.addEventListener('click', function (event) {
            signaturePad.clear();
        });
    </script>
</div> 
