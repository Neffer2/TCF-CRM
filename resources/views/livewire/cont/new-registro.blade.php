<div class="col-lg-12 col-12 mx-auto"> 
    <div class="card" x-data="new_project"> 
        <div class="card-body d-flex justify-content-center">
            <div class="col-lg-2 col-md-2 col-sm-3 col-4 text-center">
                <a x-on:click="show_form" href="javascript:;" class="avatar border-1 rounded-circle bg-gradient-warning"><i class="fas fa-plus text-white"></i></a>
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
                            <label for="concepto">Concepto:</label>
                            <input wire:model.lazy="concepto" id="concepto" type="text" name="concepto" class="form-control @error('concepto') is-invalid @elseif(strlen($concepto) > 0) is-valid @enderror" value="{{ old('concepto') }}" placeholder="Concepto" required>
                            @error('concepto')
                                <div id="concepto" class="invalid-feedback">
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
                    <div class="col-md-3">
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
                    <div class="col-md-2">
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
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="debito">D&eacute;bito:</label>
                            <input wire:model.lazy="debito" id="debito" type="number" name="debito" class="form-control @error('debito') is-invalid @elseif(strlen($debito) > 0) is-valid @enderror" value="{{ old('debito') }}" placeholder="D&eacute;bito">
                            @error('debito')
                                <div id="debito" class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="credito">Cr&eacute;dito: </label>
                            <input wire:model="credito" id="credito" type="number" name="credito"
                            class="form-control @error('credito') is-invalid @elseif(strlen($credito) > 0) is-valid @enderror"
                            value="{{ old('credito') }}" placeholder="Cr&eacute;dito">
                            @error('credito')
                                <div id="credito" class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <label for="porcentaje">%</label>
                            <input wire:model="porcentaje" id="porcentaje" type="number" name="porcentaje"
                            class="form-control @error('porcentaje') is-invalid @elseif(strlen($porcentaje) > 0) is-valid @enderror"
                            value="{{ old('porcentaje') }}" placeholder="%">
                            @error('porcentaje')
                                <div id="porcentaje" class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="comercial">Comercial: </label>
                            <select wire:model="comercial" id="comercial" type="text" name="comercial"
                            class="form-control @error('comercial') is-invalid @elseif(strlen($comercial) > 0) is-valid @enderror"
                            value="{{ old('comercial') }}">
                                <option value="">Seleccionar</option>
                                @foreach ($comerciales as $comercial)
                                    <option value="{{ $comercial->id }}">{{ $comercial->name }}</option>
                                @endforeach
                            </select>
                            @error('comercial')
                                <div id="comercial" class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror 
                        </div>
                    </div>
                    <div class="col-md-3">
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
                    {{-- <div class="col-md-4">
                        <div class="form-group">
                            <label for="participacion">Participaci&oacute;n: </label>
                            <input wire:model="participacion" id="participacion" type="text" name="participacion"
                            class="form-control @error('participacion') is-invalid @elseif(strlen($participacion) > 0) is-valid @enderror"
                            value="{{ old('participacion') }}" placeholder="Participaci&oacute;n">
                            @error('participacion')
                                <div id="participacion" class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div> --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="base_factura">Base factura: </label>
                            <input wire:model="base_factura" id="base_factura" type="number" name="base_factura"
                            class="form-control @error('base_factura') is-invalid @elseif(strlen($base_factura) > 0) is-valid @enderror"
                            value="{{ old('base_factura') }}" placeholder="Base factura" disabled>
                            @error('base_factura')
                                <div id="base_factura" class="invalid-feedback">
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
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="comision">Comisi&oacute;n: </label>
                            <input wire:model="comision" id="comision" type="number" name="comision"
                            class="form-control @error('comision') is-invalid @elseif(strlen($comision) > 0) is-valid @enderror"
                            value="{{ old('comision') }}" placeholder="Comisi&oacute;n">
                            @error('comision')
                                <div id="comision" class="invalid-feedback">
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
