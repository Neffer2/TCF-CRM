<div class="row">
    <div class="col-md-12">
        <h3 class="m-0">Nuevo personal:</h3>
        <p class="text-sm m-0">Registra tu personal. Los campos marcados con * son obligatorios.</p>
    </div>    
    <div class="col-md-4">
        <div class="form-group">
            <label for="">Nombre: <span class="text-danger">*</span></label>
            <input type="text" class="form-control form-control @error('nombre') is-invalid @elseif(strlen($nombre) > 0) is-valid @enderror" 
            wire:model.lazy="nombre" placeholder="Nombre">
            @error('nombre')
                <div id="nombre" class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="">Apellido: <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('apellido') is-invalid @elseif(strlen($apellido) > 0) is-valid @enderror"
            wire:model.change="apellido" placeholder="Apellido">
            @error('apellido')
                <div id="apellido" class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="">C&eacute;dula: <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('apellido') is-invalid @elseif(strlen($apellido) > 0) is-valid @enderror"
            wire:model.change="cedula" placeholder="C.C">
            @error('cedula')
                <div id="cedula" class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="">Correo: <span class="text-danger">*</span></label>
            <input type="email" class="form-control @error('correo') is-invalid @elseif(strlen($correo) > 0) is-valid @enderror"
            wire:model.change="correo" placeholder="alguien@ejemplo.com">
            @error('correo')
                <div id="correo" class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>
    <div class="col-md-4">
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
                <option value="Bogota">Bogota</option>
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
                <option value="1">1</option>
            </select>
            @error('estado')
                <div id="estado" class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>
    <div class="col-md-4 align-content-end">
        <div class="form-group">
            <button wire:click="nuevoPersonal" class="btn bg-gradient-warning m-0">Registrar</button>
        </div>
    </div>
</div>