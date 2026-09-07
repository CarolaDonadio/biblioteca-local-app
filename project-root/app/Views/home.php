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
        
        <form action="<?= base_url('catalogo') ?>" method="get" class="search-box">
            <input type="text" name="q" placeholder="Buscar por título, autor o ISBN...">
            <button type="submit">Buscar</button>
        </form>
    </section>

    <main class="container">

        <!-- 1. BIENVENIDA E INFORMACIÓN RÁPIDA -->
        <section class="info-section">
            <h2 class="section-title">Bienvenido a nuestra comunidad</h2>
            <p class="info-desc">
                Un espacio dedicado al aprendizaje, la cultura y el encuentro. Ofrecemos préstamo de libros físicos, acceso a recursos digitales, puestos de lectura y actividades gratuitas durante todo el año.
            </p>
            <div class="info-grid">
                <div class="info-box">
                    <span class="icon">🕒</span>
                    <h3>Horarios</h3>
                    <p><small>Lunes a Viernes: 08:00 - 20:00 hs<br>Sábados: 09:00 - 13:00 hs</small></p>
                </div>
                <div class="info-box">
                    <span class="icon">📍</span>
                    <h3>Ubicación</h3>
                    <p><small>Av. Principal 1234, Centro<br>Ciudad, Provincia</small></p>
                </div>
                <div class="info-box">
                    <span class="icon">💳</span>
                    <h3>Hazte Socio</h3>
                    <p><small>Accede a préstamos a domicilio, reservas online y material exclusivo.</small></p>
                </div>
            </div>
        </section>

        <!-- 2. LIBROS DESTACADOS -->
        <h2 class="section-title">Libros Destacados</h2>
        
        <div class="grid" id="grid-libros">
            <article class="card">
                <div>
                    <div class="cover">📖</div>
                    <h3>El Principito</h3>
                    <p>Antoine de Saint-Exupéry</p>
                    <span class="badge">Disponible</span>
                </div>
                <a href="<?= base_url('catalogo') ?>" class="btn-card">Ver detalle</a>
            </article>

            <article class="card">
                <div>
                    <div class="cover">📚</div>
                    <h3>Cien Años de Soledad</h3>
                    <p>Gabriel García Márquez</p>
                    <span class="badge">Disponible</span>
                </div>
                <a href="<?= base_url('catalogo') ?>" class="btn-card">Ver detalle</a>
            </article>

            <article class="card">
                <div>
                    <div class="cover">📕</div>
                    <h3>1984</h3>
                    <p>George Orwell</p>
                    <span class="badge">Disponible</span>
                </div>
                <a href="<?= base_url('catalogo') ?>" class="btn-card">Ver detalle</a>
            </article>
        </div>

        <!-- 3. PRÓXIMOS EVENTOS -->
        <h2 class="section-title section-title--spaced">Agenda de Eventos</h2>
        <div class="grid">
            <article class="card card--evento">
                <div>
                    <span class="event-date">15 de Septiembre · 18:00 hs</span>
                    <h3>Club de Lectura: Clásicos Latinoamericanos</h3>
                    <p>Debate abierto sobre «Rayuela» de Julio Cortázar. Coordinado por el equipo de literatura.</p>
                </div>
                <button class="btn-card btn-inscribir" data-evento="Club de Lectura">Inscribirme</button>
            </article>

            <article class="card card--evento">
                <div>
                    <span class="event-date">22 de Septiembre · 16:00 hs</span>
                    <h3>Taller de Encuadernación Artesanal</h3>
                    <p>Aprende técnicas básicas para reparar y conservar tus propios libros. Incluye materiales.</p>
                </div>
                <button class="btn-card btn-inscribir" data-evento="Taller de Encuadernación">Inscribirme</button>
            </article>

            <article class="card card--evento">
                <div>
                    <span class="event-date">30 de Septiembre · 17:30 hs</span>
                    <h3>Hora del Cuento Infantil</h3>
                    <p>Lecturas dramatizadas y actividades lúdicas para niños de 5 a 10 años. Entrada libre.</p>
                </div>
                <a href="<?= base_url('socio/login') ?>" class="btn-card">Ver más</a>
            </article>
        </div>

        <!-- 4. EQUIPO DE LA BIBLIOTECA -->
        <h2 class="section-title section-title--spaced">Nuestro Equipo</h2>
        <div class="grid">
            <article class="card card--team">
                <div class="team-avatar">👩‍💼</div>
                <h3>Dra. Laura Giménez</h3>
                <p><strong>Directora de la Biblioteca</strong></p>
                <p><small>Especialista en gestión de archivos y patrimonio cultural.</small></p>
            </article>

            <article class="card card--team">
                <div class="team-avatar">👨‍🔬</div>
                <h3>Lic. Martín Rossi</h3>
                <p><strong>Bibliotecario General</strong></p>
                <p><small>Encargado del área de catálogo, consultas e investigación.</small></p>
            </article>

            <article class="card card--team">
                <div class="team-avatar">👩‍💻</div>
                <h3>Sofía Benítez</h3>
                <p><strong>Coordinadora de Servicios Digitales</strong></p>
                <p><small>Soporte a socios, biblioteca virtual y recursos multimedia.</small></p>
            </article>
        </div>

    </main>

    <footer>
        <div class="footer-content">
            <div class="footer-col">
                <h4>📚 Biblioteca Virtual</h4>
                <p><small>Fomentando la lectura y el acceso libre a la información desde nuestro espacio institucional.</small></p>
            </div>
            <div class="footer-col">
                <h4>Enlaces Rápidos</h4>
                <ul>
                    <li><a href="<?= base_url('catalogo') ?>">Buscar en Catálogo</a></li>
                    <li><a href="<?= base_url('promociones') ?>">Promociones</a></li>
                    <li><a href="<?= base_url('socio/registro') ?>">Hazte Socio</a></li>
                    <li><a href="<?= base_url('admin/login') ?>">Acceso Staff</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Contacto</h4>
                <ul>
                    <li>📧 contacto@biblioteca.edu.ar</li>
                    <li>📞 (011) 4567-8900</li>
                    <li>📍 Av. Principal 1234, Centro</li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> Biblioteca Virtual · Desarrollado en CodeIgniter 4</p>
        </div>
    </footer>

    <!-- Carga del archivo JavaScript personalizado -->
    <script src="<?= base_url('assets/js/home.js') ?>"></script>
</body>
</html>