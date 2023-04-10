<div>
    <div class="row">
        <form wire:submit.prevent="updateContacto">  
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
        </form>
    </div>
    <hr class="horizontal dark my-3">
    <div class="row">
        <form wire:submit.prevent="updateOportunidad">  
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
        </form>
    </div>
    <hr class="horizontal dark my-3">
    <div class="row">
        <div class="col-md-12">
            <h5><b>EDITAR COTIZACI&Oacute;N</b></h5>  
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="">Nombre proyecto:</label>
                <input type="text" class="form-control">
            </div>
        </div>        
        <div class="col-md-6">
            <div class="form-group">
                <label for="">Presupuesto:</label>
                <input type="text" class="form-control">
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label for="">Fecha estimada:</label>
                <input type="date" class="form-control">
            </div>
        </div>
        <div class="col-md-5">
            <div class="form-group">
                <label for="">Porcentaje:</label>
                <select name="" id="" class="form-control">
                    <option value="">Seleccionar</option>
                </select>
            </div>
        </div>
        <div class="col-md-5">
            <div class="form-group">
                <label for="">Comercial 2:</label>
                <input type="text" class="form-control">
            </div>
        </div>
        <div class="col-md-5">
            <div class="form-group">
                <label for="">Archivo cotizaci&oacute;n:</label>
                <input type="file" class="form-control">
            </div>
        </div>
        <div class="col-md-12 d-flex justify-content-end">
            <button class="btn bg-gradient-warning">Guardar</button>
        </div>
    </div>
    <hr class="horizontal dark my-3">
    <div class="row">
        <div class="col-md-12">
            <h5><b>EDITAR PROPUESTA</b></h5>  
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="">Nombre proyecto:</label>
                <input type="text" class="form-control">
            </div>
        </div>        
        <div class="col-md-6">
            <div class="form-group">
                <label for="">Presupuesto:</label>
                <input type="text" class="form-control">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="">Fecha estimada:</label>
                <input type="date" class="form-control">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="">Url cotizaci&oacute;n:</label>
                <input type="text" class="form-control">
            </div>
        </div>
        <div class="col-md-12 d-flex justify-content-end">
            <button class="btn bg-gradient-warning">Guardar</button>
        </div>
    </div>
    <hr class="horizontal dark my-3">
    <div class="row">
        <div class="col-md-12">
            <h5><b>EDITAR GESTI&Oacute;N COMERCIAL</b></h5>  
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="">Gesti&oacute;n:</label>
                <select name="" id="" class="form-control">
                    <option value="">Venta</option>
                    <option value="">Perdido</option>
                </select>
            </div>
        </div>   
        <div class="col-md-12 d-flex justify-content-end">
            <button class="btn bg-gradient-warning">Guardar</button>
        </div>     
    </div>
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