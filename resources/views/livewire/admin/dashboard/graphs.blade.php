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
                        <canvas id="donut" class="chart-canvas" height="197"></canvas>
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
    
    @section('scripts')
        <script>
            // let donut = document.getElementById('donut'); 
            // // let dataGraph1 = {{ json_encode($dataGraph1) }}
            // new Chart(donut, {
            //     type: "doughnut",
            //     data: {
            //     labels: ['Uno', 'Dos', 'Tres', 'Cuatro', 'Cinco'],
            //     datasets: [{
            //         label: "Projects",
            //         weight: 9,
            //         cutout: 60,
            //         tension: 0.9,
            //         pointRadius: 2,
            //         borderWidth: 2,
            //         backgroundColor: ['#2152ff', '#3A416F', '#f53939', '#a8b8d8', '#5e72e4'],
            //         data: [1,2,3,4,5],
            //         fill: false
            //     }],
            //     },
            //     options: {
            //     responsive: true,
            //     maintainAspectRatio: false,
            //     plugins: {
            //         legend: {
            //         display: false,
            //         }
            //     },
            //     interaction: {
            //         intersect: false,
            //         mode: 'index',
            //     },
            //     scales: {
            //         y: {
            //         grid: {
            //             drawBorder: false,
            //             display: false,
            //             drawOnChartArea: false,
            //             drawTicks: false,
            //         },
            //         ticks: {
            //             display: false
            //         }
            //         },
            //         x: {
            //         grid: {
            //             drawBorder: false,
            //             display: false,
            //             drawOnChartArea: false,
            //             drawTicks: false,
            //         },
            //         ticks: {
            //             display: false,
            //         }
            //         },
            //     },
            //     },
            // });

            // function addData(chart, label, data) {
            //     chart.data.labels.push(label);
            //     chart.data.datasets.forEach((dataset) => {
            //         dataset.data.push(data);
            //     });
            //     chart.update();
            // }
        </script>
    @endsection
</div>