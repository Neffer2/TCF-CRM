<div>
    <div class="card">
        <div class="card-header p-0 mx-3 mt-3 position-relative z-index-1 col-md-6">
            <div class="row">
                <div class="col-md-12">
                    <h3 class="mb-0">Anticipos</h3>
                    <p class="text-sm mb-0">Lista de anticipos creados.</p>
                </div>
                {{-- <div class="col-md-2">
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
                </div> --}}
            </div>
        </div> 
        <div class="table-responsive">
            <table class="table align-items-center mb-0">
                <thead>
                    <tr>
                        <th colspan="8" class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">DATOS DEL ANTICIPO</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($anticipos as $key => $anticipo)
                        <tr>
                            <td style="width: 16rem;">
                                <div class="d-flex px-2 py-1" title="Orden #{{ $anticipo->id }}">
                                    <div>
                                        <img src="https://www.bullmarketing.com.co/wp-content/uploads/2022/04/cropped-favicon-bull-192x192.png" class="avatar avatar-sm me-3">
                                    </div>
                                    <div class="d-flex flex-column justify-content-center">
                                        <h6 class="mb-0 text-xs">Anticipo #{{ $anticipo->id }}</h6>
                                        <p class="text-xs text-secondary mb-0">Orden de Compra #{{ $anticipo->ordenCompra->id }}</p>
                                    </div>
                                </div>
                            </td>
                            {{-- <td>
                                <p class="text-xs font-weight-bold mb-0">Tipo</p>
                                <span class="badge badge-sm badge-info">{{ $anticipo->ordenCompra->tipo->description }}</span>
                            </td> --}}
                            @if ($anticipo->ordenCompra->tipo)
                                <td>
                                    <p class="text-xs font-weight-bold mb-0">Proveedor</p>
                                    <span class="text-xs text-secondary mb-0">{{ $anticipo->ordenCompra->proveedor->tercero }}</span>
                                </td>    
                            @else
                                {{-- <td>
                                    <p class="text-xs font-weight-bold mb-0">Tercero</p>
                                    <span class="text-xs text-secondary mb-0">{{ $anticipo->ordenCompra->naturalInfo->tercero->nombre }} {{ $anticipo->ordenCompra->naturalInfo->tercero->apellido }}</span>
                                </td> --}}
                            @endif
                            <td>
                                <p class="text-xs font-weight-bold mb-0">Valor Orden</p>
                                <p class="text-xs text-secondary mb-0">${{ number_format($anticipo->ordenCompra->ordenItems->sum('vtotal_oc'), 0, ',', '.') }}</p>
                            </td>
                            <td>
                                <p class="text-xs font-weight-bold mb-0">Porcentaje Anticipo</p>
                                <p class="text-xs text-secondary mb-0">% {{ number_format($anticipo->porcentaje_anticipo, 0, ',', '.') }}</p>
                            </td>
                            <td>
                                <p class="text-xs font-weight-bold mb-0">Valor Anticipo</p>
                                <p class="text-xs text-secondary mb-0">${{ number_format($anticipo->total_anticipo, 0, ',', '.') }}</p>
                            </td>
                            <td>
                                <p class="text-xs font-weight-bold mb-0">Estado</p>
                                <p class="text-xs text-secondary mb-0">{{ $anticipo->estado->description }}</p>
                            </td>
                            <td>
                                <p class="text-xs font-weight-bold mb-0">Fecha</p>
                                <p class="text-xs text-secondary mb-0">{{ $anticipo->fecha_solicitud }}</p>
                            </td>
                            <td class="">
                                <a href="{{ route('anticipo-admin', ['anticipo_id' => $anticipo->id]) }}" class="btn bg-gradient-primary mb-0" title="Ver detalle" data-toggle="tooltip" target="_blank">
                                    Ver
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    <tr>
                        @php
                            $anticiposArray = $anticipos->toArray();
                            $registros_page = sizeof($anticiposArray['data']);
                            $total = $anticiposArray['total'];
                        @endphp
                        <td colspan="1" class="d-flex text-xs text-secondary mb-0">Mostrando {{ $registros_page }} registros de {{ $total }}.</td>
                        <td colspan="5" class="d-flex pt-0">{{ $anticipos->links() }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

