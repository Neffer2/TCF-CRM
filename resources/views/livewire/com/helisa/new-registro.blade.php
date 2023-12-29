<div class="col-lg-12 col-12 mx-auto mt-">
    <div class="card" x-data="new_project" x-cloak>  
        <div class="card-body d-flex justify-content-center">
            <div class="col-lg-2 col-md-2 col-sm-3 col-4 text-center">
                <a x-on:click="show_form" href="javascript:;" class="avatar border-1 rounded-circle bg-gradient-warning">
                    <i x-show="form_project" class="fa-solid fa-minus text-white"></i>
                    <i x-show="!form_project" class="fas fa-plus text-white"></i>
                </a>
                <p class="mb-0 text-sm" style="margin-top:6px;">Nuevo registro</p>
            </div>
        </div>  
        <div class="card-body" x-show="form_project" x-transition x-cloak>
            <form wire:submit.prevent="store">
                <div class="row"> 
                    <div class="col-md-3">
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
                    <div class="col-md-3">
                        <div class="form-group"> 
                            <label for="tipo_doc">Tipo Doc:</label>
                            <input wire:model.lazy="tipo_doc" id="tipo_doc" type="text" name="tipo_doc" class="form-control @error('tipo_doc') is-invalid @elseif(strlen($tipo_doc) > 0) is-valid @enderror" value="{{ old('tipo_doc') }}" placeholder="Tipo documento" required>
                            @error('tipo_doc')
                                <div id="tipo_doc" class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-grup">
                            <label for="num_doc">N&uacute;mero Documento:</label>
                            <input wire:model.lazy="num_doc" id="num_doc" type="text" name="num_doc" class="form-control @error('num_doc') is-invalid @elseif(strlen($num_doc) > 0) is-valid @enderror" value="{{ old('num_doc') }}" placeholder="N&uacute;mero Documento" required>
                            @error('num_doc')
                                <div id="num_doc" class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror 
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="identidad">Identidad:</label>
                            <input wire:model.lazy="identidad" id="identidad" type="text" name="identidad" class="form-control @error('identidad') is-invalid @elseif(strlen($identidad) > 0) is-valid @enderror" value="{{ old('identidad') }}" placeholder="Identidad" required>
                            @error('identidad')
                                <div id="identidad" class="invalid-feedback">
                                    {{ $message }}
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="nom_tercero">Nombre Tercero</label>
                            <input wire:model.lazy="nom_tercero" id="nom_tercero" type="text" name="nom_tercero" class="form-control @error('nom_tercero') is-invalid @elseif(strlen($nom_tercero) > 0) is-valid @enderror" value="{{ old('nom_tercero') }}" placeholder="Nombre Tercero">
                            @error('nom_tercero')
                                <div id="nom_tercero" class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="centro">Centro de costos:</label>
                            <input wire:model.lazy="centro" id="centro" type="text" name="centro" class="form-control @error('centro') is-invalid @elseif(strlen($centro) > 0) is-valid @enderror" value="{{ old('centro') }}" placeholder="Centro de costos">
                            @error('centro')
                                <div id="centro" class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div> 
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="nom_centro_costo">Nombre centro de costos: </label>
                            <input wire:model.lazy="nom_centro_costo" id="nom_centro_costo" type="text" name="nom_centro_costo"
                            class="form-control @error('nom_centro_costo') is-invalid @elseif(strlen($nom_centro_costo) > 0) is-valid @enderror" value="{{ old('nom_centro_costo') }}" placeholder="Nombre centro de costos">
                            @error('nom_centro_costo')
                                <div id="nom_centro_costo" class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div> 
                    </div> 
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="debito">D&eacute;bito:</label>
                            <input wire:model.lazy="debito" id="debito" type="text" name="debito" class="form-control
                            @error('debito') is-invalid @elseif(strlen($debito) > 0) is-valid @enderror" value="{{ old('debito') }}"
                            placeholder="D&eacute;bito" required x-mask:dynamic="$money($input)">
                            @error('debito')
                                <div id="debito" class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="credito">Cr&eacute;dito: </label>
                            <input wire:model.lazy="credito" id="credito" type="text" name="credito"
                            class="form-control @error('credito') is-invalid @elseif(strlen($credito) > 0) is-valid @enderror"
                            value="{{ old('credito') }}" placeholder="Cr&eacute;dito" x-mask:dynamic="$money($input)">
                            @error('credito')
                                <div id="credito" class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4"></div>
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
                    <hr class="horizontal dark mb-3">
                    <div class="row">
                        @for ($i = 0; $i < $participaciones; $i++)
                            <div class="col-md-3">
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
                            <div class="col-md-3">
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
                            <div class="col-md-3">
                                <div class="form-group mb-1">
                                    <label for="base_factura{{ $i }}">Base factura:</label>
                                    <input type="text" disabled id="base_factura{{ $i }}" class="form-control
                                    @if ($errors->has("base_factura".$i)) is-invalid @elseif(strlen(${'base_factura'.$i}) > 0) is-valid @enderror"
                                    wire:model.lazy="base_factura{{ $i }}" required x-mask:dynamic="$money($input)">
                                    @if ($errors->has("base_factura".$i))
                                        <div class="text-danger">
                                            <small>{{ $errors->first("base_factura".$i) }}</small>
                                        </div>
                                    @endif 
                                </div> 
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-1">
                                    <label for="comision{{ $i }}">Comisi&oacute;n:</label>
                                    <input type="text" disabled id="comision{{ $i }}" class="form-control
                                    @if ($errors->has("comision".$i)) is-invalid @elseif(strlen(${'comision'.$i}) > 0) is-valid @enderror"
                                    wire:model.lazy="comision{{ $i }}" required x-mask:dynamic="$money($input)">
                                    @if ($errors->has("comision".$i))
                                        <div class="text-danger">
                                            <small>{{ $errors->first("comision".$i) }}</small>
                                        </div>
                                    @endif
                                </div> 
                            </div>
                        @endfor
                    </div>
                    <hr class="horizontal dark mb-3">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="id_cuenta">Cuenta: </label>
                            <select wire:model.lazy="id_cuenta" id="id_cuenta" name="id_cuenta" class="form-control @error('id_cuenta') is-invalid @elseif(strlen($id_cuenta) > 0) is-valid @enderror" value="{{ old('id_cuenta') }}" placeholder="Estado">
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
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="año">Año: </label>
                            <select wire:model="año" id="año" type="date" name="año"
                            class="form-control @error('año') is-invalid @elseif(strlen($año) > 0) is-valid @enderror"
                            value="{{ old('año') }}">
                                <option value="">Seleccionar</option>
                                @foreach ($años as $años)
                                    <option value="{{ $años->description }}">{{ $años->description }}</option>
                                @endforeach
                            </select>
                            @error('año')
                                <div id="año" class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="mes">Mes: </label>
                            <select wire:model="mes" id="mes" type="date" name="mes"
                            class="form-control @error('mes') is-invalid @elseif(strlen($mes) > 0) is-valid @enderror"
                            value="{{ old('mes') }}">
                                <option value="">Seleccionar</option>
                                @foreach ($meses as $mes)
                                    <option value="{{ $mes->description }}">{{ $mes->description }}</option>
                                @endforeach
                            </select>
                            @error('mes')
                                <div id="mes" class="invalid-feedback">
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
    </div>
</div>
