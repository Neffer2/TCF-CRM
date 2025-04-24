@if (!$this->tercero)
<div x-data="{ show: true }" x-cloak>
    <div class="row">
        <div class="col-md-12 mb-2">
            <h3 class="m-0">Nuevo personal</h3>
            <div class="form-check form-switch">
                <input class="form-check-input bg-gradient-warning" type="checkbox" id="flexSwitchCheckDefault" x-on:change="show = ! show">
                <label x-show="show" class="form-check-label" for="flexSwitchCheckDefault">Subir en bloque</label>
                <label x-show="!show" class="form-check-label" for="flexSwitchCheckDefault">Subir en formulario</label>
            </div>
        </div>
    </div>
    <div id="formulario" class="row" x-show="show" x-transition>
        <div class="col-md-12">
            <p class="text-sm m-0">Registra tu personal. Los campos marcados con * son obligatorios.</p>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="nombre">Nombres: <span class="text-danger">*</span></label>
                <input id="nombre" type="text" class="form-control form-control @error('nombre') is-invalid @elseif(strlen($nombre) > 0) is-valid @enderror"
                wire:model.lazy="nombre" placeholder="Nombre">
                @error('nombre')
                    <div id="nombre" class="text-invalid">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="apellido">Apellidos: <span class="text-danger">*</span></label>
                <input id="apellido" type="text" class="form-control @error('apellido') is-invalid @elseif(strlen($apellido) > 0) is-valid @enderror"
                wire:model.change="apellido" placeholder="Apellido">
                @error('apellido')
                    <div id="apellido" class="text-invalid">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="cedula">C&eacute;dula: <span class="text-danger">*</span></label>
                <input id="cedula" type="text" class="form-control @error('cedula') is-invalid @elseif(strlen($cedula) > 0) is-valid @enderror"
                wire:model.change="cedula" placeholder="C.C">
                @error('cedula')
                    <div id="cedula" class="text-invalid">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="email">Correo: <span class="text-danger">*</span></label>
                <input id="email" type="email" class="form-control @error('correo') is-invalid @elseif(strlen($correo) > 0) is-valid @enderror"
                wire:model.change="correo" placeholder="alguien@ejemplo.com">
                @error('correo')
                    <div id="correo" class="text-invalid">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="telefono">Tel&eacute;fono: <span class="text-danger">*</span></label>
                <input id="telefono" type="text" class="form-control @error('telefono') is-invalid @elseif(strlen($telefono) > 0) is-valid @enderror"
                wire:model.change="telefono" placeholder="Telefono">
                @error('telefono')
                    <div id="telefono" class="text-invalid">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="ciudad">Ciudad: <span class="text-danger">*</span></label>
                <select id="ciudad" class="form-control @error('ciudad') is-invalid @elseif(strlen($ciudad) > 0) is-valid @enderror"
                wire:model.change="ciudad">
                    <option value="">Seleccionar</option>
                    @foreach ($ciudades as $ciudad)
                        <option value="{{ $ciudad }}">{{ $ciudad }}</option>
                    @endforeach
                </select>
                @error('ciudad')
                    <div id="ciudad" class="text-invalid">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="banco">Banco:</label>
                <select id="banco" class="form-control @error('banco') is-invalid @elseif(strlen($banco) > 0) is-valid @enderror"
                    wire:model.change="banco">
                    <option value="">Seleccionar</option>
                    @foreach ($bancos as $item)
                        <option value="{{ $item }}">{{ $item }}</option>
                    @endforeach
                </select>
                @error('banco')
                    <div id="banco" class="text-invalid">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>
        @auth
        <div class="col-md-3">
            <div class="form-group">
                <label for="servicio">Servicio: <span class="text-danger">* </span></label>
                <select id="servicio" class="form-control @error('servicio') is-invalid @elseif(strlen($servicio) > 0) is-valid @enderror"
                    wire:model.change="servicio">
                    <option value="">Seleccionar</option>
                    @foreach ($servicios as $item)
                        <option value="{{ $item }}">{{ $item}}</option>
                    @endforeach
                </select>
                @error('servicio')
                    <div id="servicio" class="text-invalid">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>
        @endauth
        <div class="col-md-3">
            <div class="form-group">
                <label for="cert_bancaria">Certificaci&oacute;n bancaria:</label>
                <input id="cert_bancaria" type="file" class="form-control @error('cert_bancaria') is-invalid @elseif(strlen($cert_bancaria) > 0) is-valid @enderror"
                wire:model.change="cert_bancaria">
                @error('cert_bancaria')
                    <div id="cert_bancaria" class="text-invalid">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="rut">RUT:</label>
                <input id="rut" type="file" class="form-control @error('rut') is-invalid @elseif(strlen($rut) > 0) is-valid @enderror"
                wire:model.change="rut">
                @error('rut')
                    <div id="rut" class="text-invalid">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>
        {{-- <div class="col-md-4">
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
                    <div id="estado" class="text-invalid">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div> --}}
        <div class="col-md-4 align-content-end">
            <div class="form-group">
                <button wire:click="nuevoPersonal" wire:loading.attr="disabled" class="btn bg-gradient-warning m-0">Registrar</button>
            </div>
        </div>
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
    <div id="masivo" class="row" x-show="!show" x-transition>
        <div class="col-md-12 mb-2">
            <p class="text-sm m-0">Con <a href="{{ asset('formatos/terceros/Formato_subida_de_Terceros.xlsx') }}" target="_blank"><b>este</b></a> formato, puedes subir personal en bloque.</p>
        </div>
        <div class="col-md-12">
            <input type="file" wire:model="terceroXlsx">
        </div>
        @if (session()->has('import_error'))
            <div class="col-md-12 text-danger mt-1">
                @foreach (session()->get('import_error') as $error)
                    <p style="margin-bottom: .3rem">{{ $error }}</p>
                @endforeach
            </div>
        @endif
    </div>
