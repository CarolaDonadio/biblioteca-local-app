<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ingresar · Mi cuenta | Biblioteca Domingo Sarmiento</title>
  <link rel="stylesheet" href="<?= base_url('assets/css/home.css') ?>">
  <style>
    .member-login { min-height: 100vh; display: grid; grid-template-columns: minmax(0, 1.05fr) minmax(420px, .95fr); background: var(--paper); }
    .member-intro { display: flex; flex-direction: column; justify-content: space-between; min-height: 100vh; padding: 32px 7vw 38px; background: var(--green); color: white; }
    .member-brand { display: flex; align-items: center; gap: 12px; color: white; }
    .member-brand .brand-mark { border-color: #e8c87c; color: #e8c87c; }
    .member-brand .brand-copy strong { display: block; font: 600 16px 'Playfair Display', serif; }
    .member-brand .brand-copy small { display: block; margin-top: 4px; color: #c2d3ca; font-size: 10px; letter-spacing: .12em; text-transform: uppercase; }
    .member-message { max-width: 520px; padding: 7vh 0; }
    .member-message h1 { margin: 18px 0; font: 500 clamp(40px, 5vw, 68px)/1.06 'Playfair Display', serif; }
    .member-message h1 em { color: #e8c87c; }
    .member-message p { max-width: 420px; color: #d4e1d9; font-size: 16px; }
    .member-note { color: #c2d3ca; font-size: 12px; }
    .member-form-side { display: flex; align-items: center; justify-content: center; padding: 50px 7vw; }
    .member-form-wrap { width: min(100%, 390px); }
    .member-kicker { color: var(--green); font-size: 11px; font-weight: 700; letter-spacing: .18em; text-transform: uppercase; }
    .member-form-wrap h2 { margin: 13px 0 9px; color: var(--ink); font: 500 38px/1.1 'Playfair Display', serif; }
    .member-subtitle { color: var(--muted); font-size: 14px; margin-bottom: 30px; }
    .member-alert { margin-bottom: 20px; padding: 12px 14px; border-left: 3px solid #a8483e; background: #f4e5df; color: #7d3029; font-size: 13px; }
    .member-success { margin-bottom: 20px; padding: 12px 14px; border-left: 3px solid var(--green); background: #e4eee8; color: var(--green); font-size: 13px; }
    .member-field { margin-bottom: 19px; }
    .member-field label { display: block; margin-bottom: 7px; color: var(--ink); font-size: 12px; font-weight: 700; }
    .member-field input { width: 100%; padding: 13px 14px; border: 1px solid var(--line); outline: 0; background: white; color: var(--ink); font: 14px 'DM Sans', sans-serif; transition: border-color .2s, box-shadow .2s; }
    .member-field input:focus { border-color: var(--green); box-shadow: 0 0 0 3px rgba(30, 89, 77, .12); }
    .member-submit { width: 100%; margin-top: 8px; border: 0; cursor: pointer; }
    .member-register { margin-top: 25px; color: var(--muted); font-size: 13px; }
    .member-register a, .member-back { color: var(--green); font-weight: 700; border-bottom: 1px solid var(--gold); padding-bottom: 4px; }
    .member-back { display: inline-block; margin-top: 18px; font-size: 13px; }
    @media (max-width: 760px) { .member-login { display: block; }.member-intro { min-height: auto; padding: 25px 24px 38px; }.member-message { padding: 65px 0 35px; }.member-message h1 { font-size: 44px; }.member-note { display: none; }.member-form-side { padding: 58px 24px 70px; } }
  </style>
</head>
<body>
  <main class="member-login">
    <section class="member-intro" aria-label="Biblioteca Domingo Sarmiento">
      <a href="<?= base_url() ?>" class="member-brand" aria-label="Volver al inicio">
        <span class="brand-mark">DS</span>
        <span class="brand-copy"><strong>Biblioteca Domingo Sarmiento</strong><small>Chascomús · Buenos Aires</small></span>
      </a>
      <div class="member-message">
        <div class="eyebrow">Tu biblioteca, más cerca</div>
        <h1>Volvé a tus lecturas y seguí <em>descubriendo.</em></h1>
        <p>Ingresá a tu cuenta para consultar tus préstamos, gestionar reservas y encontrar tu próxima historia.</p>
      </div>
      <p class="member-note">Lectura, cultura y comunidad en Chascomús.</p>
    </section>

    <section class="member-form-side">
      <div class="member-form-wrap">
        <div class="member-kicker">Mi cuenta</div>
        <h2>Ingresar a la biblioteca</h2>
        <p class="member-subtitle">Usá tus datos para continuar.</p>

        <?php if (session('mensaje')): ?>
          <div class="member-success"><?= esc(session('mensaje')) ?></div>
        <?php endif; ?>
        <?php if (session('error')): ?>
          <div class="member-alert"><?= esc(session('error')) ?></div>
        <?php endif; ?>

        <form action="<?= base_url('socio/login') ?>" method="post">
          <?= csrf_field() ?>
          <div class="member-field">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required autofocus>
          </div>
          <div class="member-field">
            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" required>
          </div>
          <button type="submit" class="button button-primary member-submit">Ingresar <span aria-hidden="true">→</span></button>
        </form>
        <a href="<?= base_url() ?>" class="member-back">← Volver a la página de inicio</a>
      </div>
    </section>
  </main>
</body>
</html>
