<div class="card">
    <!-- Card Header: Título y Filtro de Búsqueda -->
    <div class="card-header p-0 px-3 mt-3">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h3 class="mb-0">Lista de Anticipos</h3>
                <p class="text-sm mb-0">Gestión y consulta de anticipos registrados.</p>
            </div>
            <div class="col-md-4 mb-0">
                <label for="search">Buscar:</label>
                <input type="text" 
                       id="search" 
                       wire:model.debounce.300ms="search" 
                       class="form-control" 
                       placeholder="Buscar por ID o N° Orden...">
            </div>
        </div>
    </div>

    <!-- Card Body: Tabla de Contenido -->
    <div class="card-body p-0 mt-3">
        <div class="table-responsive">
            <table class="table mb-0 align-items-center">
                <thead>
                    <tr>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">DATOS DE ORDEN / ANTICIPO</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">FECHA SOLICITUD</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">MONTO</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">TIPO DE ANTICIPO</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">ESTADO</th>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ACCIONES</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ordenes as $anticipo)
                        <tr>
                            <!-- Datos del Anticipo / Orden -->
                            <td style="min-width: 14rem;">
                                <div class="d-flex px-2 py-1">
                                    <div>
                                        <img src="https://www.bullmarketing.com.co/wp-content/uploads/2022/04/cropped-favicon-bull-192x192.png" 
                                            class="avatar avatar-sm me-3" 
                                            alt="avatar">
                                    </div>
                                    <div class="d-flex flex-column justify-content-center">
                                        <h6 class="mb-0 text-xs">
                                            Anticipo #{{ $anticipo->id }}
                                        </h6>
                                        @if ($anticipo->ordenCompra?->presupuesto?->cod_cc)
                                            <p class="text-xs text-secondary mb-0">
                                                {{ $anticipo->ordenCompra->presupuesto->cod_cc }}
                                            </p>
                                        @elseif ($anticipo->ordenCompra?->id)
                                            <p class="text-xs text-secondary mb-0">
                                                OC #{{ $anticipo->ordenCompra->id }}
                                            </p>
                                        @else
                                            <p class="text-xs text-secondary mb-0">
                                                Sin información
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Fecha Solicitud -->
                            <td>
                                <p class="text-xs font-weight-bold mb-0">
                                    {{ $anticipo->created_at ? $anticipo->created_at->format('d/m/Y H:i') : 'N/A' }}
                                </p>
                            </td>

                            <!-- Monto -->
                            <td>
                                <p class="text-xs font-weight-bold mb-0">
                                    $ {{ number_format($anticipo->total_anticipo ?? 0, 2) }}
                                </p>
                            </td>

                            <!-- Tipo de Anticipo -->
                            <td>
                                <p class="text-xs text-secondary mb-0">
                                    {{ $anticipo->ordenCompra?->tipo?->description ?? 'N/A' }}
                                </p>
                            </td>

                            <!-- Estado -->
                            <td>
                                <span class="text-xs text-secondary mb-0">
                                    {{ $anticipo->estado->description ?? ($anticipo->estado->nombre ?? 'Sin Estado') }}
                                </span>
                            </td>

                            <!-- Acciones -->
                            <td class="align-middle text-center">
                                <a class="btn btn-sm bg-gradient-primary m-0" 
                                    >
                                    Ver Detalle
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <p class="text-xs text-secondary mb-0">No se encontraron anticipos registrados.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer: Resumen de Resultados -->
        <div class="row p-3">
            <div class="col-md-6">
                <span class="text-xs text-secondary mb-0">
                    Total de registros: {{ count($ordenes) }}
                </span>
            </div>
        </div>
    </div>
</div>