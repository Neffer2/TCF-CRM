<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="icon" href="https://www.bullmarketing.com.co/wp-content/uploads/2022/04/cropped-favicon-bull-32x32.png" sizes="32x32" />
  <link rel="icon" href="https://www.bullmarketing.com.co/wp-content/uploads/2022/04/cropped-favicon-bull-192x192.png" sizes="192x192" />
  <link rel="apple-touch-icon" href="https://www.bullmarketing.com.co/wp-content/uploads/2022/04/cropped-favicon-bull-180x180.png" />
  <title>
    Inicio
  </title>
  <!--     Fonts and icons     -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
  <!-- Nucleo Icons -->
  <link href="{{ asset('assets/css/nucleo-icons.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/css/nucleo-svg.css') }}" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <link href="{{ asset('assets/css/nucleo-svg.css') }}" rel="stylesheet" />
  <!-- CSS Files -->
  <link id="pagestyle" href="{{ asset('assets/css/argon-dashboard.css?v=2.0.5') }}" rel="stylesheet" />
  @livewireStyles
</head>

<body class="">
  <main class="main-content main-content-bg mt-0">
    <div class="page-header min-vh-100" style="background-image: url('{{ asset('assets/img/hero-1.jpg') }}');">
      <span class="mask bg-gradient-dark opacity-6"></span>
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <div class="card border-0 mb-0">
              <div class="card-body">
                @yield('content')
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- -------- START FOOTER 3 w/ COMPANY DESCRIPTION WITH LINKS & SOCIAL ICONS & COPYRIGHT ------- -->
  <footer class="footer py-5">
    <div class="container">
      <div class="row">
        <div class="col-8 mx-auto text-center mt-1">
          <p class="mb-0 text-secondary">
            <a href="https://www.bullmarketing.com.co/" target="_blank">BullMarketing</a>
              Copyright © <script>
              document.write(new Date().getFullYear())
            </script> <br> Hecho con ❤ por <a href="https://iglumarketingdigital.com/" target="_blank">Iglú Marketing Digital</a>
          </p>
        </div>
      </div> 
    </div>
  </footer>
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
  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }

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
      `<ul style="list-style-type: square; text-align: left;">
            @foreach($errors->all() as $error)
            <li>{{ $error }} </li>
            @endforeach
        </ul>`,
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
  </script>
  @livewireScripts
  <!-- Github buttons -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="{{ asset('assets/js/argon-dashboard.min.js?v=2.0.5') }}"></script>
</body>
</html>