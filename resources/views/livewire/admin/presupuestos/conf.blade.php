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
                <form wire:submit.prevent="updateMeses">
                    @if ($storedAñoData) 
                    <div class="row"> 
                        <div class="row col-md-6">
                            <div class="col-md-3">
                                <label for="">{{ $storedAñoData[0]->description }}</label>
                                <hr>
                            </div>
                            <div class="col-md-9 form-group">
                                <label for="">Fecha de inicio</label>
                                <input class="form-control" type="date" wire:model="eneroIn">
                                <label for="">Fecha de finalizaci&oacute;n</label>
                                <input class="form-control" type="date" wire:model="eneroFin">
                            </div>
                        </div>
                        <div class="row col-md-6">
                            <div class="col-md-3">
                                <label for="">{{ $storedAñoData[1]->description }}</label>
                                <hr>
                            </div>
                            <div class="col-md-9 form-group">
                                <label for="">Fecha de inicio</label>
                                <input class="form-control" type="date" wire:model="febreroIn">
                                <label for="">Fecha de finalizaci&oacute;n</label>
                                <input class="form-control" type="date" wire:model="febreroFin">
                            </div>
                        </div>
                        <hr class="horizontal dark my-3">
                        <div class="row col-md-6">
                            <div class="col-md-3">
                                <label for="">{{ $storedAñoData[2]->description }}</label>
                                <hr>
                            </div>
                            <div class="col-md-9 form-group">
                                <label for="">Fecha de inicio</label>
                                <input class="form-control" type="date" wire:model="marzoIn">
                                <label for="">Fecha de finalizaci&oacute;n</label>
                                <input class="form-control" type="date" wire:model="marzoFin">
                            </div>
                        </div>
                        <div class="row col-md-6">
                            <div class="col-md-3">
                                <label for="">{{ $storedAñoData[3]->description }}</label>
                                <hr>
                            </div>
                            <div class="col-md-9 form-group">
                                <label for="">Fecha de inicio</label>
                                <input class="form-control" type="date" wire:model="abrilIn">
                                <label for="">Fecha de finalizaci&oacute;n</label>
                                <input class="form-control" type="date" wire:model="abrilFin">
                            </div>
                        </div>
                        <hr class="horizontal dark my-3">
                        <div class="row col-md-6">
                            <div class="col-md-3">
                                <label for="">{{ $storedAñoData[4]->description }}</label>
                                <hr>
                            </div>
                            <div class="col-md-9 form-group">
                                <label for="">Fecha de inicio</label>
                                <input class="form-control" type="date" wire:model="mayoIn">
                                <label for="">Fecha de finalizaci&oacute;n</label>
                                <input class="form-control" type="date" wire:model="mayoFin">
                            </div>
                        </div>
                        <div class="row col-md-6">
                            <div class="col-md-3">
                                <label for="">{{ $storedAñoData[5]->description }}</label>
                                <hr>
                            </div>
                            <div class="col-md-9 form-group">
                                <label for="">Fecha de inicio</label>
                                <input class="form-control" type="date" wire:model="junioIn">
                                <label for="">Fecha de finalizaci&oacute;n</label>
                                <input class="form-control" type="date" wire:model="junioFin">
                            </div>
                        </div>
                        <hr class="horizontal dark my-3">
                        <div class="row col-md-6">
                            <div class="col-md-3">
                                <label for="">{{ $storedAñoData[6]->description }}</label>
                                <hr>
                            </div>
                            <div class="col-md-9 form-group">
                                <label for="">Fecha de inicio</label>
                                <input class="form-control" type="date" wire:model="julioIn">
                                <label for="">Fecha de finalizaci&oacute;n</label>
                                <input class="form-control" type="date" wire:model="julioFin">
                            </div>
                        </div>
                        <div class="row col-md-6">
                            <div class="col-md-3">
                                <label for="">{{ $storedAñoData[7]->description }}</label>
                                <hr>
                            </div>
                            <div class="col-md-9 form-group">
                                <label for="">Fecha de inicio</label>
                                <input class="form-control" type="date" wire:model="agostoIn">
                                <label for="">Fecha de finalizaci&oacute;n</label>
                                <input class="form-control" type="date" wire:model="agostoFin">
                            </div>
                        </div>
                        <hr class="horizontal dark my-3">
                        <div class="row col-md-6">
                            <div class="col-md-3">
                                <label for="">{{ $storedAñoData[8]->description }}</label>
                                <hr>
                            </div>
                            <div class="col-md-9 form-group">
                                <label for="">Fecha de inicio</label>
                                <input class="form-control" type="date" wire:model="septiembreIn">
                                <label for="">Fecha de finalizaci&oacute;n</label>
                                <input class="form-control" type="date" wire:model="septiembreFin">
                            </div>
                        </div>
                        <div class="row col-md-6">
                            <div class="col-md-3">
                                <label for="">{{ $storedAñoData[9]->description }}</label>
                                <hr>
                            </div>
                            <div class="col-md-9 form-group">
                                <label for="">Fecha de inicio</label>
                                <input class="form-control" type="date" wire:model="octubreIn">
                                <label for="">Fecha de finalizaci&oacute;n</label>
                                <input class="form-control" type="date" wire:model="octubreFin">
                            </div>
                        </div>
                        <hr class="horizontal dark my-3">
                        <div class="row col-md-6">
                            <div class="col-md-3">
                                <label for="">{{ $storedAñoData[10]->description }}</label>
                                <hr>
                            </div>
                            <div class="col-md-9 form-group">
                                <label for="">Fecha de inicio</label>
                                <input class="form-control" type="date" wire:model="noviembreIn">
                                <label for="">Fecha de finalizaci&oacute;n</label>
                                <input class="form-control" type="date" wire:model="noviembreFin">
                            </div>
                        </div>
                        <div class="row col-md-6">
                            <div class="col-md-3">
                                <label for="">{{ $storedAñoData[11]->description }}</label>
                                <hr>
                            </div>
                            <div class="col-md-9 form-group">
                                <label for="">Fecha de inicio</label>
                                <input class="form-control" type="date" wire:model="diciembreIn">
                                <label for="">Fecha de finalizaci&oacute;n</label>
                                <input class="form-control" type="date" wire:model="diciembreFin">
                            </div>
                        </div>
                        <hr class="horizontal dark my-3">
                        <div class="col-md-6">
                            <button class="btn bg-gradient-primary">Guardar fechas</button>
                        </div>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>