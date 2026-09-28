<div>
    <div class="card">
        <div class="crm-page-head">
            <div class="crm-page-head__title">
                <span class="crm-eyebrow">Producción · {{ $gerencia ? 'Gerencia' : 'Controller' }}</span>
                <h1>Anticipos jurídicos</h1>
                <p>{{ $vista === 'todos'
                    ? 'Todos los anticipos jurídicos, del más reciente al más antiguo. Ábrelos para ver su recorrido.'
                    : ($gerencia
                        ? 'Te llegan después del Controller cuando superan $'.number_format(\App\Services\AnticipoJuridicoFlujo::umbral(), 0, ',', '.').'. Al aprobarlos se genera la orden de anticipo y pasan a Contabilidad; si los devuelves, vuelven al productor con tu motivo.'
                        : 'Te llegan después de que el líder de producción los aprueba, o cuando Contabilidad te los devuelve. Si el valor supera $'.number_format(\App\Services\AnticipoJuridicoFlujo::umbral(), 0, ',', '.').', al aprobarlos pasan a Gerencia; si no, quedan aprobados.') }}</p>
                <div class="crm-seg" role="tablist">
                    <button type="button" class="crm-seg__btn {{ $vista !== 'todos' ? 'is-active' : '' }}" wire:click="$set('vista', 'pendientes')" role="tab">Por revisar ({{ $conteo }})</button>
                    <button type="button" class="crm-seg__btn {{ $vista === 'todos' ? 'is-active' : '' }}" wire:click="$set('vista', 'todos')" role="tab">Todos</button>
                </div>
            </div>
            <div class="crm-search">
                <div class="crm-search__box">
                    <svg class="crm-search__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg>
                    <input type="text" class="form-control" placeholder="Proveedor, NIT, centro de costos o número" wire:model.debounce.400ms="buscar" aria-label="Buscar">
                </div>
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
                    @forelse ($anticipos as $anticipo)
                        @include('partials.anticipo-juridico-fila', ['anticipo' => $anticipo, 'ruta' => $ruta, 'boton' => in_array((int) $anticipo->estado_id, $estadosPendientes, true) ? 'Revisar' : 'Ver'])
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="crm-empty" style="padding: 36px 20px">
                                    <strong>{{ trim($buscar) !== '' ? 'Nada coincide con "'.$buscar.'".' : ($vista === 'todos' ? 'Todavía no hay anticipos jurídicos.' : 'No tienes anticipos por revisar.') }}</strong>
                                    <span>{{ $vista === 'todos' ? '' : ($gerencia ? 'Cuando el Controller apruebe uno de más de $'.number_format(\App\Services\AnticipoJuridicoFlujo::umbral(), 0, ',', '.').', aparecerá aquí y te llegará un correo.' : 'Cuando el líder de producción apruebe uno, aparecerá aquí y te llegará un correo.') }}</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($anticipos->hasPages())
            <div class="px-4 py-3">{{ $anticipos->links() }}</div>
        @endif
    </div>
</div>
