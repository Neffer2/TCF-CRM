<div class="col-lg-12 col-12 mx-auto">
    <div class="card card-body mt-4">
        <!-- Card header --> 
        <div class="card-header">
            <h5 class="mb-0">Base comercial</h5>            
        </div>
        <div class="table-responsive">
            <table class="table table-flush" id="datatable-search">
                <thead class="thead-light">
                <tr> 
                    <th>#</th>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>Proyecto</th> 
                    <th>COD_CC</th>
                    <th>Valor</th> 
                    <th>Estado</th>
                    <th>Inicio</th>
                    <th>Fin</th>
                    <th>ACCIONES</th>
                </tr> 
                </thead> 
                <tbody>   
                    @foreach ($list as $key => $item)
                    <tr> 
                        <td>{{ $key+=1 }}</td>
                        <td class="text-sm font-weight-normal">{{ $item->fecha }}</td>
                        <td class="text-sm font-weight-normal">
                            {{ $item->nom_cliente }}
                        </td>
                        <td class="text-sm font-weight-normal">
                            {{ $item->nom_proyecto }}
                        </td>
                        <td class="text-sm font-weight-normal">
                            {{ $item->cod_cc }}
                        </td>
                        <td class="text-sm font-weight-normal">
                            {{ number_format($item->valor_proyecto) }}
                        </td>
                        <td>
                            <form action="{{ route('update-proyecto', $item->id) }}" method="POST">
                                @csrf
                                <select name="estado_id" onchange="this.form.submit()" class="form-control" style="cursor: pointer; width: 171px">
                                    @foreach ($estados as $estado)
                                        @if ($item->estado_cuenta->id == $estado->id)
                                            <option selected value="{{ $estado->id }}">{{ $estado->description }}</option>
                                        @else 
                                            <option value="{{ $estado->id }}">{{ $estado->description }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </form>
                        </td>
                        <td class="text-sm font-weight-normal">{{ $item->fecha_inicio }}</td>
                        <td class="text-sm font-weight-normal">{{ $item->dura_mes }}</td>
                        <td  colspan="2">
                            <button class="btn bg-gradient-danger btn-sm mb-0" data-bs-toggle="modal" data-bs-target="#modal{{ $item->id }}">Eliminar</button>
                            <button class="btn bg-gradient-primary btn-sm mb-0" data-bs-toggle="modal" data-bs-target="#editmodal{{ $item->id }}"> Editar </button>
                        </td>
                    </tr> 
                    <div class="modal fade" id="editmodal{{ $item->id }}" tabindex="-1" aria-labelledby="editModal" aria-hidden="true">
                        <div class="modal-dialog"> 
                            <form action="{{ route('update-proyecto', $item->id) }}" method="POST">
                                @csrf
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="exampleModalLabel">Editar proyecto</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="">Cliente</label>
                                                    <input name="nom_cliente" class="form-control" type="text" value="{{ $item->nom_cliente }}" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="">Proyecto</label>
                                                    <input name="nom_proyecto" class="form-control" type="text" value="{{ $item->nom_proyecto }}" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="">COD_CC</label>
                                                    <input name="cod_cc" class="form-control" type="text" value="{{ $item->cod_cc }}" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="">Valor</label>
                                                    <input name="valor_proyecto" class="form-control" type="number" value="{{ $item->valor_proyecto }}" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="">Estado</label>
                                                    <select name="estado_id" class="form-control" style="cursor: pointer; width: 171px">
                                                        @foreach ($estados as $estado)
                                                            @if ($item->estado_cuenta->id == $estado->id)
                                                                <option selected value="{{ $estado->id }}">{{ $estado->description }}</option>
                                                            @else 
                                                                <option value="{{ $estado->id }}">{{ $estado->description }}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Cancelar</button>
                                        <button type="submit" class="btn bg-gradient-primary">Guardar cambios</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="modal fade" id="modal{{ $item->id }}" tabindex="-1" aria-labelledby="Modal" aria-hidden="true">
                        <div class="modal-dialog">
                            <form action="{{ route('delete-proyecto', $item->id) }}" method="POST">
                            @csrf
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exampleModalLabel">¿Estas seguro?</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                        Desea eliminar el proyeto: <b>{{ $item->nom_proyecto }}</b>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Cancelar</button>
                                        <button type="submit" class="btn bg-gradient-danger">Eliminar</button>
                                    </div>
                                </div> 
                            </div>
                        </form>
                    </div>
                    @endforeach
                </tbody>
            </table>
        </div>

        <form action="{{ route('base-export', auth()->user()->id) }}" method="POST" class="d-flex justify-content-center">
            @csrf
            <button type="submit" class="btn bg-gradient-warning mt-3">
                <span class="btn-inner--icon"><i class="ni ni-cloud-download-95 me-1"></i></span>
                <span class="btn-inner--text">Descargar Base Comercial</span>
            </button>
        </form>
    </div>
</div>
