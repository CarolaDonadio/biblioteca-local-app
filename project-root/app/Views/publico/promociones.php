<?php
$imagenesRespaldo = [
  'https://images.unsplash.com/photo-1512820790803-83ca734da794?auto=format&fit=crop&w=900&q=85',
  'https://images.unsplash.com/photo-1543002588-bfa74002ed7e?auto=format&fit=crop&w=900&q=85',
  'https://images.unsplash.com/photo-1495446815901-a7297e633e8d?auto=format&fit=crop&w=900&q=85',
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Promociones y novedades de la Biblioteca Domingo Sarmiento de Chascomús.">
  <title>Promociones | Biblioteca Domingo Sarmiento</title>
  <link rel="stylesheet" href="<?= base_url('assets/css/home.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/promociones.css') ?>">
</head>
<body>
  <header class="site-header">
    <div class="header-inner">
      <a href="<?= base_url() ?>" class="brand" aria-label="Inicio - Biblioteca Domingo Sarmiento">
        <span class="brand-mark">DS</span>
        <span class="brand-copy"><strong>Biblioteca Domingo Sarmiento</strong><small>Chascomús · Buenos Aires</small></span>
      </a>
      <nav class="main-nav" aria-label="Navegación principal">
        <a href="<?= base_url() ?>#la-biblioteca">La biblioteca</a><a href="<?= base_url() ?>#servicios">Servicios</a><a href="<?= base_url() ?>#agenda">Agenda</a><a href="<?= base_url('catalogo') ?>">Catálogo</a><a href="<?= base_url('socio/login') ?>" class="nav-button">Mi cuenta</a><a href="<?= base_url('admin/login') ?>" class="nav-button">Administración</a>
      </nav>
    </div>
  </header>

  <main class="promociones-page">
    <section class="promociones-hero">
      <div>
        <p class="promociones-kicker">Novedades de la biblioteca</p>
        <h1>Promociones para volver a <em>leer</em>.</h1>
        <p class="promociones-intro">Selecciones y propuestas para descubrir nuevas historias, compartir lecturas y acercarte a la biblioteca.</p>
      </div>
      <div class="promociones-hero__detalle">
        <span><?= count($promociones) ?></span>
        <p>promociones<br>vigentes</p>
      </div>
    </section>

    <section class="promociones-listado" aria-labelledby="promociones-titulo">
      <div class="promociones-heading">
        <div>
          <p class="promociones-kicker">Para toda la comunidad</p>
          <h2 id="promociones-titulo">Elegí tu próxima lectura</h2>
        </div>
        <p>Propuestas activas de la Biblioteca Domingo Sarmiento.</p>
      </div>

      <?php if (! empty($promociones)): ?>
        <div class="promociones-grid">
          <?php foreach ($promociones as $indice => $p): ?>
            <?php $imagenRespaldo = $imagenesRespaldo[$indice % count($imagenesRespaldo)]; ?>
            <article class="promocion-card">
              <div class="promocion-card__imagen">
                <img
                  class="lazy"
                  src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw=="
                  data-src="<?= ! empty($p['imagen_url']) ? base_url(ltrim($p['imagen_url'], '/')) : $imagenRespaldo ?>"
                  data-fallback="<?= esc($imagenRespaldo) ?>"
                  alt="<?= esc($p['titulo']) ?>"
                  loading="lazy"
                  decoding="async"
                  onerror="this.onerror=null;this.src=this.dataset.fallback || this.src;"
                >
                <span class="promocion-card__numero">0<?= $indice + 1 ?></span>
              </div>
              <div class="promocion-card__contenido">
                <span class="promocion-badge">Vigente hasta <?= esc($p['fecha_fin']) ?></span>
                <h3><?= esc($p['titulo']) ?></h3>
                <p><?= esc($p['descripcion']) ?></p>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <p class="promociones-vacio">No hay promociones vigentes en este momento.</p>
      <?php endif; ?>
    </section>
  </main>

  <footer class="site-footer"><div class="footer-main section-wrap"><div><a href="<?= base_url() ?>" class="footer-brand">Biblioteca<br><em>Domingo Sarmiento</em></a><p>Lectura, cultura y comunidad<br>en Chascomús.</p></div><div><h3>Explorá</h3><a href="<?= base_url('catalogo') ?>">Catálogo</a><a href="<?= base_url('promociones') ?>">Novedades</a></div><div><h3>Accesos</h3><a href="<?= base_url('socio/login') ?>">Mi cuenta</a><a href="<?= base_url('admin/login') ?>">Administración</a></div><div><h3>Encontranos</h3><p>Chascomús, Buenos Aires<br>Argentina</p><a href="<?= base_url() ?>#la-biblioteca">Conocé la biblioteca →</a></div></div><div class="footer-bottom"><span>© <?= date('Y') ?> Biblioteca Domingo Sarmiento</span><span>Un espacio público para leer y encontrarnos.</span></div></footer>
</body>
</html>
