<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= esc($titulo ?? 'Panel') ?> · Mi Biblioteca Virtual</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Source+Serif+4:wght@600;700&family=Inter:wght@400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/assets/css/design-system.css">
  <link rel="stylesheet" href="/assets/css/admin.css">
  <?= $css_extra ?? '' ?>
</head>
<body>
<div class="admin-layout">
  <aside class="admin-sidebar">
    <div class="admin-sidebar__marca">Mi Biblioteca Virtual<span>Panel administrativo</span></div>
    <ul class="admin-nav">
      <li><a href="/admin" class="<?= uri_string() === 'admin' ? 'activo' : '' ?>">Dashboard</a></li>

      <div class="admin-nav__grupo">Catálogo</div>
      <li><a href="/admin/libros">Libros y multimedia</a></li>
      <li><a href="/admin/ejemplares">Inventario / ejemplares</a></li>

      <div class="admin-nav__grupo">Comunidad</div>
      <li><a href="/admin/socios">Socios</a></li>
      <li><a href="/admin/prestamos">Préstamos y devoluciones</a></li>
      <li><a href="/admin/reservas">Motor de reservas</a></li>

      <div class="admin-nav__grupo">Comunicación</div>
      <li><a href="/admin/notificaciones">Notificaciones</a></li>
      <li><a href="/admin/promociones">Promociones</a></li>

      <?php if ((session('admin_rol') ?? '') === 'superadmin'): ?>
      <div class="admin-nav__grupo">Sistema</div>
      <li><a href="/admin/usuarios">Usuarios administrativos</a></li>
      <?php endif; ?>
    </ul>
  </aside>

  <main class="admin-contenido">
    <div class="admin-topbar">
      <h1><?= esc($titulo ?? '') ?></h1>
      <div class="admin-topbar__usuario">
        <?= esc(session('admin_nombre') ?? '') ?> · <a href="/admin/logout">Cerrar sesión</a>
      </div>
    </div>

    <?php if (session('mensaje')): ?>
      <div class="alerta alerta--exito" data-auto-cerrar><?= esc(session('mensaje')) ?></div>
    <?php endif; ?>
    <?php if (session('error')): ?>
      <div class="alerta alerta--error" data-auto-cerrar><?= esc(session('error')) ?></div>
    <?php endif; ?>

    <?= $contenido ?? '' ?>
  </main>
</div>
<script src="/assets/js/admin/panel.js"></script>
<?= $js_extra ?? '' ?>
</body>
</html>
