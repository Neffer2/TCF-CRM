@extends('layouts.admin.main')
    @section('hero-style')
        <div class="position-absolute w-100 min-height-300 top-0" style="background-image: url('{{ asset('assets/img/hero-2.jpg') }}'); background-position-y: 50%;">
            <span class="mask bg-gradient-warning opacity-6"></span>
        </div>
        {{-- <div class="min-height-300 bg-gradient-warning position-absolute w-100"></div>  --}}
    @endsection
    @section('content')
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header d-flex pb-0 justify-content-center">
                    <div class="">
                        <h4 class="font-weight-bolder mb-0">CUMPLIMIENTO DEL PRESUPUESTO COMERCIAL</h4>
                    </div>
                </div>
                <div class="card-body"></div>
            </div>
        </div>
        <hr>
        <div class="col-xl-7">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">
                                    <h6 class="mb-0 font-weight-bolder text-uppercase">
                                        Mes
                                    </h6>
                                </label>
                                <select name="" id="" class="form-control">
                                    <option value="">General</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">
                                    <h6 class="mb-0 font-weight-bolder text-uppercase">
                                        Comerical
                                    </h6>
                                </label>
                                <select name="" id="" class="form-control">
                                    <option value="">General</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-1">
                    <div class="tab-content" id="v-pills-tabContent">
                        <div class="row mt-4">
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body p-3">
                                        <div class="d-flex">
                                            <div class="numbers">
                                                <p class="text-sm mb-0 text-uppercase font-weight-bold">VENTA FACTURADA</p>
                                                <h5 class="font-weight-bolder mb-0">$87,000</h5>
                                            </div>
                                            <div class="icon icon-shape bg-gradient-dark text-center rounded-circle ms-auto">
                                                <i class="ni ni-collection text-lg opacity-10" aria-hidden="true"></i>
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
                                                <p class="text-sm mb-0 text-uppercase font-weight-bold">PRESUPUESTO MENSUAL</p>
                                                <h5 class="font-weight-bolder mb-0">$87,000</h5>
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
                                                        99 %
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
                                                <h5 class="font-weight-bolder mb-0">$87,000</h5>
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
                                                <h5 class="font-weight-bolder mb-0">$87,000</h5>
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
                                                        99 %
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
                                                        99 %
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
                </div>
            </div>
        </div>
        <div class="col-xl-5 ms-auto mt-xl-0 mt-4">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <h6 class="mb-0 font-weight-bolder">ESTADO DE FACTURACI&Oacute;N</h6>
                        </div>
                        <div class="col-md-4 text-center">
                            <h6 class="mb-0 font-weight-bolder">SUMATORIA DE VENTAS</h6>
                        </div>
                        <div class="col-md-4 text-center">
                            <h6 class="mb-0 font-weight-bolder">% ESTADO POR FACTURAR + VENTA FACTURADA</h6>
                        </div>
                    </div>                    
                </div>
                <div class="card-body pt-1">
                    <div class="row">
                        <div class="col-md-4 text-center d-flex justify-content-center">
                            <div class="">
                                <div class="p-3">
                                    <div class="d-flex">
                                        <div class="numbers">
                                            <p class="text-sm mb-0 text-uppercase font-weight-bold">EJECUCIÓN X FACTURAR</p>
                                            <h5 class="font-weight-bolder mb-0">$87,000</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-center d-flex justify-content-center pb-3">
                            <div class="d-flex align-items-end">
                                <div class="numbers">
                                    <h5 class="font-weight-bolder mb-0">$87,000</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-center d-flex justify-content-center pb-3">
                            <div class="d-flex align-items-end">
                                <div class="numbers">
                                    <h5 class="font-weight-bolder mb-0">%80</h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 text-center d-flex justify-content-center">
                            <div class="">
                                <div class="p-3">
                                    <div class="d-flex">
                                        <div class="numbers">
                                            <p class="text-sm mb-0 text-uppercase font-weight-bold">VENTA<br>EJECUCIÓN</p>
                                            <h5 class="font-weight-bolder mb-0">$87,000</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-center d-flex justify-content-center pb-3">
                            <div class="d-flex align-items-end">
                                <div class="numbers">
                                    <h5 class="font-weight-bolder mb-0">$87,000</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-center d-flex justify-content-center pb-3">
                            <div class="d-flex align-items-end">
                                <div class="numbers">
                                    <h5 class="font-weight-bolder mb-0">%80</h5>
                                </div>
                            </div>
                        </div>
                    </div>  

                    <div class="row">
                        <div class="col-md-4 text-center d-flex justify-content-center">
                            <div class="">
                                <div class="p-3">
                                    <div class="d-flex">
                                        <div class="numbers">
                                            <p class="text-sm mb-0 text-uppercase font-weight-bold">VENTA</p>
                                            <h5 class="font-weight-bolder mb-0">$87,000</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-center d-flex justify-content-center pb-3">
                            <div class="d-flex align-items-end">
                                <div class="numbers">
                                    <h5 class="font-weight-bolder mb-0">$87,000</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-center d-flex justify-content-center pb-3">
                            <div class="d-flex align-items-end">
                                <div class="numbers">
                                    <h5 class="font-weight-bolder mb-0">%80</h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 text-center d-flex justify-content-center">
                            <div class="">
                                <div class="p-3">
                                    <div class="d-flex">
                                        <div class="numbers text-aling">
                                            <p class="text-sm mb-0 text-uppercase font-weight-bold">VENTA TOTAL</p>
                                            <h5 class="font-weight-bolder mb-0">$87,000</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-lg-6 ms-auto">
                <div class="card">
                <div class="card-header pb-0 p-3">
                    <div class="d-flex align-items-center">
                    <h6 class="mb-0">Consumption by room</h6>
                    <button type="button" class="btn btn-icon-only btn-rounded btn-outline-secondary mb-0 ms-2 btn-sm d-flex align-items-center justify-content-center ms-auto" data-bs-toggle="tooltip" data-bs-placement="bottom" title="See the consumption per room">
                        <i class="fas fa-info"></i>
                    </button>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="row">
                    <div class="col-5 text-center">
                        <div class="chart">
                        <canvas id="chart-consumption" class="chart-canvas" height="197"></canvas>
                        </div>
                        <h4 class="font-weight-bold mt-n8">
                        <span>310.0</span>
                        <span class="d-block text-body text-sm">WATTS</span>
                        </h4>
                    </div>
                    <div class="col-7">
                        <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <tbody>
                            <tr>
                                <td>
                                <div class="d-flex px-2 py-0">
                                    <span class="badge bg-primary me-3"> </span>
                                    <div class="d-flex flex-column justify-content-center">
                                    <h6 class="mb-0 text-sm">Living Room</h6>
                                    </div>
                                </div>
                                </td>
                                <td class="align-middle text-center text-sm">
                                <span class="text-xs font-weight-bold"> 15% </span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                <div class="d-flex px-2 py-0">
                                    <span class="badge bg-secondary me-3"> </span>
                                    <div class="d-flex flex-column justify-content-center">
                                    <h6 class="mb-0 text-sm">Kitchen</h6>
                                    </div>
                                </div>
                                </td>
                                <td class="align-middle text-center text-sm">
                                <span class="text-xs font-weight-bold"> 20% </span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                <div class="d-flex px-2 py-0">
                                    <span class="badge bg-info me-3"> </span>
                                    <div class="d-flex flex-column justify-content-center">
                                    <h6 class="mb-0 text-sm">Attic</h6>
                                    </div>
                                </div>
                                </td>
                                <td class="align-middle text-center text-sm">
                                <span class="text-xs font-weight-bold"> 13% </span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                <div class="d-flex px-2 py-0">
                                    <span class="badge bg-success me-3"> </span>
                                    <div class="d-flex flex-column justify-content-center">
                                    <h6 class="mb-0 text-sm">Garage</h6>
                                    </div>
                                </div>
                                </td>
                                <td class="align-middle text-center text-sm">
                                <span class="text-xs font-weight-bold"> 32% </span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                <div class="d-flex px-2 py-0">
                                    <span class="badge bg-warning me-3"> </span>
                                    <div class="d-flex flex-column justify-content-center">
                                    <h6 class="mb-0 text-sm">Basement</h6>
                                    </div>
                                </div>
                                </td>
                                <td class="align-middle text-center text-sm">
                                <span class="text-xs font-weight-bold"> 20% </span>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        </div>
                    </div>
                    </div>
                </div>
                </div>
            </div>
            <div class="col-lg-6 mt-lg-0 mt-4">
                <div class="row">
                <div class="col-sm-6">
                    <div class="card h-100">
                    <div class="card-body p-3">
                        <h6>Consumption per day</h6>
                        <div class="chart pt-3">
                        <canvas id="chart-cons-week" class="chart-canvas" height="170"></canvas>
                        </div>
                    </div>
                    </div>
                </div>
                <div class="col-sm-6 mt-sm-0 mt-4">
                    <div class="card h-100">
                    <div class="card-body text-center p-3">
                        <h6 class="text-start">Device limit</h6>
                        <round-slider value="21" valueLabel="Temperature"></round-slider>
                        <h4 class="font-weight-bold mt-n7"><span class="text-dark" id="value">21</span><span class="text-body">°C</span></h4>
                        <p class="ps-1 mt-5 mb-0"><span class="text-xs">16°C</span><span class="px-3">Temperature</span><span class="text-xs">38°C</span></p>
                    </div>
                    </div>
                </div>
                </div>
            </div>
        </div>
        {{-- <hr class="horizontal dark my-5"> --}}
    @endsection