</div>
@elseif($this->tercero)
    <div>
        <div class="modal-body pt-1">
            @auth
                <div style="position: absolute; right: 1%; top: 0%; cursor: pointer;" data-bs-dismiss="modal">
                    <i class="fa-regular fa-circle-xmark"></i>
                </div>
            @endauth
            <div class="row">
                @auth
                    <div class="col-md-12">
                        <h3 class="m-0">Editar personal:</h3>
                        <p class="text-sm m-0">Cambia la información de tu personal. Los campos marcados con * son obligatorios.</p>
                    </div>
                @endauth
                @guest
                    <div class="col-md-12">
                        <h3 class="m-0">Actualiza tu información:</h3>
                        <p class="text-sm m-0">Verifica tu información y confirma que est&eacute; correctamente diligenciada. Los campos marcados con * son obligatorios.</p>
                    </div>
                @endguest

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="nombre">Nombres: <span class="text-danger">*</span></label>
                        <input id="nombre" type="text" class="form-control form-control @error('nombre') is-invalid @elseif(strlen($nombre) > 0) is-valid @enderror"
                        wire:model.lazy="nombre" placeholder="Nombre">
                        @error('nombre')
                            <div id="nombre" class="text-invalid">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="apellido">Apellidos: <span class="text-danger">*</span></label>
                        <input id="apellido" type="text" class="form-control @error('apellido') is-invalid @elseif(strlen($apellido) > 0) is-valid @enderror"
                        wire:model.change="apellido" placeholder="Apellido">
                        @error('apellido')
                            <div id="apellido" class="text-invalid">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="cedula">C&eacute;dula: <span class="text-danger">*</span></label>
                        <input id="cedula" type="text" class="form-control @error('cedula') is-invalid @elseif(strlen($apellido) > 0) is-valid @enderror"
                        wire:model.change="cedula" placeholder="C.C">
                        @error('cedula')
                            <div id="cedula" class="text-invalid">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="correo">Correo: <span class="text-danger">*</span></label>
                        <input id="correo" type="email" class="form-control @error('correo') is-invalid @elseif(strlen($correo) > 0) is-valid @enderror"
                        wire:model.change="correo" placeholder="alguien@ejemplo.com">
                        @error('correo')
                            <div id="correo" class="text-invalid">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="telefono">Tel&eacute;fono: <span class="text-danger">*</span></label>
                        <input id="telefono" type="text" class="form-control @error('telefono') is-invalid @elseif(strlen($telefono) > 0) is-valid @enderror"
                        wire:model.change="telefono" placeholder="Telefono">
                        @error('telefono')
                            <div id="telefono" class="text-invalid">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="num_rut">N&uacute;mero RUT: <span class="text-danger">*</span></label>
                        <input id="num_rut" type="text" class="form-control @error('num_rut') is-invalid @elseif(strlen($num_rut) > 0) is-valid @enderror"
                        wire:model.change="num_rut" placeholder="N&uacute;mero RUT">
                        @error('num_rut')
                            <div id="num_rut" class="text-invalid">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="ciudad">Ciudad: <span class="text-danger">*</span></label>
                        <select id="ciudad" class="form-control @error('ciudad') is-invalid @elseif(strlen($ciudad) > 0) is-valid @enderror"
                        wire:model.change="ciudad">
                            <option value="">Seleccionar</option>
                            @foreach ($ciudades as $ciudad)
                                <option value="{{ $ciudad }}">{{ $ciudad }}</option>
                            @endforeach
                        </select>
                        @error('ciudad')
                            <div id="ciudad" class="text-invalid">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Fotocopia c&eacute;dula: @guest <span class="text-danger">*</span>@endguest</label>
                        <input type="file" class="form-control @error('copia_cedula') is-invalid @elseif(strlen($copia_cedula) > 0) is-valid @enderror"
                        wire:model.change="copia_cedula">
                        <label>
                            @if ($tercero->copia_cedula)
                                <a href="{{ asset(str_replace("public", "storage", $tercero->copia_cedula)) }}" target="_blank">
                                    Archivo actual:
                                    <i class="fa-regular fa-eye"></i>
                                </a>
                            @endif
                        </label>
                        @error('copia_cedula')
                            <div id="copia_cedula" class="text-invalid">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Certificaci&oacute;n bancaria: @guest <span class="text-danger">*</span>@endguest</label>
                        <input type="file" class="form-control @error('cert_bancaria') is-invalid @elseif(strlen($cert_bancaria) > 0) is-valid @enderror"
                        wire:model.change="cert_bancaria">
                        <label>
                            @if ($tercero->cert_bancaria)
                                <a href="{{ asset(str_replace("public", "storage", $tercero->cert_bancaria)) }}" target="_blank">
                                    Archivo actual:
                                    <i class="fa-regular fa-eye"></i>
                                </a>
                            @endif
                        </label>
                        @error('cert_bancaria')
                            <div id="cert_bancaria" class="text-invalid">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">ARCHIVO RUT: @guest <span class="text-danger">*</span> @endguest</label>
                        <input type="file" class="form-control @error('rut') is-invalid @elseif(strlen($rut) > 0) is-valid @enderror"
                        wire:model.change="rut">
                        @if ($tercero->rut)
                                <label>
                                    <a href="{{ asset(str_replace("public", "storage", $tercero->rut)) }}" target="_blank">
                                        Archivo actual:
                                        <i class="fa-regular fa-eye"></i>
                                    </a>
                                </label>
                        @endif
                        @error('rut')
                            <div id="rut" class="text-invalid">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Banco: @guest <span class="text-danger">*</span> @endguest</label>
                        <select id="" class="form-control @error('banco') is-invalid @elseif(strlen($banco) > 0) is-valid @enderror"
                        wire:model.change="banco">
                            <option value="">Seleccionar</option>
                            <option value="BANCO AGRARIO DE COLOMBIA">BANCO AGRARIO DE COLOMBIA</option>
                            <option value="BANCO AV VILLAS">BANCO AV VILLAS</option>
                            <option value="BANCO CAJA SOCIAL">BANCO CAJA SOCIAL</option>
                            <option value="BANCO DAVIVIENDA S.A.">BANCO DAVIVIENDA S.A.</option>
                            <option value="BANCO DE BOGOTÁ">BANCO DE BOGOTÁ</option>
                            <option value="BANCO DE OCCIDENTE">BANCO DE OCCIDENTE</option>
                            <option value="BANCO FALABELLA S.A.">BANCO FALABELLA S.A.</option>
                            <option value="BANCOOMEVA">BANCOOMEVA</option>
                            <option value="BANCOLOMBIA">BANCOLOMBIA</option>
                            <option value="BANCO PICHINCHA">BANCO PICHINCHA</option>
                            <option value="BANCO POPULAR">BANCO POPULAR</option>
                            <option value="BBVA COLOMBIA">BBVA COLOMBIA</option>
                            <option value="DAVIPLATA">DAVIPLATA</option>
                            <option value="FINANCIERA JURISCOOP">FINANCIERA JURISCOOP</option>
                            <option value="ITAU">ITAU</option>
                            <option value="LINK">LINK</option>
                            <option value="LULO BANK">LULO BANK</option>
                            <option value="NEQUI">NEQUI</option>
                            <option value="NU BANK">NU BANK</option>
                            <option value="SCOTIABANCK COLPATRIA">SCOTIABANCK COLPATRIA</option>
                        </select>
                        @error('banco')
                            <div id="banco" class="text-invalid">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-12">
                    <button type="button" class="btn bg-gradient-primary" wire:click="generarContrato">
                        Confirmar informaci&oacute;n
                    </button>
                    @if ($contrato)
                        <div class="d-flex justify-content-center">
                            <embed src="{{ $contrato }}" width="100%" height="900" type="application/pdf">
                        </div>
                        @guest
                        <div class="col-md-12 mt-3">
                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="fcustomCheck1" wire:model.lazy="terminos">
                                    <label class="custom-control-label" for="customCheck1">
                                        Al marcar esta casilla estas aceptando la <a>pol&iacute;tica de tratamiento de datos y el contrato de prestaci&oacute;n de servicios <span class="text-danger">*</span>
                                    </label>
                                    @error('terminos')
                                        <div id="terminos" class="text-invalid">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        @endguest
                    @endif
                </div>
            </div>
        </div>
        @if ($terminos)
            <div class="modal-footer">
                <div class="row w-100">
                    <div class="col-md-12 @guest ps-0 @endguest">
                        @auth
                            <button type="button" class="btn bg-gradient-danger" wire:click.prefetch="toggelConfirm">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        @endauth
                        <button id="enviar-btn" type="button" class="btn bg-gradient-primary" wire:click="actualizarTercero" wire:loading.attr="disabled">Guardar cambios</button>
                    </div>
                    @if ($deleteConfirm)
                        <div class="card shadow-lg" style="position: absolute; left: 0%; top: 35%;">
                            <div class="card-body">
                                <p class="text-center">
                                    <b>¿Estas seguro de eliminar a {{ $nombre }} {{ $apellido }}?</b>
                                </p>
                                <div class="d-flex justify-content-center">
                                    <button type="button" wire:click="toggelConfirm" class="btn bg-gradient-secondary me-1">Cancelar</button>
                                    <button type="button" wire:click="deletePersonal" class="btn bg-gradient-danger">Eliminar</button>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
@endif
