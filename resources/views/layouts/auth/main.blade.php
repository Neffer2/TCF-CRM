<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta name="color-scheme" content="dark">
  <meta name="theme-color" content="#0B0908">
  <link rel="icon" href="{{ asset('assets/img/favicon.png') }}" />
  <link rel="apple-touch-icon" href="https://www.bullmarketing.com.co/wp-content/uploads/2022/04/cropped-favicon-bull-180x180.png" />
  <title>BULLCRM · @yield('auth-title')</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;700;800&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
  <link href="{{ asset('assets/css/crm-auth.css') }}?v=1" rel="stylesheet" />
  @livewireStyles
</head>
<body class="bl-auth">

  {{-- Atmósfera --}}
  <div class="bl-scene" aria-hidden="true">
    <div class="bl-orb bl-orb--1"></div>
    <div class="bl-orb bl-orb--2"></div>
    <div class="bl-orb bl-orb--3"></div>
    <div class="bl-grid"></div>
    <div class="bl-vignette"></div>
  </div>
  <div class="bl-grain" aria-hidden="true"></div>

  <div class="bl-shell">
    <header class="bl-top bl-in bl-d1">
      <a class="bl-brand" href="{{ url('/') }}" aria-label="Bull Marketing">
        <img src="{{ asset('assets/img/bull-logo.png') }}" alt="Bull Marketing">
      </a>
      <div class="bl-pill"><i></i><span>Sistema comercial y de producción</span></div>
    </header>

    <main class="bl-main">
      <section class="bl-hero">
        <div class="bl-eyebrow bl-in bl-d1">BULLCRM · Bull Marketing</div>
        <h1 class="bl-h1" aria-label="La agencia del ¡Siempre se puede!">
          <span class="w"><span style="animation-delay:.10s">La</span></span>
          <span class="w"><span style="animation-delay:.18s">agencia</span></span>
          <span class="w"><span style="animation-delay:.26s">del</span></span><br>
          <span class="w"><span class="accent" style="animation-delay:.38s">¡Siempre</span></span>
          <span class="w"><span class="accent" style="animation-delay:.48s">se</span></span>
          <span class="w"><span class="accent" style="animation-delay:.58s">puede!</span></span>
        </h1>
        <p class="bl-lede bl-in bl-d3">Ventas, presupuestos, órdenes de compra, anticipos y facturación en un solo lugar. Cada aprobación, con su responsable y su rastro.</p>
        <div class="bl-signals bl-in bl-d4">
          <span class="bl-signal"><b>V</b> Gestión comercial</span>
          <span class="bl-signal"><b>P</b> Producción</span>
          <span class="bl-signal"><b>C</b> Contabilidad y tesorería</span>
        </div>
      </section>

      <div class="bl-card-wrap bl-in bl-d2">
        <div class="bl-halo" aria-hidden="true"></div>
        <div class="bl-card-outer" id="blCard">
          <div class="bl-card">
            <img class="bl-card__logo" src="{{ asset('assets/img/bull-logo.png') }}" alt="">
            <h1>@yield('auth-title')</h1>
            <p class="bl-card__sub">@yield('auth-subtitle', 'Ingresa con tu cuenta de Bull Marketing.')</p>

            @if ($errors->any())
              <div class="bl-alert" role="alert">
                @foreach ($errors->all() as $error)
                  <p>{{ $error }}</p>
                @endforeach
              </div>
            @endif
            @if (session('success') || session('status'))
              <div class="bl-alert bl-alert--ok" role="status"><p>{{ session('success') ?? session('status') }}</p></div>
            @endif

            @yield('form')
          </div>
        </div>
      </div>
    </main>

    <footer class="bl-foot bl-in bl-d6">
      <span>© {{ date('Y') }} <b>Bull Marketing S.A.S.</b> · BULLCRM</span>
      <span>Acceso restringido al equipo. Si no tienes cuenta, pídela al administrador.</span>
    </footer>
  </div>

  @livewireScripts
  <script>
    (function () {
      var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
      var fine = window.matchMedia('(pointer: fine)').matches;

      // Inclinación 3D de la tarjeta siguiendo el cursor (solo escritorio, solo transform)
      var card = document.getElementById('blCard');
      if (card && fine && !reduce) {
        var raf = null, rx = 0, ry = 0;
        function apply () { card.style.transform = 'rotateX(' + rx + 'deg) rotateY(' + ry + 'deg)'; raf = null; }
        window.addEventListener('mousemove', function (e) {
          var r = card.getBoundingClientRect();
          var cx = r.left + r.width / 2, cy = r.top + r.height / 2;
          var dx = (e.clientX - cx) / Math.max(window.innerWidth, 1), dy = (e.clientY - cy) / Math.max(window.innerHeight, 1);
          ry = Math.max(-6, Math.min(6, dx * 14)); rx = Math.max(-6, Math.min(6, -dy * 14));
          if (!raf) raf = requestAnimationFrame(apply);
        }, { passive: true });
        window.addEventListener('mouseleave', function () { rx = 0; ry = 0; if (!raf) raf = requestAnimationFrame(apply); });
      }

      // Mostrar / ocultar contraseña
      document.querySelectorAll('[data-toggle-password]').forEach(function (btn) {
        btn.addEventListener('click', function () {
          var input = document.getElementById(btn.getAttribute('data-toggle-password'));
          if (!input) return;
          var show = input.type === 'password';
          input.type = show ? 'text' : 'password';
          btn.setAttribute('aria-pressed', show ? 'true' : 'false');
          btn.querySelector('.eye-on').style.display = show ? 'none' : '';
          btn.querySelector('.eye-off').style.display = show ? '' : 'none';
          input.focus();
        });
      });
    })();
  </script>
</body>
</html>
