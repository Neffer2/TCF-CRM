<div class="row">
    <div class="col-md-12">
        <div class="card card-frame">
            <div class="card-header">
                <div> 
                    <h5 class="mb-0">Configuraci&oacute;n</h5> 
                    <p class="text-sm mb-0">Configura las fechas de inicio y final de cada mes. 
                    Puedes generar un nuevo año si lo necesitas.</p>
                </div>
                <hr>
                <select class="form-control" wire:model="añoModel">
                    <option value="#">Seleccionar</option>
                    @foreach ($años as $año)
                        <option value="{{ $año->id }}"> {{ $año->description }}</option>
                    @endforeach
                </select>
            </div>
            <div class="card card-body">
                <form action="">
                    @if ($storedAñoData)
                    <div class="row">
                            @foreach ($storedAñoData as $key => $mes)
                                <div class="row col-md-6">
                                    <div class="col-md-3">
                                        <label for="">{{ $mes->description }}</label>
                                        <hr>
                                    </div>
                                    <div class="col-md-9 form-group">
                                        <label for="">Fecha de inicio</label>
                                        <input class="form-control" type="date" value="{{ $mes->f_inicio }}">
                                        <label for="">Fecha de finalizaci&oacute;n</label>
                                        <input class="form-control" type="date" value="{{ $mes->f_fin }}">
                                    </div>
                                </div>
                                @if (($key + 1)%2 == 0)
                                    <hr class="horizontal dark my-3">
                                @endif
                            @endforeach
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>