<div>
    <h5 class="mb-0">Informaci&oacute;n general</h5> 
    <div class="row">
        <div class="col-md-4">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="">Categoria<span class="text-danger">*</span>: @if($categoria) {{ $categorias->find($categoria)->description }} @endif</label>
                        <select size="3" class="form-control" wire:model.lazy="categoria">
                            @foreach ($categorias as $categoria)
                                <option value="{{ $categoria->id }}">{{ $categoria->description }}</option>
                            @endforeach
                        </select>
                        @error('categoria')
                            <div id="categoria" class="text-invalid">
                                {{ $message }}
                            </div>
                        @enderror
                    </div> 
                </div>
                <div class="col-md-12">
                    <div class="col-md-12">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="Nueva categor&iacute;a" wire:model.lazy="nueva_categoria">
                            <button wire:click="newCategoria" class="btn btn-primary mb-0" type="button" id="button-addon2">+</button>
                        </div>
                    </div>
                </div>
            </div>    
        </div> 
        <div class="col-md-8">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="tercero">Tercero<span class="text-danger">*</span>: </label>
                        <input id="tercero" type="text" class="form-control" placeholder="Nombre proveedor" wire:model.lazy="tercero"> 
                        @error('tercero')
                            <div id="tercero" class="text-invalid"> 
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="">Tipo<span class="text-danger">*</span>: </label>
                        <select class="form-control" wire:model.lazy="tipo">
                            <option value="">Seleccionar</option>
                            <option value="natural">Natural</option>
                            <option value="juridica">Juridica</option>
                        </select>
                        @error('tipo')
                            <div id="tipo" class="text-invalid">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="">Tipo de documento<span class="text-danger">*</span>: </label>
                        <select class="form-control" wire:model.lazy="tipo_documento">
                            <option value="">Seleccionar</option>
                            <option value="CEDULA">CEDULA</option>
                            <option value="NIT">NIT</option>
                            <option value="RUT">RUT</option>
                        </select>
                        @error('tipo_documento')
                            <div id="tipo_documento" class="text-invalid">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Documento<span class="text-danger">*</span>: </label>
                        <input type="text" class="form-control" wire:model.lazy="documento">
                        @error('documento')
                            <div id="documento" class="text-invalid">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">DV<span class="text-danger">*</span>: </label>
                        <input type="text" class="form-control" wire:model.lazy="dv">
                        @error('dv')
                            <div id="dv" class="text-invalid">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Servicio<span class="text-danger">*</span>: </label>
                        <textarea cols="30" rows="1" class="form-control" wire:model.lazy="servicio" placeholder="Servicio que presta"></textarea>
                        @error('servicio')
                            <div id="servicio" class="text-invalid">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Anticipo<span class="text-danger">*</span>: </label>
                        <input type="text" class="form-control" wire:model.lazy="anticipo" placeholder="%">
                        @error('anticipo')
                            <div id="anticipo" class="text-invalid">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    <h5 class="mb-0">Informaci&oacute;n de contacto</h5> 
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label for="">Contacto<span class="text-danger">*</span>: </label>
                <input type="text" class="form-control" wire:model.lazy="contacto">
                @error('contacto')
                    <div id="contacto" class="text-invalid">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="">Celular<span class="text-danger">*</span>: </label>
                <input type="text" class="form-control" wire:model.lazy="celular">
                @error('celular')
                    <div id="celular" class="text-invalid">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="">Fijo: </label>
                <input type="text" class="form-control" wire:model.lazy="fijo">
                @error('fijo')
                    <div id="fijo" class="text-invalid">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="">Correo<span class="text-danger">*</span>: </label>
                <input type="text" class="form-control" placeholder="alguien@example.com" wire:model.lazy="correo">
                @error('correo')
                    <div id="correo" class="text-invalid">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="">Web: </label>
                <input type="text" class="form-control" wire:model.lazy="web">
                @error('web')
                    <div id="web" class="text-invalid">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="">Direcci&oacute;n: </label>
                <input type="text" class="form-control" wire:model.lazy="direccion">
                @error('direccion')
                    <div id="direccion" class="text-invalid">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div> 
        <div class="col-md-3"> 
            <div class="form-group">
                <label for="">Departamento<span class="text-danger">*</span>: </label>
                <select class="form-control" wire:model.lazy="departamento">
                    <option value="">Seleccionar</option>
                    @foreach ($departamentos as $departamento)
                        <option value="{{ $departamento }}">{{ $departamento }}</option>
                    @endforeach
                </select>
                @error('departamento')
                    <div id="departamento" class="text-invalid">
                        {{ $message }}
                    </div>
                @enderror 
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="">Ciudad<span class="text-danger">*</span>: </label>
                <select class="form-control" wire:model.lazy="ciudad">
                    <option value="">Seleccionar</option>
                    @foreach ($this->ciudades as $ciudad)
                        <option value="{{ $ciudad }}">{{ $ciudad }}</option>                        
                    @endforeach
                </select>
                @error('ciudad')
                    <div id="ciudad" class="text-invalid">
                        {{ $message }}
                    </div>
                @enderror 
            </div>
        </div>
    </div>

    <h5 class="mb-0">Mas informaci&oacute;n</h5> 
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label for="">Plazo: </label>
                <textarea class="form-control" rows="1" wire:model.lazy="plazo"></textarea>
                @error('plazo')
                    <div id="plazo" class="text-invalid">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="">Observaciones: </label>
                <textarea class="form-control" rows="1" wire:model.lazy="observaciones"></textarea>
                @error('observaciones')
                    <div id="observaciones" class="text-invalid">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="">Estado<span class="text-danger">*</span>: </label>
                <select class="form-control" wire:model="estado">
                    <option value="">Seleccionar</option>
                    <option value="CONFIRMADO">CONFIRMADO</option>
                    <option value="CONFIRMADO - COMUNICADO">CONFIRMADO - COMUNICADO</option>
                    <option value="NO APLICA">NO APLICA</option>
                    <option value="SE CANCELO ACUERDO">SE CANCELO ACUERDO</option>
                </select>
                @error('estado')
                    <div id="estado" class="text-invalid">
                        {{ $message }}
                    </div>
                @enderror 
            </div> 
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <button class="btn bg-gradient-warning" data-bs-dismiss="modal" wire:click="store">
                @if ($this->proveedor_id) Guardar cambios @else Crear proveedor @endif
            </button>
            @if ($this->proveedor_id)
                <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Cerrar</button>
            @endif
        </div>
    </div>
    <div class="alerts"> 
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
</div>
