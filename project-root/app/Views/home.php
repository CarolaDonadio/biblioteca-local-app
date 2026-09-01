<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biblioteca Virtual</title>
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

    <section class="hero">
        <h1>Tu biblioteca, donde sea que estés</h1>
        <p>Explora nuestro catálogo en línea, reserva ejemplares y gestiona tu cuenta de socio fácilmente.</p>
        
        <!-- Formulario que envía la búsqueda directo a la página de catálogo -->
        <form action="<?= base_url('catalogo') ?>" method="get" class="search-box">
            <input type="text" name="q" placeholder="Buscar por título, autor o ISBN...">
            <button type="submit">Buscar</button>
        </form>
    </section>

    <main class="container">
        <h2 class="section-title">Libros Destacados</h2>
        
        <div class="grid" id="grid-libros">
            <article class="card">
                <div>
                    <div class="cover">📖</div>
                    <h3>El Principito</h3>
                    <p>Antoine de Saint-Exupéry</p>
                    <span class="badge">Disponible</span>
                </div>
                <a href="<?= base_url('catalogo') ?>" class="btn-card" style="text-align: center; text-decoration: none; display: block;">Ver detalle</a>
            </article>

            <article class="card">
                <div>
                    <div class="cover">📚</div>
                    <h3>Cien Años de Soledad</h3>
                    <p>Gabriel García Márquez</p>
                    <span class="badge">Disponible</span>
                </div>
                <a href="<?= base_url('catalogo') ?>" class="btn-card" style="text-align: center; text-decoration: none; display: block;">Ver detalle</a>
            </article>

            <article class="card">
                <div>
                    <div class="cover">📕</div>
                    <h3>1984</h3>
                    <p>George Orwell</p>
                    <span class="badge">Disponible</span>
                </div>
                <a href="<?= base_url('catalogo') ?>" class="btn-card" style="text-align: center; text-decoration: none; display: block;">Ver detalle</a>
            </article>
        </div>
    </main>

    <footer>
        <p>&copy; <?= date('Y') ?> Biblioteca Virtual · Desarrollado en CodeIgniter 4</p>
    </footer>

</body>
</html>