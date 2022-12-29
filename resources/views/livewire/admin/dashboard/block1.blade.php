<div class="tab-content" id="v-pills-tabContent">
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="d-flex">
                        <div class="numbers">
                            <p class="text-sm mb-0 text-uppercase font-weight-bold">VENTA FACTURADA</p>
                            <h5 class="font-weight-bolder mb-0">$ {{ $venta_facturada }}</h5>
                        </div>
                        <div class="icon icon-shape bg-gradient-dark text-center rounded-circle ms-auto">
                            <i class="ni ni-collection text-lg opacity-10" aria-hidden="true"></i>
                        </div>
                    </div>
                    <p class="mb-0">
                        Diciembre -  {{ date("Y") }}
                    </p> 
                </div>
            </div>
        </div>
        <div class="col-md-4 mt-md-0 mt-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="d-flex">
                        <div class="numbers">
                            <p class="text-sm mb-0 text-uppercase font-weight-bold">PRESUPUESTO MENSUAL</p>
                            <h5 class="font-weight-bolder mb-0">$ {{ $presto_mensual }}</h5>
                        </div>
                        <div class="icon icon-shape bg-gradient-dark text-center rounded-circle ms-auto">
                            <i class="ni ni-book-bookmark text-lg opacity-10" aria-hidden="true"></i>
                        </div>
                    </div>
                    <p class="mb-0">
                        Mes - año
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mt-md-0 mt-4">
            <div class="card bg-gradient-danger">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-white text-uppercase text-sm mb-0 opacity-7">CUMPLIMIENTO VTA MENSUAL</p>
                                <h5 class="text-white font-weight-bolder mb-0">
                                    {{ $cumpli_acum_venta_men }} %
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
            <div class="card">
                <div class="card-body p-3">
                    <div class="d-flex">
                        <div class="numbers">
                            <p class="text-sm mb-0 text-uppercase font-weight-bold">VENTA CONSOLIDADA</p>
                            <h5 class="font-weight-bolder mb-0">$ {{ $venta_consolidada }}</h5>
                        </div>
                        <div class="icon icon-shape bg-gradient-dark text-center rounded-circle ms-auto">
                            <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
                        </div>
                    </div>
                    <p class="mb-0">
                        Mes - año
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mt-md-0 mt-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="d-flex">
                        <div class="numbers">
                            <p class="text-sm mb-0 text-uppercase font-weight-bold">PRESUPUESTO ACUMULADO</p>
                            <h5 class="font-weight-bolder mb-0">$ {{ $presto_acumulado }}</h5>
                        </div>
                        <div class="icon icon-shape bg-gradient-dark text-center rounded-circle ms-auto">
                            <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
                        </div>
                    </div>
                    <p class="mb-0">
                        Mes - año
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mt-md-0 mt-4">
            <div class="card bg-gradient-success">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-white text-uppercase text-sm mb-0 opacity-7">CUMPLIMIENTO VTA MENSUAL</p>
                                <h5 class="text-white font-weight-bolder mb-0">
                                    {{ $cumpli_venta_men }} %
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
            <div class="card bg-gradient-warning">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-white text-uppercase text-sm mb-0 opacity-7">CUMPLIMIENTO VTA ACUMULADO</p>
                                <h5 class="text-white font-weight-bolder mb-0">
                                    {{ $presto_x_cumplir }} %
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
 