<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="icon" href="https://www.bullmarketing.com.co/wp-content/uploads/2022/04/cropped-favicon-bull-32x32.png" sizes="32x32" />
  <link rel="icon" href="https://www.bullmarketing.com.co/wp-content/uploads/2022/04/cropped-favicon-bull-192x192.png" sizes="192x192" />
  <link rel="apple-touch-icon" href="https://www.bullmarketing.com.co/wp-content/uploads/2022/04/cropped-favicon-bull-180x180.png" />
  <title>
    Productor - {{ Auth::user()->name}}
  </title>
  <!-- Nucleo Icons -->
  <link href="{{ asset('assets/css/nucleo-icons.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/css/nucleo-svg.css') }}" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <script src="https://kit.fontawesome.com/15bc5276a1.js" crossorigin="anonymous"></script>
  <link href="{{ asset('assets/css/nucleo-svg.css') }}" rel="stylesheet" />
  <!-- CSS Files -->
  <link id="pagestyle" href="{{ asset('assets/css/argon-dashboard.css?v=2.0.5') }}" rel="stylesheet" />
  <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/mask@3.x.x/dist/cdn.min.js"></script>
  <script defer src="https://unpkg.com/alpinejs@3.10.5/dist/cdn.min.js"></script>
  @livewireStyles
  <style>
    .invalid-input {
      border: 2px solid red;
    }

    .valid-input {
      border: 2px solid green;
    }

    .font-table {
      font-size: 9px;
    }

    .item-table-background-tarifario {
      background-image: linear-gradient(310deg, rgba(17, 113, 239, .1) 0%, rgba(17, 205, 239, .5) 100%)

    }

    .item-table-background-interno {
      background-image: linear-gradient(310deg, rgba(251, 99, 64, .1) 0%, rgba(251, 177, 64, .5) 100%)
    }

    .item-table-background-control {
      background-image: linear-gradient(310deg, rgba(45, 206, 137, .1) 0%, rgba(45, 206, 204, .5) 100%);
    }

    .small {
      width: 90%;
    }

    .bg-rentabilidad {
      border-color: #fbb140;
      background-color: #fbb140 !important;
    }
  </style>
