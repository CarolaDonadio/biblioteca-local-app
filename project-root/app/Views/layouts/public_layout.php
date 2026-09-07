<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">

  <meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
  >

  <title><?= esc($titulo ?? 'Mi Biblioteca Virtual') ?></title>

  <link
    rel="stylesheet"
    href="<?= base_url('assets/css/design-system.css') ?>"
  >

  <link
    rel="stylesheet"
    href="<?= base_url('assets/css/public.css') ?>"
  >

  <?= $css_extra ?? '' ?>
</head>

<body>

<header class="pub-header">

  <div class="pub-header__contenedor">

    <a href="<?= site_url('/') ?>" class="pub-header__marca">
      Mi Biblioteca Virtual
      <span>Catálogo y comunidad</span>
    </a>

    <button
      class="pub-menu-btn"
      type="button"
      aria-label="Abrir menú de navegación"
      aria-expanded="false"
      aria-controls="pub-nav"
    >
      <span></span>
      <span></span>
      <span></span>
    </button>

    <nav class="pub-nav" id="pub-nav">

      <a href="<?= site_url('catalogo') ?>">
        Catálogo
      </a>

      <a href="<?= site_url('promociones') ?>">
        Promociones
      </a>

      <?php if (session('socio_id')): ?>

        <a href="<?= site_url('socio/panel') ?>">
          Mi cuenta
        </a>

        <a href="<?= site_url('socio/logout') ?>">
          Salir
        </a>

      <?php else: ?>

        <a href="<?= site_url('socio/login') ?>">
          Ingresar
        </a>

        <a
          href="<?= site_url('socio/registro') ?>"
          class="pub-nav__destacado"
        >
          Asociarme
        </a>

      <?php endif; ?>

    </nav>

  </div>

</header>


<?php if (session('mensaje') || session('error')): ?>

  <div class="pub-contenido" style="padding-bottom:0;">

    <?php if (session('mensaje')): ?>

      <div
        class="alerta alerta--exito"
        data-auto-cerrar
      >
        <?= esc(session('mensaje')) ?>
      </div>

    <?php endif; ?>


    <?php if (session('error')): ?>

      <div
        class="alerta alerta--error"
        data-auto-cerrar
      >
        <?= esc(session('error')) ?>
      </div>

    <?php endif; ?>

  </div>

<?php endif; ?>


<?= $contenido ?? '' ?>


<script src="<?= base_url('assets/js/public/lazyload.js') ?>"></script>
<script src="<?= base_url('assets/js/public/portal.js') ?>"></script>

<?= $js_extra ?? '' ?>

</body>
</html>
