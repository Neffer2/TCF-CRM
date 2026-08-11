<div class="card">
    <div class="card-header pb-0">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h6 class="mb-0">Nuevo Cliente Jurídico/Comercial</h6>
                <p class="text-sm text-secondary mb-0">Completa la información corporativa para enviar la solicitud a validación.</p>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form wire:submit.prevent="storage">
            <div class="row g-3">

                <!-- 1. Nombre Comercial -->
                <div class="col-md-4">
                    <div class="form-group mb-0">
                        <label for="nombre" class="form-control-label">Nombre Comercial</label>
                        <input id="nombre" type="text" wire:model.lazy="nombre" class="form-control @error('nombre') is-invalid @elseif(strlen($nombre) > 0) is-valid @enderror" placeholder="Nombre de marca / Comercial">
                        @error('nombre')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>

                <!-- 2. Razón Social -->
                <div class="col-md-4">
                    <div class="form-group mb-0">
                        <label for="razon_social" class="form-control-label">Razón Social</label>
                        <input id="razon_social" type="text" wire:model.lazy="razon_social" class="form-control @error('razon_social') is-invalid @elseif(strlen($razon_social) > 0) is-valid @enderror" placeholder="Razón social legal">
                        @error('razon_social')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>

                <!-- 3. NIT -->
                <div class="col-md-4">
                    <div class="form-group mb-0">
                        <label for="nit" class="form-control-label">NIT / Identificación</label>
                        <input id="nit" type="text" wire:model.lazy="nit" class="form-control @error('nit') is-invalid @elseif(strlen($nit) > 0) is-valid @enderror" placeholder="Ej: 900123456-1">
                        @error('nit')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>

                <div class="col-12">
                    <hr class="horizontal dark my-1">
                </div>

                <!-- 4. Dirección -->
                <div class="col-md-6">
                    <div class="form-group mb-0">
                        <label for="direccion" class="form-control-label">Dirección</label>
                        <input id="direccion" type="text" wire:model.lazy="direccion" class="form-control @error('direccion') is-invalid @elseif(strlen($direccion) > 0) is-valid @enderror" placeholder="Dirección de correspondencia">
                        @error('direccion')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>

                <!-- 5. Página Web -->
                <div class="col-md-6">
                    <div class="form-group mb-0">
                        <label for="pagina_web" class="form-control-label">Página Web</label>
                        <input id="pagina_web" type="url" wire:model.lazy="pagina_web" class="form-control @error('pagina_web') is-invalid @elseif(strlen($pagina_web) > 0) is-valid @enderror" placeholder="https://www.ejemplo.com">
                        @error('pagina_web')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>

                <!-- 6. Teléfono Fijo -->
                <div class="col-md-3">
                    <div class="form-group mb-0">
                        <label for="telefono" class="form-control-label">Teléfono Fijo</label>
                        <input id="telefono" type="text" wire:model.lazy="telefono" class="form-control @error('telefono') is-invalid @elseif(strlen($telefono) > 0) is-valid @enderror" placeholder="Línea fija (Opcional)">
                        @error('telefono')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>

                <!-- 7. Número Teléfono (Celular) -->
                <div class="col-md-3">
                    <div class="form-group mb-0">
                        <label for="numero_telefono" class="form-control-label">Número Celular</label>
                        <input id="numero_telefono" type="text" wire:model.lazy="numero_telefono" class="form-control @error('numero_telefono') is-invalid @elseif(strlen($numero_telefono) > 0) is-valid @enderror" placeholder="Ej: 3001234567">
                        @error('numero_telefono')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>

                <!-- 8. Cargo -->
                <div class="col-md-6">
                    <div class="form-group mb-0">
                        <label for="cargo" class="form-control-label">Cargo del Contacto</label>
                        <input id="cargo" type="text" wire:model.lazy="cargo" class="form-control @error('cargo') is-invalid @elseif(strlen($cargo) > 0) is-valid @enderror" placeholder="Ej: Director de Compras / Representante">
                        @error('cargo')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>

                <div class="col-12">
                    <hr class="horizontal dark my-1">
                </div>

                <!-- 9. Correo de Contacto -->
                <div class="col-md-6">
                    <div class="form-group mb-0">
                        <label for="correo" class="form-control-label">Correo de Contacto</label>
                        <input id="correo" type="email" wire:model.lazy="correo" class="form-control @error('correo') is-invalid @elseif(strlen($correo) > 0) is-valid @enderror" placeholder="correo@empresa.com">
                        @error('correo')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>
                <!-- 10. Correo de Recepción de Facturas -->
                <div class="col-md-6">
                    <div class="form-group mb-0">
                        <label for="correo_recpcion_facturas" class="form-control-label">Correo Recepción Facturas</label>
                        <input id="correo_recpcion_facturas" type="email" wire:model.lazy="correo_recpcion_facturas" class="form-control @error('correo_recpcion_facturas') is-invalid @elseif(strlen($correo_recpcion_facturas) > 0) is-valid @enderror" placeholder="facturacion@empresa.com">
                        @error('correo_recpcion_facturas')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>

                <!-- 11. Adjuntar Archivos -->
                <div class="col-md-12">
                    <div class="form-group mb-0">
                        <label for="adjuntar_archivos" class="form-control-label">Adjuntar Documentación (RUT)</label>
                        <input id="adjuntar_archivos" type="file" wire:model="adjuntar_archivos" class="form-control @error('adjuntar_archivos') is-invalid @enderror">
                        @error('adjuntar_archivos')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group mb-0">
                        <label for="adjuntar_archivos" class="form-control-label">Adjuntar Documentación (Cámara de Comercio)</label>
                        <input id="adjuntar_archivos" type="file" wire:model="adjuntar_archivos" class="form-control @error('adjuntar_archivos') is-invalid @enderror">
                        @error('adjuntar_archivos')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>
                <!-- Botón de Envío -->
                <div class="col-12 d-flex justify-content-end mt-3">
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
