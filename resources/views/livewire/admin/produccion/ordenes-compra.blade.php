@if (Auth::user()->rol == 1)
    <div>
        <!-- MENÚ DE NAVEGACIÓN SUPERIOR (TABS) -->
        <div class="card mb-3">
            <div class="card-body p-2">
                <ul class="nav nav-pills nav-fill flex-row p-1">
                    <li class="nav-item">
                        <a class="nav-link mb-0 px-0 py-1 @if($submódulo == 'listado') active bg-gradient-primary text-white @endif"
                           wire:click="$set('submódulo', 'listado')" style="cursor: pointer;">
                            <i class="fas fa-list me-2"></i> Listado de Órdenes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link mb-0 px-0 py-1 @if($submódulo == 'anticipo') active bg-gradient-primary text-white @endif"
                           wire:click="$set('submódulo', 'anticipo')" style="cursor: pointer;">
                            <i class="fas fa-hand-holding-usd me-2"></i> Anticipo Colaborador
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link mb-0 px-0 py-1 @if($submódulo == 'legalizacion') active bg-gradient-primary text-white @endif"
                           wire:click="$set('submódulo', 'legalizacion')" style="cursor: pointer;">
                            <i class="fas fa-file-invoice-dollar me-2"></i> Legalización
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link mb-0 px-0 py-1 @if($submódulo == 'reintegros') active bg-gradient-primary text-white @endif"
                           wire:click="$set('submódulo', 'reintegros')" style="cursor: pointer;">
                            <i class="fas fa-undo me-2"></i> Reintegros
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link mb-0 px-0 py-1 @if($submódulo == 'vehiculos_bodega') active bg-gradient-primary text-white @endif"
                           wire:click="$set('submódulo', 'vehiculos_bodega')" style="cursor: pointer;">
                            <i class="fas fa-truck-loading me-2"></i> Consumo Vehículos / Bodega
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link mb-0 px-0 py-1 @if($submódulo == 'nomina') active bg-gradient-primary text-white @endif"
                           wire:click="$set('submódulo', 'nomina')" style="cursor: pointer;">
                            <i class="fas fa-users-cog me-2"></i> Nómina
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- CONTENIDO DINÁMICO SEGÚN LA PESTAÑA SELECCIONADA -->
        @if ($submódulo == 'listado')
            <!-- AQUÍ VA TU CARD COMPLETA DE LISTADO/TABLA QUE YA TENÍAS -->
            <div class="card">
                <div class="card-header p-0 mx-3 mt-3 position-relative z-index-1">
                    <div class="row">
                        <div class="col-md-12">
                            <h3 class="mb-0">Ordenes de compra</h3>
                            <p class="text-sm mb-0">Lista de ordenes de compra por revisar.</p>
                        </div>
                    </div>
                    <!-- Filtros existentes -->
                    <div class="row">
                        <div class="col-md-1">
                            <label for="año">Año:</label>
                            <select wire:model="año" class="form-control">
                                <option value="">Seleccionar</option>
                                @foreach ($años as $año)
                                    <option value="{{ $año->id }}">{{ $año->description }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-1">
                            <label for="comercial">COD OC:</label>
                            <input type="text" wire:model="cod_oc" class="form-control" placeholder="Código OC">
                        </div>
                        <div class="col-md-2">
                            <label for="comercial">Buscar:</label>
                            <input type="text" wire:model="cod_cc" class="form-control" placeholder="Centro de costos">
                        </div>
                        <div class="col-md-2">
                            <label for="comercial">Buscar:</label>
                            <input type="text" wire:model="documento" class="form-control" placeholder="Documento tercero">
                        </div>
                        <div class="col-md-2">
                            <label for="productor">Productor:</label>
                            <select id="productor" class="form-control" wire:model="productor">
                                <option value="">Seleccionar</option>
                                @foreach ($productores as $productor)
                                    <option value="{{ $productor->id }}">{{ $productor->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="estado">Estados:</label>
                            <select id="estado" class="form-control" wire:model="estado">
                                <option value="">Seleccionar</option>
                                @foreach ($estados as $estado)
                                    <option value="{{ $estado->id }}">{{ $estado->description }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="Tipo">Tipo:</label>
                            <select id="Tipo" class="form-control" wire:model="tipo">
                                <option value="">Seleccionar</option>
                                @foreach ($tipos as $tipo)
                                    <option value="{{ $tipo->id }}">{{ $tipo->description }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table align-items-center mb-0">
                        <!-- Tu tabla previa -->
                        <thead>
                        <tr>
                            <th colspan="1" class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">DATOS DE PROYECTO</th>
                            <th colspan="6" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">M&eacute;tricas</th>
                            <th colspan="2" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($ordenes as $orden)
                            @if ($orden->tipo_oc == 1)
                                <tr>
                                    <td style="width: 16rem;">
                                        <div class="d-flex px-2 py-1" title="{{ $orden->presupuesto->gestion->nom_proyecto_cot }}">
                                            <div>
                                                <img src="https://www.bullmarketing.com.co/wp-content/uploads/2022/04/cropped-favicon-bull-192x192.png" class="avatar avatar-sm me-3">
                                            </div>
                                            <div class="d-flex flex-column justify-content-center">
                                                @if (strlen($orden->presupuesto->gestion->nom_proyecto_cot) > 80)
                                                    <h6 class="mb-0 text-xs" >{{ substr($orden->presupuesto->gestion->nom_proyecto_cot, 0, 80) }}...</h6>
                                                @else
                                                    <h6 class="mb-0 text-xs" >{{ substr($orden->presupuesto->gestion->nom_proyecto_cot, 0, 80) }}</h6>
                                                @endif
                                                <p class="text-xs text-secondary mb-0">{{ $orden->presupuesto->gestion->contacto->empresa }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">Tipo</p>
                                        <span class="badge badge-sm badge-primary">{{ $orden->tipo->description }}</span>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">Proveedor</p>
                                        <span class="text-xs text-secondary mb-0">{{ $orden->proveedor->tercero }}</span>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">Centro de costos</p>
                                        <textarea disabled rows="1" class="text-xs text-secondary mb-0">{{ $orden->presupuesto->cod_cc }}</textarea>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">Fecha env&iacute;o (producci&oacute;n)</p>
                                        <p class="text-xs text-secondary mb-0">{{ $orden->fecha_envio_produccion }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">Productor</p>
                                        <p class="text-xs text-secondary mb-0">
                                            @if ($orden->presupuesto->productor_info)
                                                {{ $orden->presupuesto->productor_info->name }}
                                            @else
                                                NO ASIGNADO
                                            @endif
                                        </p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">Comercial</p>
                                        <p class="text-xs text-secondary mb-0">{{ $orden->presupuesto->gestion->comercial->name }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">Estado</p>
                                        <p class="text-xs text-secondary mb-0">{{ $orden->estado_oc->description }}</p>
                                    </td>
                                    <td class="d-flex align-items-center justify-content-center">
                                        <a class="btn bg-gradient-primary m-0 me-1 mb-1" href="{{ route('orden-juridica', ['orden' => $orden->id]) }}" target="_blank">Ver</a>
                                    </td>
                                </tr>
                            @elseif($orden->tipo_oc == 2)
                                <tr @if ($orden->actualizado && ($orden->estado_id == 2 || $orden->estado_id == 14)) style="background-color: #fee0d9" @endif>
                                    <td style="width: 16rem;">
                                        <div class="d-flex px-2 py-1" title="Orden #{{ $orden->id }}">
                                            <div>
                                                <img src="https://www.bullmarketing.com.co/wp-content/uploads/2022/04/cropped-favicon-bull-192x192.png" class="avatar avatar-sm me-3">
                                            </div>
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-xs" >Orden #{{ $orden->id }}</h6>
                                                <p class="text-xs text-secondary mb-0">{{ $orden->id }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">Tipo</p>
                                        <span class="badge badge-sm badge-info">{{ $orden->tipo->description }}</span>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">Tercero</p>
                                        <span class="text-xs text-secondary mb-0">{{ $orden->naturalInfo->tercero->nombre }} {{ $orden->naturalInfo->tercero->apellido }}</span>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">Fecha Generaci&oacute;n</p>
                                        <p class="text-xs text-secondary mb-0">{{ $orden->created_at }}</p>
                                    </td>
                                    {{-- <td>
                                        <p class="text-xs font-weight-bold mb-0">Fecha Evidencias</p>
                                        @if (!$orden->evidencias->isEmpty()) <p class="text-xs text-secondary mb-0">{{ $orden->evidencias->last()->created_at }}</p> @endif
                                    </td> --}}
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">Fecha env&iacute;o (producci&oacute;n)</p>
                                        <p class="text-xs text-secondary mb-0">{{ $orden->fecha_envio_produccion }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">Productor</p>
                                        <p class="text-xs text-secondary mb-0">{{ $orden->naturalInfo->productor->name }}</p>
                                    </td>
                                    <td colspan="2">
                                        <p class="text-xs font-weight-bold mb-0">Estado</p>
                                        <p class="text-xs text-secondary mb-0">{{ $orden->estado_oc->description }}
                                            @if ($orden->estado_oc->id == 2)
                                                <span class="badge badge-sm bg-gradient-warning">CONTROLLER</span>
                                            @elseif ($orden->estado_oc->id == 7)
                                                <span class="badge badge-sm bg-gradient-warning">TERCERO</span>
                                            @elseif ($orden->estado_oc->id == 5 && !($orden->cod_causal))
                                                <span class="badge badge-sm bg-gradient-warning">CONTABILIDAD</span>
                                            @elseif ($orden->estado_oc->id == 5 && $orden->cod_causal && !($orden->archivo_comprobante_pago))
                                                <span class="badge badge-sm bg-gradient-warning">TESORER&Iacute;A</span>
                                            @elseif ($orden->archivo_comprobante_pago)
                                                <span class="badge badge-sm bg-gradient-success">PAGADA</span>
                                            @elseif ($orden->estado_oc->id == 8)
                                                <span class="badge badge-sm bg-gradient-warning">LIDER PRODUCCIÓN</span>
                                            @elseif ($orden->estado_oc->id == 9)
                                                <span class="badge badge-sm bg-gradient-warning">GERENCIA</span>
                                            @endif
                                        </p>
                                    </td>
                                    <td class="d-flex align-items-center justify-content-center">
                                        <a class="btn bg-gradient-primary m-0 me-1 mb-1" href="{{ route('orden-natural', ['orden_id' => $orden->id]) }}" target="_blank">Ver</a>
                                    </td>
                                </tr>
                            @elseif ($orden->tipo_oc == 3)
                                <tr>
                                    <td style="width: 16rem;">
                                        <div class="d-flex px-2 py-1" title="{{ $orden->presupuesto->gestion->nom_proyecto_cot }}">
                                            <div>
                                                <img src="https://www.bullmarketing.com.co/wp-content/uploads/2022/04/cropped-favicon-bull-192x192.png" class="avatar avatar-sm me-3">
                                            </div>
                                            <div class="d-flex flex-column justify-content-center">
                                                @if (strlen($orden->presupuesto->gestion->nom_proyecto_cot) > 80)
                                                    <h6 class="mb-0 text-xs" >{{ substr($orden->presupuesto->gestion->nom_proyecto_cot, 0, 80) }}...</h6>
                                                @else
                                                    <h6 class="mb-0 text-xs" >{{ substr($orden->presupuesto->gestion->nom_proyecto_cot, 0, 80) }}</h6>
                                                @endif
                                                <p class="text-xs text-secondary mb-0">{{ $orden->presupuesto->gestion->contacto->empresa }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">Tipo</p>
                                        <span class="badge badge-sm badge-warning">{{ $orden->tipo->description }}</span>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">Proveedor</p>
                                        <span class="text-xs text-secondary mb-0">{{ $orden->proveedor->tercero }}</span>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">Centro de costos</p>
                                        <textarea disabled rows="1" class="text-xs text-secondary mb-0">{{ $orden->presupuesto->cod_cc }}</textarea>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">Fecha env&iacute;o (producci&oacute;n)</p>
                                        <p class="text-xs text-secondary mb-0">{{ $orden->fecha_envio_produccion }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">Productor</p>
                                        <p class="text-xs text-secondary mb-0">
                                            @if ($orden->presupuesto->productor_info)
                                                {{ $orden->presupuesto->productor_info->name }}
                                            @else
                                                NO ASIGNADO
                                            @endif
                                        </p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">Comercial</p>
                                        <p class="text-xs text-secondary mb-0">{{ $orden->presupuesto->gestion->comercial->name }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">Estado</p>
                                        <p class="text-xs text-secondary mb-0">{{ $orden->estado_oc->description }}</p>
                                    </td>
                                    <td class="d-flex align-items-center justify-content-center">
                                        <a class="btn bg-gradient-primary m-0 me-1 mb-1" href="{{ route('orden-nomina', ['orden' => $orden->id]) }}" target="_blank">Ver</a>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mb-3 ms-2">
                    {{ $ordenes->links() }}
                </div>
            </div>

        @elseif ($submódulo == 'anticipo')
            <!-- VISTA DEL FORMULARIO ANTICIPO COLABORADOR -->
            @livewire('admin.produccion.ordenes_-compra.ordenes-compra-anticipo', ['orden_id' => $orden_id], key('anticipo-'.$orden_id))

        @elseif ($submódulo == 'legalizacion')
            <!-- VISTA DE LEGALIZACIÓN -->
            @include('livewire.admin.produccion.ordenes-compra.orden-compra-legalizacion')

        @elseif ($submódulo == 'reintegros')
            <!-- VISTA DE REINTEGROS -->
            <div class="card card-body">
                <h3>Módulo de Reintegros</h3>
                <p>Próximamente carga desde Excel...</p>
            </div>

        @elseif ($submódulo == 'vehiculos_bodega')
            <!-- VISTA DE VEHÍCULOS / BODEGA -->
            <div class="card card-body">
                <h3>Módulo de Consumo de Vehículos / Bodega</h3>
                <p>Próximamente carga desde Excel...</p>
            </div>

        @elseif ($submódulo == 'nomina')
            <!-- VISTA DE NÓMINA -->
            <div class="card card-body">
                <h3>Módulo de Nómina</h3>
                <p>Próximamente carga desde Excel...</p>
            </div>
        @endif
    </div>
@elseif(Auth::user()->rol == 6)
    <div>
        <div class="card">
            <div class="card-header p-0 mx-3 mt-3 position-relative z-index-1">
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="mb-0">Ordenes de compra</h3>
                        <p class="text-sm mb-0">Lista de ordenes de compra por revisar.</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-1">
                        <label for="año">Año:</label>
                        <select wire:model="año" class="form-control">
                            <option value="">Seleccionar</option>
                            @foreach ($años as $año)
                                <option value="{{ $año->id }}">{{ $año->description }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-1">
                        <label for="comercial">COD OC:</label>
                        <input type="text" wire:model="cod_oc" class="form-control" placeholder="Código OC">
                    </div>
                    <div class="col-md-2">
                        <label for="comercial">Buscar:</label>
                        <input type="text" wire:model="cod_cc" class="form-control" placeholder="Centro de costos">
                    </div>
                    <div class="col-md-2">
                        <label for="comercial">Buscar:</label>
                        <input type="text" wire:model="documento" class="form-control" placeholder="Documento tercero">
                    </div>
                    <div class="col-md-2">
                        <label for="productor">Productor:</label>
                        <select id="productor" class="form-control" wire:model="productor">
                            <option value="">Seleccionar</option>
                            @foreach ($productores as $productor)
                                <option value="{{ $productor->id }}">{{ $productor->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="estado">Estados:</label>
                        <select id="estado" class="form-control" wire:model="estado" disabled>
                            <option value="">Seleccionar</option>
                            @foreach ($estados as $estado)
                                <option value="{{ $estado->id }}">{{ $estado->description }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="Tipo">Tipo:</label>
                        <select id="Tipo" class="form-control" wire:model="tipo">
                            <option value="">Seleccionar</option>
                            @foreach ($tipos as $tipo)
                                <option value="{{ $tipo->id }}">{{ $tipo->description }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table align-items-center mb-0">
                    <thead>
                        <tr>
                            <th colspan="1" class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">DATOS DE PROYECTO</th>
                            <th colspan="6" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">M&eacute;tricas</th>
                            <th colspan="2" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($ordenes as $orden)
                            @if ($orden->tipo_oc == 1)
                                <tr>
                                    <td style="width: 16rem;">
                                        <div class="d-flex px-2 py-1" title="{{ $orden->presupuesto->gestion->nom_proyecto_cot }}">
                                            <div>
                                                <img src="https://www.bullmarketing.com.co/wp-content/uploads/2022/04/cropped-favicon-bull-192x192.png" class="avatar avatar-sm me-3">
                                            </div>
                                            <div class="d-flex flex-column justify-content-center">
                                                @if (strlen($orden->presupuesto->gestion->nom_proyecto_cot) > 80)
                                                    <h6 class="mb-0 text-xs" >{{ substr($orden->presupuesto->gestion->nom_proyecto_cot, 0, 80) }}...</h6>
                                                @else
                                                    <h6 class="mb-0 text-xs" >{{ substr($orden->presupuesto->gestion->nom_proyecto_cot, 0, 80) }}</h6>
                                                @endif
                                                <p class="text-xs text-secondary mb-0">{{ $orden->presupuesto->gestion->contacto->empresa }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">Tipo</p>
                                        <span class="badge badge-sm badge-primary">{{ $orden->tipo->description }}</span>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">Proveedor</p>
                                        <span class="text-xs text-secondary mb-0">{{ $orden->proveedor->tercero }}</span>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">Centro de costos</p>
                                        <textarea disabled rows="1" class="text-xs text-secondary mb-0">{{ $orden->presupuesto->cod_cc }}</textarea>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">Fecha env&iacute;o (producci&oacute;n)</p>
                                        <p class="text-xs text-secondary mb-0">{{ $orden->fecha_envio_produccion }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">Productor</p>
                                        <p class="text-xs text-secondary mb-0">
                                            @if ($orden->presupuesto->productor_info)
                                                {{ $orden->presupuesto->productor_info->name }}
                                            @else
                                                NO ASIGNADO
                                            @endif
                                        </p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">Comercial</p>
                                        <p class="text-xs text-secondary mb-0">{{ $orden->presupuesto->gestion->comercial->name }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">Estado</p>
                                        <p class="text-xs text-secondary mb-0">{{ $orden->estado_oc->description }}</p>
                                    </td>
                                    <td class="d-flex align-items-center justify-content-center">
                                        <a class="btn bg-gradient-primary m-0 me-1 mb-1" href="{{ route('orden-compra-juridica', ['orden' => $orden->id]) }}" target="_blank">Ver</a>
                                    </td>
                                </tr>
                            @elseif($orden->tipo_oc == 2)
                                <tr>
                                    <td style="width: 16rem;">
                                        <div class="d-flex px-2 py-1" title="Orden #{{ $orden->id }}">
                                            <div>
                                                <img src="https://www.bullmarketing.com.co/wp-content/uploads/2022/04/cropped-favicon-bull-192x192.png" class="avatar avatar-sm me-3">
                                            </div>
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-xs" >Orden #{{ $orden->id }}</h6>
                                                <p class="text-xs text-secondary mb-0">{{ $orden->id }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">Tipo</p>
                                        <span class="badge badge-sm badge-info">{{ $orden->tipo->description }}</span>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">Tercero</p>
                                        <span class="text-xs text-secondary mb-0">{{ $orden->naturalInfo->tercero->nombre }} {{ $orden->naturalInfo->tercero->apellido }}</span>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">Fecha Generaci&oacute;n</p>
                                        <p class="text-xs text-secondary mb-0">{{ $orden->created_at }}</p>
                                    </td>
                                    {{-- <td>
                                        <p class="text-xs font-weight-bold mb-0">Fecha Evidencias</p>
                                        @if (!$orden->evidencias->isEmpty()) <p class="text-xs text-secondary mb-0">{{ $orden->evidencias->last()->created_at }}</p> @endif
                                    </td> --}}
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">Fecha env&iacute;o (producci&oacute;n)</p>
                                        <p class="text-xs text-secondary mb-0">{{ $orden->fecha_envio_produccion }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">Productor</p>
                                        <p class="text-xs text-secondary mb-0">{{ $orden->naturalInfo->productor->name }}</p>
                                    </td>
                                    <td colspan="2">
                                        <p class="text-xs font-weight-bold mb-0">Estado</p>
                                        <p class="text-xs text-secondary mb-0">{{ $orden->estado_oc->description }}
                                            @if ($orden->estado_oc->id == 2)
                                                <span class="badge badge-sm bg-gradient-warning">CONTROLLER</span>
                                            @elseif ($orden->estado_oc->id == 7)
                                                <span class="badge badge-sm bg-gradient-warning">TERCERO</span>
                                            @elseif ($orden->estado_oc->id == 5 && !($orden->cod_causal))
                                                <span class="badge badge-sm bg-gradient-warning">CONTABILIDAD</span>
                                            @elseif ($orden->estado_oc->id == 5 && $orden->cod_causal && !($orden->archivo_comprobante_pago))
                                                <span class="badge badge-sm bg-gradient-warning">TESORER&Iacute;A</span>
                                            @elseif ($orden->archivo_comprobante_pago)
                                                <span class="badge badge-sm bg-gradient-success">PAGADA</span>
                                            @elseif ($orden->estado_oc->id == 8)
                                                <span class="badge badge-sm bg-gradient-warning">LIDER PRODUCCIÓN</span>
                                            @elseif ($orden->estado_oc->id == 9)
                                                <span class="badge badge-sm bg-gradient-warning">GERENCIA</span>
                                            @endif
                                        </p>
                                    </td>
                                    <td class="d-flex align-items-center justify-content-center">
                                        <a class="btn bg-gradient-primary m-0 me-1 mb-1" href="{{ route('oc-natural', ['orden_id' => $orden->id]) }}" target="_blank">Ver</a>
                                    </td>
                                </tr>
                            @elseif ($orden->tipo_oc == 3)
                                <tr>
                                    <td style="width: 16rem;">
                                        <div class="d-flex px-2 py-1" title="{{ $orden->presupuesto->gestion->nom_proyecto_cot }}">
                                            <div>
                                                <img src="https://www.bullmarketing.com.co/wp-content/uploads/2022/04/cropped-favicon-bull-192x192.png" class="avatar avatar-sm me-3">
                                            </div>
                                            <div class="d-flex flex-column justify-content-center">
                                                @if (strlen($orden->presupuesto->gestion->nom_proyecto_cot) > 80)
                                                    <h6 class="mb-0 text-xs" >{{ substr($orden->presupuesto->gestion->nom_proyecto_cot, 0, 80) }}...</h6>
                                                @else
                                                    <h6 class="mb-0 text-xs" >{{ substr($orden->presupuesto->gestion->nom_proyecto_cot, 0, 80) }}</h6>
                                                @endif
                                                <p class="text-xs text-secondary mb-0">{{ $orden->presupuesto->gestion->contacto->empresa }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">Tipo</p>
                                        <span class="badge badge-sm badge-warning">{{ $orden->tipo->description }}</span>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">Proveedor</p>
                                        <span class="text-xs text-secondary mb-0">{{ $orden->proveedor->tercero }}</span>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">Centro de costos</p>
                                        <textarea disabled rows="1" class="text-xs text-secondary mb-0">{{ $orden->presupuesto->cod_cc }}</textarea>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">Fecha env&iacute;o (producci&oacute;n)</p>
                                        <p class="text-xs text-secondary mb-0">{{ $orden->fecha_envio_produccion }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">Productor</p>
                                        <p class="text-xs text-secondary mb-0">
                                            @if ($orden->presupuesto->productor_info)
                                                {{ $orden->presupuesto->productor_info->name }}
                                            @else
                                                NO ASIGNADO
                                            @endif
                                        </p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">Comercial</p>
                                        <p class="text-xs text-secondary mb-0">{{ $orden->presupuesto->gestion->comercial->name }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">Estado</p>
                                        <p class="text-xs text-secondary mb-0">{{ $orden->estado_oc->description }}</p>
                                    </td>
                                    <td class="d-flex align-items-center justify-content-center">
                                        <a class="btn bg-gradient-primary m-0 me-1 mb-1" href="{{ route('orden-nomina-lid', ['orden' => $orden->id]) }}" target="_blank">Ver</a>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                        <tr>
                            @php
                                $ordenesArray = $ordenes->toArray();
                                $registros_page = sizeof($ordenesArray['data']);
                                $total = $ordenesArray['total'];
                            @endphp
                            <td colspan="1" class="d-flex text-xs text-secondary mb-0">Mostrando {{ $registros_page }} registros de {{ $total }}.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="mb-3 ms-2">
                {{ $ordenes->links() }}
            </div>
        </div>
    </div>
@elseif(Auth::user()->rol == 7)
    @if ($tipo_oc == 2)
        {{-- ORDENES DE COMPRA - NATURALES --}}
        <div>
            <div class="card">
                <div class="card-header p-0 mx-3 mt-3 position-relative z-index-1 col-md-6">
                    <div class="row">
                        <div class="col-md-12">
                            <h3 class="mb-0">Ordenes de compra</h3>
                            <p class="text-sm mb-0">Lista de ordenes de compra a proveedores naturales.</p>
                        </div>
                        {{-- <div class="col-md-4">
                            <label for="comercial">Buscar:</label>
                            <input type="text" wire:model="cod_cc" class="form-control" placeholder="Centro de costos">
                        </div> --}}
                        <div class="col-md-2">
                            <label for="año">Año:</label>
                            <select wire:model="año" class="form-control">
                                <option value="">Seleccionar</option>
                                @foreach ($años as $año)
                                    <option value="{{ $año->id }}">{{ $año->description }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="filtro_fecha">Fecha:</label>
                            <select id="filtro_fecha" class="form-control" wire:model="fecha">
                                <option value="asc">Seleccionar</option>
                                <option value="asc">M&aacute;s antiguos</option>
                                <option value="desc">M&aacute;s recientes</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="estado">Estados:</label>
                            <select id="estado" class="form-control" wire:model="estado">
                                <option value="">Seleccionar</option>
                                @foreach ($estados as $estado)
                                    <option value="{{ $estado->id }}">{{ $estado->description }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th colspan="5" class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">DATOS DE PROYECTO</th>
                                <th colspan="2" class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">COMPARTIR</th>
                                <th colspan="1" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($ordenes as $key => $orden)
                                <tr>
                                    <td style="width: 16rem;">
                                        <div class="d-flex px-2 py-1" title="Orden #{{ $orden->id }}">
                                            <div>
                                                <img src="https://www.bullmarketing.com.co/wp-content/uploads/2022/04/cropped-favicon-bull-192x192.png" class="avatar avatar-sm me-3">
                                            </div>
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-xs" >Orden #{{ $orden->id }}</h6>
                                                <p class="text-xs text-secondary mb-0">{{ $orden->id }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">Tipo</p>
                                        <span class="badge badge-sm badge-info">{{ $orden->tipo->description }}</span>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">Tercero</p>
                                        <span class="text-xs text-secondary mb-0">{{ $orden->naturalInfo->tercero->nombre }} {{ $orden->naturalInfo->tercero->apellido }}</span>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">Estado</p>
                                        <p class="text-xs text-secondary mb-0">{{ $orden->estado_oc->description }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">Fecha</p>
                                        <p class="text-xs text-secondary mb-0">{{ $orden->created_at }}</p>
                                    </td>
                                    <td>
                                        <a class="btn btn-success m-0 me-1 mb-1"
                                        href="https://wa.me/{{ $orden->naturalInfo->tercero->telefono }}?text=¡Hola! Tu número de orden es: {{ $orden->id }}. Puedes seguir el estado de tu pago desde este enlace: {{ route('consulta-terceros') }}?orden={{ $orden->id }}. ¡Gracias por tus servicios y que tengas un día fabuloso! Bullmarketing.com.co"
                                        target="_blank">
                                            <i class="fa-brands fa-whatsapp"></i>
                                        </a>
                                    </td>
                                    <td>
                                        <div x-data="init">
                                            <p class="text-xs font-weight-bold mb-0"><a href="#" @click="copyToClipboard('{{ route('consulta-terceros') }}?orden={{ $orden->id }}')">Copiar enlace</a></p>
                                        </div>
                                    </td>
                                    <td class="d-flex align-items-center justify-content-center">
                                        <a class="btn bg-gradient-primary m-0 me-1 mb-1 position-relative" href="{{ route('orden-natural-prod', ['orden_id' => $orden->id]) }}">
                                            Ver
                                            @if ((!$orden->evidencias->isEmpty() || $orden->naturalInfo->contrato) && $orden->estado_id == 3)
                                                <span class="position-absolute top-0 start-100 translate-middle p-2 bg-danger border border-light rounded-circle">
                                                    <span class="visually-hidden">New alerts</span>
                                                </span>
                                            @endif
                                        </a>
                                        @if ($orden->naturalInfo->contrato)
                                            <button type="button" class="btn bg-gradient-secondary m-0 ms-1 mb-1 position-relative" data-bs-toggle="collapse" data-bs-target="#collapse{{ $orden->naturalInfo->id }}" aria-expanded="false" aria-controls="collapse" >
                                                Consultar @if (!$orden->evidencias->isEmpty()) evidencias @else respuestas @endif

                                                @if (!$orden->evidencias->isEmpty() && $orden->estado_id == 3)
                                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                                        {{ $orden->evidencias->count() }}
                                                        <span class="visually-hidden">unread messages</span>
                                                    </span>
                                                @endif
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                                @if ($orden->naturalInfo->contrato)
                                    <tr>
                                        <td colspan="8">
                                            <div class="collapse" id="collapse{{ $orden->naturalInfo->id }}">
                                                <div class="card card-body px-3 py-0">
                                                    <div class="table-responsive">
                                                        <table class="table align-items-center mb-0">
                                                            <thead>
                                                                <tr>
                                                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nombre</th>
                                                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Cédula</th>
                                                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Correo</th>
                                                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tel&eacute;fono</th>
                                                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Documentos</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td colspan="1">
                                                                        <p class="text-xs text-secondary mb-0">
                                                                            {{ $orden->naturalInfo->tercero->nombre }} {{ $orden->naturalInfo->tercero->apellido }}
                                                                        </p>
                                                                    </td>
                                                                    <td>
                                                                        <p class="text-xs text-secondary mb-0">
                                                                            {{ $orden->naturalInfo->tercero->cedula }}
                                                                        </p>
                                                                    </td>
                                                                    <td>
                                                                        <p class="text-xs text-secondary mb-0">
                                                                            {{ $orden->naturalInfo->tercero->correo }}
                                                                        </p>
                                                                    </td>
                                                                    <td>
                                                                        <p class="text-xs text-secondary mb-0">
                                                                            {{ $orden->naturalInfo->tercero->telefono }}
                                                                        </p>
                                                                    </td>
                                                                    <td>
                                                                        <p class="text-xs text-secondary mb-0">
                                                                            <a href="{{ asset(str_replace('public', 'storage', $orden->naturalInfo->tercero->cert_bancaria)) }}" target="_blank">Certificaci&oacute;n Bancaria</a><br>
                                                                            <a href="{{ asset(str_replace('public', 'storage', $orden->naturalInfo->tercero->rut)) }}" target="_blank">RUT</a><br>
                                                                            <a href="{{ asset(str_replace('public', 'storage', $orden->naturalInfo->tercero->planilla_aportes)) }}" target="_blank">Planilla de Aportes</a><br>
                                                                            <a href="{{ asset(str_replace('public', 'storage', $orden->naturalInfo->contrato)) }}" target="_blank">Contrato</a>
                                                                        </p>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    @if ($orden->evidencias)
                                                        <div class="table-responsive">
                                                            <table class="table align-items-center mb-0">
                                                                <thead>
                                                                    <tr>
                                                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Fecha</th>
                                                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Foto</th>
                                                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Observaciones</th>
                                                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Fecha subida</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach ($orden->evidencias as $key => $evidencia)
                                                                        @if ($key < $orden->ordenItems->sum('cant_oc'))
                                                                            <tr>
                                                                                <td>
                                                                                    <p class="text-xs text-secondary mb-0">
                                                                                        {{ $evidencia->fecha_evidencia }}
                                                                                    </p>
                                                                                </td>
                                                                                <td>
                                                                                    <p class="text-xs text-secondary mb-0">
                                                                                        <a href="{{ asset(str_replace("public", "storage", $evidencia->foto_evidencia)) }}" target="_blank">
                                                                                            <img src="{{ asset(str_replace("public", "storage", $evidencia->foto_evidencia)) }}" height="40">
                                                                                        </a>
                                                                                    </p>
                                                                                </td>
                                                                                <td>
                                                                                    <p class="text-xs text-secondary mb-0">
                                                                                        {{ $evidencia->observacion_evidencia }}
                                                                                    </p>
                                                                                </td>
                                                                                <td>
                                                                                    <p class="text-xs text-secondary mb-0">
                                                                                        {{ $evidencia->created_at }}
                                                                                    </p>
                                                                                </td>
                                                                            </tr>
                                                                        @else
                                                                            <tr>
                                                                                <td class="position-relative">
                                                                                    <p class="text-xs text-secondary mb-0">
                                                                                        {{ $evidencia->fecha_evidencia }}
                                                                                    </p>
                                                                                    <span class="position-absolute top-0 start-100 translate-middle p-2 bg-danger border border-light rounded-circle">
                                                                                        <span class="visually-hidden">New alerts</span>
                                                                                    </span>
                                                                                </td>
                                                                                <td>
                                                                                    <p class="text-xs text-secondary mb-0">
                                                                                        <a href="{{ asset(str_replace("public", "storage", $evidencia->foto_evidencia)) }}" target="_blank">
                                                                                            <img src="{{ asset(str_replace("public", "storage", $evidencia->foto_evidencia)) }}" height="40">
                                                                                        </a>
                                                                                    </p>
                                                                                </td>
                                                                                <td>
                                                                                    <p class="text-xs text-secondary mb-0">
                                                                                        {{ $evidencia->observacion_evidencia }}
                                                                                    </p>
                                                                                </td>
                                                                                <td>
                                                                                    <p class="text-xs text-secondary mb-0">
                                                                                        {{ $evidencia->created_at }}
                                                                                    </p>
                                                                                </td>
                                                                            </tr>
                                                                        @endif
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                            <tr>
                                @php
                                    $ordenesArray = $ordenes->toArray();
                                    $registros_page = sizeof($ordenesArray['data']);
                                    $total = $ordenesArray['total'];
                                @endphp
                                <td colspan="1" class="d-flex text-xs text-secondary mb-0">Mostrando {{ $registros_page }} registros de {{ $total }}.</td>
                                <td colspan="5" class="d-flex pt-0">{{ $ordenes->links() }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <script>
                function init() {
                    return {
                        copyToClipboard(text) {
                            navigator.clipboard.writeText(text).then(() => {
                                alert('Enlace copiado');
                            }, (err) => {
                                console.error('Error al copiar el texto: ', err);
                            });
                        }
                    }
                }
            </script>
        </div>
    @elseif ($tipo_oc == 3)
        {{-- ORDENES DE COMPRA - NÓMINA --}}
        <div>
            <div class="card">
                <div class="card-header p-0 mx-3 mt-3 position-relative z-index-1 col-md-6">
                    <div class="row">
                        <div class="col-md-12">
                            <h3 class="mb-0">Ordenes de nómina</h3>
                            <p class="text-sm mb-0">Lista de ordenes de nómina.</p>
                        </div>
                        {{-- <div class="col-md-4">
                            <label for="comercial">Buscar:</label>
                            <input type="text" wire:model="cod_cc" class="form-control" placeholder="Centro de costos">
                        </div> --}}
                        <div class="col-md-2">
                            <label for="año">Año:</label>
                            <select wire:model="año" class="form-control">
                                <option value="">Seleccionar</option>
                                @foreach ($años as $año)
                                    <option value="{{ $año->id }}">{{ $año->description }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="filtro_fecha">Fecha:</label>
                            <select id="filtro_fecha" class="form-control" wire:model="fecha">
                                <option value="asc">Seleccionar</option>
                                <option value="asc">M&aacute;s antiguos</option>
                                <option value="desc">M&aacute;s recientes</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="estado">Estados:</label>
                            <select id="estado" class="form-control" wire:model="estado">
                                <option value="">Seleccionar</option>
                                @foreach ($estados as $estado)
                                    <option value="{{ $estado->id }}">{{ $estado->description }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th colspan="5" class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">DATOS DE PROYECTO</th>
                                <th colspan="2" class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">COMPARTIR</th>
                                <th colspan="1" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($ordenes as $key => $orden)
                                <tr>
                                    <td style="width: 16rem;">
                                        <div class="d-flex px-2 py-1" title="{{ $orden->presupuesto->gestion->nom_proyecto_cot }}">
                                            <div>
                                                <img src="https://www.bullmarketing.com.co/wp-content/uploads/2022/04/cropped-favicon-bull-192x192.png" class="avatar avatar-sm me-3">
                                            </div>
                                            <div class="d-flex flex-column justify-content-center">
                                                @if (strlen($orden->presupuesto->gestion->nom_proyecto_cot) > 80)
                                                    <h6 class="mb-0 text-xs" >{{ substr($orden->presupuesto->gestion->nom_proyecto_cot, 0, 80) }}...</h6>
                                                @else
                                                    <h6 class="mb-0 text-xs" >{{ substr($orden->presupuesto->gestion->nom_proyecto_cot, 0, 80) }}</h6>
                                                @endif
                                                <p class="text-xs text-secondary mb-0">{{ $orden->presupuesto->gestion->contacto->empresa }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">Tipo</p>
                                        <span class="badge badge-sm badge-warning">{{ $orden->tipo->description }}</span>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">Proveedor</p>
                                        <span class="text-xs text-secondary mb-0">{{ $orden->proveedor->tercero }}</span>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">Centro de costos</p>
                                        <textarea disabled rows="1" class="text-xs text-secondary mb-0">{{ $orden->presupuesto->cod_cc }}</textarea>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">Fecha env&iacute;o (producci&oacute;n)</p>
                                        <p class="text-xs text-secondary mb-0">{{ $orden->fecha_envio_produccion }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">Productor</p>
                                        <p class="text-xs text-secondary mb-0">
                                            @if ($orden->presupuesto->productor_info)
                                                {{ $orden->presupuesto->productor_info->name }}
                                            @else
                                                NO ASIGNADO
                                            @endif
                                        </p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">Comercial</p>
                                        <p class="text-xs text-secondary mb-0">{{ $orden->presupuesto->gestion->comercial->name }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">Estado</p>
                                        <p class="text-xs text-secondary mb-0">{{ $orden->estado_oc->description }}</p>
                                    </td>
                                    <td class="d-flex align-items-center justify-content-center">
                                        <a class="btn bg-gradient-primary m-0 me-1 mb-1" href="{{ route('orden-nomina-prod', ['orden_id' => $orden->id]) }}">Ver</a>
                                    </td>
                                </tr>
                            @endforeach
                            <tr>
                                @php
                                    $ordenesArray = $ordenes->toArray();
                                    $registros_page = sizeof($ordenesArray['data']);
                                    $total = $ordenesArray['total'];
                                @endphp
                                <td colspan="1" class="d-flex text-xs text-secondary mb-0">Mostrando {{ $registros_page }} registros de {{ $total }}.</td>
                                <td colspan="5" class="d-flex pt-0">{{ $ordenes->links() }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
@endif
