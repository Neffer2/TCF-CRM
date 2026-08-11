<div>
    <form wire:submit.prevent="store">
        <div class="row g-3">
            {{-- Nombre --}}
            <div class="col-md-4">
                <div class="form-group">
                    <label for="nombre" class="form-label">Nombre *</label>
                    <input id="nombre" type="text" wire:model.blur="nombre" class="form-control @error('nombre') is-invalid @enderror" placeholder="Nombre">
                    @error('nombre')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Apellido --}}
            <div class="col-md-4">
                <div class="form-group">
                    <label for="apellido" class="form-label">Apellido *</label>
                    <input id="apellido" type="text" wire:model.blur="apellido" class="form-control @error('apellido') is-invalid @enderror" placeholder="Apellido">
                    @error('apellido')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Empresa --}}
            <div class="col-md-4">
                <div class="form-group">
                    <label for="empresa" class="form-label">Empresa *</label>
                    <input id="empresa" type="text" wire:model.blur="empresa" class="form-control @error('empresa') is-invalid @enderror" placeholder="Empresa">
                    @error('empresa')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Cargo --}}
            <div class="col-md-3">
                <div class="form-group">
                    <label for="cargo" class="form-label">Cargo</label>
                    <input id="cargo" type="text" wire:model.blur="cargo" class="form-control @error('cargo') is-invalid @enderror" placeholder="Cargo">
                    @error('cargo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Celular --}}
            <div class="col-md-3">
                <div class="form-group">
                    <label for="celular" class="form-label">Celular</label>
                    <input id="celular" type="text" wire:model.blur="celular" class="form-control @error('celular') is-invalid @enderror" placeholder="Celular">
                    @error('celular')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Correo --}}
            <div class="col-md-3">
                <div class="form-group">
                    <label for="correo" class="form-label">Correo</label>
                    <input id="correo" type="email" wire:model.blur="correo" class="form-control @error('correo') is-invalid @enderror" placeholder="ejemplo@correo.com">
                    @error('correo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Cliente --}}
            <div class="col-md-3">
                <div class="form-group">
                    <label for="cliente_id" class="form-label">Seleccionar Cliente *</label>
                    <select id="cliente_id" wire:model.blur="cliente_id" class="form-select @error('cliente_id') is-invalid @enderror">
                        <option value="">-- Seleccione un cliente --</option>
                        @foreach($listaClientes as $cliente)
                            <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
                        @endforeach
                    </select>
                    @error('cliente_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- PBX --}}
            <div class="col-md-3">
                <div class="form-group">
                    <label for="pbx" class="form-label">PBX EXT</label>
                    <input id="pbx" type="text" wire:model.blur="pbx" class="form-control @error('pbx') is-invalid @enderror" placeholder="PBX">
                    @error('pbx')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Web --}}
            <div class="col-md-3">
                <div class="form-group">
                    <label for="web" class="form-label">Web</label>
                    <input id="web" type="text" wire:model.blur="web" class="form-control @error('web') is-invalid @enderror" placeholder="https://sitio.com">
                    @error('web')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Dirección --}}
            <div class="col-md-3">
                <div class="form-group">
                    <label for="direccion" class="form-label">Dirección</label>
                    <input id="direccion" type="text" wire:model.blur="direccion" class="form-control @error('direccion') is-invalid @enderror" placeholder="Dirección">
                    @error('direccion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Ciudad --}}
            <div class="col-md-3">
                <div class="form-group">
                    <label for="ciudad" class="form-label">Ciudad</label>
                    <select id="ciudad" wire:model.blur="ciudad" class="form-select @error('ciudad') is-invalid @enderror">
                        <option value="">Seleccionar</option>
                        <option value="BOGOTÁ D.C.">BOGOTÁ D.C.</option>
                        <option value="MEDELLÍN">MEDELLÍN</option>
                        <option value="CALI">CALI</option>
                        <option value="BARRANQUILLA">BARRANQUILLA</option>
                        <option value="CARTAGENA">CARTAGENA</option>
                        <option value="SOLEDAD">SOLEDAD</option>
                        <option value="CÚCUTA">CÚCUTA</option>
                        <option value="IBAGUÉ">IBAGUÉ</option>
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
                        <option value="POPAYÁN">POPAYÁN</option>
                        <option value="ITAGÜÍ">ITAGÜÍ</option>
                        <option value="FLORIDABLANCA">FLORIDABLANCA</option>
                        <option value="ENVIGADO">ENVIGADO</option>
                        <option value="TULUÁ">TULUÁ</option>
                        <option value="SAN ANDRÉS">SAN ANDRÉS</option>
                        <option value="DOSQUEBRADAS">DOSQUEBRADAS</option>
                        <option value="APARTADÓ">APARTADÓ</option>
                        <option value="TUNJA">TUNJA</option>
                        <option value="GIRÓN">GIRÓN</option>
                        <option value="URIBIA">URIBIA</option>
                        <option value="BARRANCABERMEJA">BARRANCABERMEJA</option>
                        <option value="FLORENCIA">FLORENCIA</option>
                        <option value="TURBO">TURBO</option>
                        <option value="MAICAO">MAICAO</option>
                        <option value="PIEDECUESTA">PIEDECUESTA</option>
                        <option value="YOPAL">YOPAL</option>
                    </select>
                    @error('ciudad')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Botón de envío --}}
            <div class="col-12 mt-4 d-flex justify-content-end">
                <button type="submit" class="btn bg-gradient-warning mb-0" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="store">
                        <i class="fas fa-plus me-1"></i> Guardar contacto
                    </span>
                    <span wire:loading wire:target="store">
                        <i class="fas fa-spinner fa-spin me-1"></i> Guardando...
                    </span>
                </button>
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