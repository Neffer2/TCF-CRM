<div>
    <form wire:submit.prevent="updateContacto">  
        <div class="row">
            <div class="col-md-12">
                <h5><b>EDITAR CONTACTO</b></h5>  
            </div>        
            <div class="col-md-12">
                <div class="form-group">
                    <label for="id_contacto">Tipo de contacto:</label>
                    <select id="id_contacto" wire:model.lazy="id_contacto" class="form-control @error('id_contacto') is-invalid @elseif(strlen($id_contacto) > 0) is-valid @enderror" value="{{ old('id_contacto') }}">
                        <option value="">Seleccionar</option>
                        @foreach ($contactos as $contacto)
                            <option value="{{ $contacto->id }}">{{ $contacto->nombre }} {{ $contacto->apellido }} - {{ $contacto->empresa }}</option>
                        @endforeach
                    </select>
                    @error('id_contacto')
                        <div id="id_contacto" class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="col-md-12 d-flex justify-content-end">
                <button class="btn bg-gradient-warning">Guardar</button>
            </div>
        </div>
    </form>
    <hr class="horizontal dark my-3">
    <form wire:submit.prevent="updateOportunidad">  
        <div class="row">
            <div class="col-md-12">
                <h5><b>EDITAR PROPUESTA</b></h5>  
            </div>        
            <div class="col-md-12">
                <div class="form-group"> 
                    <label for="tipo_contacto">Tipo de contacto:</label>
                    <select id="tipo_contacto" wire:model.lazy="tipo_contacto" class="form-control @error('tipo_contacto') is-invalid @elseif(strlen($tipo_contacto) > 0) is-valid @enderror" value="{{ old('tipo_contacto') }}">
                        <option value="">Seleccionar</option>
                        @foreach ($tiposContacto as $tipoContacto)
                            <option value="{{ $tipoContacto }}"> {{ $tipoContacto }} </option>
                        @endforeach
                    </select>
                    @error('tipo_contacto')
                        <div id="tipo_contacto" class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="desc_contacto">Descripci&oacute;n contacto:</label>
                    <textarea id="desc_contacto" wire:model.lazy="desc_contacto" class="form-control @error('desc_contacto') is-invalid @elseif(strlen($desc_contacto) > 0) is-valid @enderror" value="{{ old('desc_contacto') }}">></textarea>
                    @error('desc_contacto')
                        <div id="desc_contacto" class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="col-md-12 d-flex justify-content-end">
                <button class="btn bg-gradient-warning">Guardar</button>
            </div>        
        </div>
    </form>
    <hr class="horizontal dark my-3">
    <form wire:submit.prevent="updateCotizacion">
        <div class="row">
            <div class="col-md-12">
                <h5><b>EDITAR COTIZACI&Oacute;N</b></h5>  
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="nom_proyecto">Nombre proyecto:</label>
                    <input id="nom_proyecto" wire:model.lazy="nom_proyecto" class="form-control @error('nom_proyecto') is-invalid @elseif(strlen($nom_proyecto) > 0) is-valid @enderror" value="{{ old('nom_proyecto') }}">
                    @error('nom_proyecto')
                        <div id="nom_proyecto" class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>        
            <div class="col-md-6">
                <div class="form-group">
                    <label for="presupuesto">Presupuesto:</label>
                    <input id="presupuesto" wire:model.lazy="presupuesto" class="form-control @error('presupuesto') is-invalid @elseif(strlen($presupuesto) > 0) is-valid @enderror" value="{{ old('presupuesto') }}">
                    @error('presupuesto')
                        <div id="presupuesto" class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="fecha">Fecha estimada:</label>
                    <input type="date" id="fecha" wire:model.lazy="fecha" class="form-control @error('fecha') is-invalid @elseif(strlen($fecha) > 0) is-valid @enderror" value="{{ old('fecha') }}">
                    @error('fecha')
                        <div id="fecha" class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="porcentaje">Porcentaje:</label>
                    <select id="porcentaje" wire:model.lazy="porcentaje" class="form-control @error('porcentaje') is-invalid @elseif(strlen($porcentaje) > 0) is-valid @enderror" value="{{ old('porcentaje') }}">
                        @foreach ($porcentajes as $porcentaje_)
                            <option value="{{ $porcentaje_ }}">{{ $porcentaje_ }}%</option>
                        @endforeach
                    </select>
                    @error('porcentaje')
                        <div id="porcentaje" class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            @if ($porcentaje == 50)
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="com_2">Comercial 2:</label>
                        <select type="text" id="com_2" wire:model.lazy="com_2" class="form-control @error('com_2') is-invalid @elseif(strlen($com_2) > 0) is-valid @enderror" value="{{ old('com_2') }}">
                            <option value="">Seleccionar</option>
                            @foreach ($comerciales as $comerciale)
                                <option value="{{ $comerciale->id }}">{{ $comerciale->name }}</option>
                            @endforeach
                        </select>
                        @error('com_2')
                            <div id="com_2" class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
            @endif
            <div class="col-md-4">
                <div class="form-group">
                    <label for="cotizacionFile">Archivo cotizaci&oacute;n:</label>
                    <input type="file" id="cotizacionFile" wire:model.lazy="cotizacionFile" class="form-control @error('cotizacionFile') is-invalid @elseif(strlen($cotizacionFile) > 0) is-valid @enderror" value="{{ old('cotizacionFile') }}">
                    @error('cotizacionFile')
                        <div id="cotizacionFile" class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="col-md-12 d-flex justify-content-end">
                <button class="btn bg-gradient-warning">Guardar</button>
            </div>
        </div>
    </form>
    <hr class="horizontal dark my-3">
    <form wire:submit.prevent="updatePropuesta">
        <div class="row">
            <div class="col-md-12">
                <h5><b>EDITAR PROPUESTA</b></h5>  
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="nom_proyecto_prop">Nombre proyecto:</label>
                    <input type="text" id="nom_proyecto_prop" wire:model.lazy="nom_proyecto_prop" class="form-control @error('nom_proyecto_prop') is-invalid @elseif(strlen($nom_proyecto_prop) > 0) is-valid @enderror" value="{{ old('nom_proyecto_prop') }}">
                    @error('nom_proyecto_prop')
                        <div id="nom_proyecto_prop" class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>        
            <div class="col-md-6">
                <div class="form-group">
                    <label for="presupuesto_prop">Presupuesto:</label>
                    <input type="text" id="presupuesto_prop" wire:model.lazy="presupuesto_prop" class="form-control @error('presupuesto_prop') is-invalid @elseif(strlen($presupuesto_prop) > 0) is-valid @enderror" value="{{ old('presupuesto_prop') }}">
                    @error('presupuesto_prop')
                        <div id="presupuesto_prop" class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="fecha_prop">Fecha estimada:</label>
                    <input type="date" id="fecha_prop" wire:model.lazy="fecha_prop" class="form-control @error('fecha_prop') is-invalid @elseif(strlen($fecha_prop) > 0) is-valid @enderror" value="{{ old('fecha_prop') }}">
                    @error('fecha_prop')
                        <div id="fecha_prop" class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="cotizacionUrl_prop">Url cotizaci&oacute;n:</label>
                    <input type="text" id="cotizacionUrl_prop" wire:model.lazy="cotizacionUrl_prop" class="form-control @error('cotizacionUrl_prop') is-invalid @elseif(strlen($cotizacionUrl_prop) > 0) is-valid @enderror" value="{{ old('cotizacionUrl_prop') }}">
                    @error('cotizacionUrl_prop')
                        <div id="cotizacionUrl_prop" class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="col-md-12 d-flex justify-content-end">
                <button class="btn bg-gradient-warning">Guardar</button>
            </div>
        </div>
    </form>
    @if($errors->any()) 
        <script>
        Swal.fire(
            '!Oppss tenemos un problema',
            `<ul style='text-align: initial; list-style-type: none;'>
            @foreach($errors->all() as $error) 
                <li>{{ $error }}<li>
            @endforeach
            </ul>`,
            'error'
        );
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