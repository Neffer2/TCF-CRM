<div>
    <form wire:submit.prevent="store" class="p-3">  
        <div class="row p-2"> 
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
            <div class="col-md-6">
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
            <div class="col-md-6">
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