<div>
    <div class="card">
        <div class="card-header p-0 px-3 mt-3 position-relative z-index-1 col-md-12">
            <div class="row">            
                <div class="col-md-12">
                    <h3 class="mb-0">Facturaci&oacute;n Helisa</h3> 
                    <p class="text-sm mb-0">Aqu&iacute; encontrar&aacute;s toda la facturaci&oacute;n de Helisa.</p>
                </div>
                <div class="form-group col-md-1">
                    <label for="comercial">Año:</label>
                    <select wire:model="año" class="form-control">
                        <option value="">Seleccionar</option>
                        @foreach ($años as $año)
                            <option value="{{ $año->id }}">{{ $año->description }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <label for="comercial">Buscar:</label> 
                    <input type="text" wire:model="centro" class="form-control" placeholder="Centro de costos">
                </div> 
                <div class="form-group col-md-2">
                    <label for="comercial">Fecha:</label> 
                    <select wire:model="orderBy" class="form-control">
                        <option value="DESC">Seleccionar</option>
                        <option value="ASC">Mas antiguos</option>
                        <option value="DESC">Mas recientes</option>
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <label for="mes">Mes:</label> 
                    <select wire:model="mes" class="form-control">
                        <option value="">Seleccionar</option>
                        @foreach ($meses as $mes)
                            <option value="{{ $mes->description }}">{{ $mes->description }}</option>
                        @endforeach
                    </select>
                </div>
            </div> 
        </div>
        <div class="card-body">
            <div class="table-responsive"> 
                <table class="table font-table align-items-center table-striped mb-0">
                    <thead class="font-weight-bold bg-gradient-warning text-white">
                        <tr class="text-center"> 
                            <th>FECHA</th>
                            <th>TIPO DOC</th>
                            <th>N&Uacute;MERO DOC</th>
                            <th>IDENTIDAD</th>
                            <th>NOMBRE DEL TERCERO</th>
                            <th>CENTRO COSTOS</th>
                            <th>NOMBRE CENTRO DE COSTOS</th>
                            <th>D&Eacute;BITO</th>
                            <th>CR&Eacute;DITO</th>
                            <th>COMERCIAL</th>
                            <th>CUENTA</th>
                            <th>PARTICIPACI&Oacute;N</th>
                            <th>BASE FACTURA</th>
                            <th>MES</th>
                            <th>AÑO</th>
                            <th>COMISI&Oacute;N</th> 
                            <th>ACCIONES</th>
                        </tr> 
                    </thead>
                    <tbody> 
                        @foreach ($registrosHelisa as $key => $registroHelisa)  
                            <tr>
                                <td class="text-center text-sm font-weight-normal">{{ $registroHelisa->fecha }}</td>
                                <td class="text-center text-sm font-weight-normal">{{ $registroHelisa->tipo_doc }}</td>
                                <td class="text-center text-sm font-weight-normal">{{ $registroHelisa->num_doc }}</td>
                                <td class="text-center text-sm font-weight-normal">{{ $registroHelisa->identidad }}</td>
                                <td class="text-sm font-weight-normal">{{ $registroHelisa->nom_tercero }}</td>
                                <td class="text-center text-sm font-weight-normal">{{ $registroHelisa->centro }}</td>
                                <td class="text-sm font-weight-normal">{{ $registroHelisa->nom_centro_costo }}</td>
                                <td class="text-center text-sm font-weight-normal">$ {{ number_format($registroHelisa->debito) }}</td>
                                <td class="text-center text-sm font-weight-normal">$ {{ number_format($registroHelisa->credito) }}</td> 
                                <td class="text-sm font-weight-normal">{{ $registroHelisa->comercial_user->name }}</td>
                                <td class="text-sm font-weight-normal">{{ $registroHelisa->cuenta->description }}</td>
                                <td class="text-center text-sm font-weight-normal">{{ $registroHelisa->participacion }}</td>
                                <td class="text-sm font-weight-normal">$ {{ number_format($registroHelisa->base_factura) }}</td>
                                <td class="text-sm font-weight-normal">{{ $registroHelisa->mes }}</td>
                                <td class="text-center text-sm font-weight-normal">{{ $registroHelisa->año }}</td>
                                <td class="text-center text-sm font-weight-normal">$ {{ number_format($registroHelisa->comision) }}</td>
                                <td class="text-sm font-weight-normal">
                                    <button class="btn bg-gradient-danger btn-sm mb-0" data-bs-toggle="modal" data-bs-target="#modal{{ $registroHelisa->id }}">Eliminar</button>
                                    <button class="btn bg-gradient-primary btn-sm mb-0" data-bs-toggle="modal" data-bs-target="#editmodal{{ $registroHelisa->id }}"> Editar </button>
                                </td>
                            </tr>
                            {{-- edit modal --}}   
                            <div class="modal fade" id="editmodal{{ $registroHelisa->id }}" tabindex="-1" aria-labelledby="editModal" aria-hidden="true">
                                <div :wire:key="'proyecto-'.$registroHelisa->id" class="modal-dialog">
                                    @livewire('com.helisa.edit', ['id_helisa' => $registroHelisa->id, key('proyecto-'.$registroHelisa->id)])  
                                </div>
                            </div> 
                            {{-- delete modal --}} 
                            <div class="modal fade" id="modal{{ $registroHelisa->id }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div :wire:key="'delete-'.$registroHelisa->id" class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="exampleModalLabel">¿Estas seguro?</h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body"> 
                                            ¿Deseas eliminar &eacute;ste registro?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Cancelar</button>
                                            <button type="submit" class="btn bg-gradient-warning" data-bs-dismiss="modal" wire:click="delete_registro({{ $registroHelisa->id }})">Eliminar</button>
                                        </div>
                                    </div> 
                                </div>
                            </div> 
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="row p-2">
                <div class="col-md-6">
                    @php
                        $registrosHelisaArray = $registrosHelisa->toArray();
                        $registros_page = sizeof($registrosHelisaArray['data']);
                        $total = $registrosHelisaArray['total'];
                    @endphp
                    <span class="text-xs text-secondary mb-0">Mostrando {{ $registros_page }} registros de {{ $total }}.</span>        
                </div>
                <div class="col-md-12 table-responsive">
                    {{ $registrosHelisa->links() }}
                </div>
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
</div>