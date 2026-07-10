<div>
    <form wire:submit.prevent="storage">
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="codigocliente">C&oacute;digo: </label>
                    <input id="codigocliente" type="text" wire:model.lazy="codigocliente" class="form-control" placeholder="Automático al aprobar" readonly disabled>
                    @error('codigocliente')
                    <div id="codigocliente" class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="TipoCliente">Tipo de Cliente: </label>
                    <select id="TipoCliente" wire:model.lazy="TipoCliente"
                            class="form-control @error('TipoCliente') is-invalid @elseif(strlen($TipoCliente) > 0) is-valid @enderror">
                        <option value="">Seleccionar</option>
                        <option value="NATURAL">NATURAL</option>
                        <option value="JUR&Iacute;DICO">JUR&Iacute;DICO</option>
                    </select>
                    @error('TipoCliente')
                    <div id="TipoCliente" class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="nombreCliente">Nombre: </label>
                    <input id="nombreCliente" wire:model.lazy="nombreCliente" class="form-control @error('nombreCliente') is-invalid @elseif(strlen($nombreCliente) > 0) is-valid @enderror" value="{{ old('nombreCliente') }}" placeholder="Nombre">
                    @error('nombreCliente')
                    <div id="nombreCliente" class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="apellidoCliente">Apellido: </label>
                    <input id="apellidoCliente" wire:model.lazy="apellidoCliente" class="form-control @error('apellidoCliente') is-invalid @elseif(strlen($apellidoCliente) > 0) is-valid @enderror" value="{{ old('apellidoCliente') }}" placeholder="Apellido">
                    @error('apellidoCliente')
                    <div id="apellidoCliente" class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="RazonSocialCliente">Raz&oacute;n Social: </label>
                    <input id="RazonSocialCliente" type="text" wire:model.lazy="RazonSocialCliente" class="form-control @error('RazonSocialCliente') is-invalid @elseif(strlen($RazonSocialCliente) > 0) is-valid @enderror" value="{{ old('RazonSocialCliente') }}" placeholder="Raz&oacute;n Social">
                    @error('RazonSocialCliente')
                    <div id="RazonSocialCliente" class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="DireccionCliente">Direcci&oacute;n: </label>
                    <input id="DireccionCliente" type="text" wire:model.lazy="DireccionCliente" class="form-control @error('DireccionCliente') is-invalid @elseif(strlen($DireccionCliente) > 0) is-valid @enderror" value="{{ old('DireccionCliente') }}" placeholder="Direcci&oacute;n">
                    @error('DireccionCliente')
                    <div id="DireccionCliente" class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="telefonoCliente">Tel&eacute;fono: </label>
                    <input id="telefonoCliente" type="number" wire:model.lazy="telefonoCliente" class="form-control @error('telefonoCliente') is-invalid @elseif(strlen($telefonoCliente) > 0) is-valid @enderror" value="{{ old('telefonoCliente') }}" placeholder="Tel&eacute;fono">
                    @error('telefonoCliente')
                    <div id="telefonoCliente" class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="emailCliente">Correo: </label>
                    <input id="emailCliente" type="email" wire:model.lazy="emailCliente" class="form-control @error('emailCliente') is-invalid @elseif(strlen($emailCliente) > 0) is-valid @enderror" value="{{ old('emailCliente') }}" placeholder="Correo">
                    @error('emailCliente')
                    <div id="emailCliente" class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="DescripcionCliente">Descripci&oacute;n: </label>
                    <textarea id="DescripcionCliente" wire:model.lazy="DescripcionCliente" rows="3" class="form-control @error('DescripcionCliente') is-invalid @elseif(strlen($DescripcionCliente) > 0) is-valid @enderror" placeholder="Descripci&oacute;n">{{ old('DescripcionCliente') }}</textarea>
                    @error('DescripcionCliente')
                    <div id="DescripcionCliente" class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
            </div>
            <div class="col-md-5">
                <button class="btn bg-gradient-warning mb-0">Enviar Solicitud</button>
            </div>
        </div>
    </form>
</div>
