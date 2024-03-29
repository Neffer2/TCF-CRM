<!DOCTYPE html>
<html lang="es">
<head> 
  <meta charset="utf-8" /> 
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  @livewireStyles
  <link rel="icon" href="https://www.bullmarketing.com.co/wp-content/uploads/2022/04/cropped-favicon-bull-32x32.png" sizes="32x32" />
  <link rel="icon" href="https://www.bullmarketing.com.co/wp-content/uploads/2022/04/cropped-favicon-bull-192x192.png" sizes="192x192" />
  <link rel="apple-touch-icon" href="https://www.bullmarketing.com.co/wp-content/uploads/2022/04/cropped-favicon-bull-180x180.png" />
  <title>
    Comercial - {{ Auth::user()->name}}
  </title> 
  <!-- Nucleo Icons -->
  <link href="{{ asset('assets/css/nucleo-icons.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/css/nucleo-svg.css') }}" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <link href="{{ asset('assets/css/nucleo-svg.css') }}" rel="stylesheet" />
  <!-- CSS Files -->
  <link id="pagestyle" href="{{ asset('assets/css/argon-dashboard.css?v=2.0.5') }}" rel="stylesheet" />
  <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/mask@3.x.x/dist/cdn.min.js"></script>
  <script defer src="https://unpkg.com/alpinejs@3.10.5/dist/cdn.min.js"></script>
  <style>
    .invalid-input {
      border: 2px solid red;
    }

    .valid-input {
      border: 2px solid green;
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
    <div class="main-content position-relative max-height-vh-100 h-100">
      <!-- Navbar -->
      <nav class="navbar navbar-main navbar-expand-lg  px-0 mx-4 shadow-none border-radius-xl z-index-sticky">
        <div class="container-fluid py-1">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 ps-2 me-sm-6 me-5">
              <li class="breadcrumb-item text-sm"><a class="text-white opacity-8" href="javascript:;">Inicio</a></li>
              <li class="breadcrumb-item text-sm text-white active" aria-current="page">Base comercial</li>
            </ol>
            <h6 class="text-white font-weight-bolder ms-2">Comercial</h6>
          </nav>
          <div class="sidenav-toggler sidenav-toggler-inner d-xl-block d-none">
            <a href="javascript:;" class="nav-link text-white p-0">
              <div class="sidenav-toggler-inner">
                <i class="sidenav-toggler-line bg-white"></i>
                <i class="sidenav-toggler-line bg-white"></i>
                <i class="sidenav-toggler-line bg-white"></i>
              </div>
            </a>
          </div>
          <div class="collapse navbar-collapse me-md-0 me-sm-4 mt-sm-0 mt-2" id="navbar">
            <div class="ms-md-auto pe-md-3 d-flex align-items-center">
              <div class="input-group">
              </div>
            </div>
            <ul class="navbar-nav justify-content-end">
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
              <li class="nav-item d-xl-none ps-3 pe-0 d-flex align-items-center">
                <a href="javascript:;" class="nav-link text-body p-0" id="iconNavbarSidenav">
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
      <!-- End Navbar -->
      @yield('profile-card')
        <div class="container-fluid py-4"> 
      @yield('content')
        
        <footer class="footer pt-3  ">
          <div class="container-fluid">
            <div class="row align-items-center justify-content-lg-between">
              <div class="col-lg-6 mb-lg-0 mb-4">
                <div class="copyright text-center text-sm text-muted text-lg-start copy">
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
    </div>
    <div class="fixed-plugin">
      <!-- <a class="fixed-plugin-button text-dark position-fixed px-3 py-2">
        <i class="fa fa-cog py-2"> </i>
      </a> -->
      <div class="card shadow-lg">
        <div class="card-header pb-0 pt-3 bg-transparent ">
          <div class="float-start">
            <h5 class="mt-3 mb-0">Argon Configurator</h5>
            <p>See our dashboard options.</p>
          </div>
          <div class="float-end mt-4">
            <button class="btn btn-link text-dark p-0 fixed-plugin-close-button">
              <i class="fa fa-close"></i>
            </button>
          </div>
          <!-- End Toggle Button -->
        </div>
        <hr class="horizontal dark my-1">
        <div class="card-body pt-sm-3 pt-0 overflow-auto">
          <!-- Sidebar Backgrounds -->
          <div>
            <h6 class="mb-0">Sidebar Colors</h6>
          </div>
          <a href="javascript:void(0)" class="switch-trigger background-color">
            <div class="badge-colors my-2 text-start">
              <span class="badge filter bg-gradient-primary active" data-color="primary" onclick="sidebarColor(this)"></span>
              <span class="badge filter bg-gradient-dark" data-color="dark" onclick="sidebarColor(this)"></span>
              <span class="badge filter bg-gradient-info" data-color="info" onclick="sidebarColor(this)"></span>
              <span class="badge filter bg-gradient-success" data-color="success" onclick="sidebarColor(this)"></span>
              <span class="badge filter bg-gradient-warning" data-color="warning" onclick="sidebarColor(this)"></span>
              <span class="badge filter bg-gradient-danger" data-color="danger" onclick="sidebarColor(this)"></span>
            </div>
          </a>
          <!-- Sidenav Type -->
          <div class="mt-3">
            <h6 class="mb-0">Sidenav Type</h6>
            <p class="text-sm">Choose between 2 different sidenav types.</p>
          </div>
          <div class="d-flex">
            <button class="btn bg-gradient-primary w-100 px-3 mb-2 active me-2" data-class="bg-white" onclick="sidebarType(this)">White</button>
            <button class="btn bg-gradient-primary w-100 px-3 mb-2" data-class="bg-default" onclick="sidebarType(this)">Dark</button>
          </div>
          <p class="text-sm d-xl-none d-block mt-2">You can change the sidenav type just on desktop view.</p>
          <!-- Navbar Fixed -->
          <div class="d-flex my-3">
            <h6 class="mb-0">Navbar Fixed</h6>
            <div class="form-check form-switch ps-0 ms-auto my-auto">
              <input class="form-check-input mt-1 ms-auto" type="checkbox" id="navbarFixed" onclick="navbarFixed(this)">
            </div>
          </div> 
          <hr class="horizontal dark mb-1">
          <div class="d-flex my-4">
            <h6 class="mb-0">Sidenav Mini</h6>
            <div class="form-check form-switch ps-0 ms-auto my-auto">
              <input class="form-check-input mt-1 ms-auto" type="checkbox" id="navbarMinimize" onclick="navbarMinimize(this)">
            </div>
          </div>
          <hr class="horizontal dark my-sm-4">
          <div class="mt-2 mb-5 d-flex">
            <h6 class="mb-0">Light / Dark</h6>
            <div class="form-check form-switch ps-0 ms-auto my-auto">
              <input class="form-check-input mt-1 ms-auto" type="checkbox" id="dark-version" onclick="darkMode(this)">
            </div>
          </div>
          <a class="btn btn-primary w-100" href="https://www.creative-tim.com/product/argon-dashboard-pro">Buy now</a>
          <a class="btn btn-dark w-100" href="https://www.creative-tim.com/product/argon-dashboard">Free demo</a>
          <a class="btn btn-outline-dark w-100" href="https://www.creative-tim.com/learning-lab/bootstrap/overview/argon-dashboard">View documentation</a>
          <div class="w-100 text-center">
            <a class="github-button" href="https://github.com/creativetimofficial/ct-argon-dashboard-pro" data-icon="octicon-star" data-size="large" data-show-count="true" aria-label="Star creativetimofficial/argon-dashboard on GitHub">Star</a>
            <h6 class="mt-3">Thank you for sharing!</h6>
            <a href="https://twitter.com/intent/tweet?text=Check%20Argon%20Dashboard%20PRO%20made%20by%20%40CreativeTim%20%23webdesign%20%23dashboard%20%23bootstrap5&amp;url=https%3A%2F%2Fwww.creative-tim.com%2Fproduct%2Fargon-dashboard-pro" class="btn btn-dark mb-0 me-2" target="_blank">
              <i class="fab fa-twitter me-1" aria-hidden="true"></i> Tweet
            </a>
            <a href="https://www.facebook.com/sharer/sharer.php?u=https://www.creative-tim.com/product/argon-dashboard-pro" class="btn btn-dark mb-0 me-2" target="_blank">
              <i class="fab fa-facebook-square me-1" aria-hidden="true"></i> Share
            </a>
          </div>
        </div>
      </div>
    </div>
    <!--   Core JS Files   -->
    <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/smooth-scrollbar.min.js') }}"></script>
    <!-- Kanban scripts -->
    <script src="{{ asset('assets/js/plugins/dragula/dragula.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/jkanban/jkanban.js') }}"></script>
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
    </script>
    @if($errors->any()) 
      <script>
        Swal.fire(
          '!Oppss tenemos un problema',
          `<ul style='text-align: initial; list-style-type: none;'>
            @foreach($errors->all() as $error) 
              <li>{{ $error }}<li>
            @endforeach
          </ul>`, 
          'error'
        );
      </script>
    @endif 
    @if (session('success'))
      <script>
        Swal.fire(
          'Hecho',
          `{{ session('success') }}`,
          'success'
        );
      </script>
    @endif 
    <!-- Github buttons -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
    <script src="{{ asset('assets/js/argon-dashboard.min.js?v=2.0.5') }}"></script>
    @livewireScripts
  </body>
</html>
