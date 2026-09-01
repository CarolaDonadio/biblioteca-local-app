<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// -----------------------------------------------------------------
// 1. TU HOME PERSONALIZADO (Carga app/Controllers/Inicio.php)
// -----------------------------------------------------------------
$routes->get('/', 'Inicio::index');


// -----------------------------------------------------------------
// 2. PORTAL PÚBLICO — Consulta pública + Promociones + Portal del socio
// -----------------------------------------------------------------
$routes->get('catalogo', 'Publico\CatalogoController::index');
$routes->get('catalogo/buscar', 'Publico\CatalogoController::buscar');
$routes->get('catalogo/libro/(:num)', 'Publico\CatalogoController::detalle/$1');

$routes->get('promociones', 'Publico\PromocionPublicaController::index');

$routes->get('socio/login', 'Publico\SocioPortalController::login');
$routes->post('socio/login', 'Publico\SocioPortalController::autenticar');
$routes->get('socio/registro', 'Publico\SocioPortalController::registro');
$routes->post('socio/registro', 'Publico\SocioPortalController::guardarRegistro');
$routes->get('socio/logout', 'Publico\SocioPortalController::logout');

$routes->group('socio/panel', ['filter' => 'socioAuth'], static function ($routes) {
    $routes->get('/', 'Publico\SocioPortalController::panel');
    $routes->get('prestamos', 'Publico\SocioPortalController::misPrestamos');
    $routes->post('reservar/(:num)', 'Publico\SocioPortalController::reservar/$1');
    $routes->post('renovar/(:num)', 'Publico\SocioPortalController::renovar/$1');
    $routes->post('sugerir', 'Publico\SocioPortalController::sugerirLibro');
});

// -----------------------------------------------------------------
// 3. PANEL DE ADMINISTRACIÓN — auth + módulos internos, todo bajo /admin
// -----------------------------------------------------------------
$routes->get('admin/login', 'Admin\AuthController::login');
$routes->post('admin/login', 'Admin\AuthController::autenticar');
$routes->get('admin/logout', 'Admin\AuthController::logout');

$routes->group('admin', ['filter' => 'adminAuth'], static function ($routes) {

    $routes->get('/', 'Admin\DashboardController::index');

    // Gestión del catálogo (libros)
    $routes->resource('libros', ['controller' => 'Admin\LibroController']);

    // Multimedia asociado a cada libro
    $routes->get('libros/(:num)/multimedia', 'Admin\LibroController::multimedia/$1');
    $routes->post('libros/(:num)/multimedia', 'Admin\LibroController::subirMultimedia/$1');
    $routes->post('multimedia/(:num)/eliminar', 'Admin\LibroController::eliminarMultimedia/$1');

    // Administración e inventario (ejemplares físicos)
    $routes->resource('ejemplares', ['controller' => 'Admin\EjemplarController']);
    $routes->post('ejemplares/(:num)/marcar-perdido', 'Admin\EjemplarController::marcarPerdido/$1');
    $routes->post('ejemplares/(:num)/marcar-danado', 'Admin\EjemplarController::marcarDanado/$1');
    $routes->get('inventario/reportes', 'Admin\EjemplarController::reportes');

    // Socios
    $routes->resource('socios', ['controller' => 'Admin\SocioController']);
    $routes->get('socios/(:num)/historial', 'Admin\SocioController::historial/$1');

    // Préstamos y devoluciones
    $routes->get('prestamos', 'Admin\PrestamoController::index');
    $routes->get('prestamos/nuevo', 'Admin\PrestamoController::nuevo');
    $routes->post('prestamos', 'Admin\PrestamoController::registrar');
    $routes->post('prestamos/(:num)/devolver', 'Admin\PrestamoController::devolver/$1');
    $routes->post('prestamos/(:num)/renovar', 'Admin\PrestamoController::renovar/$1');

    // Motor de reservas (sincrónico)
    $routes->get('reservas', 'Admin\ReservaController::index');
    $routes->post('reservas/(:num)/confirmar', 'Admin\ReservaController::confirmar/$1');
    $routes->post('reservas/(:num)/cancelar', 'Admin\ReservaController::cancelar/$1');
    $routes->post('reservas/(:num)/completar', 'Admin\ReservaController::completar/$1');

    // Notificaciones automatizadas
    $routes->get('notificaciones', 'Admin\NotificacionController::index');
    $routes->post('notificaciones/reenviar/(:num)', 'Admin\NotificacionController::reenviar/$1');
    $routes->get('notificaciones/configuracion', 'Admin\NotificacionController::configuracion');
    $routes->post('notificaciones/configuracion', 'Admin\NotificacionController::guardarConfiguracion');

    // Gestión de promociones
    $routes->resource('promociones', ['controller' => 'Admin\PromocionController']);

    // Roles / usuarios administradores
    $routes->resource('usuarios', ['controller' => 'Admin\UsuarioAdminController']);
});