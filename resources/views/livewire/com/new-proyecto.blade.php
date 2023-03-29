<div class="col-lg-12 col-12 mx-auto"> 
    <div class="card" x-data="new_project"> 
        <div class="card-body d-flex justify-content-center">
            <div class="col-lg-2 col-md-2 col-sm-3 col-4 text-center">
                <a x-on:click="show_form" href="javascript:;" class="avatar border-1 rounded-circle bg-gradient-warning"><i class="fas fa-plus text-white"></i></a>
                <p class="mb-0 text-sm" style="margin-top:6px;">Nuevo Proyecto</p>
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
                    <div class="col-md-9">
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
                        <div class="form-group">
                            <label for="com_1">Comercial 1:</label>
                            <input wire:model.lazy="com_1" id="com_1" type="text" name="com_1" class="form-control @error('com_1') is-invalid @elseif(strlen($com_1) > 0) is-valid @enderror" value="{{ old('com_1') }}" placeholder="Comercial 1">
                            @error('com_1')
                                <div id="com_1" class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="com_2">Comercial 2:</label>
                            <input wire:model.lazy="com_2" id="com_2" type="text" name="com_2" class="form-control @error('com_2') is-invalid @elseif(strlen($com_2) > 0) is-valid @enderror" value="{{ old('com_2') }}" placeholder="Comercial 2">
                            @error('com_2')
                                <div id="com_2" class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div> 
                    </div>
                    <div class="col-md-2">
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
                    <div class="col-md-2">
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
                            <label for="fecha_inicio">Fecha inicio:</label>
                            <input wire:model="fecha_inicio" id="fecha_inicio" type="date" name="fecha_inicio" class="form-control @error('fecha_inicio') is-invalid @elseif(strlen($fecha_inicio) > 0) is-valid @enderror" value="{{ old('fecha_inicio') }}">
                            @error('fecha_inicio')
                                <div id="fecha_inicio" class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
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
                    <div class="col-md-4">
                        <button class="btn bg-gradient-warning">Crear nuevo proyecto</button>
                    </div>
                </div>
            </form>
        </div> 
    </div>
</div>
