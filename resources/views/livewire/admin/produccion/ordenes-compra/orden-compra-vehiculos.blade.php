<div class="card card-body mb-4">

    <!-- MENSAJES DE ALERTA -->
    @if(session('message'))
        <div class="alert alert-success alert-dismissible fade show text-white mb-3" role="alert">
            <i class="fas fa-check-circle me-1"></i> {{ session('message') }}
            <button type="button" class="btn-close text-white" data-bs-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show text-white mb-3" role="alert">
            <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close text-white" data-bs-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- ENCABEZADO -->
    <div class="row border-bottom pb-3 mb-3">
        <div class="col-md-12 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="font-weight-bolder text-primary mb-0">
                    <i class="fas fa-file-excel me-2"></i> Importación de Orden de Compra desde Excel
                </h5>
                <p class="text-xs text-muted mb-0">Carga un archivo (.xlsx, .xls, .csv) para precargar la lista de ítems.</p>
            </div>
        </div>
    </div>

    <!-- ZONA DE CARGA DE ARCHIVO -->
    <div class="row justify-content-center mb-3">
        <div class="col-md-8">
            <div class="p-4 border border-2 border-dashed rounded text-center bg-light">

                <div wire:loading.remove wire:target="file">
                    <i class="fas fa-cloud-upload-alt fa-3x text-secondary mb-2"></i>
                    <h6 class="text-sm font-weight-bold mb-1">Selecciona o arrastra tu archivo Excel</h6>
                    <p class="text-xxs text-muted mb-3">Formatos soportados: .xlsx, .xls, .csv</p>

                    <input type="file" id="upload_file" class="d-none" wire:model="file" accept=".xlsx,.xls,.csv">
                    <label for="upload_file" class="btn btn-sm bg-gradient-primary mb-0">
                        <i class="fas fa-search me-1"></i> Buscar Archivo
                    </label>
                </div>

                <!-- LOADING AL SELECCIONAR ARCHIVO -->
                <div wire:loading wire:target="file" class="py-2">
                    <div class="spinner-border text-primary spinner-border-sm me-2" role="status"></div>
                    <span class="text-sm font-weight-bold text-secondary">Procesando y analizando archivo Excel...</span>
                </div>

            </div>

            @error('file')
            <span class="text-danger text-xs font-weight-bold d-block mt-1 text-center">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <!-- VISTA PREVIA Y RESUMEN -->
    @if($totalRows > 0)
        <!-- TARJETAS DE RESUMEN DEL ARCHIVO DETECTADO -->
        <div class="row bg-light p-3 rounded mb-3 align-items-center border">
            <div class="col-md-4 mb-2 mb-md-0 border-end">
                <span class="text-xxs text-uppercase text-secondary font-weight-bold d-block">Proyecto / Centro de Costos</span>
                <span class="text-sm font-weight-bolder text-dark">
                    <i class="fas fa-project-diagram text-primary me-1"></i>
                    {{ $presupuestoDetectado ?? 'No detectado' }}
                </span>
            </div>
            <div class="col-md-4 mb-2 mb-md-0 border-end text-md-center">
                <span class="text-xxs text-uppercase text-secondary font-weight-bold d-block">Total de Filas Leídas</span>
                <span class="badge bg-gradient-info text-white font-weight-bold p-2 text-xs">
                    {{ $totalRows }} ítems
                </span>
            </div>
            <div class="col-md-4 text-md-end">
                <span class="text-xxs text-uppercase text-secondary font-weight-bold d-block">Filas con Errores</span>
                <span class="badge bg-gradient-{{ $totalErrors > 0 ? 'danger' : 'success' }} text-white font-weight-bold p-2 text-xs">
                    {{ $totalErrors }} erróneas
                </span>
            </div>
        </div>

        <!-- TABLA PREVIEW -->
        <div class="table-responsive p-0 border rounded mb-3">
            <table class="table align-items-center table-sm mb-0">
                <thead>
                <tr>
                    <th class="bg-gradient-primary text-white text-center text-xs">Fila</th>
                    <th class="bg-gradient-primary text-white text-xs">Ítem #</th>
                    <th class="bg-gradient-primary text-white text-xs">Descripción</th>
                    <th class="bg-gradient-primary text-white text-center text-xs">Cantidad</th>
                    <th class="bg-gradient-primary text-white text-end text-xs">V. Unitario</th>
                    <th class="bg-gradient-primary text-white text-end text-xs">V. Total</th>
                    <th class="bg-gradient-primary text-white text-center text-xs">Estado / Validación</th>
                </tr>
                </thead>
                <tbody>
                @foreach($preview as $row)
                    <tr class="{{ $row['valid'] ? '' : 'table-danger' }}">
                        <td class="text-center text-xs font-weight-bold">{{ $row['fila_excel'] }}</td>
                        <td class="text-xs font-weight-bold">{{ $row['item_numero'] }}</td>
                        <td class="text-xs">
                                <span class="d-inline-block text-truncate" style="max-width: 250px;" title="{{ $row['item_desc'] ?? '' }}">
                                    {{ $row['item_desc'] ?? '-' }}
                                </span>
                        </td>
                        <td class="text-center text-xs">{{ $row['cantidad'] }}</td>
                        <td class="text-end text-xs">${{ number_format($row['valor_unitario'], 2) }}</td>
                        <td class="text-end text-xs font-weight-bold">${{ number_format($row['valor_total'], 2) }}</td>
                        <td class="text-center text-xs">
                            @if($row['valid'])
                                <span class="badge badge-sm bg-gradient-success">
                                        <i class="fas fa-check me-1"></i> Ok
                                    </span>
                            @else
                                <span class="badge badge-sm bg-gradient-danger" title="{{ implode(' | ', $row['errores']) }}">
                                        <i class="fas fa-times me-1"></i> {{ implode(' | ', $row['errores']) }}
                                    </span>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <!-- BOTONES DE ACCIÓN -->
        <div class="row">
            <div class="col-md-12 text-end">
                <button
                    type="button"
                    wire:click="confirmImport"
                    wire:loading.attr="disabled"
                    @if($totalErrors === $totalRows) disabled @endif
                    class="btn bg-gradient-warning text-white mb-0"
                >
                    <span wire:loading.remove wire:target="confirmImport">
                        <i class="fas fa-file-invoice me-1"></i> Generar Orden de Compra ({{ $totalRows - $totalErrors }} válidos)
                    </span>
                    <span wire:loading wire:target="confirmImport">
                        <i class="fas fa-spinner fa-spin me-1"></i> Procesando Orden...
                    </span>
                </button>
            </div>
        </div>
    @endif

</div>
