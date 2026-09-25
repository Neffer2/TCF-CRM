<div class="card crm-page-card">
    <div class="card-header pb-0">
        <h5 class="mb-1">Papelera</h5>
        <p class="text-sm text-muted mb-3">Lo que se elimina desde las pantallas no se borra de la base: queda aquí con su fecha y se puede restaurar.</p>
        <div class="d-flex flex-wrap gap-2 mb-3">
            @foreach ($tipos as $clave => $t)
                <button type="button" wire:click="$set('tipo', '{{ $clave }}')"
                        class="btn btn-sm mb-0 {{ $tipo === $clave ? 'bg-gradient-primary' : 'btn-outline-secondary' }}">
                    {{ $t['titulo'] }} <span class="badge bg-white text-dark ms-1">{{ $conteos[$clave] }}</span>
                </button>
            @endforeach
        </div>
        <div class="row align-items-center">
            <div class="col-md-6">
                <input type="search" class="form-control" placeholder="Buscar en {{ mb_strtolower($def['titulo']) }}…" wire:model.debounce.400ms="buscar">
            </div>
            <div class="col-md-6 text-md-end">
                @if ($def['nota'])<small class="text-muted">{{ $def['nota'] }}</small>@endif
            </div>
        </div>
        @if ($mensaje)
            <div class="alert alert-success text-white text-sm py-2 mt-3 mb-0">{{ $mensaje }}</div>
        @endif
    </div>
    <div class="card-body px-0 pt-3 pb-2">
        <div class="table-responsive">
            <table class="table align-items-center mb-0 crm-table--data">
                <thead>
                    <tr>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-4">Registro</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Enviado a la papelera</th>
                        <th class="text-secondary opacity-7"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($filas as $f)
                        <tr>
                            <td class="ps-4"><span class="text-sm font-weight-bold">{{ $f['resumen'] }}</span> <span class="text-xs text-muted">#{{ $f['id'] }}</span></td>
                            <td class="text-sm">{{ optional($f['borrado'])->format('Y-m-d H:i') }} <span class="text-xs text-muted">({{ optional($f['borrado'])->diffForHumans() }})</span></td>
                            <td class="text-end pe-4">
                                <button type="button" class="btn btn-sm btn-outline-primary mb-0" wire:click="restaurar({{ $f['id'] }})"
                                        onclick="return confirm('¿Restaurar este registro? Volverá a verse en sus pantallas.')">Restaurar</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center text-sm text-muted py-4">No hay registros en la papelera de {{ mb_strtolower($def['titulo']) }}.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($filas->count() === 100)
            <p class="text-xs text-muted px-4 pt-2 mb-0">Se muestran los 100 más recientes; usa el buscador para encontrar otros.</p>
        @endif
    </div>
</div>