</head>
<body class="g-sidenav-show bg-gray-100 @yield('nav-hidden')">
  @yield('hero-style')
  <!-- Barra lateral -->
  <aside class="sidenav bg-white navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-4 " id="sidenav-main">
    <div class="sidenav-header d-flex align-items-center justify-content-center">
      <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
      <a class="navbar-brand m-0 d-flex flex-column" href="{{ route('dashboard') }}">
        <img src="{{ asset('assets/img/bull-logo.png') }}" class="navbar-brand-img h-100" alt="main_logo">
      </a>
    </div>
    <hr class="horizontal dark mt-0">
    <div class="collapse navbar-collapse  w-auto h-auto" id="sidenav-collapse-main">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a data-bs-toggle="collapse" href="#dashboardsExamples" class="nav-link active" aria-controls="dashboardsExamples" role="button" aria-expanded="false">
            <div class="icon icon-shape icon-sm text-center d-flex align-items-center justify-content-center">
              <i class="ni ni-box-2 text-success text-sm opacity-10"></i>
            </div>
            <span class="nav-link-text ms-1">Inicio</span>
          </a>
          <div class="collapse show" id="dashboardsExamples">
            <ul class="nav ms-4">
              <li @class(['active' => request()->is('dashboard-productor'), 'nav-item' => true])>
                <a @class(['active' => request()->is('dashboard-productor'), 'nav-link' => true]) href="{{ route('dashboard-productor') }}">
                  <span class="sidenav-mini-icon"> D </span>
                  <span class="sidenav-normal"> Dashboard </span>
                </a>
              </li>
              <li @class(['active' => request()->is('proveedores'), 'nav-item' => true])>
                <a @class(['active' => request()->is('proveedores'), 'nav-link' => true]) href="{{ route('proveedores') }}">
                  <span class="sidenav-mini-icon"> P </span>
                  <span class="sidenav-normal"> Proveedores </span>
                </a>
              </li>
              <li @class(['active' => request()->is('consumidos-prod'), 'nav-item' => true])>
                <a @class(['active' => request()->is('consumidos-prod'), 'nav-link' => true]) href="{{ route('consumidos-prod') }}">
                  <span class="sidenav-mini-icon text-xs"> C </span>
                  <span class="sidenav-normal"> Consumidos </span>
                </a>
              </li>
            </ul>
          </div>
        </li>
        <li class="nav-item">
          <a data-bs-toggle="collapse" href="#terceros" class="nav-link" aria-controls="applicationsExamples" role="button" aria-expanded="false">
            <div class="icon icon-shape icon-sm text-center d-flex align-items-center justify-content-center">
              <i class="ni ni-single-02 text-info text-sm opacity-10"></i>
            </div>
            <span class="nav-link-text ms-1">Terceros</span>
          </a>

          <div @class([
            'collapse' => true,
            'show' => (request()->is('orden-compra-natural') ||
                        request()->is('personal') ||
                        request()->is('ordenes-compra-prod'))]) id="terceros">
                <ul class="nav nav-sm flex-column">
                    <li @class(['active' => request()->is('orden-compra-natural'), 'nav-item' => true])>
                    <a @class(['active' => request()->is('orden-compra-natural'),
                        'nav-link' => true,
                        'ps-4' => true, 'pe-0' => true]) href="{{ route('orden-natural-prod') }}">
                        <span class="sidenav-mini-icon"> NO </span>
                        <span class="sidenav-normal"> Nueva Orden </span>
                    </a>
                    </li>
                    <li @class(['active' => request()->is('ordenes-compra-prod'), 'nav-item' => true])>
                    <a @class(['active' => request()->is('ordenes-compra-prod'),
                        'nav-link' => true,
                        'ps-4' => true, 'pe-0' => true]) href="{{ route('ordenes-prod') }}">
                        <span class="sidenav-mini-icon"> Oc's </span>
                        <span class="sidenav-normal"> Mis Ordenes </span>
                    </a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="nav-item">
          <a data-bs-toggle="collapse" href="#ajustes" class="nav-link" aria-controls="applicationsExamples" role="button" aria-expanded="false">
            <div class="icon icon-shape icon-sm text-center d-flex align-items-center justify-content-center">
              <i class="ni ni-settings text-secondary text-sm opacity-10"></i>
            </div>
            <span class="nav-link-text ms-1">Ajustes</span>
          </a>
          <div class="collapse" id="ajustes" style="">
            <ul class="nav ms-4">
              <li class="nav-item">
                <a class="nav-link" href="{{ route('actualizar-perfil-adm') }}">
                  <span class="sidenav-mini-icon"> K </span>
                  <span class="sidenav-normal"> Actualizar perfil </span>
                </a>
              </li>
            </ul>
          </div>
        </li>
      </ul>
    </div>
  </aside>
  <!-- End Barra lateral -->

  <main class="main-content position-relative border-radius-lg ">
    <!-- Navbar -->
    <nav class="navbar navbar-main navbar-expand-lg  px-0 mx-4 shadow-none border-radius-xl z-index-sticky " id="navbarBlur" data-scroll="false">
      <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-4 me-5">
            <li class="breadcrumb-item text-sm">
              <a class="text-white" href="javascript:;">
                <i class="ni ni-box-2"></i>
              </a>
            </li>
            <li class="breadcrumb-item text-sm text-white"><a class="opacity-5 text-white" href="javascript:;">Inicio</a></li>
            <li class="breadcrumb-item text-sm text-white active" aria-current="page">Dashboard</li>
          </ol>
          <h6 class="font-weight-bolder mb-0 text-white">Dashboard</h6>
        </nav>
        <div class="sidenav-toggler sidenav-toggler-inner d-xl-block d-none ">
          <a href="javascript:;" class="nav-link p-0">
            <div class="sidenav-toggler-inner" x-cloak x-data="{sideHidden: false}" x-on:click="sideHidden = !sideHidden">
              <i class="fa-solid fa-bars fa-xl" style="color: #ffffff;" x-show="!sideHidden"></i>
              <i class="fa-solid fa-bars-staggered fa-xl" style="color: #ffffff;" x-show="sideHidden"></i>
            </div>
          </a>
        </div>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
          <div class="ms-md-auto pe-md-3 d-flex align-items-center">
            <div class="input-group">
              {{-- <span class="input-group-text text-body"><i class="fas fa-search" aria-hidden="true"></i></span> --}}
              {{-- <input type="text" class="form-control" placeholder="Type here..."> --}}
            </div>
          </div>
          <ul class="navbar-nav  justify-content-end">
            <li class="nav-item d-flex align-items-center">
              @auth
                <form action="{{ route('logout') }}" method="POST">
                  @csrf
                  <a href="" onclick="this.closest('form').submit();return false;" class="nav-link text-white font-weight-bold px-0">
                    <i class="ni ni-button-power"></i>
                    <span class="d-sm-inline d-none">Salir</span>
                  </a>
                </form>
              @endauth
              @guest
                <a href="../../../pages/authentication/signin/illustration.html" class="nav-link text-white font-weight-bold px-0" target="_blank">
                  <i class="fa fa-user me-sm-1"></i>
                  <span class="d-sm-inline d-none">Sign In</span>
                </a>
              @endguest
            </li>
            <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
              <a href="javascript:;" class="nav-link text-white p-0" id="iconNavbarSidenav">
                <div class="sidenav-toggler-inner">
                  <i class="sidenav-toggler-line bg-white"></i>
                  <i class="sidenav-toggler-line bg-white"></i>
                  <i class="sidenav-toggler-line bg-white"></i>
                </div>
              </a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    @yield('profile-card')
    <!-- End Navbar -->
    <div class="container-fluid py-4">
      <div class="row">
        @yield('content')
      </div>

      <footer class="footer pt-3">
        <div class="container-fluid">
          <div class="row align-items-center justify-content-lg-between">
            <div class="col-lg-6 mb-lg-0 mb-4">
              <div class="copyright text-center text-sm text-muted text-lg-start">
                © <script>
                  document.write(new Date().getFullYear())
                </script>,
                Hecho con <i class="fa fa-heart"></i> por
                <a href="https://iglumarketingdigital.com/" class="font-weight-bold" target="_blank">Igl&uacute; Marketing Digital</a>
              </div>
            </div>
          </div>
        </div>
      </footer>
    </div>
  </main>
  <!--   Core JS Files   -->
  <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
  <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/smooth-scrollbar.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/choices.min.js') }}"></script>
  <!-- Kanban scripts -->
  <script src="{{ asset('assets/js/plugins/dragula/dragula.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/jkanban/jkanban.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/countup.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/chartjs.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/round-slider.min.js') }}"></script>
  <!-- Sweet Alerts -->
  <script src="{{ asset('assets/js/plugins/sweetalert.min.js') }}"></script>
  @yield('scripts-imports')
  @yield('scripts')
  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
    @if($errors->any())
      Swal.fire(
      '!Oppss tenemos un problema',
      `@foreach($errors->all() as $error)
          {{ $error }}
      @endforeach`,
      'error'
      );
    @endif
    @if (session('success'))
    Swal.fire(
      'Hecho',
      `{{ session('success') }}`,
      'success'
      );
    @endif

    // Rounded slider
    const setValue = function(value, active) {
      document.querySelectorAll("round-slider").forEach(function(el) {
        if (el.value === undefined) return;
        el.value = value;
      });
      const span = document.querySelector("#value");
      span.innerHTML = value;
      if (active)
        span.style.color = 'red';
      else
        span.style.color = 'black';
    }

    document.querySelectorAll("round-slider").forEach(function(el) {
      el.addEventListener('value-changed', function(ev) {
        if (ev.detail.value !== undefined)
          setValue(ev.detail.value, false);
        else if (ev.detail.low !== undefined)
          setLow(ev.detail.low, false);
        else if (ev.detail.high !== undefined)
          setHigh(ev.detail.high, false);
      });

      el.addEventListener('value-changing', function(ev) {
        if (ev.detail.value !== undefined)
          setValue(ev.detail.value, true);
        else if (ev.detail.low !== undefined)
          setLow(ev.detail.low, true);
        else if (ev.detail.high !== undefined)
          setHigh(ev.detail.high, true);
      });
    });

    // Count To
    if (document.getElementById('status1')) {
      const countUp = new CountUp('status1', document.getElementById("status1").getAttribute("countTo"));
      if (!countUp.error) {
        countUp.start();
      } else {
        console.error(countUp.error);
      }
    }
    if (document.getElementById('status2')) {
      const countUp = new CountUp('status2', document.getElementById("status2").getAttribute("countTo"));
      if (!countUp.error) {
        countUp.start();
      } else {
        console.error(countUp.error);
      }
    }
    if (document.getElementById('status3')) {
      const countUp = new CountUp('status3', document.getElementById("status3").getAttribute("countTo"));
      if (!countUp.error) {
        countUp.start();
      } else {
        console.error(countUp.error);
      }
    }
    if (document.getElementById('status4')) {
      const countUp = new CountUp('status4', document.getElementById("status4").getAttribute("countTo"));
      if (!countUp.error) {
        countUp.start();
      } else {
        console.error(countUp.error);
      }
    }
    if (document.getElementById('status5')) {
      const countUp = new CountUp('status5', document.getElementById("status5").getAttribute("countTo"));
      if (!countUp.error) {
        countUp.start();
      } else {
        console.error(countUp.error);
      }
    }
    if (document.getElementById('status6')) {
      const countUp = new CountUp('status6', document.getElementById("status6").getAttribute("countTo"));
      if (!countUp.error) {
        countUp.start();
      } else {
        console.error(countUp.error);
      }
    }

    // Chart Doughnut Consumption by room
    var ctx1 = document.getElementById("chart-consumption").getContext("2d");

    var gradientStroke1 = ctx1.createLinearGradient(0, 230, 0, 50);

    gradientStroke1.addColorStop(1, 'rgba(203,12,159,0.2)');
    gradientStroke1.addColorStop(0.2, 'rgba(72,72,176,0.0)');
    gradientStroke1.addColorStop(0, 'rgba(203,12,159,0)'); //purple colors

    new Chart(ctx1, {
      type: "doughnut",
      data: {
        labels: ['Living Room', 'Kitchen', 'Attic', 'Garage', 'Basement'],
        datasets: [{
          label: "Consumption",
          weight: 9,
          cutout: 90,
          tension: 0.9,
          pointRadius: 2,
          borderWidth: 2,
          backgroundColor: ['#5e72e4', '#8392ab', '#11cdef', '#2dce89', '#fb6340'],
          data: [15, 20, 13, 32, 20],
          fill: false
        }],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false,
          }
        },
        interaction: {
          intersect: false,
          mode: 'index',
        },
        scales: {
          y: {
            grid: {
              drawBorder: false,
              display: false,
              drawOnChartArea: false,
              drawTicks: false,
            },
            ticks: {
              display: false
            }
          },
          x: {
            grid: {
              drawBorder: false,
              display: false,
              drawOnChartArea: false,
              drawTicks: false,
            },
            ticks: {
              display: false,
            }
          },
        },
      },
    });

    // Chart Consumption by day
    var ctx = document.getElementById("chart-cons-week").getContext("2d");
    new Chart(ctx, {
      type: "bar",
      data: {
        labels: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
        datasets: [{
          label: "Watts",
          tension: 0.4,
          borderWidth: 0,
          borderRadius: 4,
          borderSkipped: false,
          backgroundColor: "#3A416F",
          data: [150, 230, 380, 220, 420, 200, 70],
          maxBarThickness: 6
        }, ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false,
          }
        },
        interaction: {
          intersect: false,
          mode: 'index',
        },
        scales: {
          y: {
            grid: {
              drawBorder: false,
              display: false,
              drawOnChartArea: false,
              drawTicks: false,
            },
            ticks: {
              display: false
            },
          },
          x: {
            grid: {
              drawBorder: false,
              display: false,
              drawOnChartArea: false,
              drawTicks: false
            },
            ticks: {
              beginAtZero: true,
              font: {
                size: 12,
                family: "Open Sans",
                style: 'normal',
              },
              color: "#9ca2b7"
            },
          },
          y: {
            grid: {
              drawBorder: false,
              display: false,
              drawOnChartArea: true,
              drawTicks: false,
              borderDash: [5, 5]
            },
            ticks: {
              display: true,
              padding: 10,
              color: '#9ca2b7'
            }
          },
          x: {
            grid: {
              drawBorder: false,
              display: true,
              drawOnChartArea: true,
              drawTicks: false,
              borderDash: [5, 5]
            },
            ticks: {
              display: true,
              padding: 10,
              color: '#9ca2b7'
            }
          },
        },
      },
    });
  </script>
  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
  </script>
  <!-- Github buttons -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="{{ asset('assets/js/argon-dashboard.min.js?v=2.0.5') }}"></script>
  @livewireScripts
</body>
</html>
