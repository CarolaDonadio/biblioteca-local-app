# Mi Biblioteca Virtual

Gestión y comunidad para bibliotecas locales. Proyecto modularizado según el
MVP definido en la planilla del equipo, sobre **CodeIgniter 4 (PHP) + MySQL**
en el backend y **HTML/CSS/JS** en el frontend.

## 1. Instalación

Este paquete contiene las carpetas `app/`, `public/assets/` y `database/` para
integrarlas en un esqueleto estándar de CodeIgniter 4.

```bash
# 1. Crear el esqueleto base de CI4 (trae vendor/, index.php, etc.)
composer create-project codeigniter4/appstarter biblioteca-virtual-ci4
cd biblioteca-virtual-ci4

# 2. Copiar/pisar las carpetas de este paquete sobre el esqueleto:
#    - app/            (Controllers, Models, Views, Config, Filters, Libraries)
#    - public/assets/   (css, js, img)
#    - database/schema.sql

# 3. Registrar los filtros de autenticación (ver app/Config/Filters.registro.txt)
#    Editar app/Config/Filters.php y agregar los alias 'adminAuth' y 'socioAuth'.

# 4. Variables de entorno
cp .env.example .env
# completar credenciales de MySQL y de los canales de notificación (Telegram/WhatsApp/Email)

# 5. Base de datos
mysql -u root -p < database/schema.sql

# 6. Levantar el servidor de desarrollo
php spark serve
```

Panel administrativo: `http://localhost:8080/admin/login`
(usuario de ejemplo en el schema: `admin@biblioteca.local` — actualizar el
hash de la contraseña antes de usarlo en un entorno real).

Portal público: `http://localhost:8080/`

## 2. Módulos (según el MVP de la planilla)

| Módulo | Rutas | Controladores | Modelos | Vistas |
|---|---|---|---|---|
| Panel de administración | `/admin/*` | `Admin\AuthController`, `Admin\DashboardController` | `UsuarioAdminModel` | `admin/auth`, `admin/dashboard` |
| Gestión del catálogo | `/admin/libros/*` | `Admin\LibroController` | `LibroModel` | `admin/libros/*` |
| Multimedia (PDF/audiolibro) | `/admin/libros/:id/multimedia` | `Admin\LibroController` | `MultimediaModel` | `admin/libros/multimedia` |
| Administración e inventario | `/admin/ejemplares/*` | `Admin\EjemplarController` | `EjemplarModel` | `admin/ejemplares/*` |
| Socios | `/admin/socios/*` | `Admin\SocioController` | `SocioModel` | `admin/socios/*` |
| Préstamos y devoluciones | `/admin/prestamos/*` | `Admin\PrestamoController` | `PrestamoModel` | `admin/prestamos/*` |
| Motor de reservas sincrónico | `/admin/reservas/*`, `/socio/panel/reservar/:id` | `Admin\ReservaController`, `Publico\SocioPortalController` | `ReservaModel` | `admin/reservas/index`, `publico/libro_detalle` |
| Notificaciones automatizadas | `/admin/notificaciones/*` | `Admin\NotificacionController` | `NotificacionModel` + `Libraries\NotificadorService` | `admin/notificaciones/*` |
| Promociones | `/admin/promociones/*`, `/promociones` | `Admin\PromocionController`, `Publico\PromocionPublicaController` | `PromocionModel` | `admin/promociones/*`, `publico/promociones` |
| Consulta pública | `/`, `/catalogo`, `/catalogo/buscar` | `Publico\CatalogoController` | `LibroModel` | `publico/catalogo`, `publico/libro_detalle` |
| Portal del socio | `/socio/*` | `Publico\SocioPortalController` | `SocioModel`, `PrestamoModel`, `SugerenciaModel` | `publico/socio_*` |

Cada módulo separa claramente su capa: **Controller (PHP/CodeIgniter)** →
**Model (PHP/MySQL)** → **View (HTML embebido con PHP)**, y comparte los
assets estáticos modulares en `public/assets/css/` y `public/assets/js/`
(por ejemplo `js/public/lazyload.js` para el Lazy Load, `js/public/catalogo.js`
para el buscador AJAX, `js/admin/panel.js` para confirmaciones del panel).

## 3. Decisiones clave de implementación

- **Motor de reservas sincrónico** (`ReservaModel::crearReserva`): usa una
  transacción de MySQL para calcular la posición en la cola y decidir el
  estado inicial de forma segura ante pedidos simultáneos, y dispara la
  notificación automática al socio inmediatamente.
- **Préstamos atómicos** (`PrestamoModel::registrarPrestamo` /
  `registrarDevolucion`): igual que las reservas, todo ocurre dentro de una
  transacción para que un ejemplar nunca quede prestado dos veces. Al
  devolver, si hay reservas en cola, el ejemplar pasa automáticamente a
  "reservado" y se notifica al siguiente socio.
- **Notificaciones multicanal**: `NotificacionModel` decide el canal
  (Telegram > WhatsApp > Email, según los datos cargados del socio) y delega
  el envío real a `Libraries\NotificadorService`, que aísla las llamadas a
  las APIs externas. Los tokens viven en `.env`, nunca en el código.
- **Lazy Load**: `public/assets/js/public/lazyload.js` usa
  `IntersectionObserver` para cargar portadas e imágenes de promociones solo
  cuando entran en pantalla, con fallback para navegadores viejos.
- **Seguridad de sesiones**: `AdminAuthFilter` expira la sesión administrativa
  tras 30 minutos de inactividad; `UsuarioAdminController` restringe la
  gestión de roles a usuarios `superadmin`.
- **Inventario**: cada ejemplar físico es una fila independiente
  (`ejemplares`), lo que permite registrar libros perdidos/dañados de forma
  granular sin afectar el resto de las copias del mismo título.

## 4. Backlog (fuera del MVP, ya contemplado en el esquema)

La tabla `sugerencias` y el modelo `SugerenciaModel` ya están armados para
cuando se aborde el backlog de recomendaciones y sugerencias de compra.
Reseñas, listas de deseos, importación CSV, carnet QR y pagos en línea quedan
pendientes de un futuro sprint, tal como se definió en la planilla.

## 5. Próximos pasos sugeridos

- Correr `php spark migrate` con Migrations de CI4 en vez de `schema.sql`
  directo, si el equipo prefiere versionar la base con el CLI de CodeIgniter.
- Agregar un cron (`php spark` command) que recorra `PrestamoModel::vencidos()`
  y dispare `NotificacionModel::notificarDevolucionProxima()` diariamente.
- Escribir tests con el framework de testing de CI4 para
  `ReservaModel::crearReserva` y `PrestamoModel::registrarPrestamo`, que son
  el corazón transaccional del sistema.
