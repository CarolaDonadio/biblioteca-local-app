<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Biblioteca Domingo Sarmiento de Chascomús: libros, cultura y comunidad.">
    <title>Biblioteca Domingo Sarmiento | Chascomús</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/home.css') ?>">
</head>
<body>
    <header class="site-header">
        <div class="header-inner">
            <a href="<?= base_url() ?>" class="brand" aria-label="Inicio - Biblioteca Domingo Sarmiento">
                <span class="brand-mark">DS</span>
                <span class="brand-copy"><strong>Biblioteca Domingo Sarmiento</strong><small>Chascomús · Buenos Aires</small></span>
            </a>
            <nav class="main-nav" aria-label="Navegación principal">
                <a href="#la-biblioteca">La biblioteca</a><a href="#servicios">Servicios</a><a href="#agenda">Agenda</a><a href="<?= base_url('catalogo') ?>">Catálogo</a><a href="<?= base_url('socio/login') ?>" class="nav-button">Mi cuenta</a>
            </nav>
        </div>
    </header>

    <main>
        <section class="hero">
            <div class="hero-content">
                <p class="eyebrow">Una biblioteca popular para toda la comunidad</p>
                <h1>Historias que nos encuentran en <em>Chascomús</em></h1>
                <p class="hero-lead">Un espacio de lectura, aprendizaje y encuentro. Descubrí nuestro catálogo, asociate y participá de las propuestas culturales de la Biblioteca Domingo Sarmiento.</p>
                <div class="hero-actions"><a href="<?= base_url('catalogo') ?>" class="button button-primary">Explorar el catálogo <span aria-hidden="true">→</span></a><a href="<?= base_url('socio/registro') ?>" class="button button-light">Quiero asociarme</a></div>
                <form action="<?= base_url('catalogo') ?>" method="get" class="catalog-search"><label for="home-search">Buscar un libro</label><div class="search-row"><input id="home-search" type="search" name="q" placeholder="Título, autor o ISBN..."><button type="submit">Buscar</button></div></form>
            </div>
            <div class="hero-photo" role="img" aria-label="Interior cálido de una biblioteca con estanterías de libros"></div>
        </section>

        <section class="welcome section-wrap" id="la-biblioteca"><div class="section-kicker">Nuestra biblioteca</div><div class="welcome-grid"><div><h2>Un lugar para volver, una comunidad para compartir.</h2></div><div class="welcome-copy"><p>La Biblioteca Domingo Sarmiento es un espacio cultural abierto a vecinos, estudiantes, lectores y familias de Chascomús. Ponemos los libros al alcance de todos y acompañamos cada búsqueda con atención cercana.</p><p>Además del préstamo y la consulta de ejemplares, impulsamos actividades que hacen de la lectura una experiencia colectiva.</p><a href="#servicios" class="text-link">Conocé lo que ofrecemos <span aria-hidden="true">↗</span></a></div></div></section>

        <section class="services-band" id="servicios"><div class="section-wrap"><div class="section-heading"><div><div class="section-kicker">A tu alcance</div><h2>Todo lo que podés hacer en la biblioteca</h2></div><p>Recursos y acompañamiento para estudiar, leer, investigar y encontrarnos.</p></div><div class="service-grid">
            <article class="service-item"><span class="service-number">01</span><h3>Encontrar tu próxima lectura</h3><p>Buscá por título, autor o ISBN en nuestro catálogo online.</p><a href="<?= base_url('catalogo') ?>">Ir al catálogo →</a></article>
            <article class="service-item"><span class="service-number">02</span><h3>Asociarte a la biblioteca</h3><p>Formá parte de la comunidad y accedé al préstamo de ejemplares.</p><a href="<?= base_url('socio/registro') ?>">Quiero asociarme →</a></article>
            <article class="service-item"><span class="service-number">03</span><h3>Reservar y administrar préstamos</h3><p>Ingresá a tu cuenta para revisar tus préstamos y reservas.</p><a href="<?= base_url('socio/login') ?>">Ingresar a mi cuenta →</a></article>
            <article class="service-item"><span class="service-number">04</span><h3>Participar de la agenda cultural</h3><p>Compartimos encuentros, talleres y propuestas para todas las edades.</p><a href="#agenda">Ver agenda →</a></article>
        </div></div></section>

        <section class="agenda section-wrap" id="agenda"><div class="section-heading"><div><div class="section-kicker">Para hacer comunidad</div><h2>Agenda de la biblioteca</h2></div><a href="<?= base_url('promociones') ?>" class="text-link">Ver todas las novedades <span aria-hidden="true">↗</span></a></div><div class="event-grid">
            <article class="event-card"><div class="event-date"><strong>Club</strong><span>de lectura</span></div><div><p class="event-type">Encuentro literario</p><h3>Leer, conversar, descubrir</h3><p>Un espacio para compartir lecturas y miradas con otros lectores.</p></div></article>
            <article class="event-card"><div class="event-date event-date--gold"><strong>Para</strong><span>infancias</span></div><div><p class="event-type">Actividad cultural</p><h3>Historias para crecer</h3><p>Lecturas y propuestas para que chicos y chicas se acerquen a los libros.</p></div></article>
            <article class="event-card"><div class="event-date event-date--blue"><strong>Taller</strong><span>abierto</span></div><div><p class="event-type">Aprendizaje</p><h3>Crear con palabras</h3><p>Propuestas para escribir, imaginar y encontrarnos alrededor de la cultura.</p></div></article>
        </div><p class="agenda-note">La agenda y las fechas se actualizan periódicamente. Consultá las novedades antes de acercarte.</p></section>

        <section class="visit section-wrap"><div class="visit-photo" role="img" aria-label="Persona leyendo un libro junto a una ventana"></div><div class="visit-content"><div class="section-kicker">Te esperamos</div><h2>La biblioteca también sucede cuando abrimos la puerta.</h2><p>Acercate a conocernos, consultá disponibilidad y encontrá el modo de participar que mejor se adapte a vos.</p><div class="visit-details"><div><strong>Ubicación</strong><span>Chascomús, Provincia de Buenos Aires</span></div><div><strong>Consultas</strong><span>En la biblioteca o a través de nuestros canales oficiales</span></div></div><a href="<?= base_url('socio/registro') ?>" class="button button-dark">Sumarme a la comunidad <span aria-hidden="true">→</span></a></div></section>
    </main>

    <footer class="site-footer"><div class="footer-main section-wrap"><div><a href="<?= base_url() ?>" class="footer-brand">Biblioteca<br><em>Domingo Sarmiento</em></a><p>Lectura, cultura y comunidad<br>en Chascomús.</p></div><div><h3>Explorá</h3><a href="<?= base_url('catalogo') ?>">Catálogo</a><a href="<?= base_url('promociones') ?>">Novedades</a><a href="<?= base_url('socio/registro') ?>">Asociarme</a></div><div><h3>Accesos</h3><a href="<?= base_url('socio/login') ?>">Mi cuenta</a><a href="<?= base_url('admin/login') ?>">Administración</a></div><div><h3>Encontranos</h3><p>Chascomús, Buenos Aires<br>Argentina</p><a href="#la-biblioteca">Conocé la biblioteca →</a></div></div><div class="footer-bottom"><span>© <?= date('Y') ?> Biblioteca Domingo Sarmiento</span><span>Un espacio público para leer y encontrarnos.</span></div></footer>
+</body>
+</html>
