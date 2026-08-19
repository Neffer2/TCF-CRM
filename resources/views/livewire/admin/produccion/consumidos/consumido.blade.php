<div x-data>
    <div class="card card-frame p-2">
        <div class="row justify-content-md-center">
            <div class="col-md-3">
                <div class="card">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <tr>
                                <td class="font-weight-bold font-table">MARGEN GENERAL</td>
                                <td class="font-table">{{ number_format($presupuesto->margen_general, 4) }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold font-table">VENTA PROYECTO</td>
                                <td class="font-table">{{ number_format($presupuesto->margen_proy) }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold font-table">COSTOS DEL PROYECTO</td>
                                <td class="font-table">{{ number_format($presupuesto->costos_proy) }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold font-table">MARGEN DEL PROYECTO</td>
                                <td class="font-table">{{ number_format($presupuesto->margen_proy, 2) }} %</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold font-table">MARGEN BRUTO (PESOS)</td>
                                <td class="font-table">{{ number_format($presupuesto->margen_bruto) }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <tr>
                                <td class="font-weight-bold font-table">CONTACTO</td>
                                <td class="font-table">
                                    {{ $presupuesto->gestion->contacto->nombre }} {{ $presupuesto->gestion->contacto->apellido }}
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold font-table">CLIENTE</td>
                                <td class="font-table">
                                    {{ $presupuesto->gestion->contacto->empresa }}
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold font-table">PROYECTO</td>
                                <td class="font-table">
                                    {{ $presupuesto->gestion->nom_proyecto_cot }}
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold font-table">CIUDAD</td>
                                <td class="font-table">
                                    {{ $presupuesto->gestion->contacto->ciudad }}
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <tr>
                                <td class="font-weight-bold font-table">IMPREVISTOS</td>
                                <td class="font-table">
                                    <input type="text" disabled value="{{ $presupuesto->imprevistos }}">
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold font-table">ADMINISTRACI&Oacute;N</td>
                                <td class="font-table">
                                    <input type="text" disabled value="{{ $presupuesto->administracion }}">
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold font-table">FEE AGENCIA</td>
                                <td class="font-table">
                                    <input type="text" disabled value="{{ $presupuesto->fee }}">
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold font-table">TIEMPO</td>
                                <td class="font-table">
                                    <input type="text" disabled value="{{ $presupuesto->tiempo_factura }}">
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <tr>
                                <td class="font-weight-bold font-table">NOTAS</td>
                                <td class="font-table">
                                    <textarea wire:model.lazy="notas" cols="55" rows="8" disabled>{{ $presupuesto->notas }}</textarea>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="table-responsive mt-2 rounded bg-white">
        <table class="table">
            <thead>
                <tr>
                    <th class="font-weight-bold font-table bg-gradient-info text-white" >COD</th>

                    <th class="font-weight-bold font-table bg-gradient-warning text-white">ITEM</th>
                    <th class="font-weight-bold font-table bg-gradient-warning text-white">CANTIDAD</th>
                    <th class="font-weight-bold font-table bg-gradient-warning text-white">DIA</th>
                    <th class="font-weight-bold font-table bg-gradient-warning text-white">OTROS</th>
                    <th class="font-weight-bold font-table bg-gradient-warning text-white">DESCRIPCION</th>
                    <th class="font-weight-bold font-table bg-gradient-warning text-white">V. UNITARIO</th>
                    <th class="font-weight-bold font-table bg-gradient-warning text-white">V. TOTAL</th>
                    <th class="font-weight-bold font-table bg-gradient-warning text-white">PROVEEDOR</th>
                    {{-- <th class="font-weight-bold font-table bg-gradient-warning text-white">UTILIDAD</th> --}}

                    {{-- <th class="font-weight-bold font-table bg-gradient-success text-white">MES</th> --}}
                    <th class="font-weight-bold font-table bg-gradient-success text-white">DIAS</th>
                    <th class="font-weight-bold font-table bg-gradient-success text-white">CIUDAD</th>

                    <th colspan="2" class="font-weight-bold font-table bg-gradient-primary text-white">ACCIONES</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($presupuesto->presupuestoItems as $key => $item)
                    @php
                        // Inicializar acumuladores por ítem
                        $cont_cant_oc = 0;
                        $cont_dias_oc = 0;
                        $cont_otros_oc = 0;
                        $acum_total_oc = 0;

                        if (count($item->consumidos) > 0) {
                            foreach ($item->consumidos as $consumido) {
                                if ($consumido->OrdenCompra && $consumido->OrdenCompra->estado_id != 6) {
                                    $cont_cant_oc += $consumido->cant_oc;
                                    $cont_dias_oc += $consumido->dias_oc;
                                    $cont_otros_oc += $consumido->otros_oc;
                                    $acum_total_oc += $consumido->vtotal_oc;
                                }
                            }
                        }

                        // 1. Condición para determinar si el ítem ya está consumido al 100% (Fila Roja)
                        $estaAgotado = count($item->consumidos) > 0 && (
                            ($item->cantidad - $cont_cant_oc <= 0) || 
                            ($item->v_total - $acum_total_oc <= 0)
                        );

                        // 2. Saldo restante disponible en el presupuesto
                        $saldoRestante = $item->v_total - $acum_total_oc;
                        
                        // 3. Porcentaje de presupuesto consumido hasta el momento
                        $porcentajeConsumido = $item->v_total > 0 ? round(($acum_total_oc / $item->v_total) * 100, 1) : 0;
                    @endphp

                    @if ($item->evento)
                        <!-- FILA DE ENCABEZADO DE EVENTO -->
                        <tr>
                            <td colspan="13" class="font-weight-bold font-table text-center bg-gradient-info text-white">
                                {{ $item->descripcion }}
                            </td>
                            @if (Auth::user()->rol != 1)
                                <td class="font-weight-bold font-table">
                                    <button wire:click="deleteItem({{ $item->id }})">✖️</button>
                                </td>
                            @endif
                            @if (Auth::user()->rol != 1)
                                <td class="font-weight-bold font-table">
                                    <button wire:click="getDataEdit({{ $item->id }})">📝</button>
                                </td>
                            @endif
                        </tr>
                    @else
                        <!-- FILA PRINCIPAL DEL ÍTEM -->
                        <tr @if ($estaAgotado) style="background-color: #f5365c; color: white;" @endif>
                            <td class="font-weight-bold font-table">
                                {{ $item->cod }}
                            </td>
                            <td class="font-weight-bold font-table">
                                {{ $key += 1 }}
                            </td>
                            <td class="font-weight-bold font-table">
                                {{ $item->cantidad }}
                            </td>
                            <td class="font-weight-bold font-table">
                                {{ $item->dia }}
                            </td>
                            <td class="font-weight-bold font-table">
                                {{ $item->otros }}
                            </td>
                            <td class="font-weight-bold font-table">
                                <textarea cols="30" rows="1" readonly>{{ $item->descripcion }}</textarea>
                            </td>
                            <td class="font-weight-bold font-table">
                                $ {{ number_format($item->v_unitario) }}
                            </td>
                            <td class="font-weight-bold font-table">
                                $ {{ number_format($item->v_total) }}
                            </td>
                            <td class="font-weight-bold font-table">
                                @if ($proveedores_item = @unserialize($item->proveedor))
                                    @foreach ($proveedores_item as $proveedor)
                                        {{ $proveedores->find($proveedor)->tercero ?? '' }} <br>
                                    @endforeach
                                @else
                                    @if ($proveedores->find($item->proveedor))
                                        {{ $proveedores->find($item->proveedor)->tercero }}
                                    @else
                                        {{ $item->proveedor }}
                                    @endif
                                @endif
                            </td>
                            <td class="font-weight-bold font-table">
                                {{ $item->dias }}
                            </td>
                            <td class="font-weight-bold font-table">
                                {{ $item->ciudad }}
                            </td>

                            <!-- BOTÓN DESPLEGABLE / SUBMENÚ -->
                            @if (count($item->consumidos) > 0)
                                <td class="font-weight-bold font-table">
                                    <button data-bs-toggle="collapse" href="#collapseOrden{{ $key }}" role="button" aria-expanded="false"
                                        aria-controls="collapseOrden" class="m-0 p-0 d-flex justify-content-center" style="width: 50%;">
                                        <i class="fa-solid fa-caret-down">📝</i>
                                    </button>
                                </td>

                                <!-- CONTENIDO DEL SUBMENÚ DESPLEGABLE -->
                                <tr class="collapse" id="collapseOrden{{ $key }}" wire:ignore.self wire:key="collapse-row-{{ $item->id }}">
                                    <td colspan="11" class="m-0 p-0">
                                        <div class="table-responsive px-5 py-3 rounded bg-white border my-2">
                                            
                                            <!-- SUB-TABLA DE ÓRDENES DE COMPRA -->
                                            <table class="table font-table mb-3">
                                                <thead>
                                                    <tr>
                                                        <th class="font-weight-bold bg-gradient-primary text-white">No. ITEM</th>
                                                        <th class="font-weight-bold bg-gradient-primary text-white">CANT</th>
                                                        <th class="font-weight-bold bg-gradient-primary text-white">DIAS</th>
                                                        <th class="font-weight-bold bg-gradient-primary text-white">OTROS</th>
                                                        <th class="font-weight-bold bg-gradient-primary text-white">CARACTERISTICAS</th>
                                                        <th class="font-weight-bold bg-gradient-primary text-white">V. UNI</th>
                                                        <th class="font-weight-bold bg-gradient-primary text-white">V. TOTAL</th>
                                                        <th class="font-weight-bold bg-gradient-primary text-white">ESTADO</th>
                                                        <th class="font-weight-bold bg-gradient-primary text-white">PROVEEDOR</th>
                                                        <th class="font-weight-bold bg-gradient-primary text-white">ORDEN DE COMPRA</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($item->consumidos as $ordenItem)
                                                        <tr @if ($ordenItem->OrdenCompra && $ordenItem->OrdenCompra->estado_id == 6) style="text-decoration-line: line-through;" @endif>
                                                            <td class="font-weight-bold font-table">
                                                                {{ $key }}
                                                            </td>
                                                            <td class="font-weight-bold font-table">
                                                                {{ $ordenItem->cant_oc }}
                                                            </td>
                                                            <td class="font-weight-bold font-table">
                                                                {{ $ordenItem->dias_oc }}
                                                            </td>
                                                            <td class="font-weight-bold font-table">
                                                                {{ $ordenItem->otros_oc }}
                                                            </td>
                                                            <td class="font-weight-bold font-table" style="width: 1rem;">
                                                                <textarea cols="30" rows="1" readonly>{{ $ordenItem->desc_oc }}</textarea>
                                                            </td>
                                                            <td class="font-weight-bold font-table">
                                                                $ {{ number_format($ordenItem->vunit_oc) }}
                                                            </td>
                                                            <td class="font-weight-bold font-table">
                                                                $ {{ number_format($ordenItem->vtotal_oc) }}
                                                            </td>
                                                            <td class="font-weight-bold font-table">
                                                                {{ $ordenItem->OrdenCompra->estado_oc->description ?? 'N/A' }}
                                                            </td>
                                                            <td class="font-weight-bold font-table">
                                                                {{ $ordenItem->OrdenCompra->proveedor->tercero ?? 'N/A' }} - {{ $ordenItem->OrdenCompra->proveedor->documento ?? '' }}
                                                            </td>
                                                            <td class="font-weight-bold font-table">
                                                                @if($ordenItem->OrdenCompra)
                                                                    <a href="{{ route('orden-juridica', $ordenItem->OrdenCompra->id) }}" target="_blank">Orden de compra</a>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>

                                            <!-- PANEL INFERIOR: RESUMEN INFORMATIVO DE CONSUMO Y REGLAS DE NEGOCIO -->
                                            <div class="card bg-light border-0 p-3 mt-3">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <h6 class="font-weight-bold text-uppercase m-0 text-secondary" style="font-size: 0.85rem;">
                                                        <i class="fa-solid fa-chart-pie me-1"></i> Balance de Consumo del Ítem
                                                    </h6>
                                                    <span class="badge {{ $estaAgotado ? 'bg-danger' : 'bg-info' }}">
                                                        {{ $porcentajeConsumido }}% Consumido
                                                    </span>
                                                </div>

                                                <div class="row align-items-center">
                                                    <!-- 1. VALOR TOTAL ORIGINAL -->
                                                    <div class="col-md-4 mb-2 mb-md-0">
                                                        <div class="p-2 border rounded bg-white">
                                                            <small class="text-muted d-block font-weight-bold" style="font-size: 0.75rem;">1. Valor Total Asignado:</small>
                                                            <span class="h6 font-weight-bold mb-0 text-dark">$ {{ number_format($item->v_total) }}</span>
                                                        </div>
                                                    </div>

                                                    <!-- 2. TOTAL CONSUMIDO REAL POR OCs -->
                                                    <div class="col-md-4 mb-2 mb-md-0">
                                                        <div class="p-2 border rounded bg-white">
                                                            <small class="text-muted d-block font-weight-bold" style="font-size: 0.75rem;">2. Total Consumido (OCs Activas):</small>
                                                            <span class="h6 font-weight-bold mb-0 text-warning">$ {{ number_format($acum_total_oc) }}</span>
                                                            <small class="text-muted d-block mt-1" style="font-size: 0.68rem;">
                                                                Cant. OCs: {{ $cont_cant_oc }} / {{ $item->cantidad * ($item->dia ?? 1) * ($item->otros ?? 1) }}
                                                            </small>
                                                        </div>
                                                    </div>

                                                    <!-- 3. SALDO DISPONIBLE RESTANTE -->
                                                    <div class="col-md-4">
                                                        <div class="p-2 border rounded bg-white">
                                                            <small class="text-muted d-block font-weight-bold" style="font-size: 0.75rem;">3. Saldo Restante Disponible:</small>
                                                            <span class="h6 font-weight-bold mb-0 {{ $saldoRestante <= 0 ? 'text-danger' : 'text-success' }}">
                                                                $ {{ number_format($saldoRestante) }}
                                                            </span>
                                                            @if($estaAgotado)
                                                                <small class="text-danger d-block font-weight-bold mt-1" style="font-size: 0.68rem;">
                                                                    🔒 Totalmente ejecutado
                                                                </small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- BARRA VISUAL DE PROGRESO -->
                                                <div class="progress mt-3" style="height: 6px;">
                                                    <div 
                                                        class="progress-bar {{ $estaAgotado ? 'bg-danger' : 'bg-primary' }}" 
                                                        role="progressbar" 
                                                        style="width: {{ min(100, $porcentajeConsumido) }}%;" 
                                                        aria-valuenow="{{ $porcentajeConsumido }}" 
                                                        aria-valuemin="0" 
                                                        aria-valuemax="100">
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </td>
                                </tr>
                            @else
                                <td class="font-weight-bold font-table">
                                    <div class="m-0 p-0 d-flex justify-content-center" style="width: 100%; color: #825ee4;">
                                        <i class="fa-solid fa-ban"></i>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @endif
                @endforeach
            </tbody>
            
        </table>
    </div>
</div>
