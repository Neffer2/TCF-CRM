<div>
    <form wire:submit.prevent="store"> 
        <div class="row">  
            <div class="col-md-12">
                <div class="form-group mb-1">
                    <label for="nom_proyecto">Nombre Proyecto:</label>
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
                    <input type="text" id="presupuesto" class="form-control @error('presupuesto') is-invalid @elseif(strlen($presupuesto) > 0) is-valid @enderror" value="{{ old('presupuesto') }}" wire:model.lazy="presupuesto" required>
                    @error('presupuesto')
                        <div id="presupuesto" class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div> 
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group mb-1">
                        <label for="participaciones">Participaciones:</label>
                        <input type="number" id="participaciones" class="form-control @error('participaciones') is-invalid @elseif(strlen($participaciones) > 0) is-valid @enderror" value="{{ old('participaciones') }}" wire:model="participaciones" required>
                        @error('participaciones')
                            <div id="participaciones" class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div> 
                </div> 
                <div class="col-md-4">
                    <label for="testigoPorcentaje">Total %: </label>
                    <input disabled type="text" id="testigoPorcentaje" class="form-control @error('testigoPorcentaje') is-invalid @enderror" value="{{ old('testigoPorcentaje') }}" wire:model="testigoPorcentaje" required>
                    @error('testigoPorcentaje')
                        <div id="testigoPorcentaje" class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="col-md-4"></div>
            </div>
            <hr class="horizontal dark mb-3">
            <div class="row">
                @for ($i = 0; $i < $participaciones; $i++)
                    <div class="col-md-4">
                        <div class="form-group mb-1">
                            <label for="comercial{{ $i }}">Comercial:</label>
                            <select type="text" id="comercial{{ $i }}" class="form-control @if ($errors->has("comercial".$i)) is-invalid @elseif(strlen(${'comercial'.$i}) > 0) is-valid @enderror" wire:model.lazy="comercial{{ $i }}" required>
                                <option value="">Seleccionar</option>
                                @foreach ($comerciales as $comercial)
                                    <option value="{{ $comercial->id }}">{{ $comercial->name }}</option>
                                @endforeach
                            </select>                                    
                            {{-- {{ ${'comercial'.$i} }} --}}
                            @if ($errors->has("comercial".$i))
                                <div class="text-danger">
                                    <small>{{ $errors->first("comercial".$i) }}</small>
                                </div>
                            @endif
                        </div> 
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-1">
                            <label for="porcentaje{{ $i }}">%:</label>
                            <input type="text" id="porcentaje{{ $i }}" class="form-control @if ($errors->has("porcentaje".$i)) is-invalid @elseif(strlen(${'porcentaje'.$i}) > 0) is-valid @enderror" wire:model.lazy="porcentaje{{ $i }}" required/>
                            @if ($errors->has("porcentaje".$i))
                                <div class="text-danger">
                                    <small>{{ $errors->first("porcentaje".$i) }}</small>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-1">
                            <label for="valor{{ $i }}">Valor:</label>
                            <input type="text" disabled id="valor{{ $i }}" class="form-control @if ($errors->has("valor".$i)) is-invalid @elseif(strlen(${'valor'.$i}) > 0) is-valid @enderror" wire:model.lazy="valor{{ $i }}" required/>
                            @if ($errors->has("valor".$i))
                                <div class="text-danger">
                                    <small>{{ $errors->first("valor".$i) }}</small>
                                </div>
                            @endif
                        </div> 
                    </div>
                @endfor
            </div>
            <hr class="horizontal dark mb-3">
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
