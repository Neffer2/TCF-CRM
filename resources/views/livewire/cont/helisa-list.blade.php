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
                    <th>Tipo Doc</th>
                    <th>N&uacute;mero Doc</th>
                    <th>Concepto</th>
                    <th>Identidad</th>
                    <th>Nombre del tercero</th>
                    <th>Centro costo</th>
                    <th>Nombre Centro de Costo</th>
                    <th>Debito</th>
                    <th>Credito</th>
                    <th>Comercial</th>
                    <th>Cuenta</th>
                    <th>Participaci&oacute;n</th>
                    <th>Base factura</th>
                    <th>Mes</th>
                    <th>Año</th>
                    <th>Comision</th> 
                    <th>Acciones</th> 
                </tr>  
                </thead> 
                <tbody>  
                    @foreach ($list as $key => $item)  
                        <tr>
                            <td class="text-sm font-weight-normal">{{ $key+=1 }}</td>
                            <td class="text-sm font-weight-normal">{{ $item->fecha }}</td>
                            <td class="text-sm font-weight-normal">{{ $item->tipo_doc }}</td>
                            <td class="text-sm font-weight-normal">{{ $item->num_doc }}</td>
                            <td class="text-sm font-weight-normal">{{ $item->concepto }}</td>
                            <td class="text-sm font-weight-normal">{{ $item->identidad }}</td>
                            <td class="text-sm font-weight-normal">{{ $item->nom_tercero }}</td>
                            <td class="text-sm font-weight-normal">{{ $item->centro }}</td>
                            <td class="text-sm font-weight-normal">{{ $item->nom_centro_costo }}</td>
                            <td class="text-sm font-weight-normal">{{ $item->debito }}</td>
                            <td class="text-sm font-weight-normal">{{ $item->credito }}</td> 
                            <td class="text-sm font-weight-normal">{{ $item->comercial_user->name }}</td>
                            <td class="text-sm font-weight-normal">{{ $item->cuenta->description }}</td>
                            <td class="text-sm font-weight-normal">{{ $item->participacion }}</td>
                            <td class="text-sm font-weight-normal">{{ $item->base_factura }}</td>
                            <td class="text-sm font-weight-normal">{{ $item->mes }}</td>
                            <td class="text-sm font-weight-normal">{{ $item->año }}</td>
                            <td class="text-sm font-weight-normal">{{ $item->comision }}</td>
                            <td class="text-sm font-weight-normal">
                                <button class="btn bg-gradient-danger btn-sm mb-0" data-bs-toggle="modal" data-bs-target="#modal{{ $item->id }}">Eliminar</button>
                                <button class="btn bg-gradient-primary btn-sm mb-0" data-bs-toggle="modal" data-bs-target="#editmodal{{ $item->id }}"> Editar </button>
                            </td>
                        </tr>
                        {{-- edit modal --}} 
                        <div class="modal fade" id="editmodal{{ $item->id }}" tabindex="-1" aria-labelledby="editModal" aria-hidden="true">
                            <div class="modal-dialog"> 
                                <form action="{{ route('update-helisa', $item->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="exampleModalLabel">Editar</h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="fecha">Fecha:</label>
                                                        <input id="fecha" type="date" name="fecha" class="form-control" value="{{ $item->fecha }}" placeholder="Nombre" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group"> 
                                                        <label for="tipo_doc">Tipo Doc:</label>
                                                        <input id="tipo_doc" type="text" name="tipo_doc" class="form-control" value="{{ $item->tipo_doc }}" placeholder="Tipo documento" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-grup">
                                                        <label for="num_doc">N&uacute;mero Documento:</label>
                                                        <input id="num_doc" type="text" name="num_doc" class="form-control" value="{{ $item->num_doc }}" placeholder="N&uacute;mero Documento" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="concepto">Concepto:</label>
                                                        <input id="concepto" type="text" name="concepto" class="form-control" value="{{ $item->concepto }}" placeholder="Concepto" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="identidad">Identidad:</label>
                                                        <input id="identidad" type="text" name="identidad" class="form-control" value="{{ $item->identidad }}" placeholder="Identidad" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="nom_tercero">Nombre Tercero</label>
                                                        <input id="nom_tercero" type="text" name="nom_tercero" class="form-control" value="{{ $item->nom_tercero }}" placeholder="Nombre Tercero">
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="centro">Centro de costos:</label>
                                                        <input id="centro" type="text" name="centro" class="form-control" value="{{ $item->centro }}" placeholder="Centro de costos">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="nom_centro_costo">Nombre centro de costos: </label>
                                                        <input id="nom_centro_costo" type="text" name="nom_centro_costo" class="form-control" value="{{ $item->nom_centro_costo }}" placeholder="Nombre centro de costos">
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="debito">D&eacute;bito:</label>
                                                        <input id="debito" type="number" name="debito" class="form-control" value="{{ $item->debito }}" placeholder="D&eacute;bito">
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="credito">Cr&eacute;dito: </label>
                                                        <input id="credito" type="number" name="credito" class="form-control" value="{{ $item->credito }}" placeholder="Cr&eacute;dito">
                                                    </div>
                                                </div>
                                                <div class="col-md-1">
                                                    <div class="form-group">
                                                        <label for="porcentaje">%</label>
                                                        <input id="porcentaje" type="number" name="porcentaje" class="form-control" value="{{ $item->porcentaje }}" placeholder="%">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="comercial">Comercial: </label>
                                                        <select id="comercial" type="text" name="comercial" class="form-control" value="">
                                                            <option value="">Seleccionar</option>
                                                            @foreach ($comerciales as $comercial)
                                                                @if ($comercial->id == $item->comercial_user->id)
                                                                    <option selected value="{{ $comercial->id }}">{{ $comercial->name }}</option>
                                                                @else
                                                                    <option value="{{ $comercial->id }}">{{ $comercial->name }}</option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="id_cuenta">Cuenta: </label>
                                                        <select id="id_cuenta" name="id_cuenta" class="form-control" value="" placeholder="Estado">
                                                            <option value="">Seleccionar</option>
                                                            @foreach ($cuentas as $cuenta)
                                                                @if ($cuenta->id == $item->cuenta->id)
                                                                    <option selected value="{{ $cuenta->id }}">{{ $cuenta->description }}</option>
                                                                @else
                                                                    <option value="{{ $cuenta->id }}">{{ $cuenta->description }}</option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="participacion">Participaci&oacute;n: </label>
                                                        <input id="participacion" type="text" name="participacion" class="form-control" value="{{ $item->participacion }}" placeholder="Participaci&oacute;n">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="base_factura">Base factura: </label>
                                                        <input id="base_factura" type="number" name="base_factura" class="form-control" value="{{ $item->base_factura }}" placeholder="Base factura">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="año">Año: </label>
                                                        <select id="año" type="date" name="año" class="form-control">
                                                            <option value="">Seleccionar</option>
                                                            @foreach ($años as $año)
                                                                @if ($año->description == $item->año)
                                                                    <option selected value="{{ $año->description }}">{{ $año->description }}</option>
                                                                @else
                                                                    <option value="{{ $año->description }}">{{ $año->description }}</option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="mes">Mes: </label>
                                                        <select id="mes" type="date" name="mes" class="form-control">
                                                            <option value="">Seleccionar</option>
                                                            @foreach ($meses as $mes)
                                                                @if ($mes->description == $item->mes)
                                                                    <option selected value="{{ $mes->description }}">{{ $mes->description }}</option>    
                                                                @else
                                                                    <option value="{{ $mes->description }}">{{ $mes->description }}</option>    
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4"> 
                                                    <div class="form-group">
                                                        <label for="comision">Comisi&oacute;n: </label>
                                                        <input id="comision" type="number" name="comision" class="form-control" value="{{ $item->comision }}" placeholder="Comisi&oacute;n">
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <button class="btn bg-gradient-warning">Guardar cambios</button>
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
                        {{-- delete modal --}}
                        <div class="modal fade" id="modal{{ $item->id }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <form action="{{ route('delete-registro', $item->id) }}" method="POST">
                                @csrf 
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="exampleModalLabel">¿Estas seguro?</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                            ¿Desea eliminar el registro: <b>{{ $item->concepto }}</b>?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Cancelar</button>
                                            <button type="submit" class="btn bg-gradient-warning">Eliminar</button>
                                        </div>
                                    </div> 
                                </div>
                            </form>
                        </div> 
                    @endforeach
                </tbody>
            </table>
        </div>
        <br>
        <!-- Button trigger modal -->
        <button type="button" class="btn bg-gradient-warning" data-bs-toggle="modal" data-bs-target="#exampleModal">
            Vaciar Helisa
        </button>

        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Advertencia</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Los datos se eliminar&aacute;n permanentemente.
                    </div>
                    <form action="{{ route('vaciar_helisa') }}" method="POST">
                        @csrf
                        <div class="modal-footer">
                            <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn bg-gradient-warning">Vaciar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
 
        <form action="{{ route('helisa-export', auth()->user()->id) }}" method="POST" class="d-flex justify-content-center">
            @csrf
            <button type="submit" class="btn bg-gradient-warning mt-3">
                <span class="btn-inner--icon"><i class="ni ni-cloud-download-95 me-1"></i></span>
                <span class="btn-inner--text">Descargar Reporte Helisa</span>
            </button>
        </form>
    </div>
</div>