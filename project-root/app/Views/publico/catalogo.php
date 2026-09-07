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

        <!-- Barra de búsqueda y Filtros visuales -->
        <section style="margin-bottom: 2rem;">
            <form action="<?= base_url('catalogo') ?>" method="get" class="search-box">
                <input type="text" name="q" value="<?= esc($termino) ?>" placeholder="Buscar por título, autor o ISBN...">
                <button type="submit">Buscar</button>
            </form>
        </section>

        <!-- Grid de Libros -->
        <div class="grid">
            <?php foreach ($libros as $libro): ?>
                <article class="card">
                    <div>
                        <div class="cover">
                            <?php if (!empty($libro['portada_url'])): ?>
                                <!-- Lazy Load para optimización de carga en el MVP -->
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
                        <p><?= esc($libro['autor']) ?></p>
                        
                        <div style="display: flex; gap: 0.5rem; margin-bottom: 1rem;">
                            <span class="badge"><?= esc($libro['categoria']) ?></span>
                            
                            <?php if ($libro['disponible']): ?>
                                <span class="badge" style="background: #dcfce7; color: #166534;">Disponible</span>
                            <?php else: ?>
                                <span class="badge" style="background: #fee2e2; color: #991b1b;">Prestado</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <a href="<?= base_url('catalogo/libro/' . $libro['id']) ?>" class="btn-card" style="text-align: center; text-decoration: none; display: block;">
                        Ver detalle
                    </a>
                </article>
            <?php endforeach; ?>
        </div>
    </main>

    <footer>
        <p>&copy; <?= date('Y') ?> Biblioteca Virtual · Maquetación Front-End</p>
    </footer>

</body>
</html>
