<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ingresar · Administración | Biblioteca Domingo Sarmiento</title>
  <link rel="stylesheet" href="<?= base_url('assets/css/home.css') ?>">
  <style>
    .login-page { min-height: 100vh; display: grid; grid-template-columns: minmax(0, 1.05fr) minmax(420px, .95fr); background: var(--paper); }
    .login-intro { display: flex; flex-direction: column; justify-content: space-between; min-height: 100vh; padding: 32px 7vw 38px; background: var(--green); color: white; }
    .login-brand { display: flex; align-items: center; gap: 12px; color: white; }
    .login-brand .brand-mark { border-color: #e8c87c; color: #e8c87c; }
    .login-brand .brand-copy strong { display: block; font: 600 16px 'Playfair Display', serif; }
    .login-brand .brand-copy small { display: block; margin-top: 4px; color: #c2d3ca; font-size: 10px; letter-spacing: .12em; text-transform: uppercase; }
    .login-message { max-width: 520px; padding: 7vh 0; }
    .login-message h1 { margin: 18px 0; font: 500 clamp(40px, 5vw, 68px)/1.06 'Playfair Display', serif; }
    .login-message h1 em { color: #e8c87c; }
    .login-message p { max-width: 420px; color: #d4e1d9; font-size: 16px; }
    .login-note { color: #c2d3ca; font-size: 12px; }
    .login-form-side { display: flex; align-items: center; justify-content: center; padding: 50px 7vw; }
    .login-form-wrap { width: min(100%, 390px); }
    .login-kicker { color: var(--green); font-size: 11px; font-weight: 700; letter-spacing: .18em; text-transform: uppercase; }
    .login-form-wrap h2 { margin: 13px 0 9px; color: var(--ink); font: 500 38px/1.1 'Playfair Display', serif; }
    .login-subtitle { color: var(--muted); font-size: 14px; margin-bottom: 30px; }
    .login-alert { margin-bottom: 20px; padding: 12px 14px; border-left: 3px solid #a8483e; background: #f4e5df; color: #7d3029; font-size: 13px; }
    .login-field { margin-bottom: 19px; }
    .login-field label { display: block; margin-bottom: 7px; color: var(--ink); font-size: 12px; font-weight: 700; }
    .login-field input { width: 100%; padding: 13px 14px; border: 1px solid var(--line); outline: 0; background: white; color: var(--ink); font: 14px 'DM Sans', sans-serif; transition: border-color .2s, box-shadow .2s; }
    .login-field input:focus { border-color: var(--green); box-shadow: 0 0 0 3px rgba(30, 89, 77, .12); }
    .login-submit { width: 100%; margin-top: 8px; border: 0; cursor: pointer; }
    .login-back { display: inline-block; margin-top: 25px; color: var(--green); font-size: 13px; font-weight: 700; border-bottom: 1px solid var(--gold); padding-bottom: 4px; }
    @media (max-width: 760px) { .login-page { display: block; }.login-intro { min-height: auto; padding: 25px 24px 38px; }.login-message { padding: 65px 0 35px; }.login-message h1 { font-size: 44px; }.login-note { display: none; }.login-form-side { padding: 58px 24px 70px; } }
  </style>
</head>
<body>
  <main class="login-page">
    <section class="login-intro" aria-label="Biblioteca Domingo Sarmiento">
      <a href="<?= base_url() ?>" class="login-brand" aria-label="Volver al inicio">
        <span class="brand-mark">DS</span>
        <span class="brand-copy"><strong>Biblioteca Domingo Sarmiento</strong><small>Chascomús · Buenos Aires</small></span>
      </a>
      <div class="login-message">
        <div class="eyebrow">Área de gestión</div>
        <h1>Todo lo que hace posible <em>la biblioteca.</em></h1>
        <p>Ingresá para administrar el catálogo, acompañar a los socios y mantener viva nuestra comunidad lectora.</p>
      </div>
      <p class="login-note">Un espacio público para leer y encontrarnos.</p>
    </section>

    <section class="login-form-side">
      <div class="login-form-wrap">
        <div class="login-kicker">Acceso administrativo</div>
        <h2>Bienvenido de nuevo</h2>
        <p class="login-subtitle">Ingresá con tus credenciales para continuar.</p>

        <?php if (session('error')): ?>
          <div class="login-alert"><?= esc(session('error')) ?></div>
        <?php endif; ?>

        <form action="<?= base_url('admin/login') ?>" method="post">
          <?= csrf_field() ?>
          <div class="login-field">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required autofocus>
          </div>
          <div class="login-field">
            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" required>
          </div>
          <button type="submit" class="button button-primary login-submit">Ingresar <span aria-hidden="true">→</span></button>
        </form>
        <a href="<?= base_url() ?>" class="login-back">← Volver a la página de inicio</a>
      </div>
    </section>
  </main>
</body>
</html>
