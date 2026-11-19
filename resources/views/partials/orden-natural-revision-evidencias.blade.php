<div class="card mt-3">
    <div class="card-header pb-0">
        <h3 class="mb-0">Evidencias</h3>
        <p class="text-sm mb-0">
            <b>{{ $queriedOrden->naturalInfo->tercero->nombre }} {{ $queriedOrden->naturalInfo->tercero->apellido }}</b> ha enviado las siguientes evidencias.
        </p>
    </div>
    <div class="card-body">
        @include('partials.orden-natural-evidencias', ['evidencias' => $queriedOrden->evidencias])

        <button type="button" class="btn bg-gradient-success mt-3" data-bs-toggle="modal" data-bs-target="#aprobarEvidenciasNatural">
            APROBAR EVIDENCIAS
        </button>
        <button type="button" wire:click="toggleRechazo" @if($this->toggleRechazo) disabled @endif class="btn bg-gradient-danger mt-3">
            RECHAZAR EVIDENCIAS
        </button>

        <div class="modal fade" id="aprobarEvidenciasNatural" tabindex="-1" aria-labelledby="aprobarEvidenciasNaturalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="aprobarEvidenciasNaturalLabel">Aprobar evidencias</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">¿Estás seguro de aprobar las evidencias y enviar la orden a líder de producción?</div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" wire:click="updateOC" wire:loading.attr="disabled" data-bs-dismiss="modal" class="btn bg-gradient-success">Aprobar evidencias</button>
                    </div>
                </div>
            </div>
        </div>

        @if ($this->toggleRechazo)
            <div class="card mt-2">
                <div class="card-body">
                    ¿Estás seguro de rechazar estas evidencias?
                    <div class="form-group mt-2">
                        <label for="justificacion_rechazo">Justificación de rechazo</label>
                        <textarea id="justificacion_rechazo" class="form-control @error('justificacion_rechazo') is-invalid @enderror" wire:model.lazy="justificacion_rechazo" rows="5"></textarea>
                        @error('justificacion_rechazo')
                            <div class="text-invalid">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="button" wire:click="validateEvidencia(7)" wire:loading.attr="disabled" class="btn bg-gradient-danger">Rechazar</button>
                    <button type="button" wire:click="toggleRechazo" class="btn btn-secondary">Cancelar</button>
                </div>
            </div>
        @endif
    </div>
</div>
