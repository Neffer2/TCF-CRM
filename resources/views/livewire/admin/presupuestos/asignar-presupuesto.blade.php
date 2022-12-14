<div class="row">
    <div class="col-md-12">
        <div class="card card-frame">
            <div class="card-header">
                <div>
                    <h5 class="mb-0">Asignar</h5> 
                    <p class="text-sm mb-0">Asigna un presupuesto a cada uno de los miembros de tu equipo.</p>
                </div> 
                <hr>  
                <div class="row"> 
                    <form wire:submit.prevent="getPresupuestoStored" class="row">
                        <div class="col-md-6"> 
                            <select class="form-control" wire:model="comercialesModel" required>
                                <option value="">Seleccionar</option>
                                @foreach ($comercialesStored as $comercial)
                                    <option value="{{ $comercial->id }}">{{ $comercial->name }}</option>
                                @endforeach 
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-control" wire:model="añosModel" required>
                                <option value="">Seleccionar</option>
                                @foreach ($añosStored as $año)
                                    <option value="{{ $año->id }}">{{ $año->description }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button class="btn bg-gradient-warning">Consultar</button>
                        </div>
                    </form>
                </div> 
            </div>
            <div class="card card-body">
                <form wire:submit.prevent="updatePresupuestos" class="row">
                    <div class="row">
                        <div class="col-md-12 d-flex justify-content-center card-header">
                            <h5>{{ $comercialName }} - {{ $añoDescription }}</h5>
                        </div> 
                        <div class="col-md-2 d-flex justify-content-center">
                            <label for="">Enero</label>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <input class="form-control" type="number" wire:model="eneroModel">
                            </div>
                        </div>
                        
                        <div class="col-md-2 d-flex justify-content-center aling-items-center">
                            <label for="">Febrero</label>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <input class="form-control" type="number" wire:model="febreroModel">
                            </div>
                        </div>
                    </div>
                    <hr class="horizontal dark my-3">
                    <div class="row">
                        <div class="col-md-2 d-flex justify-content-center aling-items-center">
                            <label for="">Marzo</label>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <input class="form-control" type="number" wire:model="marzoModel">
                            </div>
                        </div>

                        <div class="col-md-2 d-flex justify-content-center aling-items-center">
                            <label for="">Abril</label>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <input class="form-control" type="number" wire:model="abrilModel">
                            </div>
                        </div>
                    </div>
                    <hr class="horizontal dark my-3">
                    <div class="row">
                        <div class="col-md-2 d-flex justify-content-center aling-items-center">
                            <label for="">Mayo</label>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <input class="form-control" type="number" wire:model="mayoModel">
                            </div>
                        </div>

                        <div class="col-md-2 d-flex justify-content-center aling-items-center" wire:model="mayoModel">
                            <label for="">Junio</label>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <input class="form-control" type="number" wire:model="junioModel">
                            </div>
                        </div>
                    </div>
                    <hr class="horizontal dark my-3">
                    <div class="row">
                        <div class="col-md-2 d-flex justify-content-center aling-items-center">
                            <label for="">Julio</label>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <input class="form-control" type="number" wire:model="julioModel">
                            </div>
                        </div>

                        <div class="col-md-2 d-flex justify-content-center aling-items-center">
                            <label for="">Agosto</label>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <input class="form-control" type="number" wire:model="agostoModel">
                            </div>
                        </div>
                    </div>
                    <hr class="horizontal dark my-3">
                    <div class="row">
                        <div class="col-md-2 d-flex justify-content-center aling-items-center">
                            <label for="">Septiembre</label>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <input class="form-control" type="number" wire:model="septiembreModel">
                            </div>
                        </div>

                        <div class="col-md-2 d-flex justify-content-center aling-items-center">
                            <label for="">Octubre</label>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <input class="form-control" type="number" wire:model="octubreModel">
                            </div>
                        </div> 
                    </div>
                    <hr class="horizontal dark my-3">
                    <div class="row">
                        <div class="col-md-2 d-flex justify-content-center aling-items-center">
                            <label for="">Noviembre</label>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <input class="form-control" type="number" wire:model="noviembreModel">
                            </div>
                        </div>

                        <div class="col-md-2 d-flex justify-content-center aling-items-center">
                            <label for="">Diciembre</label>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <input class="form-control" type="number" wire:model="diciembreModel">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <button class="btn bg-gradient-warning"> Guardar cambios </button>
                    </div>
                </form>  
            </div>
        </div>
    </div>
    @if (session('success'))
        <script>
            Swal.fire(
                'Hecho',
                `{{ session('success') }}`,
                'success'
            ); 
        </script>  
    @endif
    @if($errors->any()) 
        <script>
            Swal.fire(
                'Upps!',
                `<ul style="list-style: square inside;">
                    @foreach ($errors->all() as $error)
                        <li> {{ $error }} </li>
                    @endforeach
                </ul>`,
                'error'
            );
        </script>
    @enderror
</div>
