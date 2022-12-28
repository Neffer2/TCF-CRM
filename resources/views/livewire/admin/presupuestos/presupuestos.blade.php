<div>
    <div class="card card-frame">
        <div class="card-header">
            <div>
                <h5 class="mb-0">Lista de presupuestos</h5> 
            </div>
            <hr>
            <select class="form-control" wire:model="comercial"> 
                <option value="">Seleccionar</option>
                @foreach ($comerciales as $comercial)
                    <option value="{{ $comercial->id }}">{{ $comercial->name }}</option>
                @endforeach
            </select>  
        </div>
        <div class="card-body">
            <div class="row"> 
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Año</label>
                        <select name="" id="" class="form-control" wire:model="año">
                            <option value="">Seleccionar</option>
                            @foreach ($años as $año)
                                <option value="{{ $año->id }}">{{ $año->description }}</option>
                            @endforeach
                        </select>
                    </div>
                    {{-- <div class="author align-items-center">
                        <img src="./assets/img/kit/pro/team-2.jpg" alt="..." class="avatar shadow">
                    </div> --}}
                </div>
                <div class="col-md-9">
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Mes</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Presupuesto</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($presupuestos as $presupuesto)
                                    <tr>
                                        <td>
                                            <p class="text-sm text-secondary mb-0">{{ $presupuesto->presupuesto_mes->description }}</p>
                                        </td>
                                        <td>
                                            <p class="text-sm text-secondary mb-0">{{ $presupuesto->valor }}</p>
                                        </td>
                                    </tr>
                                @endforeach 
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
