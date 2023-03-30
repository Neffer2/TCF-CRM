<div>
    <form wire:submit.prevent="storePerdido"> 
        <div class="row">
            <div class="col-md-12">
                <div class="form-group mt-0 mb-2">
                    <div id="passwordHelpBlock mt-0" class="form-text">
                        Al guardar los cambios, confirmas que &eacute;ste prospecto se convierte en <b>perdido.</b>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group mt-0 mb-2">
                    <label for="causa">Causa:</label>
                    <textarea id="causa" cols="30" rows="5" class="form-control @error('causa') is-invalid @elseif(strlen($causa) > 0) is-valid @enderror" value="{{ old('causa') }}" placeholder="Describe detalles del porqué se perdió éste prospecto" wire:model.lazy="causa" required></textarea>
                    @error('causa')
                        <div id="causa" class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <button class="btn bg-gradient-danger">Convertir en perdido</button>
            </div>
        </div>
    </form>
</div>
