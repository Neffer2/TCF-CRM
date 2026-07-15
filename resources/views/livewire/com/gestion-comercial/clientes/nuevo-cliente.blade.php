<div class="card">
    <div class="card-header pb-0">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h6 class="mb-0">Nuevo cliente</h6>
                <p class="text-sm text-secondary mb-0">Completa la informaci&oacute;n para enviar la solicitud.</p>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form wire:submit.prevent="storage">
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="form-group mb-0">
                        <label for="codigocliente" class="form-control-label">C&oacute;digo</label>
                        <input id="codigocliente" type="text" wire:model.lazy="codigocliente" class="form-control" placeholder="Autom&aacute;tico al aprobar" readonly disabled>
                        @error('codigocliente')
                        <div id="codigocliente" class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group mb-0">
                        <label for="TipoCliente" class="form-control-label">Tipo de cliente</label>
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
                    <div class="form-group mb-0">
                        <label for="nombreCliente" class="form-control-label">Nombre</label>
                        <input id="nombreCliente" wire:model.lazy="nombreCliente" class="form-control @error('nombreCliente') is-invalid @elseif(strlen($nombreCliente) > 0) is-valid @enderror" value="{{ old('nombreCliente') }}" placeholder="Nombre">
                        @error('nombreCliente')
                        <div id="nombreCliente" class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group mb-0">
                        <label for="apellidoCliente" class="form-control-label">Apellido</label>
                        <input id="apellidoCliente" wire:model.lazy="apellidoCliente" class="form-control @error('apellidoCliente') is-invalid @elseif(strlen($apellidoCliente) > 0) is-valid @enderror" value="{{ old('apellidoCliente') }}" placeholder="Apellido">
                        @error('apellidoCliente')
                        <div id="apellidoCliente" class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>

                <div class="col-12">
                    <hr class="horizontal dark my-1">
                </div>

                <div class="col-md-4">
                    <div class="form-group mb-0">
                        <label for="RazonSocialCliente" class="form-control-label">Raz&oacute;n social</label>
                        <input id="RazonSocialCliente" type="text" wire:model.lazy="RazonSocialCliente" class="form-control @error('RazonSocialCliente') is-invalid @elseif(strlen($RazonSocialCliente) > 0) is-valid @enderror" value="{{ old('RazonSocialCliente') }}" placeholder="Raz&oacute;n social">
                        @error('RazonSocialCliente')
                        <div id="RazonSocialCliente" class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group mb-0">
                        <label for="DireccionCliente" class="form-control-label">Direcci&oacute;n</label>
                        <input id="DireccionCliente" type="text" wire:model.lazy="DireccionCliente" class="form-control @error('DireccionCliente') is-invalid @elseif(strlen($DireccionCliente) > 0) is-valid @enderror" value="{{ old('DireccionCliente') }}" placeholder="Direcci&oacute;n">
                        @error('DireccionCliente')
                        <div id="DireccionCliente" class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group mb-0">
                        <label for="telefonoCliente" class="form-control-label">Tel&eacute;fono</label>
                        <input id="telefonoCliente" type="number" wire:model.lazy="telefonoCliente" class="form-control @error('telefonoCliente') is-invalid @elseif(strlen($telefonoCliente) > 0) is-valid @enderror" value="{{ old('telefonoCliente') }}" placeholder="Tel&eacute;fono">
                        @error('telefonoCliente')
                        <div id="telefonoCliente" class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-0">
                        <label for="emailCliente" class="form-control-label">Correo</label>
                        <input id="emailCliente" type="email" wire:model.lazy="emailCliente" class="form-control @error('emailCliente') is-invalid @elseif(strlen($emailCliente) > 0) is-valid @enderror" value="{{ old('emailCliente') }}" placeholder="Correo electr&oacute;nico">
                        @error('emailCliente')
                        <div id="emailCliente" class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group mb-0">
                        <label for="DescripcionCliente" class="form-control-label">Descripci&oacute;n</label>
                        <textarea id="DescripcionCliente" wire:model.lazy="DescripcionCliente" rows="3" class="form-control @error('DescripcionCliente') is-invalid @elseif(strlen($DescripcionCliente) > 0) is-valid @enderror" placeholder="Agrega una descripci&oacute;n breve del cliente">{{ old('DescripcionCliente') }}</textarea>
                        @error('DescripcionCliente')
                        <div id="DescripcionCliente" class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>
                <div class="col-12 d-flex justify-content-end mt-2">
                    <button class="btn bg-gradient-warning mb-0" type="submit">Enviar solicitud</button>
                </div>
            </div>
        </form>
    </div>

    @if($errors->any())
        <script>
            Swal.fire({
                title: '!Oppss tenemos un problema',
                html: `<ul style="text-align: initial; list-style-type: none; padding-left: 0;">
                @foreach($errors->all() as $error)
                <li>• {{ $error }}</li>
                @endforeach
                </ul>`,
                icon: 'error'
            });
        </script>
    @endif

    @if (session('success'))
        <script>
            Swal.fire(
                'Hecho',
                `{{ session('success') }}`,
                'success'
            );
        </script>
    @endif
</div>
