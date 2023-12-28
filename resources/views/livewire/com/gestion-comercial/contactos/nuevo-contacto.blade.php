<div>
    <form wire:submit.prevent="store">  
        <div class="row"> 
            <div class="col-md-4"> 
                <div class="form-group">
                    <label for="nombre">Nombre: </label>
                    <input id="nombre" wire:model.lazy="nombre" class="form-control @error('nombre') is-invalid @elseif(strlen($nombre) > 0) is-valid @enderror" value="{{ old('nombre') }}" placeholder="Nombre">
                    @error('nombre')
                        <div id="nombre" class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="col-md-4"> 
                <div class="form-group">
                    <label for="apellido">Apellido: </label>
                    <input id="apellido" wire:model.lazy="apellido" class="form-control @error('apellido') is-invalid @elseif(strlen($apellido) > 0) is-valid @enderror" value="{{ old('apellido') }}" placeholder="Apellido">
                    @error('apellido')
                        <div id="apellido" class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="empresa">Empresa: </label>
                    <input id="empresa" wire:model.lazy="empresa" class="form-control @error('empresa') is-invalid @elseif(strlen($empresa) > 0) is-valid @enderror" value="{{ old('empresa') }}" placeholder="Empresa">
                    @error('empresa')
                        <div id="empresa" class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="cargo">Cargo: </label>
                    <input id="cargo" wire:model.lazy="cargo" class="form-control @error('cargo') is-invalid @elseif(strlen($cargo) > 0) is-valid @enderror" value="{{ old('cargo') }}" placeholder="Cargo">                
                    @error('cargo')
                        <div id="cargo" class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="celular">Celular: </label>
                    <input id="celular" type="number" wire:model.lazy="celular" class="form-control @error('celular') is-invalid @elseif(strlen($celular) > 0) is-valid @enderror" value="{{ old('celular') }}" placeholder="Celular">                
                    @error('celular')
                        <div id="celular" class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="correo">Correo: </label>
                    <input id="correo" type="email" wire:model.lazy="correo" class="form-control @error('correo') is-invalid @elseif(strlen($correo) > 0) is-valid @enderror" value="{{ old('correo') }}" placeholder="Correo">                
                    @error('correo')
                        <div id="correo" class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="pbx">PBX EXT: </label>
                    <input id="pbx" type="text" wire:model.lazy="pbx" class="form-control @error('pbx') is-invalid @elseif(strlen($pbx) > 0) is-valid @enderror" value="{{ old('pbx') }}" placeholder="PBX">                
                    @error('pbx')
                        <div id="pbx" class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="col-md-4"> 
                <div class="form-group">
                    <label for="web">Web: </label>
                    <input id="web" type="text" wire:model.lazy="web" class="form-control @error('web') is-invalid @elseif(strlen($web) > 0) is-valid @enderror" value="{{ old('web') }}" placeholder="Web">                
                    @error('web')
                        <div id="web" class="invalid-feedback">
                            {{ $message }}
                        </div> 
                    @enderror
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group"> 
                    <label for="direccion">Direcci&oacute;n: </label>
                    <input id="direccion" type="text" wire:model.lazy="direccion" class="form-control @error('direccion') is-invalid @elseif(strlen($direccion) > 0) is-valid @enderror" value="{{ old('direccion') }}" placeholder="Direccion">                
                    @error('direccion')
                        <div id="direccion" class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="col-md-4"> 
                <div class="form-group">
                    <label for="ciudad">Ciudad: </label>
                    <select id="ciudad" type="text" wire:model.lazy="ciudad"
                    class="form-control @error('ciudad') is-invalid @elseif(strlen($ciudad) > 0) is-valid @enderror" placeholder="Direccion">                
                        <option value="">Seleccionar</option>
                        <option value="BOGOT&Aacute; D.C">BOGOT&Aacute; D.C.</option>
                        <option value="MEDELL&Iacute;N">MEDELL&Iacute;N</option>
                        <option value="CALI">CALI</option>
                        <option value="BARRANQUILLA">BARRANQUILLA</option>
                        <option value="CARTAGENA">CARTAGENA</option>
                        <option value="SOLEDAD">SOLEDAD</option>
                        <option value="C&Uacute;CUTA">C&Uacute;CUTA</option>
                        <option value="IBAGU&Eacute;">IBAGU&Eacute;</option>
                        <option value="SOACHA">SOACHA</option>
                        <option value="VILLAVICENCIO">VILLAVICENCIO</option>
                        <option value="BUCARAMANGA">BUCARAMANGA</option>
                        <option value="SANTA MARTA">SANTA MARTA</option>
                        <option value="VALLEDUPAR">VALLEDUPAR</option>
                        <option value="BELLO">BELLO</option>
                        <option value="PEREIRA">PEREIRA</option>
                        <option value="PASTO">PASTO</option>
                        <option value="BUENAVENTURA">BUENAVENTURA</option>
                        <option value="MANIZALES">MANIZALES</option>
                        <option value="NEIVA">NEIVA</option>
                        <option value="PALMIRA">PALMIRA</option>
                        <option value="RIOHACHA">RIOHACHA</option>
                        <option value="SINCELEJO">SINCELEJO</option>
                        <option value="POPAY&Aacute;N">POPAY&Aacute;N</option>
                        <option value="ITAGÜ&Iacute;">ITAGÜ&Iacute;</option>
                        <option value="FLORIDABLANCA">FLORIDABLANCA</option>
                        <option value="ENVIGADO">ENVIGADO</option>
                        <option value="TULU&Aacute;">TULU&Aacute;</option>
                        <option value="SAN ANDR&Eacute;S">SAN ANDR&Eacute;S</option>
                        <option value="DOSQUEBRADAS">DOSQUEBRADAS</option>
                        <option value="APARTAD&Oacute;">APARTAD&Oacute;</option>
                        <option value="TUNJA">TUNJA</option>
                        <option value="GIR&Oacute;N">GIR&Oacute;N</option>
                        <option value="URIBIA">URIBIA</option>
                        <option value="BARRANQUILLA">BARRANQUILLA</option>
                        <option value="BARRANCABERMEJA">BARRANCABERMEJA</option>
                        <option value="FLORENCIA">FLORENCIA</option>
                        <option value="TURBO">TURBO</option>
                        <option value="MAICAO">MAICAO</option>
                        <option value="PIEDECUESTA">PIEDECUESTA</option>
                        <option value="YOPAL">YOPAL</option>
                    </select>
                </div>
            </div>
            <div class="col-md-5">
                <button class="btn bg-gradient-warning mb-0">Nuevo contacto</button>
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