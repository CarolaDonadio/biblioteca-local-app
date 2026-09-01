<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Biblioteca Virtual</title>
    
    <!-- Enlaces corregidos con base_url() para CodeIgniter 4 -->
    <link rel="stylesheet" href="<?= base_url('assets/css/design-system.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/public.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/home.css') ?>">
</head>
<body>

    <header>
        <strong>📚 Mi Biblioteca Virtual</strong>
        <nav>
            <a href="#catalogo">Catálogo</a>
            <a href="#promociones">Promociones</a>
            <a href="#socio">Socios</a>
            <a href="#admin">Administración</a>
        </nav>
    </header>

    <section class="hero">
        <h1>Gestión y Comunidad para Bibliotecas</h1>
        <p>Consulta libros, promociones y servicios de la biblioteca.</p>
        <div class="search">
            <input id="buscar" placeholder="Buscar por título, autor o ISBN">
            <button onclick="buscarLibro()">Buscar</button>
        </div>
        <p id="resultado"></p>
    </section>

    <main class="container">
        <section id="catalogo">
            <h2>Catálogo público</h2>
            <div class="grid">
                <article class="card">
                    <div class="cover">📖</div>
                    <h3>El Principito</h3>
                    <p>Antoine de Saint-Exupéry</p>
                    <p><strong>Disponible</strong></p>
                    <button>Ver detalles</button>
                </article>
                <article class="card">
                    <div class="cover">📚</div>
                    <h3>Cien años de soledad</h3>
                    <p>Gabriel García Márquez</p>
                    <p><strong>Disponible</strong></p>
                    <button>Reservar</button>
                </article>
                <article class="card">
                    <div class="cover">📕</div>
                    <h3>Don Quijote</h3>
                    <p>Miguel de Cervantes</p>
                    <p><strong>Consultar disponibilidad</strong></p>
                    <button>Ver detalles</button>
                </article>
            </div>
        </section>

        <section id="promociones">
            <h2>Promociones y novedades</h2>
            <div class="grid">
                <article class="card">
                    <h3 style="margin-top: 0;">Campaña nuevos socios</h3>
                    <p>Beneficios especiales para quienes se registren durante este mes.</p>
                    <button>Ver promoción</button>
                </article>
                <article class="card">
                    <h3 style="margin-top: 0;">Semana de lectura</h3>
                    <p>Actividades y novedades para toda la comunidad.</p>
                    <button>Más información</button>
                </article>
            </div>
        </section>

        <section id="socio">
            <h2>Portal del socio</h2>
            <div class="grid">
                <article class="card">
                    <h3 style="margin-top: 0;">Iniciar sesión</h3>
                    <p>Consultá préstamos, reservas y devoluciones.</p>
                    <button>Ingresar</button>
                </article>
                <article class="card">
                    <h3 style="margin-top: 0;">Registrarme</h3>
                    <p>Creá tu cuenta para acceder a los servicios.</p>
                    <button>Registrarme</button>
                </article>
            </div>
        </section>

        <section id="admin">
            <h2>Panel de administración</h2>
            <div class="stats">
                <div class="stat"><strong>1.250</strong><br>Libros registrados</div>
                <div class="stat"><strong>340</strong><br>Socios activos</div>
                <div class="stat"><strong>87</strong><br>Préstamos activos</div>
                <div class="stat"><strong>12</strong><br>Reservas pendientes</div>
            </div>
            <div class="grid" style="margin-top:20px">
                <article class="card"><h3 style="margin-top: 0;">Catálogo</h3><p>Gestión de libros y multimedia.</p></article>
                <article class="card"><h3 style="margin-top: 0;">Inventario</h3><p>Control de ejemplares.</p></article>
                <article class="card"><h3 style="margin-top: 0;">Préstamos</h3><p>Préstamos, devoluciones y renovaciones.</p></article>
                <article class="card"><h3 style="margin-top: 0;">Reservas</h3><p>Solicitudes y disponibilidad.</p></article>
                <article class="card"><h3 style="margin-top: 0;">Notificaciones</h3><p>Email, Telegram y WhatsApp.</p></article>
                <article class="card"><h3 style="margin-top: 0;">Promociones</h3><p>Campañas y beneficios.</p></article>
            </div>
        </section>
    </main>

    <footer>Mi Biblioteca Virtual · MVP académico · HTML, CSS, JavaScript, PHP, CodeIgniter 4 y MySQL</footer>

    <script>
        function buscarLibro(){
            const t = document.getElementById('buscar').value.trim();
            document.getElementById('resultado').textContent = t 
                ? 'Buscando: "' + t + '". La búsqueda real se conectará a MySQL mediante CodeIgniter.'
                : 'Ingresá un título, autor o ISBN.';
        }
    </script>
</body>
</html>