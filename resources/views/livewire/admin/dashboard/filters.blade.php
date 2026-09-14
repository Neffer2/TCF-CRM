<div class="crm-filters">
    <div class="form-group">
        <label for="filtro_año">Año</label>
        <select id="filtro_año" class="form-control" wire:model="año">
            <option value="">Seleccionar</option>
            @foreach ($StdAño as $item)
                <option value="{{ $item->id }}">{{ $item->description }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label for="filtro_mes">Mes</label>
        <select id="filtro_mes" class="form-control" wire:model="mes">
            <option value="">Todo el año</option>
            @foreach ($StdMes as $item)
                <option value="{{ $item->id }}">{{ $item->description }}</option>
            @endforeach
        </select>
    </div>

    {{-- Comercial: escribe para filtrar la lista; la selección se guarda en $comercial --}}
    <div class="form-group crm-combo">
        <label for="filtro_comercial">Comercial</label>
        <div class="crm-combo__box">
            <svg class="crm-combo__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><circle cx="11" cy="11" r="6.5"/><path d="m20 20-4.2-4.2"/></svg>
            <input id="filtro_comercial" type="text" class="form-control" autocomplete="off"
                   placeholder="{{ $año ? 'Todo el equipo — escribe un nombre' : 'Elige primero un año' }}"
                   wire:model.debounce.150ms="buscarComercial"
                   {{ $año ? '' : 'disabled' }}>
            @if ($comercial)
                <button type="button" class="crm-combo__clear" wire:click="limpiarComercial" title="Quitar filtro de comercial" aria-label="Quitar filtro de comercial">×</button>
            @endif
        </div>
        <ul class="crm-combo__list" role="listbox">
            <li>
                <button type="button" class="crm-combo__item {{ $comercial ? '' : 'is-active' }}" wire:click="elegirComercial(null)" role="option">
                    <span class="crm-combo__avatar">∑</span> Todo el equipo
                </button>
            </li>
            @forelse ($this->comercialesFiltrados as $c)
                <li>
                    <button type="button" class="crm-combo__item {{ $comercial == $c->id ? 'is-active' : '' }}" wire:click="elegirComercial({{ $c->id }})" role="option">
                        <span class="crm-combo__avatar">{{ mb_strtoupper(mb_substr($c->name, 0, 1)) }}</span> {{ $c->name }}
                    </button>
                </li>
            @empty
                <li class="crm-combo__empty">Ningún comercial coincide con "{{ $buscarComercial }}"</li>
            @endforelse
        </ul>
    </div>
</div>
