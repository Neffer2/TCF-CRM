<div>
    <form wire:submit.prevent="store"> 
        <div class="row"> 
            <div class="col-md-12">
                <div class="form-group mb-1">
                    <label for="contacto">Nombre Proyecto:</label>
                    <input type="text" id="nom_proyecto" class="form-control @error('nom_proyecto') is-invalid @elseif(strlen($nom_proyecto) > 0) is-valid @enderror" value="{{ old('nom_proyecto') }}" wire:model.lazy="nom_proyecto" required>
                    @error('nom_proyecto')
                        <div id="nom_proyecto" class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group mb-1">
                    <label for="presupuesto">Presupuesto:</label>
                    <input type="number" id="presupuesto" class="form-control @error('presupuesto') is-invalid @elseif(strlen($presupuesto) > 0) is-valid @enderror" value="{{ old('presupuesto') }}" wire:model.lazy="presupuesto" required>
                    @error('presupuesto')
                        <div id="presupuesto" class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div> 
            </div>
            <div class="col-md-12">
                <label for="fecha">Fecha estimada de respuesta:</label>
                <input type="date" id="fecha" class="form-control @error('fecha') is-invalid @elseif(strlen($fecha) > 0) is-valid @enderror" value="{{ old('fecha') }}" wire:model.lazy="fecha" required>
                @error('fecha')
                    <div id="fecha" class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>
            <div class="col-md-12">
                <label for="cotizacionFile">Archivo cotizaci&oacute;n:</label>
                <input type="file" id="cotizacionFile" class="form-control @error('cotizacionFile') is-invalid @elseif(strlen($cotizacionFile) > 0) is-valid @enderror" value="{{ old('cotizacionFile') }}" wire:model.lazy="cotizacionFile" required>
                @error('cotizacionFile')
                    <div id="cotizacionFile" class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>
            <div class="col-md-12">
                <div class="form-group mt-4">
                    <div id="passwordHelpBlock mt-0" class="form-text">
                        Al guardar los cambios, confirmas que &eacute;ste prospecto se convierte en <b>cotizacion.</b>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <button class="btn bg-gradient-warning">Guardar cambios</button>
            </div>
        </div>
    </form>
</div>
