<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ingresar · Panel administrativo</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Source+Serif+4:wght@600;700&family=Inter:wght@400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/assets/css/design-system.css">
  <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body style="background:var(--tinta);min-height:100vh;">
  <div class="tarjeta login-box">
    <h2>Panel administrativo</h2>
    <p style="color:var(--gris-texto);font-size:.88rem;margin-top:-.5em;">Mi Biblioteca Virtual</p>

    <?php if (session('error')): ?>
      <div class="alerta alerta--error"><?= esc(session('error')) ?></div>
    <?php endif; ?>

    <form action="/admin/login" method="post">
      <?= csrf_field() ?>
      <div class="campo">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required autofocus>
      </div>
      <div class="campo">
        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" required>
      </div>
      <button type="submit" class="btn" style="width:100%;">Ingresar</button>
    </form>
  </div>
</body>
</html>
