<div class="card">
    <div class="card-header p-0 px-3 mt-3 position-relative z-index-1 col-md-12">
        <div class="row">            
            <div class="col-md-12">
                <h3 class="mb-0">Remisiones - Orden Jurídica</h3>
                <p class="text-sm mb-0">Verifica los ítems, adjunta y firma la remisión enviada.</p>
            </div> 
        </div> 
    </div>

    <div class="card-body pt-2">
        <!-- Tabla de Ítems de la Orden de Compra -->
        <div class="row font-table px-2 mb-4">
            <div class="col-md-12">
                @if (session()->has('success_item'))
                    <div class="alert alert-success text-white py-2" role="alert">
                        {{ session('success_item') }}
                    </div>
                @endif

                <div class="card card-body table-responsive mb-3 rounded bg-white p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="font-weight-bold bg-gradient-primary text-white text-center">No. ITEM</th>
                                <th class="font-weight-bold bg-gradient-primary text-white text-center">CANT</th>
                                <th class="font-weight-bold bg-gradient-primary text-white text-center">DIAS</th>
                                <th class="font-weight-bold bg-gradient-primary text-white text-center">OTROS</th>
                                <th class="font-weight-bold bg-gradient-primary text-white text-center">DESCRIPCION</th>
                                <th class="font-weight-bold bg-gradient-primary text-white text-center">V. UNI</th>
                                <th class="font-weight-bold bg-gradient-primary text-white text-center">V. TOTAL</th>
                                @if(isset(auth()->user()->rol) && auth()->user()->rol != 1)
                                    <th class="font-weight-bold bg-gradient-primary text-white text-center">ACCIONES</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($ocItems as $index => $item)
                                <tr>
                                    <td class="text-center align-middle">{{ $item['num_item'] }}</td>
                                    
                                    <!-- Cantidad con actualización en tiempo real -->
                                    <td class="text-center align-middle" style="width: 80px;">
                                        <input type="number" wire:model.live="ocItems.{{ $index }}.cant" class="form-control form-control-sm text-center" step="any">
                                    </td>

                                    <!-- Días con actualización en tiempo real -->
                                    <td class="text-center align-middle" style="width: 80px;">
                                        <input type="number" wire:model.live="ocItems.{{ $index }}.dias" class="form-control form-control-sm text-center" step="any">
                                    </td>

                                    <!-- Otros con actualización en tiempo real -->
                                    <td class="text-center align-middle" style="width: 80px;">
                                        <input type="number" wire:model.live="ocItems.{{ $index }}.otros" class="form-control form-control-sm text-center" step="any">
                                    </td>

                                    <td class="align-middle">
                                        <textarea disabled class="form-control form-control-sm" rows="1">{{ $item['desc'] }}</textarea>
                                    </td>

                                    <!-- Valor Unitario con actualización en tiempo real -->
                                    <td class="text-center">{{ number_format($item['vUnit'] ?? 0) }}</td>

                                    <!-- Valor Total que se recalcula visualmente al instante -->
                                    <td class="text-center align-middle font-weight-bold text-success">
                                        ${{ number_format($item['vTotal'], 2) }}
                                    </td>

                                    @if(isset(auth()->user()->rol) && auth()->user()->rol != 1)
                                        <td class="d-flex justify-content-center align-items-center py-3 align-middle">
                                            <button type="button" class="btn btn-sm btn-success me-1 mb-0 px-2" wire:click="updateItem({{ $index }})" title="Guardar cambios en BD">💾</button>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-3">No hay ítems asociados a esta orden de compra.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Formulario de Carga, Observaciones y Firma -->
        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-12">
                        <!-- Ver Orden Helisa -->
                        @if($storedOrden && $storedOrden->archivo_orden_helisa)
                            <div class="form-group"> 
                                @php 
                                    $archivo_orden_helisa = str_replace('public/', '', $storedOrden->archivo_orden_helisa); 
                                @endphp 
                                <h6 style="margin: 0">Orden de compra:</h6>
                                <div style="font-size: 9px;" class="mb-1">
                                    Consulta la orden de compra generada por Helisa.
                                </div>
                                <a href="{{ asset("storage/$archivo_orden_helisa") }}" class="btn btn-icon btn-3 mb-3 me-1 bg-gradient-secondary text-white" target="_blank" style="width: 100%">
                                    <span class="btn-inner--icon"><i class="ni ni-single-copy-04"></i></span>
                                    <span class="btn-inner--text">Ver Orden Helisa</span>
                                </a>
                            </div>
                        @endif

                        <!-- Adjuntar Remisión -->
                        <div class="form-group"> 
                            <h6 style="margin: 0">Remisión:</h6>
                            <div style="font-size: 9px;">
                                Adjunta la remisión (PDF o Excel).
                            </div>
                            <input id="remision" wire:model="remision" type="file" class="form-control" accept=".pdf,.xls,.xlsx">
                            
                            <div wire:loading wire:target="remision" class="py-2">
                                <div class="spinner-border text-warning spinner-border-sm" role="status">
                                    <span class="sr-only">Cargando...</span>
                                </div> 
                                <span class="text-xs text-warning">Subiendo archivo...</span>
                            </div> 

                            @error('remision')
                                <div class="text-danger text-xs mt-1">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Observaciones -->
                        <div class="form-group"> 
                            <h6 style="margin: 0">Observaciones:</h6>
                            <div style="font-size: 9px;">
                                Notíficale a compras si hay alguna novedad u observación.
                            </div>
                            <textarea id="observaciones" wire:model="observaciones" class="form-control" rows="3"></textarea>
                            @error('observaciones')
                                <div class="text-danger text-xs mt-1">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-12 mt-3">
                        <button id="enviar-btn" type="button" class="btn bg-gradient-warning w-100" disabled>Enviar remisión</button>
                    </div>
                </div>
            </div>

            <!-- Sección de Firma Digital -->
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-12">
                        <h6>Firma del Productor:</h6>
                        <div class="border rounded bg-white p-1 d-inline-block">
                            <canvas id="signature-pad" class="signature-pad" width="400" height="210"></canvas>
                        </div>
                        <input id="firma_hidden" type="text" wire:model="firma" style="display: none">
                    </div>
                    <div class="col-md-12 mt-2">
                        <div>
                            <button id="save" type="button" class="btn bg-gradient-primary">Guardar Firma</button>
                            <button id="clear" type="button" class="btn bg-gradient-secondary">Borrar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Scripts del Signature Pad y comunicación con Livewire -->
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let saveButton = document.getElementById('save');
            let cancelButton = document.getElementById('clear');
            let enviarBtn = document.getElementById('enviar-btn');
            
            enviarBtn.addEventListener('click', enviar);

            let signaturePad = new SignaturePad(document.getElementById('signature-pad'), {
                backgroundColor: 'rgb(255, 255, 255)',
                penColor: 'rgb(0, 0, 0)'
            });

            signaturePad.addEventListener('beginStroke', () => {
                enviarBtn.disabled = true;
            });

            saveButton.addEventListener('click', function (event) {
                if (signaturePad.isEmpty()) {
                    alert("Por favor, realiza la firma antes de guardar.");
                    return;
                }
                let data = signaturePad.toDataURL('image/png');
                enviarBtn.disabled = false;
                document.getElementById('firma_hidden').value = data;
                alert("Firma guardada temporalmente. Ya puedes enviar la remisión.");
            }); 

            cancelButton.addEventListener('click', function (event) {
                signaturePad.clear();
                enviarBtn.disabled = true;
            });

            function enviar(){
                let firmaVal = document.getElementById('firma_hidden').value;
                Livewire.emit('store-signal', firmaVal);
            }
        });
    </script>
</div>