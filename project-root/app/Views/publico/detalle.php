<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Detalle de <?= esc($libro['titulo'] ?? 'libro') ?> en la Biblioteca Domingo Sarmiento.">
    <title><?= esc($libro['titulo'] ?? 'Detalle del libro') ?> | Biblioteca Domingo Sarmiento</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/detalle.css') ?>">
</head>
<body>
    <header class="detail-header">
        <div class="detail-header__inner">
            <a href="<?= base_url() ?>" class="detail-brand" aria-label="Volver al inicio">
                <span class="detail-brand__mark">DS</span>
                <span><strong>Biblioteca Domingo Sarmiento</strong><small>Chascomús · Catálogo</small></span>
            </a>
            <nav class="detail-nav" aria-label="Navegación principal">
                <a href="<?= base_url() ?>">Inicio</a>
                <a href="<?= base_url('catalogo') ?>">Catálogo</a>
                <a href="<?= base_url('promociones') ?>">Novedades</a>
                <a href="<?= base_url('socio/login') ?>" class="detail-nav__account">Mi cuenta</a>
            </nav>
        </div>
    </header>

    <?php if (session('mensaje') || session('error')): ?>
        <div class="detail-alerts detail-wrap">
            <?php if (session('mensaje')): ?><div class="detail-alert detail-alert--success"><?= esc(session('mensaje')) ?></div><?php endif; ?>
            <?php if (session('error')): ?><div class="detail-alert detail-alert--error"><?= esc(session('error')) ?></div><?php endif; ?>
        </div>
    <?php endif; ?>

    <main class="detail-main detail-wrap">
        <a href="<?= base_url('catalogo') ?>" class="back-link"><span aria-hidden="true">←</span> Volver al catálogo</a>
        <div class="detail-layout">
            <div class="detail-cover-area">
                <div class="detail-cover">
                    <?php if (!empty($libro['portada_url'])): ?>
                        <img src="<?= base_url('uploads/' . $libro['portada_url']) ?>" alt="Portada de <?= esc($libro['titulo']) ?>">
                    <?php else: ?>
                        <div class="detail-cover__placeholder"><span>DS</span><small>Biblioteca<br>Domingo Sarmiento</small></div>
                    <?php endif; ?>
                </div>
                <p class="cover-caption">Parte de la colección de la<br>Biblioteca Domingo Sarmiento</p>
            </div>

            <article class="detail-content">
                <p class="detail-kicker"><?= esc($libro['categoria'] ?? 'Colección general') ?></p>
                <h1><?= esc($libro['titulo']) ?></h1>
                <p class="detail-author">por <strong><?= esc($libro['autor'] ?? 'Autor no registrado') ?></strong></p>

                <div class="detail-facts">
                    <div><span>ISBN</span><strong><?= esc($libro['isbn'] ?? 'No registrado') ?></strong></div>
                    <div><span>Editorial</span><strong><?= esc($libro['editorial'] ?? 'No especificada') ?></strong></div>
                    <div><span>Año</span><strong><?= esc($libro['anio'] ?? 'No registrado') ?></strong></div>
                    <div><span>Estado</span>
                        <?php if (!empty($libro['disponible']) && (int) ($libro['cantidad'] ?? 0) > 0): ?>
                            <strong class="availability availability--yes">Disponible · <?= (int) $libro['cantidad'] ?> ejemplar<?= (int) $libro['cantidad'] === 1 ? '' : 'es' ?></strong>
                        <?php else: ?>
                            <strong class="availability availability--no">No disponible</strong>
                        <?php endif; ?>
                    </div>
                </div>

                <section class="synopsis"><p class="detail-kicker">Sobre este libro</p><h2>Sinopsis</h2><p><?= esc($libro['sinopsis'] ?? 'Todavía no hay una descripción disponible para este libro.') ?></p></section>

                <div class="detail-action">
                    <?php if (session('socio_dni')): ?>
                        <form action="<?= base_url('socio/reservar/' . $libro['id']) ?>" method="post">
                            <?= csrf_field() ?>
                            <button type="submit" class="detail-button">Reservar este libro <span aria-hidden="true">→</span></button>
                        </form>
                        <small>La reserva quedará asociada a tu cuenta de socio.</small>
                    <?php else: ?>
                        <a href="<?= base_url('socio/login') ?>" class="detail-button">Ingresá para reservar <span aria-hidden="true">→</span></a>
                        <small>¿Todavía no sos socio? <a href="<?= base_url('socio/registro') ?>">Asociate a la biblioteca</a>.</small>
                    <?php endif; ?>
                </div>
            </article>
        </div>
    </main>

    <footer class="detail-footer"><div class="detail-wrap"><div><strong>Biblioteca Domingo Sarmiento</strong><span>Lectura, cultura y comunidad en Chascomús.</span></div><div><span>¿Querés seguir explorando?</span><a href="<?= base_url('catalogo') ?>">Volver al catálogo →</a></div><small>© <?= date('Y') ?> Biblioteca Domingo Sarmiento</small></div></footer>
</body>
</html>
