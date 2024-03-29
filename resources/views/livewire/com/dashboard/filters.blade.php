<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label for="comercial_selector">
                <h6 class="mb-0 font-weight-bolder text-uppercase">
                    Año
                </h6>
            </label>
            <select name="" id="comercial_selector" class="form-control" wire:model="año">
                <option value="">Seleccionar</option>
                @foreach ($StdAño as $item)
                    <option value="{{ $item->id }}">{{ $item->description }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="mes_selector">
                <h6 class="mb-0 font-weight-bolder text-uppercase">
                    Mes
                </h6>
            </label>
            <select name="" id="mes_selector" class="form-control" wire:model="mes">
                <option value="general">General</option>
                @foreach ($StdMes as $item)
                    <option value="{{ $item->id }}">{{ $item->description }}</option>
                @endforeach
            </select>
        </div>
    </div>
    {{-- <div class="col-md-3">
        <div class="form-group">
            <label for="comercial_selector">
                <h6 class="mb-0 font-weight-bolder text-uppercase">
                    Comerical
                </h6>
            </label>
            <select name="" id="comercial_selector" class="form-control" wire:model="comercial">
                <option value="">General</option>
                @foreach ($StdComercial as $item)
                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                @endforeach
            </select>
        </div>
    </div> --}}
    <div class="col-md-4">
        <div class="form-group">
            <label for="cuenta_selector">
                <h6 class="mb-0 font-weight-bolder text-uppercase">
                    Cuenta
                </h6> 
            </label>
            <select name="" id="cuenta_selector" class="form-control" wire:model="cuenta">
                <option value="">General</option>
                @foreach ($StdCuenta as $item)
                    <option value="{{ $item->id }}">{{ $item->description }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>
  