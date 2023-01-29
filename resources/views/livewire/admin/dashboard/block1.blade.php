<div class="tab-content" id="v-pills-tabContent">
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card @if($venta_facturada < $presto_mensual) bg-gradient-danger @elseif($venta_facturada > $presto_mensual)  bg-gradient-success @endif">
                <div class="card-body p-3">
                    <div class="d-flex">
                        <div class="numbers">
                            <p class="@if($venta_facturada < $presto_mensual) text-white @elseif($venta_facturada > $presto_mensual) text-white @endif text-sm mb-0 text-uppercase font-weight-bold">VENTA FACTURADA</p>
                            <h5 class="@if($venta_facturada < $presto_mensual) text-white @elseif($venta_facturada < $presto_mensual) text-white @endif font-weight-bolder mb-0"><br>${{ number_format($venta_facturada,2,".",",") }}</h5>
                        </div>
                        <div class="icon icon-shape bg-gradient-dark text-center rounded-circle ms-auto">
                            <i class="ni ni-collection text-lg opacity-10" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mt-md-0 mt-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="d-flex">
                        <div class="numbers">
                            <p class="text-sm mb-0 text-uppercase font-weight-bold">PRESUPUESTO MENSUAL</p>
                            <h5 class="font-weight-bolder mb-0"><br>${{ number_format($presto_mensual,2,".",",") }}</h5>
                        </div>
                        <div class="icon icon-shape bg-gradient-dark text-center rounded-circle ms-auto">
                            <i class="ni ni-book-bookmark text-lg opacity-10" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mt-md-0 mt-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-black text-uppercase text-sm mb-0 opacity-7">CUMPLIMIENTO VTA MENSUAL</p>
                                <h5 class="text-black font-weight-bolder mb-0">
                                    {{ sprintf("%.1f", $cumpli_venta_men) }} %
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-white shadow text-center border-radius-md">
                                <i class="ni ni-chart-pie-35 text-dark text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card @if($venta_consolidada < $presto_acumulado) bg-gradient-danger @elseif($venta_consolidada > $presto_acumulado)  bg-gradient-success @endif">
                <div class="card-body p-3">
                    <div class="d-flex">
                        <div class="numbers">
                            <p class="@if($venta_consolidada < $presto_acumulado) text-white @elseif($venta_consolidada > $presto_acumulado) text-white @endif text-sm mb-0 text-uppercase font-weight-bold">VENTA CONSOLIDADA</p>
                            <h5 class="font-weight-bolder mb-0"><br>${{ number_format($venta_consolidada,2,".",",") }}</h5>
                        </div>
                        <div class="icon icon-shape bg-gradient-dark text-center rounded-circle ms-auto">
                            <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mt-md-0 mt-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="d-flex">
                        <div class="numbers">
                            <p class="text-sm mb-0 text-uppercase font-weight-bold">PRESUPUESTO ACUM</p>
                            <h5 class="font-weight-bolder mb-0"><br>${{ number_format($presto_acumulado,2,".",",") }}</h5>
                        </div>
                        <div class="icon icon-shape bg-gradient-dark text-center rounded-circle ms-auto">
                            <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mt-md-0 mt-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-black text-uppercase text-sm mb-0 opacity-7">CUMPLIMIENTO VTA ACUMULADO</p>
                                <h5 class="text-black font-weight-bolder mb-0">
                                    {{ sprintf("%.1f", $cumpli_acum_venta_men) }} %
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-white shadow text-center border-radius-md">
                                <i class="ni ni-chart-pie-35 text-dark text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-md-4"></div>
        <div class="col-md-4 mt-md-0 mt-4"></div>
        <div class="col-md-4 mt-md-0 mt-4">
            <div class="card bg-gradient-danger">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8"> 
                            <div class="numbers">
                                <p class="text-white text-uppercase text-sm mb-0 opacity-7">PRESUPUESTO POR CUMPLIR</p>
                                <h5 class="text-white font-weight-bolder mb-0">
                                    {{ sprintf("%.1f", $presto_x_cumplir) }} %
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-white shadow text-center border-radius-md">
                                <i class="ni ni-chart-pie-35 text-dark text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
 