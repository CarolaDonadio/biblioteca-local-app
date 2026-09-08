<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo Público - Mi Biblioteca Virtual</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/home.css') ?>">
</head>
<body>

    <header>
        <a href="<?= base_url() ?>" class="logo">📚 Biblioteca Virtual</a>
        <nav>
            <a href="<?= base_url('catalogo') ?>">Catálogo</a>
            <a href="<?= base_url('promociones') ?>">Promociones</a>
            <a href="<?= base_url('socio/login') ?>">Portal Socios</a>
            <a href="<?= base_url('admin/login') ?>">Administración</a>
        </nav>
    </header>

    <main class="container">
        <h2 class="section-title">Catálogo de la Biblioteca</h2>

        <!-- Barra de búsqueda -->
        <section style="margin-bottom: 2rem;">
            <form action="<?= base_url('catalogo') ?>" method="get" class="search-box">
                <input type="text" name="q" value="<?= esc($termino) ?>" placeholder="Buscar por título, autor o ISBN...">
                <button type="submit">Buscar</button>
            </form>
        </section>

        <!-- Grid de Libros -->
        <div class="grid">
            <?php if (!empty($libros) && is_array($libros)): ?>
                <?php foreach ($libros as $libro): ?>
                    <article class="card">
                        <div>
                            <div class="cover">
                                <?php if (!empty($libro['portada_url'])): ?>
                                    <img src="<?= base_url('uploads/' . $libro['portada_url']) ?>" 
                                         alt="<?= esc($libro['titulo']) ?>" 
                                         loading="lazy" 
                                         width="100%" 
                                         height="100%" 
                                         style="object-fit: cover; border-radius: 8px;">
                                <?php else: ?>
                                    📖
                                <?php endif; ?>
                            </div>

                            <h3><?= esc($libro['titulo']) ?></h3>
                            <p><strong>Autor:</strong> <?= esc($libro['autor']) ?></p>
                            
                            <div style="display: flex; gap: 0.5rem; margin-bottom: 1rem; flex-wrap: wrap;">
                                <span class="badge"><?= esc($libro['categoria'] ?? 'General') ?></span>
                                
                                <?php if ($libro['disponible'] && $libro['cantidad'] > 0): ?>
                                    <span class="badge" style="background: #dcfce7; color: #166534;">Disponible (<?= $libro['cantidad'] ?>)</span>
                                <?php else: ?>
                                    <span class="badge" style="background: #fee2e2; color: #991b1b;">Agotado</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <a href="<?= base_url('catalogo/libro/' . $libro['id']) ?>" class="btn-card" style="text-align: center; text-decoration: none; display: block;">
                            Ver detalle
                        </a>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 3rem 1rem;">
                    <p style="font-size: 1.2rem; color: #666;">No se encontraron libros que coincidan con la búsqueda "<strong><?= esc($termino) ?></strong>".</p>
                    <a href="<?= base_url('catalogo') ?>" style="color: #2563eb; text-decoration: underline;">Ver todo el catálogo</a>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <p>&copy; <?= date('Y') ?> Biblioteca Virtual · Maquetación Front-End</p>
    </footer>

</body>
</html>
