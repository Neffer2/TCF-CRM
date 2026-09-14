<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="icon" href="https://www.bullmarketing.com.co/wp-content/uploads/2022/04/cropped-favicon-bull-32x32.png" sizes="32x32" />
  <link rel="icon" href="https://www.bullmarketing.com.co/wp-content/uploads/2022/04/cropped-favicon-bull-192x192.png" sizes="192x192" />
  <link rel="apple-touch-icon" href="https://www.bullmarketing.com.co/wp-content/uploads/2022/04/cropped-favicon-bull-180x180.png" />
  <title>
    Admin - {{ Auth::user()->name}}
  </title>
  <!-- Nucleo Icons -->
  <link href="{{ asset('assets/css/nucleo-icons.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/css/nucleo-svg.css') }}" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <script src="https://kit.fontawesome.com/15bc5276a1.js" crossorigin="anonymous"></script>
  <link href="{{ asset('assets/css/nucleo-svg.css') }}" rel="stylesheet" />
  <!-- CSS Files -->
  <link id="pagestyle" href="{{ asset('assets/css/argon-dashboard.css?v=2.0.5') }}" rel="stylesheet" />
  <!-- Capa visual BULLCRM (tipografía + tema) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
  <link href="{{ asset('assets/css/crm-premium.css') }}?v=21" rel="stylesheet" />
  <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/mask@3.x.x/dist/cdn.min.js"></script>
  <script defer src="https://unpkg.com/alpinejs@3.10.5/dist/cdn.min.js"></script>
  @livewireStyles
</head>
@php
  // Título de la sección por nombre de ruta (las vistas pueden sobrescribirlo con @section('titulo'))
  $crmTitulos = [
    'dashboard-admin' => 'Dashboard', 'dashboard-controller' => 'Dashboard Controller', 'dashboard-lider-comercial' => 'Dashboard del equipo',
    'base-comercial-general' => 'Base comercial general', 'helisa-general' => 'Helisa general', 'estado-facturacion' => 'Estado de facturación', 'estados' => 'Estado de facturación',
    'presupuesto-proyecto' => 'Presupuestos', 'presupuestos-admin' => 'Presupuesto', 'presupuesto' => 'Presupuesto', 'presupuestos' => 'Presupuestos',
    'actualizaciones' => 'Actualizaciones', 'validaciones' => 'Validaciones', 'validacionesCliente' => 'Solicitudes',
    'ordenes-compra' => 'Órdenes de compra', 'orden-juridica' => 'Orden jurídica', 'orden-natural' => 'Orden natural', 'orden-nomina' => 'Orden de nómina', 'orden-compra_anticipate' => 'Anticipo',
    'consumidos' => 'Consumidos', 'consumido' => 'Consumido', 'reporte-consumidos' => 'Reporte de consumidos', 'proveedores' => 'Proveedores', 'personal' => 'Personal',
    'lista-anticipos-admin' => 'Anticipos', 'anticipos-admin' => 'Anticipos', 'anticipo-admin' => 'Anticipo', 'mi-equpo' => 'Mi equipo', 'notificaciones' => 'Notificaciones', 'actividad' => 'Registro de actividad', 
    'dashboard-com' => 'Dashboard', 'dashboard-base' => 'Base comercial', 'gestion-helisa' => 'Helisa', 'gestion-comercial' => 'Prospectos', 'contactos' => 'Contactos', 'clientes' => 'Clientes',
    'consumidos-com' => 'Consumidos', 'update-gestion-comercial' => 'Gestión comercial',
  ];
  $crmTitulo = $crmTitulos[optional(request()->route())->getName()] ?? 'Inicio';
