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

    {{-- Líder comercial: filtra todo el dashboard por el equipo del líder (tabla lider_comercial_user) --}}
    <div class="form-group">
        <label for="filtro_lider">Líder comercial</label>
        @if ($liderFijo)
            {{-- Rol Líder comercial: el dashboard es siempre el de su equipo --}}
            <div id="filtro_lider" class="form-control is-filled crm-filters__fijo">Mi equipo ({{ count($this->equipoActual()) }})</div>
        @else
            <select id="filtro_lider" class="form-control {{ $lider ? 'is-filled' : '' }}" wire:model="lider">
                <option value="">Todos los equipos</option>
                @foreach ($StdLider as $l)
                    <option value="{{ $l['id'] }}">{{ $l['name'] }} ({{ count($l['equipo']) }})</option>
                @endforeach
            </select>
        @endif
    </div>

    {{-- Comercial: lista desplegable en la que se puede escribir para filtrar; la selección se guarda en $comercial --}}
    <div class="form-group crm-combo">
        <label for="filtro_comercial">Comercial</label>
        <div class="crm-combo__box">
            <svg class="crm-combo__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><circle cx="11" cy="11" r="6.5"/><path d="m20 20-4.2-4.2"/></svg>
            <input id="filtro_comercial" type="text" class="form-control" autocomplete="off"
                   placeholder="{{ $lider ? 'Todo el equipo del líder — escribe o elige' : 'Todos los comerciales — escribe o elige' }}"
                   wire:model.debounce.150ms="buscarComercial">
            @if ($comercial)
                <button type="button" class="crm-combo__clear" wire:click="limpiarComercial" title="Quitar filtro de comercial" aria-label="Quitar filtro de comercial">×</button>
            @else
                <span class="crm-combo__caret" aria-hidden="true"></span>
            @endif
        </div>
        <ul class="crm-combo__list" role="listbox" aria-label="Comerciales">
            <li>
                <button type="button" class="crm-combo__item {{ $comercial ? '' : 'is-active' }}" wire:click="elegirComercial(null)" role="option">
                    <span class="crm-combo__avatar">∑</span> {{ $lider ? 'Todo el equipo del líder' : 'Todos los comerciales' }}
                </button>
            </li>
            @forelse ($this->comercialesFiltrados as $c)
                <li>
                    <button type="button" class="crm-combo__item {{ $comercial == $c['id'] ? 'is-active' : '' }}" wire:click="elegirComercial({{ $c['id'] }})" role="option">
                        <span class="crm-combo__avatar">{{ mb_strtoupper(mb_substr($c['name'], 0, 1)) }}</span> {{ $c['name'] }}
                    </button>
                </li>
            @empty
                <li class="crm-combo__empty">Ningún comercial coincide con "{{ $buscarComercial }}"</li>
            @endforelse
        </ul>
    </div>
</div>
