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
        <select id="filtro_mes" class="form-control" wire:model="mes" {{ empty($StdMes) ? 'disabled' : '' }}>
            <option value="">Todo el año</option>
            @foreach ($StdMes as $item)
                <option value="{{ $item->id }}">{{ $item->description }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label for="filtro_cuenta">Cuenta</label>
        <select id="filtro_cuenta" class="form-control {{ $cuenta ? 'is-filled' : '' }}" wire:model="cuenta" {{ empty($StdCuenta) ? 'disabled' : '' }}>
            <option value="">Todas las cuentas</option>
            @foreach ($StdCuenta as $item)
                <option value="{{ $item->id }}">{{ $item->description }}</option>
            @endforeach
        </select>
    </div>
</div>
