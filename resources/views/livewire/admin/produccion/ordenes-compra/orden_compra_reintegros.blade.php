<div class="card my-4">
    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Reintegros por Excedente en Legalizaciones</h6>

        <!-- Buscador dinámico por CC -->
        <div class="w-30">
            <input type="text"
                   class="form-control form-control-sm"
                   placeholder="Filtrar por CC..."
                   wire:model.live.debounce.300ms="searchCC">
        </div>
    </div>

    <div class="card-body px-0 pt-0 pb-2">
        <div class="table-responsive p-0">
            <table class="table align-items-center mb-0">
                <thead>
                <tr>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">OC / CC</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Productor</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Monto OC</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Total Legalizado</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Valor a Reintegrar</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Acción</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($anticiposPendientes as $item)
                    @php
                        $montoOC = $item->ordenItems->sum('vtotal_oc');
                        $montoLegalizado = $item->ordenItems->sum('monto_legalizado');
                        $valorReintegro = $montoLegalizado - $montoOC;
                        $isExpanded = ($expandedOcId === $item->id);
                    @endphp

                        <!-- FILA PRINCIPAL -->
                    <tr class="{{ $isExpanded ? 'bg-light' : '' }}">
                        <td>
                            <div class="d-flex px-3 py-1">
                                <div class="d-flex flex-column justify-content-center">
                                    <h6 class="text-xs text-secondary mb-0">CC: {{ $item->presupuesto->cod_cc ?? 'S/C' }}</h6>
                                </div>
                            </div>
                        </td>
                        <td>
                            <p class="text-xs font-weight-bold mb-0">
                                {{ $item->presupuesto->productor_info->name ?? 'Sin Productor' }}
                            </p>
                        </td>
                        <td class="align-middle text-center text-sm">
                            <span class="text-xs font-weight-bold">${{ number_format($montoOC, 2) }}</span>
                        </td>
                        <td class="align-middle text-center text-sm">
                            <span class="text-xs font-weight-bold text-info">${{ number_format($montoLegalizado, 2) }}</span>
                        </td>
                        <td class="align-middle text-center text-sm">
                                <span class="badge badge-sm bg-gradient-danger">
                                    ${{ number_format($valorReintegro, 2) }}
                                </span>
                        </td>
                        <td class="align-middle text-center">
                            <button type="button"
                                    class="btn btn-xs {{ $isExpanded ? 'btn-secondary' : 'bg-gradient-info' }} mb-0"
                                    wire:click="toggleDetalle({{ $item->id }})">
                                <i class="fas {{ $isExpanded ? 'fa-chevron-up' : 'fa-chevron-down' }} me-1"></i>
                                {{ $isExpanded ? 'Ocultar' : 'Ver Detalle' }}
                            </button>
                        </td>
                    </tr>

                    <!-- FILA DESPLEGABLE (DETALLE DE ÍTEMS LEGALIZADOS) -->
                    @if($isExpanded)
                        <tr>
                            <td colspan="6" class="p-3 bg-gray-100">
                                <div class="card card-body shadow-none border mb-0">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="text-xs font-weight-bold text-uppercase mb-0">
                                            <i class="fas fa-list me-1"></i> Detalle de Ítems Legalizados (#{{ $item->cod_oc ?? $item->id }})
                                        </h6>
                                        <span class="text-xs text-muted">
                                                Productor: <strong>{{ $item->presupuesto->productor_info->name ?? 'N/A' }}</strong> | CC: <strong>{{ $item->presupuesto->cod_cc ?? 'N/A' }}</strong>
                                            </span>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-sm align-items-center mb-0 bg-white rounded">
                                            <thead>
                                            <tr class="bg-light">
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Concepto / Descripción</th>
                                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Valor Aprobado OC</th>
                                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Valor Total Legalizado</th>
                                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Excedente Ítem</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($item->ordenItems as $subItem)
                                                @php
                                                    $diferenciaItem = $subItem->monto_legalizado - $subItem->vtotal_oc;
                                                @endphp
                                                <tr>
                                                    <td class="text-xs ps-3">
                                                        <span class="font-weight-bold">{{ $subItem->desc_oc ?? $subItem->concepto }}</span>
                                                    </td>
                                                    <td class="text-xs text-center">
                                                        ${{ number_format($subItem->vtotal_oc, 2) }}
                                                    </td>
                                                    <td class="text-xs text-center font-weight-bold text-success">
                                                        ${{ number_format($subItem->monto_legalizado, 2) }}
                                                    </td>
                                                    <td class="text-xs text-center font-weight-bold {{ $diferenciaItem > 0 ? 'text-danger' : 'text-muted' }}">
                                                        {{ $diferenciaItem > 0 ? '+$' . number_format($diferenciaItem, 2) : '$0.00' }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endif

                @empty
                    <tr>
                        <td colspan="6" class="text-center text-xs text-muted py-4">
                            No hay reintegros pendientes por legalizaciones.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
