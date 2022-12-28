<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="mes_selector">
                <h6 class="mb-0 font-weight-bolder text-uppercase">
                    Mes
                </h6>
            </label>
            <select name="" id="mes_selector" class="form-control" wire:model="mes">
                <option value="">General</option>
                @foreach ($StdMes as $item)
                    <option value="{{ $item->id }}">{{ $item->description }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-6">
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
    </div>
</div>
 