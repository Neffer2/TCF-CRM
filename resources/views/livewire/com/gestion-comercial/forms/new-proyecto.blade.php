<div> 
    <form wire:submit.prevent="store">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="fecha">Fecha:</label>
                    <input wire:model.lazy="fecha" id="fecha" type="date" name="fecha" class="form-control @error('fecha') is-invalid @elseif(strlen($fecha) > 0) is-valid @enderror" value="{{ old('date') }}" placeholder="Nombre" required>
                    @error('fecha')
                        <div id="fecha" class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div> 
            </div>
            <div class="col-md-8">
                <div class="form-group"> 
                    <label for="nom_cliente">Nombre Cliente:</label>
                    <input wire:model.lazy="nom_cliente" id="nom_cliente" type="text" name="nom_cliente" class="form-control @error('nom_cliente') is-invalid @elseif(strlen($nom_cliente) > 0) is-valid @enderror" value="{{ old('nom_cliente') }}" placeholder="Nombre Cliente" required>
                    @error('nom_cliente')
                        <div id="nom_cliente" class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-grup">
                    <label for="nom_proyecto">Nombre Proyecto:</label>
                    <input wire:model.lazy="nom_proyecto" id="nom_proyecto" type="text" name="nom_proyecto" class="form-control @error('nom_proyecto') is-invalid @elseif(strlen($nom_proyecto) > 0) is-valid @enderror" value="{{ old('nom_proyecto') }}" placeholder="Nombre Proyecto" required>
                    @error('nom_proyecto')
                        <div id="nom_proyecto" class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="cod_cc">COD C.C:</label>
                    <input wire:model.lazy="cod_cc" id="cod_cc" type="text" name="cod_cc" class="form-control @error('cod_cc') is-invalid @elseif(strlen($cod_cc) > 0) is-valid @enderror" value="{{ old('cod_cc') }}" placeholder="COD C.C." required>
                    @error('cod_cc')
                        <div id="cod_cc" class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="valor_proyecto">Valor Proyecto:</label>
                        <input wire:model.lazy="valor_proyecto" id="valor_proyecto" type="number" name="valor_proyecto" class="form-control @error('valor_proyecto') is-invalid @elseif(strlen($valor_proyecto) > 0) is-valid @enderror" value="{{ old('valor_proyecto') }}" placeholder="Valor proyecto" required>
                        @error('valor_proyecto')
                            <div id="valor_proyecto" class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror 
                    </div>
                </div>
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
            <div class="col-md-6">
                <div class="form-group">
                    <label for="id_estado">Estado: </label>
                    <select wire:model.lazy="id_estado" id="id_estado" name="id_estado" class="form-control @error('id_estado') is-invalid @elseif(strlen($id_estado) > 0) is-valid @enderror" value="{{ old('id_estado') }}" placeholder="Estado">
                        <option value="">Seleccionar</option>
                        @foreach ($estados as $estado)
                            <option value="{{ $estado->id }}">{{ $estado->description }}</option>
                        @endforeach
                    </select>
                    @error('id_estado')
                        <div id="id_estado" class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="id_cuenta">Cuenta: </label>
                    <select wire:model.lazy="id_cuenta" id="id_cuenta" name="id_cuenta" class="form-control @error('id_cuenta') is-invalid @elseif(strlen($id_cuenta) > 0) is-valid @enderror" value="{{ old('id_cuenta') }}" placeholder="Estado" required>
                        <option value="">Seleccionar</option>
                        @foreach ($cuentas as $cuenta)
                            <option value="{{ $cuenta->id }}">{{ $cuenta->description }}</option>
                        @endforeach
                    </select>
                    @error('id_cuenta')
                        <div id="id_cuenta" class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
             
            <div class="col-md-6">
                <div class="form-group">
                    <label for="fecha_inicio">Fecha inicio:</label>
                    <input wire:model="fecha_inicio" id="fecha_inicio" type="date" name="fecha_inicio" class="form-control @error('fecha_inicio') is-invalid @elseif(strlen($fecha_inicio) > 0) is-valid @enderror" value="{{ old('fecha_inicio') }}">
                    @error('fecha_inicio')
                        <div id="fecha_inicio" class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="dura_mes">Dura Mes: </label>
                    <input wire:model="dura_mes" id="dura_mes" type="date" name="dura_mes" class="form-control @error('dura_mes') is-invalid @elseif(strlen($dura_mes) > 0) is-valid @enderror" value="{{ old('dura_mes') }}">
                    @error('dura_mes')
                        <div id="dura_mes" class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="col-md-12">
                <button class="btn bg-gradient-warning">Crear nuevo proyecto</button>
            </div>
        </div>
    </form>
</div>
