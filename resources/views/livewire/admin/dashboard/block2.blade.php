<div class="card">
    <div class="card-header pb-0">
        <div class="row">
            <div class="col-md-6 text-center">
                <h6 class="mb-0 font-weight-bolder">ESTADO DE FACTURACI&Oacute;N</h6>
            </div>
            <div class="col-md-6 text-center">
                <h6 class="mb-0 font-weight-bolder">SUMATORIA DE VENTAS</h6>
            </div>
        </div>                    
    </div>
    <div class="card-body pt-1">
        <div class="row"> 
            <div class="col-md-6 text-center d-flex justify-content-center">
                <div class="">
                    <div class="p-3">
                        <div class="d-flex">
                            <div class="numbers">
                                <a href="{{ route('estados', ['año' => $año, 'mes' => $mes, 'comercial' => $comercial]) }}" target="_blank">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">
                                        EJECUCIÓN X FACTURAR
                                    </p>
                                    <h5 class="font-weight-bolder mb-0">${{ number_format($xfacturar,2,".",",") }}</h5>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 text-center d-flex justify-content-center pb-3">
                <div class="d-flex align-items-end">
                    <div class="numbers">
                        <h5 class="font-weight-bolder mb-0">${{ number_format($sum_1,2,".",",") }}</h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 text-center d-flex justify-content-center">
                <div class="">
                    <div class="p-3">
                        <div class="d-flex">
                            <div class="numbers"> 
                                <a href="{{ route('estados', ['año' => $año, 'mes' => $mes, 'comercial' => $comercial]) }}" target="_blank">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">
                                        VENTA<br>EJECUCIÓN
                                    </p>
                                    <h5 class="font-weight-bolder mb-0">${{ number_format($ventaejecucion,2,".",",") }}</h5>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 text-center d-flex justify-content-center pb-3">
                <div class="d-flex align-items-end">
                    <div class="numbers">
                        <h5 class="font-weight-bolder mb-0">${{ number_format($sum_2,2,".",",") }}</h5>
                    </div>
                </div>
            </div>
        </div>  
        <div class="row">
            <div class="col-md-6 text-center d-flex justify-content-center">
                <div class="">
                    <div class="p-3">
                        <div class="d-flex">
                            <div class="numbers">
                                <a href="{{ route('estados', ['año' => $año, 'mes' => $mes, 'comercial' => $comercial]) }}" target="_blank">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">
                                        VENTA
                                    </p>
                                    <h5 class="font-weight-bolder mb-0">${{ number_format($venta,2,".",",") }}</h5>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 text-center d-flex justify-content-center pb-3">
                <div class="d-flex align-items-end">
                    <div class="numbers">
                        <h5 class="font-weight-bolder mb-0">${{ number_format($sum_3,2,".",",") }}</h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 text-center d-flex justify-content-center">
                <div class="">
                    <div class="p-3">
                        <div class="d-flex">
                            <div class="numbers text-aling">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">VENTA TOTAL</p>
                                <h5 class="font-weight-bolder mb-0">${{ number_format($ventatotal,2,".",",") }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div> 
        </div>

        <div class="row">
            <div class="row mb-4">
                <div class="col-lg-12">
                    <div class="d-flex flex-column h-100 text-center">
                        <h6 class="font-weight-bolder mb-0 mt-4 fadeIn1 fadeInBottom">
                            % ESTADO POR FACTURAR + VENTA FACTURADA
                        </h6>
                    </div> 
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="text-center font-weight-bolder mb-0 mt-4 fadeIn1 fadeInBottom">
                            VF + EXF
                        </h6>
                    </div>
                    <div class="card-body text-center">
                        <h3 class="text-gradient text-primary"><span countto="{{ $per_1 }}">{{ sprintf("%.1f", $per_1) }}</span> <span class="text-lg ms-n2">%</span></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4"> 
                <div class="card">
                    <div class="card-header">
                        <h6 class="text-center font-weight-bolder mb-0 mt-4 fadeIn1 fadeInBottom">
                            VF + EXF + VE
                        </h6>
                    </div>
                    <div class="card-body text-center">
                        <h3 class="text-gradient text-primary"><span countto="{{ $per_2 }}">{{ sprintf("%.1f", $per_2) }}</span> <span class="text-lg ms-n2">%</span></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="text-center font-weight-bolder mb-0 mt-4 fadeIn1 fadeInBottom">
                            VF + EXF +VE + V
                        </h6>
                    </div>
                    <div class="card-body text-center">
                        <h3 class="text-gradient text-primary"><span countto="{{ $per_3 }}">{{ sprintf("%.1f", $per_3) }}</span> <span class="text-lg ms-n2">%</span></h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>