@endphp
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
    {{-- Usuario: foto, nombre y cargo (enlaza a Mi perfil) --}}
    <a class="crm-user" href="{{ route('mi-perfil') }}" title="Ver mi perfil">
      <span class="crm-user__foto">@if (Auth::user()->avatarUrl())<img src="{{ Auth::user()->avatarUrl() }}" alt="" onerror="this.remove()">@endif<b>{{ mb_strtoupper(mb_substr(Auth::user()->name, 0, 1)) }}</b></span>
      <b class="crm-user__nombre">{{ Auth::user()->name }}</b>
      <span class="crm-user__cargo">{{ Auth::user()->cargo() }}</span>
      <span class="crm-user__link">Ver mi perfil →</span>
    </a>
    <div class="collapse navbar-collapse  w-auto h-auto" id="sidenav-collapse-main">
      {{-- Menú según el rol activo: Controller y Líder comercial tienen el suyo; Admin/Gerencia el completo --}}
      @if (Auth::user()->esControl())
        @include('layouts.admin.menu-controller')
      @elseif (Auth::user()->esLiderComercial())
        @include('layouts.admin.menu-lider-comercial')
      @else
      <ul class="navbar-nav">
        <li class="nav-item">
          <a data-bs-toggle="collapse" href="#dashboardsExamples" class="nav-link active" aria-controls="dashboardsExamples" role="button" aria-expanded="false">
            <div class="icon icon-shape icon-sm text-center d-flex align-items-center justify-content-center">
              <i class="ni ni-shop text-primary text-sm opacity-10"></i>
            </div>
            <span class="nav-link-text ms-1">Inicio</span>
          </a>
          <div @class([
            'show' => (request()->is('dashboard-admin') || request()->is('estado-facturacion') || request()->is('base-comercial-general')|| request()->is('helisa-general')),
            'collapse' => true
            ]) id="dashboardsExamples">
            <ul class="nav ms-4">
              <li @class(['active' => request()->is('dashboard-admin'), 'nav-item' => true])>
                <a @class(['active' => request()->is('dashboard-admin'), 'nav-link' => true]) href="{{ route('dashboard-admin') }}">
                  <span class="sidenav-mini-icon"> D </span>
                  <span class="sidenav-normal"> Dashboard </span>
                </a>
              </li>
              <li @class(['active' => request()->is('base-comercial-general'), 'nav-item' => true])>
                <a @class(['active' => request()->is('base-comercial-general'), 'nav-link' => true]) href="{{ route('base-comercial-general') }}">
                  <span class="sidenav-mini-icon"> B </span>
                  <span class="sidenav-normal"> Base comercial general </span>
                </a>
              </li>
              <li @class(['active' => request()->is('helisa-general'), 'nav-item' => true])>
                <a @class(['active' => request()->is('helisa-general'), 'nav-link' => true]) href="{{ route('helisa-general') }}">
                  <span class="sidenav-mini-icon"> B </span>
                  <span class="sidenav-normal"> Helisa general </span>
                </a>
              </li>
            </ul>
          </div>
        </li>
        <li class="nav-item">
          <a data-bs-toggle="collapse" href="#dashboardsGestion" class="nav-link" aria-controls="dashboardsGestion" role="button" aria-expanded="false">
            <div class="icon icon-shape icon-sm text-center d-flex align-items-center justify-content-center">
              <i class="ni ni-badge text-primary text-sm opacity-10"></i>
            </div>
            <span class="nav-link-text ms-1">Gesti&oacute;n comercial</span>
          </a>
          <div @class([
                'show' => (request()->is('presupuesto-proyecto') || request()->is('actualizaciones') || request()->is('validaciones') || request()->is('validacionesCliente')),
                'collapse' => true
              ]) id="dashboardsGestion">
            <ul class="nav ms-4">
              <li @class(['active' => request()->is('presupuesto-proyecto'), 'nav-item' => true])>
                <a @class(['active' => request()->is('presupuesto-proyecto'), 'nav-link' => true]) href="{{ route('presupuesto-proyecto') }}">
                  <span class="sidenav-mini-icon text-xs"> P </span>
                  <span class="sidenav-normal"> Presupuestos </span>
                </a>
              </li>
              <li @class(['active' => request()->is('actualizaciones'), 'nav-item' => true])>
                <a @class(['active' => request()->is('actualizaciones'), 'nav-link' => true]) href="{{ route('actualizaciones') }}">
                  <span class="sidenav-mini-icon text-xs"> A </span>
                  <span class="sidenav-normal"> Actualizaciones </span>
                </a>
              </li>
              @if(Auth::user()->can('ver-menu-admin-avanzado'))
                  <li @class(['active' => request()->is('validacionesCliente'), 'nav-item' => true])>
                      <a @class(['active' => request()->is('validacionesCliente'), 'nav-link' => true]) href="{{ route('validacionesCliente') }}">
                          <span class="sidenav-mini-icon text-xs"> C </span>
                          <span class="sidenav-normal"> Solicitudes </span>
                      </a>
                  </li>
              @endif
            </ul>
          </div>
        </li>
        <li class="nav-item">
          <a data-bs-toggle="collapse" href="#dashboardsProduccion" class="nav-link" aria-controls="dashboardsProduccion" role="button" aria-expanded="false">
            <div class="icon icon-shape icon-sm text-center d-flex align-items-center justify-content-center">
              <i class="ni ni-box-2 text-success text-sm opacity-10"></i>
            </div>
            <span class="nav-link-text ms-1">Producci&oacute;n</span>
          </a>
          <div @class([
            'show' => (request()->is('proveedores') || request()->is('ordenes-compra') || request()->is('consumidos') || request()->is('personal')),
            'collapse' => true
            ]) id="dashboardsProduccion">
            <ul class="nav ms-4">
                <li @class(['active' => request()->is('proveedores'), 'nav-item' => true])>
                    <a @class(['active' => request()->is('proveedores'), 'nav-link' => true]) href="{{ route('proveedores') }}">
                        <span class="sidenav-mini-icon text-xs"> AN </span>
                        <span class="sidenav-normal"> Proveedores </span>
                    </a>
                </li>
                <li @class(['active' => request()->is('ordenes-compra'), 'nav-item' => true])>
                    <a @class(['active' => request()->is('ordenes-compra'), 'nav-link' => true]) href="{{ route('ordenes-compra') }}">
                        <span class="sidenav-mini-icon text-xs"> OC </span>
                        <span class="sidenav-normal"> Ordenes de compra </span>
                    </a>
                </li>
                <li @class(['active' => request()->is('consumidos'), 'nav-item' => true])>
                    <a @class(['active' => request()->is('consumidos'), 'nav-link' => true]) href="{{ route('consumidos') }}">
                        <span class="sidenav-mini-icon text-xs"> C </span>
                        <span class="sidenav-normal"> Consumidos </span>
                    </a>
                </li>
                <li @class(['active' => request()->is('personal'), 'nav-item' => true])>
                    <a @class(['active' => request()->is('personal'), 'nav-link' => true]) href="{{ route('personal') }}">
                        <span class="sidenav-mini-icon"> P </span>
                        <span class="sidenav-normal"> Personal </span>
                    </a>
                </li>
            </ul>
          </div>
        </li>
        <li class="nav-item">
          <a data-bs-toggle="collapse" href="#acciones" class="nav-link" aria-controls="acciones" role="button" aria-expanded="false">
            <div class="icon icon-shape icon-sm text-center d-flex align-items-center justify-content-center">
              <i class="ni ni-ui-04 text-info text-sm opacity-10"></i>
            </div>
            <span class="nav-link-text ms-1">Acciones</span>
          </a>
          <div @class([
            'show' => (request()->is('presupuesto') || request()->is('mi-equpo')),
            'collapse' => true
            ]) id="acciones" style="">
            <ul class="nav ms-4">
              <li @class(['active' => request()->is('presupuesto'), 'nav-item' => true])>
                <a @class(['active' => request()->is('presupuesto'), 'nav-link' => true]) href="{{ route('presupuestos-admin') }}">
                  <span class="sidenav-mini-icon"> P </span>
                  <span class="sidenav-normal"> Presupuesto </span>
                </a>
              </li>
              <li @class(['active' => request()->is('mi-equpo'), 'nav-item' => true])>
                <a @class(['active' => request()->is('mi-equpo'), 'nav-link' => true]) href="{{ route('mi-equpo') }}">
                  <span class="sidenav-mini-icon"> M </span>
                  <span class="sidenav-normal"> Mi equipo </span>
                </a>
              </li>
              <li @class(['active' => request()->is('notificaciones'), 'nav-item' => true])>
                <a @class(['active' => request()->is('notificaciones'), 'nav-link' => true]) href="{{ route('notificaciones') }}">
                  <span class="sidenav-mini-icon"> N </span>
                  <span class="sidenav-normal"> Notificaciones </span>
                </a>
              </li>
              <li @class(['active' => request()->is('actividad'), 'nav-item' => true])>
                <a @class(['active' => request()->is('actividad'), 'nav-link' => true]) href="{{ route('actividad') }}">
                  <span class="sidenav-mini-icon"> R </span>
                  <span class="sidenav-normal"> Registro de actividad </span>
                </a>
              </li>
            </ul>
          </div>
        </li>
      </ul>
      @endif
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
              <a class="text-white" href="{{ route('dashboard') }}">
                <i class="ni ni-box-2"></i>
              </a>
            </li>
            <li class="breadcrumb-item text-sm text-white"><a class="opacity-5 text-white" href="{{ route('dashboard') }}">Inicio</a></li>
            <li class="breadcrumb-item text-sm text-white active" aria-current="page">@yield('titulo', $crmTitulo)</li>
          </ol>
          <h6 class="font-weight-bolder mb-0 text-white">@yield('titulo', $crmTitulo)</h6>
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
            @include('components.selector-rol')
            <li class="nav-item d-flex align-items-center pe-3">
              <a href="{{ route('mi-perfil') }}" class="nav-link text-white font-weight-bold px-0 crm-nav-perfil" title="Ver mi perfil">
                <span class="crm-nav-perfil__avatar">@if (Auth::user()->avatarUrl())<img src="{{ Auth::user()->avatarUrl() }}" alt="">@endif{{ mb_strtoupper(mb_substr(Auth::user()->name, 0, 1)) }}</span>
                <span class="d-sm-inline d-none">{{ explode(' ', Auth::user()->name)[0] }} <small class="crm-nav-perfil__cargo">· {{ Auth::user()->cargo() }}</small></span>
              </a>
            </li>
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
            <div class="col-lg-6">
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
  {{-- (scripts de gráficas de demo de Argon eliminados: buscaban canvas inexistentes y ensuciaban la consola) --}}
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
  <script src="{{ asset('assets/js/crm-dashboard.js') }}?v=7"></script>
  @livewireScripts
</body>
</html>
