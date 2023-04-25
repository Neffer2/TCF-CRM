<div>
    <form wire:submit.prevent="update_proyecto">
        <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nom_cliente">Cliente:</label>
                        <input wire:model.lazy="nom_cliente" type="text" id="nom_cliente" class="form-control @error('nom_cliente') is-invalid @elseif(strlen($nom_cliente) > 0) is-valid @enderror" required>
                        @error('nom_cliente')
                            <div id="nom_cliente" class="invalid-feedback">
                                {{ $message }} 
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nom_proyecto">Nombre Proyecto:</label>
                        <input type="text" id="nom_proyecto" class="form-control @error('nom_proyecto') is-invalid @elseif(strlen($nom_proyecto) > 0) is-valid @enderror" value="{{ old('nom_proyecto') }}" wire:model.lazy="nom_proyecto" required>
                        @error('nom_proyecto')
                            <div id="nom_proyecto" class="invalid-feedback">
                                {{ $message }} 
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="CC">C&oacute;digo centro de costos:</label>
                        <input disabled type="text" id="CC" class="form-control @error('CC') is-invalid @elseif(strlen($CC) > 0) is-valid @enderror" value="{{ old('CC') }}" wire:model.lazy="CC" required>
                        @error('CC')
                            <div id="CC" class="invalid-feedback">
                                {{ $message }} 
                            </div>
                        @enderror
                    </div>
                </div> 
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="valor">Valor:</label>
                            <input type="text" id="valor" class="form-control @error('valor') is-invalid @elseif(strlen($valor) > 0) is-valid @enderror" value="{{ old('valor') }}" wire:model.lazy="valor" required>
                            @error('valor')
                                <div id="valor" class="invalid-feedback">
                                    {{ $message }} 
                                </div>
                            @enderror
                        </div>
                    </div>                    
                    <div class="col-md-6">
                        <div class="form-group mb-1">
                            <label for="participaciones">Participaciones:</label>
                            <input disabled type="number" id="participaciones" class="form-control @error('participaciones') is-invalid @elseif(strlen($participaciones) > 0) is-valid @enderror" value="{{ old('participaciones') }}" wire:model="participaciones" required>
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
                                <select disabled type="text" id="comercial{{ $i }}" class="form-control @if ($errors->has("comercial".$i)) is-invalid @elseif(strlen(${'comercial'.$i}) > 0) is-valid @enderror" wire:model.lazy="comercial{{ $i }}" required>
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
                <div class="col-md-6"> 
                    <div class="form-group">
                        <label for="estado">Estado:</label>
                        <select type="text" id="estado" class="form-control @error('estado') is-invalid @elseif(strlen($estado) > 0) is-valid @enderror" value="{{ old('estado') }}" wire:model.lazy="estado" required>
                            <option value="">Seleccionar</option>
                            @foreach ($estados as $estado)
                                <option value="{{ $estado->id }}">{{ $estado->description }}</option>
                            @endforeach
                        </select>    
                        @error('estado')
                            <div id="estado" class="invalid-feedback">
                                {{ $message }} 
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group"> 
                        <label for="cuenta">Cuenta:</label>
                        <select type="text" id="cuenta" class="form-control @error('cuenta') is-invalid @elseif(strlen($cuenta) > 0) is-valid @enderror" value="{{ old('cuenta') }}" wire:model.lazy="cuenta" required>
                            <option value="">Seleccionar</option>
                            @foreach ($cuentas as $cuenta)
                                <option value="{{ $cuenta->id }}">{{ $cuenta->description }}</option>
                            @endforeach
                        </select>    
                        @error('cuenta')
                            <div id="cuenta" class="invalid-feedback">
                                {{ $message }} 
                            </div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn bg-gradient-primary">Guardar cambios</button>
        </div>
    </form>
</div>