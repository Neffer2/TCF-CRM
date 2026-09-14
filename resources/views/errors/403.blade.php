<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>BULLCRM · Sin acceso</title>
  <link rel="icon" href="https://www.bullmarketing.com.co/wp-content/uploads/2022/04/cropped-favicon-bull-32x32.png" sizes="32x32">
  <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@600;700;800&display=swap" rel="stylesheet">
  <style>
    :root { --ink: #1E1915; --ink2: #6A6159; --paper: #F6F3EF; --accent: #EF5F17; --deep: #C94A0E; }
    * { box-sizing: border-box; }
    body { margin: 0; min-height: 100dvh; display: grid; place-items: center; font-family: "Manrope", system-ui, sans-serif; color: var(--ink);
      background: var(--paper) url('{{ asset('assets/img/hero-2.jpg') }}') center/cover no-repeat; }
    body::before { content: ""; position: fixed; inset: 0; background: linear-gradient(120deg, rgba(255,105,0,.88), rgba(201,74,14,.92)); }
    .card { position: relative; width: min(520px, 92vw); padding: 44px 40px 36px; border-radius: 28px; background: #fff; box-shadow: 0 40px 80px -30px rgba(30,25,21,.6);
      animation: in .8s cubic-bezier(.16,1,.3,1) both; text-align: center; }
    @keyframes in { from { opacity: 0; transform: translateY(18px) scale(.98); } }
    .logo { height: 34px; margin-bottom: 22px; }
    .eyebrow { display: block; font-size: 11px; font-weight: 800; letter-spacing: .16em; text-transform: uppercase; color: var(--accent); margin-bottom: 10px; }
    h1 { font-size: clamp(24px, 3.4vw, 32px); font-weight: 800; letter-spacing: -.02em; line-height: 1.1; margin: 0 0 12px; }
    p { color: var(--ink2); font-size: 15px; line-height: 1.55; margin: 0 0 26px; }
    .btn { display: inline-flex; align-items: center; gap: 10px; padding: 6px 6px 6px 20px; height: 48px; border-radius: 999px; text-decoration: none;
      background: linear-gradient(135deg, #FF6900, #EF5F17); color: #fff; font-weight: 700; font-size: 14px; box-shadow: 0 14px 28px -12px rgba(239,95,23,.9);
      transition: transform .35s cubic-bezier(.16,1,.3,1), box-shadow .35s ease; }
    .btn:hover { transform: translateY(-2px); box-shadow: 0 18px 32px -12px rgba(239,95,23,1); }
    .btn i { width: 36px; height: 36px; border-radius: 999px; display: inline-grid; place-items: center; background: rgba(255,255,255,.2); font-style: normal; }
    .code { position: absolute; right: 26px; top: 22px; font-size: 12px; font-weight: 800; color: #C9BFB5; letter-spacing: .1em; }
  </style>
</head>
<body>
  <main class="card">
    <span class="code">403</span>
    <img class="logo" src="{{ asset('assets/img/bull-logo.png') }}" alt="Bull Marketing">
    <span class="eyebrow">Sin acceso</span>
    <h1>No tienes acceso a esta sección</h1>
    <p>Tu rol no incluye esta pantalla. Si crees que deberías verla, pídele al administrador del CRM que revise tus permisos.</p>
    <a class="btn" href="{{ auth()->check() ? route('dashboard') : route('login') }}">Ir al inicio <i>→</i></a>
  </main>
</body>
</html>
