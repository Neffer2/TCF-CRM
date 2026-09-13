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
    <div class="form-group">
        <label for="filtro_comercial">Comercial</label>
        <select id="filtro_comercial" class="form-control" wire:model="comercial">
            <option value="">Todo el equipo</option>
            @foreach ($StdComercial as $item)
                <option value="{{ $item->id }}">{{ $item->name }}</option>
            @endforeach
        </select>
    </div>
</div>
