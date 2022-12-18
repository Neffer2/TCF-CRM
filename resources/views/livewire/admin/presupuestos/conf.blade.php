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
                <select class="form-control">
                    <option value="#">Seleccionar</option>
                    @foreach ($años as $año)
                        <option value="{{ $año->id }}"> {{ $año->description }}</option>
                    @endforeach
                </select>
            </div>
            <div class="card card-body">
                <form action="">
                    <div class="row">
                        <div class="col-md-2 d-flex justify-content-center">
                            <label for="">Enero</label>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Fecha inicio</label>
                                <input class="form-control" type="date">
                                <label for="">Fecha fin</label>
                                <input class="form-control" type="date">
                            </div>
                        </div>

                        <div class="col-md-2 d-flex justify-content-center aling-items-center">
                            <label for="">Febrero</label>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Fecha inicio</label>
                                <input class="form-control" type="date">
                                <label for="">Fecha fin</label>
                                <input class="form-control" type="date">
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
                                <label for="">Fecha inicio</label>
                                <input class="form-control" type="date">
                                <label for="">Fecha fin</label>
                                <input class="form-control" type="date">
                            </div>
                        </div>

                        <div class="col-md-2 d-flex justify-content-center aling-items-center">
                            <label for="">Abril</label>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <input class="form-control" type="date">
                                <label for="">Fecha inicio</label>
                                <label for="">Fecha fin</label>
                                <input class="form-control" type="date">
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
                                <label for="">Fecha inicio</label>
                                <input class="form-control" type="date">
                                <label for="">Fecha fin</label>
                                <input class="form-control" type="date">
                            </div>
                        </div>

                        <div class="col-md-2 d-flex justify-content-center aling-items-center">
                            <label for="">Junio</label>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Fecha inicio</label>
                                <input class="form-control" type="date">
                                <label for="">Fecha fin</label>
                                <input class="form-control" type="date">
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
                                <label for="">Fecha inicio</label>
                                <input class="form-control" type="date">
                                <label for="">Fecha fin</label>
                                <input class="form-control" type="date">
                            </div>
                        </div>

                        <div class="col-md-2 d-flex justify-content-center aling-items-center">
                            <label for="">Agosto</label>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Fecha inicio</label>
                                <input class="form-control" type="date">
                                <label for="">Fecha fin</label>
                                <input class="form-control" type="date">
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
                                <label for="">Fecha inicio</label>
                                <input class="form-control" type="date">
                                <label for="">Fecha fin</label>
                                <input class="form-control" type="date">
                            </div>
                        </div>

                        <div class="col-md-2 d-flex justify-content-center aling-items-center">
                            <label for="">Octubre</label>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Fecha inicio</label>
                                <input class="form-control" type="date">
                                <label for="">Fecha fin</label>
                                <input class="form-control" type="date">
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
                                <label for="">Fecha inicio</label>
                                <input class="form-control" type="date">
                                <label for="">Fecha fin</label>
                                <input class="form-control" type="date">
                            </div>
                        </div>

                        <div class="col-md-2 d-flex justify-content-center aling-items-center">
                            <label for="">Diciembre</label>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Fecha inicio</label>
                                <input class="form-control" type="date">
                                <label for="">Fecha fin</label>
                                <input class="form-control" type="date">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <button class="btn bg-gradient-warning">Guardar cambios</button>
                        </div>
                    </div>
                </form>
                <hr class="horizontal dark my-3">
                {{-- Nuevo año --}}
            </div>
        </div>
    </div>
</div>