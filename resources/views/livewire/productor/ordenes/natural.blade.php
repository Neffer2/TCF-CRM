<div class="card card-body mb-2">
    <div class="row">
        <div class="col-md-12 mb-3">
            <h3 class="m-0">Orden de compra natural</h3>
            <p class="text-sm m-0">Selecciona tu personal y asigna los items nesesarios.</p>
        </div>    
        <div class="col-md-6">        
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="">Nombre</label>
                        <input type="text" class="form-control"
                        wire:model.change="nombre" placeholder="Nombre">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="">C&eacute;dula</label>
                        <input type="text" class="form-control"
                        wire:model.change="cedula" placeholder="C&eacute;dula">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="">Tel&eacute;fono</label>
                        <input type="text" class="form-control"
                        wire:model.change="telefono" placeholder="Tel&eacute;fono">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="tercero">Tercero: <span class="text-danger">*</span></label>
                        <select id="tercero" size="4" class="form-control">
                            @foreach ($terceros as $tercero)
                                <option value="{{ $tercero->id }}">{{ $tercero->nombre }} {{ $tercero->apellido }} - {{ $tercero->cedula }}</option>
                            @endforeach
                        </select>
                    </div> 
                </div>
            </div>
        </div>
        {{-- <div class="col-md-4">
            <div class="form-group">
                <label for="">Tel&eacute;fono: <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('telefono') is-invalid @elseif(strlen($telefono) > 0) is-valid @enderror"
                wire:model.change="telefono" placeholder="Telefono">
                @error('telefono')
                    <div id="telefono" class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">                    
                <label for="">Ciudad: <span class="text-danger">*</span></label>
                <select id="" class="form-control @error('ciudad') is-invalid @elseif(strlen($ciudad) > 0) is-valid @enderror"
                wire:model.change="ciudad">
                    <option value="">Seleccionar</option>
                    @foreach ($ciudades as $ciudad)
                        <option value="{{ $ciudad }}">{{ $ciudad }}</option>
                    @endforeach
                </select>
                @error('ciudad')
                    <div id="ciudad" class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">                    
                <label for="">Banco:</label>
                <select id="" class="form-control @error('banco') is-invalid @elseif(strlen($banco) > 0) is-valid @enderror"
                    wire:model.change="banco">
                    <option value="">Seleccionar</option>
                    <option value="Banco 1">Banco 2</option>
                </select>
                @error('banco')
                    <div id="banco" class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">                            
                <label for="">Certificaci&oacute;n bancaria:</label>
                <input type="file" class="form-control @error('cert_bancaria') is-invalid @elseif(strlen($cert_bancaria) > 0) is-valid @enderror"
                wire:model.change="cert_bancaria">
                @error('cert_bancaria')
                    <div id="cert_bancaria" class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div> 
        <div class="col-md-4">
            <div class="form-group">                            
                <label for="">RUT:</label>
                <input type="file" class="form-control @error('rut') is-invalid @elseif(strlen($rut) > 0) is-valid @enderror"
                wire:model.change="rut">
                @error('rut')
                    <div id="rut" class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="">Estado: <span class="text-danger">*</span></label>
                <select name="" id="" class="form-control @error('estado') is-invalid @elseif(strlen($estado) > 0) is-valid @enderror"
                wire:model.change="estado">
                    <option value="">Seleccionar</option>
                    @foreach ($estados as $estado)
                        <option value="{{ $estado->id }}">{{ $estado->descripcion }}</option>                    
                    @endforeach
                </select>
                @error('estado')
                    <div id="estado" class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div> --}}
    </div>
    <hr>

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