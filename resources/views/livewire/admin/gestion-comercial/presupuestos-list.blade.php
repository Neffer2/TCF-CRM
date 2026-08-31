<div>
    <div class="card">
        <div class="card">
            <div class="card-header p-3">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                    
                    <!-- Título del panel -->
                    <div class="me-3">
                        <h3 class="h5 mb-0 fw-bold">Presupuestos</h3>
                        <small class="text-muted">Lista completa de presupuestos</small>
                    </div>

                    <!-- Barra de Filtros en una sola línea -->
                    <div class="d-flex flex-wrap align-items-center gap-2 flex-grow-1 justify-content-end">
                        
                        <!-- Año -->
                        <div style="min-width: 110px;">
                            <select wire:model.live="año" class="form-select form-select-sm" title="Año">
                                <option value="">Año: Todos</option>
                                @foreach ($años as $año)
                                    <option value="{{ $año->id }}">{{ $año->description }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Centro de Costos -->
                        <div style="min-width: 140px;">
                            <input type="text" wire:model.live.debounce.300ms="cod_cc" class="form-control form-control-sm" placeholder="Buscar C. Costos...">
                        </div>

                        <!-- Nombre Proyecto -->
                        <div style="min-width: 150px;">
                            <input type="text" wire:model.live.debounce.300ms="nom_proyecto" class="form-control form-control-sm" placeholder="Buscar Proyecto...">
                        </div>

                        <!-- Estados -->
                        <div style="min-width: 130px;">
                            <select wire:model.live="estado_id" id="estado_id" class="form-select form-select-sm">
                                <option value="">Estado: Todos</option>
                                @foreach ($estados as $estado)
                                    <option value="{{ $estado->id }}">{{ $estado->description }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Notificaciones -->
                        <div style="min-width: 130px;">
                            <select wire:model.live="notificacion" id="notificacion" class="form-select form-select-sm">
                                <option value="">Notif: Todas</option>
                                <option value="1">Con notificación</option>
                                <option value="0">Sin notificación</option>
                            </select>
                        </div>

                        <!-- Comercial -->
                        <div style="min-width: 130px;">
                            <select wire:model.live="comercial" class="form-select form-select-sm">
                                <option value="">Comercial: Todos</option>
                                @foreach ($comerciales as $comercial)
                                    <option value="{{ $comercial->id }}">{{ $comercial->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Orden -->
                        <div style="min-width: 130px;">
                            <select wire:model.live="orderBy" class="form-select form-select-sm">
                                <option value="DESC">Más recientes</option>
                                <option value="ASC">Más antiguos</option>
                            </select>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0 pt-1">
            <div class="table-responsive"> 
                <table class="table align-items-center mb-0">
                    <thead> 
                        <tr>
                            <th colspan="1" class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">DATOS DE PROYECTO</th>
                            <th colspan="5" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">M&eacute;tricas</th>
                            <th colspan="1" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Acciones</th>
                        </tr>
                    </thead> 
                    <tbody> 
                        @foreach ($presupuestos as $presupuesto)
                            <tr>
                                <td style="width: 16rem;">
                                    <div class="d-flex px-2 py-1" title="{{ $presupuesto->gestion->nom_proyecto_cot }}">
                                        <div>
                                            <img src="https://www.bullmarketing.com.co/wp-content/uploads/2022/04/cropped-favicon-bull-192x192.png" class="avatar avatar-sm me-3">
                                        </div>
                                        <div class="d-flex flex-column justify-content-center">                                        
                                            @if (strlen($presupuesto->gestion->nom_proyecto_cot) > 80)
                                                <h6 class="mb-0 text-xs" >{{ substr($presupuesto->gestion->nom_proyecto_cot, 0, 80) }}...</h6>
                                            @else
                                                <h6 class="mb-0 text-xs" >{{ $presupuesto->gestion->nom_proyecto_cot }}</h6>
                                            @endif
                                            <p class="text-xs text-secondary mb-0">{{ $presupuesto->gestion->contacto->empresa }}</p>
                                        </div>
                                    </div>
                                </td>                            
                                <td>
                                    <p class="text-xs font-weight-bold mb-0">Fecha</p>
                                    <p class="text-xs text-secondary mb-0">{{ $presupuesto->created_at }}</p>
                                </td> 
                                <td>
                                    <p class="text-xs font-weight-bold mb-0">Comercial</p>
                                    <p class="text-xs text-secondary mb-0">{{ $presupuesto->gestion->comercial->name }}</p>
                                </td>  
                                <td>
                                    <p class="text-xs font-weight-bold mb-0">Centro de costos</p>
                                    <textarea disabled rows="1" class="text-xs text-secondary mb-0">{{ $presupuesto->cod_cc }}</textarea>
                                </td>
                                <td>
                                    <p class="text-xs font-weight-bold mb-0">Estado</p>
                                    <p class="text-xs text-secondary mb-0">{{ $presupuesto->estado->description }}</p>
                                </td>
                                <td>
                                    <p class="text-xs font-weight-bold mb-0">Margen Proyecto</p>
                                    <p class="text-xs text-secondary mb-0">$ {{ $presupuesto->margen_proy }} %</p>
                                </td> 
                                <td class="d-flex align-items-center justify-content-center">
                                @php
                                    $idsEspeciales = [208, 197, 214, 145, 181, 210, 206, 171, 2];
                                @endphp

                                @if (Auth::user()->rol == 1 && in_array(Auth::user()->id, $idsEspeciales))
                                    {{-- Botón grande con notificación para los IDs específicos --}}
                                    <a href="{{ route('presupuesto', $presupuesto->id_gestion) }}"
                                        wire:click="marcarComoVisto({{ $presupuesto->id }})"
                                        class="btn btn-primary position-relative">
                                        <i class="fas fa-eye"></i> Ver
                                        @if(optional($presupuesto)->notificacion_actualizacion)
                                            <span class="position-absolute top-0 start-100 translate-middle p-2 bg-danger border border-light rounded-circle">
                                                <span class="visually-hidden">Nuevo cambio</span>
                                            </span>
                                        @endif
                                    </a>
                                @elseif (Auth::user()->rol == 1)
                                    {{-- Botón normal para los demás de rol 1 --}}
                                    <a class="btn bg-gradient-primary m-0 me-1 mb-2" target="_blank" href="{{ route('presupuesto', $presupuesto->id_gestion) }}">Ver</a>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="row p-2">
                <div class="col-md-6">
                    @php
                        $presupuestosArray = $presupuestos->toArray();
                        $registros_page = sizeof($presupuestosArray['data']);
                        $total = $presupuestosArray['total'];
                    @endphp
                    <span class="text-xs text-secondary mb-0">Mostrando {{ $registros_page }} registros de {{ $total }}.</span>        
                </div>
                <div class="col-md-12 table-responsive">
                    {{ $presupuestos->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
