<div>
    <div class="card">
        <div class="card-header p-0 mx-3 mt-3 position-relative z-index-1">
            <div class="row">
                <div class="col-md-12">
                    <h3 class="mb-0">Ordenes de compra</h3>
                    <p class="text-sm mb-0">Lista de ordenes de compra <b>COMPROBADAS</b>.</p>
                </div>
            </div>
            <div class="row">
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
                    <div class="col-md-1">
                        <label for="filtro_fecha">Fecha:</label>
                        <select id="filtro_fecha" class="form-control" wire:model="fecha">
                            <option value="asc">Seleccionar</option>
                            <option value="asc">M&aacute;s antiguos</option>
                            <option value="desc">M&aacute;s recientes</option>
                        </select>
                    </div>
                    <div class="col-md-1"> 
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
        </div>
        <div class="table-responsive">
            <table class="table align-items-center mb-0">
                <thead>
                    <tr>
                        <th colspan="1" class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">DATOS DE PROYECTO</th>
                        <th colspan="6" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">M&eacute;tricas</th>
                        <th colspan="1" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Acciones</th>
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
                                <p class="text-xs font-weight-bold mb-0">Centro de costos</p>
                                <textarea disabled rows="1" class="text-xs text-secondary mb-0">{{ $orden->presupuesto->cod_cc }}</textarea>
                            </td>
                            <td>
                                <p class="text-xs font-weight-bold mb-0">Proveedor</p>
                                <span class="text-xs text-secondary mb-1">{{ $orden->proveedor->tercero }} </span>
                                <p class="text-xs font-weight-bold mb-0">Nit</p>
                                <span class="text-xs text-secondary mb-0">{{ $orden->proveedor->documento }} </span>
                            </td>
                            <td>
                                <p class="text-xs font-weight-bold mb-0">Comercial</p>
                                <p class="text-xs text-secondary mb-1">{{ $orden->presupuesto->gestion->comercial->name }}</p>
                                <p class="text-xs font-weight-bold mb-0">Productor</p>
                                @if($orden->presupuesto->productor_info) <p class="text-xs text-secondary mb-0">{{ $orden->presupuesto->productor_info->name }}</p> @endif
                            </td>
                            <td>
                                <p class="text-xs font-weight-bold mb-0">Good Receive (GR)</p>
                                <p class="text-xs text-secondary mb-1">{{ $orden->gr }}</p>

                                <p class="text-xs font-weight-bold mb-0">Causaci&oacute;n</p>
                                <p class="text-xs text-secondary mb-1">{{ $orden->cod_causal }}</p>
                                {{-- <p class="text-xs font-weight-bold mb-0">Estado</p>
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
                                    @endif
                                </p> --}}
                            </td>
                            {{-- <td>
                                <p class="text-xs font-weight-bold mb-0">Fecha env&iacute;o (Producci&oacute;n)</p>
                                <p class="text-xs text-secondary mb-0">{{ $orden->fecha_envio_produccion }}</p>
                            </td> --}}
                            <td>
                                <p class="text-xs font-weight-bold mb-0">Fecha aprobaci&oacute;n (Controller)</p>
                                <p class="text-xs text-secondary mb-0">{{ $orden->fecha_aprobacion }}</p>
                            </td>
                            <td class="d-flex align-items-center justify-content-center">
                                @if (!$orden->archivo_comprobante_pago)
                                    @if (Auth::user()->rol == 8)
                                        <a class="btn bg-gradient-primary m-0 me-1 mb-1" href="{{ route('anticipo', ['orden' => $orden->id]) }}">Pagar</a>
                                    @else
                                        <a class="btn bg-gradient-primary m-0 me-1 mb-1" href="{{ route('anticipo-contabilidad', ['orden' => $orden->id]) }}">Causar</a>
                                    @endif
                                @else
                                    @php
                                        $archivo_comprobante_pago = str_replace('public/', '', $orden->archivo_comprobante_pago);
                                    @endphp
                                    <a class="btn bg-gradient-secondary m-0 me-1 mb-1" href="{{ asset("storage/$archivo_comprobante_pago") }}" target="_blank">PAGADO</a>
                                @endif
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
                                <p class="text-xs font-weight-bold mb-0">Proveedor</p>
                                <span class="text-xs text-secondary mb-1">{{ $orden->naturalInfo->tercero->nombre }} {{ $orden->naturalInfo->tercero->apellido }}</span>
                                <p class="text-xs font-weight-bold mb-0">Documento</p>
                                <span class="text-xs text-secondary mb-1">{{ $orden->naturalInfo->tercero->cedula }}</span>
                            </td>
                            {{-- <td>
                                <p class="text-xs font-weight-bold mb-0">Fecha</p>
                                <p class="text-xs text-secondary mb-0">{{ $orden->created_at }}</p>
                            </td> --}}
                            <td>
                                <p class="text-xs font-weight-bold mb-0">Productor</p>
                                <p class="text-xs text-secondary mb-0">{{ $orden->naturalInfo->productor->name }}</p> 
                            </td>
                            {{-- <td>
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
                                    @endif
                                </p>
                            </td> --}}
                            {{-- <td>
                                <p class="text-xs font-weight-bold mb-0">Fecha env&iacute;o (Producci&oacute;n)</p>
                                <p class="text-xs text-secondary mb-0">{{ $orden->fecha_envio_produccion }}</p>
                            </td> --}}
                            <td>
                                <p class="text-xs font-weight-bold mb-0">Causaci&oacute;n</p>
                                <p class="text-xs text-secondary mb-1">{{ $orden->cod_causal }}</p>
                            </td>
                            <td>
                                <p class="text-xs font-weight-bold mb-0">Fecha aprobaci&oacute;n (Controller)</p>
                                <p class="text-xs text-secondary mb-0">{{ $orden->fecha_aprobacion }}</p>
                            </td>
                            <td class="d-flex align-items-center justify-content-center">
                                @if (!$orden->archivo_comprobante_pago)
                                    @if (Auth::user()->rol == 8)
                                        <a class="btn bg-gradient-primary m-0 me-1 mb-1" href="{{ route('anticipo', ['orden' => $orden->id]) }}">Pagar</a>
                                    @else
                                        <a class="btn bg-gradient-primary m-0 me-1 mb-1" href="{{ route('anticipo-contabilidad', ['orden' => $orden->id]) }}">Causar</a>
                                    @endif
                                @else
                                    @php
                                        $archivo_comprobante_pago = str_replace('public/', '', $orden->archivo_comprobante_pago);
                                    @endphp
                                    <a class="btn bg-gradient-secondary m-0 me-1 mb-1" href="{{ asset("storage/$archivo_comprobante_pago") }}" target="_blank">PAGADO</a>
                                @endif
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
        <div class="row p-2">
            <div class="col-md-12">
                <button wire:click="reporteExcel" class="btn bg-gradient-warning" wire:loading.attr="disabled">Generar Reporte</button>
                <div class="spinner-border text-warning ms-1" role="status" wire:loading>
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
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
