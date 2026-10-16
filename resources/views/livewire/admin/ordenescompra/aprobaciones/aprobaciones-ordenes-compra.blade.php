<div>
    @php
        // ===== Usuarios (por ID) que ven cada estado: agrega aquí los IDs =====
        $usuariosPorEstado = [
            9  => [2],     // estos usuarios controller solo ven estado_id = 9
            10 => [9],     // estos usuarios lider comercial solo ven estado_id = 10
            11 => [10],     // estos usuarios gerencia solo ven estado_id = 11
        ];

        $userId = Auth::user()->id;

        // Estados que le corresponden al usuario actual (vacío = usuario normal, ve todo)
        $estadosVisibles = [];
        foreach ($usuariosPorEstado as $estadoId => $ids) {
            if (Auth::user()->rol == 1 && in_array($userId, $ids)) {
                $estadosVisibles[] = $estadoId;
            }
        }

        $esEspecial = !empty($estadosVisibles);
    @endphp

    <div class="card">
        <!-- Encabezado de la Tarjeta -->
        <div class="card-header pb-0 pt-3 px-3">
            <div class="row">
                <div class="col-md-12">
                    <h3 class="mb-0">Órdenes de compra</h3>
                    <p class="text-sm text-muted mb-3">Lista de órdenes de compra</p>
                </div>
            </div>

            <!-- Filtros de Búsqueda -->
            <div class="row g-2 mb-3 align-items-end">
                <div class="col-6 col-md-2 col-lg-1">
                    <label for="año" class="form-label text-xs font-weight-bold mb-1">Año:</label>
                    <select id="año" wire:model="año" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        @foreach ($años as $año)
                            <option value="{{ $año->id }}">{{ $año->description }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-2 col-lg-1">
                    <label for="cod_oc" class="form-label text-xs font-weight-bold mb-1">COD OC:</label>
                    <input type="text" id="cod_oc" wire:model="cod_oc" class="form-control form-control-sm" placeholder="Código">
                </div>

                <div class="col-6 col-md-3 col-lg-2">
                    <label for="cod_cc" class="form-label text-xs font-weight-bold mb-1">Centro de Costos:</label>
                    <input type="text" id="cod_cc" wire:model="cod_cc" class="form-control form-control-sm" placeholder="Buscar CC...">
                </div>

                <div class="col-6 col-md-3 col-lg-2">
                    <label for="documento" class="form-label text-xs font-weight-bold mb-1">Documento Tercero:</label>
                    <input type="text" id="documento" wire:model="documento" class="form-control form-control-sm" placeholder="Doc. tercero">
                </div>

                <div class="col-12 col-md-4 col-lg-2">
                    <label for="productor" class="form-label text-xs font-weight-bold mb-1">Productor:</label>
                    <select id="productor" class="form-select form-select-sm" wire:model="productor">
                        <option value="">Seleccionar</option>
                        @foreach ($productores as $productor)
                            <option value="{{ $productor->id }}">{{ $productor->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-4 col-lg-2">
                    <label for="estado" class="form-label text-xs font-weight-bold mb-1">Estado:</label>
                    <select id="estado" class="form-select form-select-sm" wire:model="estado">
                        <option value="">Seleccionar</option>
                        @foreach ($estados as $estado)
                            @if (!$esEspecial || in_array($estado->id, $estadosVisibles))
                                <option value="{{ $estado->id }}">{{ $estado->description }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-4 col-lg-2">
                    <label for="Tipo" class="form-label text-xs font-weight-bold mb-1">Tipo:</label>
                    <select id="Tipo" class="form-select form-select-sm" wire:model="tipo">
                        <option value="">Seleccionar</option>
                        @foreach ($tipos as $tipo)
                            <option value="{{ $tipo->id }}">{{ $tipo->description }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <!-- Tabla de Resultados -->
        <div class="card-body px-0 pt-0 pb-2">
            <div class="table-responsive p-0">
                <table class="table align-items-center mb-0">
                    <thead>
                        <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3" style="width: 22%;">Proyecto / Tercero</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tipo</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Proveedor / Tercero</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Centro Costos / Fecha Gen.</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Fecha Envío (Prod.)</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Productor</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Comercial</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Estado</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($ordenes as $orden)
                            @continue($esEspecial && !in_array($orden->estado_id, $estadosVisibles))
                            @if ($orden->tipo_oc == 1)
                                <tr>
                                    <td class="ps-3">
                                        <div class="d-flex px-2 py-1" title="{{ $orden->presupuesto->gestion->nom_proyecto_cot }}">
                                            <div>
                                                <img src="https://www.bullmarketing.com.co/wp-content/uploads/2022/04/cropped-favicon-bull-192x192.png" class="avatar avatar-sm me-3 border-radius-lg" alt="logo">
                                            </div>
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-xs text-truncate" style="max-width: 220px;">
                                                    {{ $orden->presupuesto->gestion->nom_proyecto_cot }}
                                                </h6>
                                                <p class="text-xs text-secondary mb-0">{{ $orden->presupuesto->gestion->contacto->empresa }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        <span class="badge badge-sm bg-gradient-primary">{{ $orden->tipo->description }}</span>
                                    </td>
                                    <td class="align-middle">
                                        <span class="text-xs font-weight-bold mb-0 text-secondary">{{ $orden->proveedor->tercero }}</span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="badge bg-light text-dark text-xs">{{ $orden->presupuesto->cod_cc }}</span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="text-xs text-secondary mb-0">{{ $orden->fecha_envio_produccion }}</span>
                                    </td>
                                    <td class="align-middle">
                                        <span class="text-xs text-secondary mb-0">
                                            {{ $orden->presupuesto->productor_info->name ?? 'NO ASIGNADO' }}
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        <span class="text-xs text-secondary mb-0">{{ $orden->presupuesto->gestion->comercial->name }}</span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="text-xs font-weight-bold text-secondary">{{ $orden->estado_oc->description }}</span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <a class="btn btn-sm bg-gradient-primary mb-0 px-3" href="{{ route('orden-juridica-ad', ['orden' => $orden->id]) }}" target="_blank">Ver</a>
                                    </td>
                                </tr>
                            @elseif($orden->tipo_oc == 2)
                                <tr>
                                    <td class="ps-3">
                                        <div class="d-flex px-2 py-1" title="Orden #{{ $orden->id }}">
                                            <div>
                                                <img src="https://www.bullmarketing.com.co/wp-content/uploads/2022/04/cropped-favicon-bull-192x192.png" class="avatar avatar-sm me-3 border-radius-lg" alt="logo">
                                            </div>
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-xs">Orden #{{ $orden->id }}</h6>
                                                <p class="text-xs text-secondary mb-0">ID: {{ $orden->id }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        <span class="badge badge-sm bg-gradient-info">{{ $orden->tipo->description }}</span>
                                    </td>
                                    <td class="align-middle">
                                        <span class="text-xs font-weight-bold mb-0 text-secondary">
                                            {{ $orden->naturalInfo->tercero->nombre }} {{ $orden->naturalInfo->tercero->apellido }}
                                        </span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="text-xs text-secondary mb-0">{{ $orden->created_at }}</span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="text-xs text-secondary mb-0">{{ $orden->fecha_envio_produccion }}</span>
                                    </td>
                                    <td class="align-middle">
                                        <span class="text-xs text-secondary mb-0">{{ $orden->naturalInfo->productor->name }}</span>
                                    </td>
                                    <td class="align-middle">
                                        <span class="text-xs text-muted mb-0">N/A</span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <div class="d-flex flex-column align-items-center">
                                            <span class="text-xs text-secondary mb-1">{{ $orden->estado_oc->description }}</span>
                                        </div>
                                    </td>
                                    <td class="align-middle text-center">
                                        <a class="btn btn-sm bg-gradient-primary mb-0 px-3" href="{{ route('orden-natural-ad', ['orden_id' => $orden->id]) }}" target="_blank">Ver</a>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Footer con Paginación e Información -->
            <div class="d-flex justify-content-between align-items-center px-3 pt-3">
                @php
                    $ordenesArray = $ordenes->toArray();
                    $registros_page = count($ordenesArray['data'] ?? []);
                    $total = $ordenesArray['total'] ?? 0;
                @endphp
                <p class="text-xs text-secondary mb-0">Mostrando {{ $registros_page }} registros de {{ $total }}.</p>
                <div>
                    {{ $ordenes->links() }}
                </div>
            </div>
        </div>
    </div>
</div>