<div>
    <div class="card">
        <div class="card-header p-0 mx-3 mt-3 position-relative z-index-1 col-md-6">
            <div class="row">
                <div class="col-md-12">
                    <h3 class="mb-0">Anticipos</h3>
                    <p class="text-sm mb-0">Lista de anticipos pendientes por revisar.</p>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table align-items-center mb-0">
                <thead>
                    <tr>
                        <th colspan="8" class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">DATOS DEL ANTICIPO</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        @foreach ($anticipos as $key => $anticipo)
                            {{-- ANTICIPOS JURIDICOS --}}
                            @if ($anticipo->oc_id)
                            {{-- ANTICIPOS PRODUCTOR --}}
                            @elseif ($anticipo->presupuesto_id)
                                <tr>
                                    <td style="width: 16rem;">
                                        <div class="d-flex px-2 py-1" title="Orden #{{ $anticipo->id }}">
                                            <div>
                                                <img src="https://www.bullmarketing.com.co/wp-content/uploads/2022/04/cropped-favicon-bull-192x192.png" class="avatar avatar-sm me-3">
                                            </div>
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-xs">Anticipo #{{ $anticipo->id }}</h6>
                                                <p class="text-xs text-secondary mb-0">Centro de costo: {{ $anticipo->presupuesto->cod_cc }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">Valor Anticipo</p>
                                        <p class="text-xs text-secondary mb-0">${{ number_format($anticipo->total_anticipo, 0, ',', '.') }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">Productor</p>
                                        <p class="text-xs text-secondary mb-0">{{ $anticipo->productor_info->name }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">Estado</p>
                                        <p class="text-xs text-secondary mb-0">{{ $anticipo->estado->description }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">Fecha</p>
                                        <p class="text-xs text-secondary mb-0">{{ $anticipo->fecha_solicitud }}</p>
                                    </td>
                                    <td class="">
                                        <a href="{{ route('anticipo-lid', ['anticipo_id' => $anticipo->id]) }}" class="btn bg-gradient-primary mb-0" title="Ver detalle" data-toggle="tooltip" target="_blank">
                                            Ver
                                        </a>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                        @php
                            $anticiposArray = $anticipos->toArray();
                            $registros_page = sizeof($anticiposArray['data']);
                            $total = $anticiposArray['total'];
                        @endphp
                        <td colspan="1" class="d-flex text-xs text-secondary mb-0">Mostrando {{ $registros_page }} registros de {{ $total }}.</td>
                        <td colspan="5" class="d-flex pt-0">{{ $anticipos->links() }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
