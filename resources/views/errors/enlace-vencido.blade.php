<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>BULLCRM · Enlace vencido</title>
  <link rel="icon" href="https://www.bullmarketing.com.co/wp-content/uploads/2022/04/cropped-favicon-bull-32x32.png" sizes="32x32">
  <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@600;700;800&display=swap" rel="stylesheet">
  <style>
    :root { --ink: #1E1915; --ink2: #6A6159; --paper: #F6F3EF; --accent: #EF5F17; }
    * { box-sizing: border-box; }
    body { margin: 0; min-height: 100dvh; display: grid; place-items: center; font-family: "Manrope", system-ui, sans-serif; color: var(--ink);
      background: var(--paper) url('{{ asset('assets/img/hero-2.jpg') }}') center/cover no-repeat; }
    body::before { content: ""; position: fixed; inset: 0; background: linear-gradient(120deg, rgba(255,105,0,.88), rgba(201,74,14,.92)); }
    .card { position: relative; width: min(520px, 92vw); padding: 44px 40px 36px; border-radius: 28px; background: #fff; box-shadow: 0 40px 80px -30px rgba(30,25,21,.6); text-align: center; }
    .logo { height: 34px; margin-bottom: 22px; }
    .eyebrow { display: block; font-size: 11px; font-weight: 800; letter-spacing: .16em; text-transform: uppercase; color: var(--accent); margin-bottom: 10px; }
    h1 { font-size: clamp(24px, 3.4vw, 32px); font-weight: 800; letter-spacing: -.02em; line-height: 1.1; margin: 0 0 12px; }
    p { color: var(--ink2); font-size: 15px; line-height: 1.55; margin: 0 0 10px; }
  </style>
</head>
<body>
  <main class="card">
    <img class="logo" src="{{ asset('assets/img/bull-logo.png') }}" alt="Bull Marketing">
    <span class="eyebrow">Enlace vencido</span>
    <h1>Este enlace ya no sirve</h1>
    <p>Los enlaces de Bull Marketing duran {{ (int) config('crm.portal_terceros.dias_enlace', 30) }} días. Pídele a tu contacto en Bull Marketing (el productor del proyecto) que te envíe uno nuevo.</p>
  </main>
</body>
</html>
