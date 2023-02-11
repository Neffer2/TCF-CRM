<div class="row gy-4">
    @php
        $cont = 0;
        $generalxfacturarAcum = 0;
        $ventaejecicionAcum = 0;
        $ventaAcum = 0;
    @endphp
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-gradient-warning">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="mb-0 text-white font-weight-bolder text-uppercase text-center">
                            Cliente/Proyecto
                        </h6>
                    </div> 
                    <div class="col-md-6">
                        <h6 class="mb-0 text-white font-weight-bolder text-uppercase text-center">
                            Ejecuci&oacute;n x Facturar
                        </h6>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach ($xfacturar as $key => $cliente)
                        <button class="accordion-button border-bottom font-weight-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $cont }}" aria-expanded="false" aria-controls="collapse{{ $cont }}">
                            <div class="col-md-6 text-left">
                                {{ $cliente->nom_cliente }}
                            </div>
                            <div class="col-md-6 text-center">
                                $ {{ number_format($cliente->valor,2,".",",") }}
                            </div>
                            <i class="collapse-close fa fa-plus text-xs pt-1 position-absolute end-0 me-3" aria-hidden="true"></i>
                            <i class="collapse-open fa fa-minus text-xs pt-1 position-absolute end-0 me-3" aria-hidden="true"></i>
                        </button>                                
                        @php
                            $generalxfacturarAcum += $cliente->valor;
                            $proyectos = \App\Models\Base_comercial::select('id','nom_cliente','nom_proyecto','valor_proyecto')->where('nom_cliente', $cliente->nom_cliente)->where('id_estado', 3)->orderBy('fecha')->get();
                        @endphp
                        <div id="collapse{{ $cont }}" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionRental">
                            <div class="accordion-body text-sm opacity-8">
                                <div class="col-md-12">
                                    <div class="tabletable-responsive">
                                        <table class="table align-items-center mb-0">
                                            @foreach($proyectos as $key => $producto)
                                                <tr>
                                                    <td class="text-secondary text-xxs font-weight-bolder opacity-7">{{ $key+1 }}</td>
                                                    <td class="text-secondary text-xxs font-weight-bolder opacity-7">{{ $producto->nom_proyecto }}</td>
                                                    <td class="text-secondary text-xxs font-weight-bolder opacity-7 text-center">$ {{ number_format($producto->valor_proyecto,2,".",",") }}</td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @php
                            $cont++
                        @endphp
                    @endforeach        
                    <hr>
                </div>
            </div>
            <div class="card-footer">
                <h6 class="mb-0 text-black font-weight-bolder text-uppercase text-center">
                    Total general $ {{ number_format($generalxfacturarAcum,2,".",",") }}
                </h6>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-gradient-success">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="mb-0 text-white font-weight-bolder text-uppercase text-center">
                            Cliente/Proyecto
                        </h6>
                    </div>
                    <div class="col-md-6">
                        <h6 class="mb-0 text-white font-weight-bolder text-uppercase text-center">
                            Venta Ejecuci&oacute;n
                        </h6>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach ($ejecucion as $key => $cliente)
                        <button class="accordion-button border-bottom font-weight-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $cont }}" aria-expanded="false" aria-controls="collapse{{ $cont }}">
                            <div class="col-md-6 text-left">
                                {{ $cliente->nom_cliente }}
                            </div>
                            <div class="col-md-6 text-center">
                                $ {{ number_format($cliente->valor,2,".",",") }}
                            </div>
                            <i class="collapse-close fa fa-plus text-xs pt-1 position-absolute end-0 me-3" aria-hidden="true"></i>
                            <i class="collapse-open fa fa-minus text-xs pt-1 position-absolute end-0 me-3" aria-hidden="true"></i>
                        </button>                                
                        @php
                            $ventaejecicionAcum += $cliente->valor;
                            $proyectos = \App\Models\Base_comercial::select('id','nom_cliente','nom_proyecto','valor_proyecto')->where('nom_cliente', $cliente->nom_cliente)->where('id_estado', 7)->orderBy('fecha')->get();
                        @endphp
                        <div id="collapse{{ $cont }}" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionRental">
                            <div class="accordion-body text-sm opacity-8">
                                <div class="col-md-12">
                                    <div class="tabletable-responsive">
                                        <table class="table align-items-center mb-0">
                                            @foreach($proyectos as $key => $producto)
                                                <tr>
                                                    <td class="text-secondary text-xxs font-weight-bolder opacity-7">{{ $key+1 }}</td>
                                                    <td class="text-secondary text-xxs font-weight-bolder opacity-7">{{ $producto->nom_proyecto }}</td>
                                                    <td class="text-secondary text-xxs font-weight-bolder opacity-7 text-center">$ {{ number_format($producto->valor_proyecto,2,".",",") }}</td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @php
                            $cont++
                        @endphp
                    @endforeach        
                    <hr>
                </div>
            </div>
            <div class="card-footer">
                <h6 class="mb-0 text-black font-weight-bolder text-uppercase text-center">
                    Total general $ {{ number_format($ventaejecicionAcum,2,".",",") }}
                </h6>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-gradient-info">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="mb-0 text-white font-weight-bolder text-uppercase text-center">
                            Cliente/Proyecto
                        </h6>
                    </div>
                    <div class="col-md-6">
                        <h6 class="mb-0 text-white font-weight-bolder text-uppercase text-center">
                            Venta
                        </h6>
                    </div> 
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach ($venta as $key => $cliente)
                        <button class="accordion-button border-bottom font-weight-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $cont }}" aria-expanded="false" aria-controls="collapse{{ $cont }}">
                            <div class="col-md-6 text-left">
                                {{ $cliente->nom_cliente }}
                            </div>
                            <div class="col-md-6 text-center">
                                $ {{ number_format($cliente->valor,2,".",",") }}
                            </div>
                            <i class="collapse-close fa fa-plus text-xs pt-1 position-absolute end-0 me-3" aria-hidden="true"></i>
                            <i class="collapse-open fa fa-minus text-xs pt-1 position-absolute end-0 me-3" aria-hidden="true"></i>
                        </button>                                
                        @php
                            $ventaAcum += $cliente->valor;
                            $proyectos = \App\Models\Base_comercial::select('id','nom_cliente','nom_proyecto','valor_proyecto')->where('nom_cliente', $cliente->nom_cliente)->where('id_estado', 6)->orderBy('fecha')->get();
                        @endphp
                        <div id="collapse{{ $cont }}" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionRental">
                            <div class="accordion-body text-sm opacity-8">
                                <div class="col-md-12">
                                    <div class="tabletable-responsive">
                                        <table class="table align-items-center mb-0">
                                            @foreach($proyectos as $key => $producto)
                                                <tr>
                                                    <td class="text-secondary text-xxs font-weight-bolder opacity-7">{{ $key+1 }}</td>
                                                    <td class="text-secondary text-xxs font-weight-bolder opacity-7">{{ $producto->nom_proyecto }}</td>
                                                    <td class="text-secondary text-xxs font-weight-bolder opacity-7 text-center">$ {{ number_format($producto->valor_proyecto,2,".",",") }}</td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @php
                            $cont++
                        @endphp
                    @endforeach        
                    <hr>
                </div>
            </div>
            <div class="card-footer">
                <h6 class="mb-0 text-black font-weight-bolder text-uppercase text-center">
                    Total general $ {{ number_format($ventaAcum,2,".",",") }}
                </h6>
            </div>
        </div>
    </div>
</div> 