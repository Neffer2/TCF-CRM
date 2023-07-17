<div x-data="asignarProyecto" x-init="construct">
    <div class="card"> 
        <div class="card-body">
            <h3 class="mb-4">Asignar proyecto</h3>
            <div class="row">
                <div class="col-md-2 mb-3">
                    <div class="list-group">
                        <select @click="isOpen" wire:model.lazy="productor" id="productor" class="form-control" size="20">
                            @foreach ($productores as $productor)
                                <option value="{{ $productor->id }}">{{ $productor->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-10" x-show="open" x-transition x-cloak>
                    <div class="card">
                        <div class="card-body pb-0">
                            <div class="d-flex justify-content-center">
                                <img src="http://localhost:8000/storage/photos/RiEgX7pjO4YQVQc4DYChx9l3r6pFwA2XAsUA5LV2.png" alt="Foto de perfil" class="avatar shadow" style="height: 100px; width: 100px; border-radius: 3rem;">
                            </div>
                        </div>       
                        <div class="row p-3 pt-0">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="exampleFormControlSelect2">Asignados</label>
                                    <select wire:model.lazy="asignado" id="asignado" class="form-control" size="10">
                                        @foreach ($asignados as $asignado)
                                            <option value="{{ $asignado->id }}">{{ $asignado->cod_cc }} - {{ $asignado->gestion->nom_proyecto_cot }} - {{ $asignado->id_gestion }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2 d-flex flex-column justify-content-center">
                                <button wire:click="asignar" class="btn btn-icon btn-3 bg-gradient-warning" type="button">
                                    <span class="btn-inner--icon"><i class="ni ni-bold-left"></i></span>
                                    <span class="btn-inner--text">Asignar</span>
                                </button>
                                <button wire:click="liberar" class="btn btn-icon btn-3 bg-gradient-danger" type="button">
                                    <span class="btn-inner--text">Liberar</span>
                                    <span class="btn-inner--icon mt-5"><i class="ni ni-bold-right"></i></span>
                                </button>
                            </div> 
                            <div class="col-md-5">
                                <div class="form-group">
                                    <div class="d-flex gap-3 align-items-baseline mb-1">
                                        <label for="exampleFormControlSelect2">Proyectos</label>
                                        <select class="form-control" name="" id="">
                                            <option value="">Comercial</option>
                                            @foreach ($comerciales as $comercial)
                                                <option value="">{{ $comercial->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <select wire:model.lazy="proyecto" id="proyecto" class="form-control" size="10">
                                        @foreach ($proyectos as $proyecto)
                                            <option value="{{ $proyecto->id }}">{{ $proyecto->cod_cc }} - {{ $proyecto->gestion->nom_proyecto_cot }} - {{ $proyecto->id_gestion }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>                        
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="alerts">
        @if($errors->any()) 
            <script>
                Swal.fire(
                '!Oppss tenemos un problema',
                `<ul style='text-align: initial; list-style-type: none;'>
                    @foreach($errors->all() as $error) 
                    <li>{{ $error }}<li>
                    @endforeach
                </ul>`, 
                'error'
                );
            </script>
        @endif 
        @if (session('success'))
            <script>
                Swal.fire(
                'Hecho',
                `{{ session('success') }}`,
                'success'
                );
            </script>
        @endif
    </div>
</div> 
