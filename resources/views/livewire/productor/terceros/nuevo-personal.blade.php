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
        @if ($orden && $orden->estado_id == 3)
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
                                <p class="text-sm m-0">Los campos marcados con * son obligatorios.</p>
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
                        <div class="col-md-4">
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
                        @if ($orden->ordenItems->sum('vtotal_oc') > $min_rut)
                            <div class="col-md-4">
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
                        @endif
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
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="banco">Banco: @guest <span class="text-danger">*</span> @endguest</label>
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
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="tipo_cuenta">Tipo de cuenta: @guest <span class="text-danger">*</span> @endguest</label>
                                <select id="tipo_cuenta" class="form-control @error('tipo_cuenta') is-invalid @elseif(strlen($tipo_cuenta) > 0) is-valid @enderror"
                                    wire:model.change="tipo_cuenta">
                                    <option value="">Seleccionar</option>
                                    <option value="AHORROS">AHORROS</option>
                                    <option value="CORRIENTE">CORRIENTE</option>
                                </select>
                                @error('tipo_cuenta')
                                    <div id="tipo_cuenta" class="text-invalid">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="num_cuenta">N&uacute;mero de cuenta: <span class="text-danger">*</span></label>
                                <input id="num_cuenta" type="text" class="form-control @error('num_cuenta') is-invalid @elseif(strlen($num_cuenta) > 0) is-valid @enderror"
                                wire:model.change="num_cuenta" placeholder="N&uacute;mero de cuenta">
                                @error('num_cuenta')
                                    <div id="num_cuenta" class="text-invalid">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
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
                        <div class="col-md-4">
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
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="planilla_aportes"> 
                                    ¿Tus pagos superan el salario m&iacute;nimo?
                                    Adjunta tu planilla de aportes:
                                </label>
                                <input type="file" class="form-control @error('planilla_aportes') is-invalid @elseif(strlen($planilla_aportes) > 0) is-valid @enderror"
                                wire:model="planilla_aportes">
                                <label>
                                    @if ($tercero->planilla_aportes)
                                        <a href="{{ asset(str_replace("public", "storage", $tercero->planilla_aportes)) }}" target="_blank">
                                            Archivo actual:
                                            <i class="fa-regular fa-eye"></i>
                                        </a>
                                    @endif
                                </label>
                                @error('planilla_aportes')
                                    <div id="planilla_aportes" class="text-invalid">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        @if ($orden->ordenItems->sum('vtotal_oc') > $min_rut)
                            <div class="col-md-4">
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
                        @endif
                        @guest
                            @if (!$mostrarContrato)
                                <!-- PASO 1: Botón para confirmar información y mostrar el contrato a leer -->
                                <div class="col-md-12 mt-3">
                                    <button type="button" class="btn bg-gradient-primary" wire:click="confirmarInformacion" wire:loading.attr="disabled">
                                        Confirmar información
                                    </button>
                                </div>
                            @else
                                <!-- PASO 2: Visualización del Contrato a leer -->
                                <div class="col-md-12 mt-3">
                                    <h5>Vista previa del Contrato (Léalo antes de firmar)</h5>
                                    <div class="d-none d-md-block w-100 mb-3">
                                        <embed src="data:application/pdf;base64,{{ $contratoBase64 }}" width="100%" height="800" type="application/pdf">
                                    </div>

                                    <div class="d-block d-md-none text-center w-100">
                                        <a href="data:application/pdf;base64,{{ $contratoBase64 }}" target="_blank" class="btn btn-primary w-100 my-2">
                                            Ver contrato (abrir PDF)
                                        </a>
                                    </div>
                                </div>

                                <!-- Canvas de Firma del Contratista -->
                                <div class="col-md-12 mt-4" wire:ignore>
                                    <div class="card p-3">
                                        <label class="form-label">Firma del contratista <span class="text-danger">*</span></label>
                                        <div>
                                            <canvas id="firma-canvas" width="600" height="200"
                                                    style="border:1px solid #ccc; border-radius:6px; width:100%; height:200px; touch-action:none; background:#fff; cursor: crosshair;"></canvas>
                                            <div>
                                                <button type="button" class="btn btn-sm btn-outline-secondary mt-1" id="firma-borrar">Borrar firma</button>
                                            </div>
                                        </div>
                                        @error('firma') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <!-- Términos y Condiciones -->
                                <div class="col-md-12 mt-3">
                                    <div class="form-group">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="fcustomCheck1" wire:model.lazy="terminos">
                                            <label class="custom-control-label" for="fcustomCheck1">
                                                Al marcar esta casilla estás aceptando la política de tratamiento de datos y el contrato de prestación de servicios <span class="text-danger">*</span>
                                            </label>
                                            @error('terminos')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- PASO 3: Botón de Guardar cambios condicionado a que acepte los términos -->
                                @if ($terminos)
                                    <div class="modal-footer">
                                        <div class="row w-100">
                                            <div class="col-md-12 @guest ps-0 @endguest">
                                                <button id="enviar-btn" type="button" class="btn bg-gradient-primary" wire:click="generarContrato" wire:loading.attr="disabled">
                                                    Guardar cambios y Firmar Contrato
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        @endguest
                    </div>
                </div>
                @auth
                    @if (Auth::user()->rol == 1)
                        <div class="modal-footer">
                            <div class="row w-100">
                                <div class="col-md-12">
                                    <button type="button" class="btn bg-gradient-danger" wire:click.prefetch="toggelConfirm">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
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
                @endauth
            </div>
        @elseif ($orden && $orden->estado_id == 7)
            <div class="modal-body pt-1">
                <div class="row">
                    <div class="col-md-12 mb-2">
                        <h3 class="m-0">Adjunta tus evidencias</h3>
                        <p class="text-sm m-0">
                            <b>
                                Recuerda que debes adjuntar las evidencias de cada uno de los servicios que has realizado.<br>
                                Debes adjuntar ({{ $orden->ordenItems->sum('cant_oc') }}) evidencias en total.
                            </b>
                        </p>
                    </div>
                    @if ($orden->justificacion_rechazo)
                        <div class="col-md-12 my-2">
                            <h6 class="m-0">Observaciones de rechazo:</h6>
                            <p class="text-sm m-0">
                                <b>
                                    {{ $orden->justificacion_rechazo }}
                                </b>
                            </p>
                        </div>
                    @endif

                    <div class="container text-sm px-5">
                        <div class="table-responsive" style="overflow-x: scroll;">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Fecha</th>
                                        <th>Foto</th>
                                        <th>Observaciones</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($evidencias as $key => $evidencia)
                                        <tr>
                                            <td>
                                                {{ $key + 1 }}
                                            </td>
                                            <td>
                                                {{ $evidencia['fecha'] }}
                                            </td>
                                            <td>
                                                <a href="{{ asset(str_replace("public", "storage", $evidencia['foto'])) }}" target="_blank">
                                                    <img src="{{ asset(str_replace("public", "storage", $evidencia['foto'])) }}" height="70">
                                                </a>
                                            </td>
                                            <td>
                                                {{ $evidencia['observacion'] }}
                                            </td>
                                            <td>
                                                <button class="btn btn-danger" wire:click="deleteEvidencia({{ $key }})">Eliminar</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row mt-5">
                    <!-- Campos existentes -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="fechaEvidencia">Fecha evidencia: <span class="text-danger">*</span></label>
                            <input id="fechaEvidencia" type="date" class="form-control @error('fechaEvidencia') is-invalid @elseif(strlen($fechaEvidencia) > 0) is-valid @enderror"
                            wire:model.lazy="fechaEvidencia" placeholder="Fecha evidencia">
                            @error('fechaEvidencia')
                                <div id="fechaEvidencia" class="text-invalid text-danger text-xs mt-1">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="fotoEvidencia">Foto evidencia: <span class="text-danger">*</span></label>
                            <input type="file" class="form-control @error('fotoEvidencia') is-invalid @elseif(strlen($fotoEvidencia) > 0) is-valid @enderror"
                            wire:model.change="fotoEvidencia">
                            @error('fotoEvidencia')
                                <div id="fotoEvidencia" class="text-invalid text-danger text-xs mt-1">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="observacionEvidencia">Observaciones: </label>
                            <textarea id="observacionEvidencia" type="text" class="form-control @error('observacionEvidencia') is-invalid @elseif(strlen($observacionEvidencia) > 0) is-valid @enderror"
                            wire:model.lazy="observacionEvidencia" placeholder="Indica el proyecto y el servicio que prestaste."></textarea>
                            @error('observacionEvidencia')
                                <div id="observacionEvidencia" class="text-invalid text-danger text-xs mt-1">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    @if ($evidencias->count() == $orden->ordenItems->sum('cant_oc'))
                    <div class="col-md-12 mt-3">
                        <h5>Vista previa de la Cuenta de Cobro</h5>
                        <div class="d-none d-md-block w-100 mb-3">
                            <embed src="data:application/pdf;base64,{{ $pdfCuentaCobroBase64 }}" width="100%" height="800" type="application/pdf">
                        </div>
                    </div>

                    <!-- Sección de Firma del Tercero -->
                    <div class="col-md-12 mt-4" wire:ignore>
                        <div class="card p-3">
                            <h5>Firma de la Cuenta de Cobro</h5>
                            <p class="text-muted small">Firme en el recuadro para aprobar el documento y finalizar el proceso.</p>
                            
                            <div style="border: 2px dashed #ccc; width: 100%; height: 200px; position: relative;">
                                <canvas id="firma-canvas" width="600" height="200" style="width: 100%; height: 100%; touch-action: none; cursor: crosshair;"></canvas>
                            </div>
                            
                            <div class="mt-3">
                                <button type="button" class="btn btn-secondary btn-sm" id="firma-borrar">Limpiar Firma</button>
                                <!-- Único botón que guarda evidencias, firma, genera el PDF y cierra el proceso -->
                                <button type="button" class="btn bg-gradient-success" wire:click="guardarCuentaCobroConFirma" wire:loading.attr="disabled">
                                    Guardar Evidencias y Firmar Cuenta de Cobro
                                </button>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="col-md-2">
                        <div class="form-group">
                            <button type="button" class="btn bg-gradient-primary" wire:click="newEvidencia" wire:loading.attr="disabled">
                                Agregar evidencia
                            </button>
                        </div>
                    </div>
                    @endif
                    <div class="col-md-12 mt-3">
                        <div wire:loading>
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
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
                        <div class="col-md-4">
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
                        <div class="col-md-4">
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
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="banco">Banco: @guest <span class="text-danger">*</span> @endguest</label>
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
                        <div class="col-md-4">
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
                        <div class="col-md-4">
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
                        <div class="col-md-4">
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
                    </div>
                </div>
                @auth
                    @if (Auth::user()->rol == 1)
                        <div class="modal-footer">
                            <div class="row w-100">
                                <div class="col-md-12">
                                    <button type="button" class="btn bg-gradient-danger" wire:click.prefetch="toggelConfirm">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
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
                @endauth
                @guest
                    @if ($terminos)
                        <div class="modal-footer">
                            <div class="row w-100">
                                <div class="col-md-12 @guest ps-0 @endguest">
                                    <button id="enviar-btn" type="button" class="btn bg-gradient-primary" wire:click="actualizarTercero" wire:loading.attr="disabled">Guardar cambios</button>
                                </div>
                            </div>
                        </div>
                    @endif
                @endguest
            </div>
        @endif
        <script>
        function initFirmaCanvas() {
            const canvas = document.getElementById('firma-canvas');
            if (!canvas || canvas.dataset.initialized) return;
            
            // Marcamos el canvas como inicializado para no duplicar eventos
            canvas.dataset.initialized = 'true';

            const ctx = canvas.getContext('2d');
            let drawing = false;

            ctx.lineWidth = 2.5;
            ctx.lineCap = 'round';
            ctx.strokeStyle = '#000';

            const pos = e => {
                const r = canvas.getBoundingClientRect();
                return {
                    x: (e.clientX - r.left) * (canvas.width / r.width),
                    y: (e.clientY - r.top) * (canvas.height / r.height)
                };
            };

            canvas.addEventListener('pointerdown', e => {
                drawing = true;
                canvas.setPointerCapture(e.pointerId);
                const p = pos(e);
                ctx.beginPath();
                ctx.moveTo(p.x, p.y);
            });

            canvas.addEventListener('pointermove', e => {
                if (!drawing) return;
                const p = pos(e);
                ctx.lineTo(p.x, p.y);
                ctx.stroke();
            });

            const fin = () => {
                if (!drawing) return;
                drawing = false;
                @this.set('firma', canvas.toDataURL('image/png'), true);
            };

            canvas.addEventListener('pointerup', fin);
            canvas.addEventListener('pointerleave', fin);

            const btnLimpiar = document.getElementById('firma-borrar');
            if (btnLimpiar) {
                btnLimpiar.addEventListener('click', () => {
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    @this.set('firma', null, true);
                });
            }
        }

        const observer = new MutationObserver(() => {
            initFirmaCanvas();
        });

        observer.observe(document.body, { childList: true, subtree: true });

        document.addEventListener('DOMContentLoaded', initFirmaCanvas);
        </script>
    </div>
@endif
