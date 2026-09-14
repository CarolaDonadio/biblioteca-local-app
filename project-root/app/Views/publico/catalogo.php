<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Explorá el catálogo de la Biblioteca Domingo Sarmiento de Chascomús.">
    <title>Catálogo | Biblioteca Domingo Sarmiento</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/catalogo.css') ?>">
</head>
<body>
    <header class="catalog-header">
        <div class="catalog-header__inner">
            <a href="<?= base_url() ?>" class="catalog-brand" aria-label="Volver al inicio">
                <span class="catalog-brand__mark">DS</span>
                <span><strong>Biblioteca Domingo Sarmiento</strong><small>Chascomús · Catálogo</small></span>
            </a>
            <nav class="catalog-nav" aria-label="Navegación principal">
                <a href="<?= base_url() ?>">Inicio</a>
                <a href="<?= base_url('promociones') ?>">Novedades</a>
                <a href="<?= base_url('socio/login') ?>" class="catalog-nav__account">Mi cuenta</a>
            </nav>
        </div>
    </header>

    <main>
        <section class="catalog-intro">
            <div class="catalog-wrap">
                <p class="catalog-kicker">Biblioteca Domingo Sarmiento</p>
                <h1>Encontrá tu próxima historia.</h1>
                <p>Explorá los libros disponibles en nuestra biblioteca y descubrí nuevas lecturas para compartir.</p>
                <form action="<?= base_url('catalogo') ?>" method="get" class="catalog-search">
                    <label for="catalog-search">Buscar en el catálogo</label>
                    <div class="catalog-search__row">
                        <span aria-hidden="true">⌕</span>
                        <input id="catalog-search" type="search" name="q" value="<?= esc($termino) ?>" placeholder="Título, autor o ISBN...">
                        <button type="submit">Buscar</button>
                    </div>
                </form>
            </div>
        </section>

        <section class="catalog-results catalog-wrap">
            <div class="results-heading">
                <div>
                    <p class="catalog-kicker">Colección</p>
                    <h2><?= $termino !== '' ? 'Resultados de búsqueda' : 'Todos los ejemplares' ?></h2>
                </div>
                <?php if (!empty($libros) && is_array($libros)): ?>
                    <span class="results-count"><?= count($libros) ?> <?= count($libros) === 1 ? 'título' : 'títulos' ?></span>
                <?php endif; ?>
            </div>

            <?php if ($termino !== ''): ?>
                <div class="active-search">Mostrando resultados para <strong>“<?= esc($termino) ?>”</strong><a href="<?= base_url('catalogo') ?>">Limpiar búsqueda</a></div>
            <?php endif; ?>

            <?php if (!empty($libros) && is_array($libros)): ?>
                <div class="book-grid">
                    <?php foreach ($libros as $libro): ?>
                        <?php $estaDisponible = !empty($libro['disponible']) && (int) ($libro['cantidad'] ?? 0) > 0; ?>
                        <article class="book-card">
                            <a href="<?= base_url('catalogo/libro/' . $libro['id']) ?>" class="book-cover" aria-label="Ver detalle de <?= esc($libro['titulo']) ?>">
                                <?php if (!empty($libro['portada_url'])): ?>
                                    <img src="<?= base_url('uploads/' . $libro['portada_url']) ?>" alt="Portada de <?= esc($libro['titulo']) ?>" loading="lazy">
                                <?php else: ?>
                                    <span class="book-cover__placeholder"><span>DS</span><small>Biblioteca<br>Domingo Sarmiento</small></span>
                                <?php endif; ?>
                                <span class="book-cover__label"><?= $estaDisponible ? 'Disponible' : 'No disponible' ?></span>
                            </a>
                            <div class="book-info">
                                <p class="book-category"><?= esc($libro['categoria'] ?? 'Colección general') ?></p>
                                <h3><a href="<?= base_url('catalogo/libro/' . $libro['id']) ?>"><?= esc($libro['titulo']) ?></a></h3>
                                <p class="book-author"><?= esc($libro['autor'] ?? 'Autor no registrado') ?></p>
                                <a href="<?= base_url('catalogo/libro/' . $libro['id']) ?>" class="book-link">Ver detalle <span aria-hidden="true">→</span></a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state"><span class="empty-state__mark">⌕</span><h2>No encontramos ese libro</h2><p>Probá con otro título, autor o ISBN. También podés explorar todo el catálogo.</p><a href="<?= base_url('catalogo') ?>" class="catalog-button">Ver todos los libros</a></div>
            <?php endif; ?>
        </section>
    </main>

    <footer class="catalog-footer"><div class="catalog-wrap"><div><strong>Biblioteca Domingo Sarmiento</strong><span>Lectura, cultura y comunidad en Chascomús.</span></div><div><span>¿Ya sos parte?</span><a href="<?= base_url('socio/login') ?>">Ingresá a tu cuenta →</a></div><small>© <?= date('Y') ?> Biblioteca Domingo Sarmiento</small></div></footer>
</body>
</html>
