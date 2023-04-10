<div>
    <form wire:submit.prevent="store"> 
        <div class="row"> 
            <div class="col-md-12">
                <div class="form-group mb-1">
                    <label for="contacto">Contacto:</label>
                    <select id="contacto" class="form-control @error('contacto') is-invalid @elseif(strlen($contacto) > 0) is-valid @enderror" value="{{ old('contacto') }}" wire:model.lazy="contacto" required>
                        <option value="">Seleccionar</option>
                        @foreach ($tiposContacto as $tipoContacto)
                            <option value="{{ $tipoContacto }}"> {{ $tipoContacto }} </option>
                        @endforeach
                    </select>
                    @error('contacto')
                        <div id="contacto" class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div> 
            </div>
            <div class="col-md-12">
                <div class="form-group mb-1">
                    <label for="contacto">Descripci&oacute;n:</label>
                    <textarea cols="30" rows="10" class="form-control @error('descOportunidad') is-invalid @elseif(strlen($descOportunidad) > 0) is-valid @enderror" value="{{ old('descOportunidad') }}" placeholder="Describe los detalles de tu reunión." wire:model.lazy="descOportunidad" required></textarea>
                    @error('descOportunidad')
                        <div id="descOportunidad" class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group mb-2">
                    <div id="passwordHelpBlock mt-0" class="form-text">
                        Al guardar los cambios, confirmas que &eacute;ste prospecto se convierte en <b>oportunidad.</b>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <button class="btn bg-gradient-warning">Guardar cambios</button>
            </div>
        </div>
    </form>
</div>
