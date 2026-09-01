<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= esc($titulo ?? 'Mi Biblioteca Virtual') ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Source+Serif+4:wght@600;700&family=Inter:wght@400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/assets/css/design-system.css">
  <link rel="stylesheet" href="/assets/css/public.css">
  <?= $css_extra ?? '' ?>
</head>
<body>
<header class="pub-header">
  <a href="/" class="pub-header__marca">Mi Biblioteca Virtual<span>Catálogo y comunidad</span></a>
  <nav class="pub-nav">
    <a href="/catalogo">Catálogo</a>
    <a href="/promociones">Promociones</a>
    <?php if (session('socio_id')): ?>
      <a href="/socio/panel">Mi cuenta</a>
      <a href="/socio/logout">Salir</a>
    <?php else: ?>
      <a href="/socio/login">Ingresar</a>
      <a href="/socio/registro">Asociarme</a>
    <?php endif; ?>
  </nav>
</header>

<?php if (session('mensaje') || session('error')): ?>
  <div class="pub-contenido" style="padding-bottom:0;">
    <?php if (session('mensaje')): ?><div class="alerta alerta--exito" data-auto-cerrar><?= esc(session('mensaje')) ?></div><?php endif; ?>
    <?php if (session('error')): ?><div class="alerta alerta--error" data-auto-cerrar><?= esc(session('error')) ?></div><?php endif; ?>
  </div>
<?php endif; ?>

<?= $contenido ?? '' ?>

<script src="/assets/js/public/lazyload.js"></script>
<?= $js_extra ?? '' ?>
</body>
</html>
