# Plantilla para presentar la idea del proyecto

**Nombre provisorio del proyecto:**  
Mi Biblioteca Virtual - Gestión y Comunidad para Bibliotecas

---

## Integrantes
* **Rafael Morilla** (Desarrollador FullStack)
* **Maximiliano Lequio** (Desarrollador FullStack)
* **Leandro Diaz Rolón** (Desarrollador FullStack)
* **Carola Donadío** (Project Manager / Desarrolladora FullStack)
* **Francisco Affonso** (Desarrollador FullStack)

---

## Problema o necesidad
Las bibliotecas locales gestionan la información de libros, socios, préstamos, devoluciones, reservas y actividades de forma manual o mediante herramientas separadas. Los sistemas web existentes en el mercado presentan severos problemas técnicos y de usabilidad: altos tiempos de carga y lentitud general por falta de optimización multimedia, pérdida de notificaciones críticas de reservas que no llegan a la administración ni a los usuarios, gestión deficiente e insegura de accesos/roles administrativos, errores por falta de conexión a Internet (WiFi), falta de buscadores para material digital (PDFs/audiolibros), ausencia de canales automatizados de comunicación (notificaciones por mensajería), sugerencias integradas y carga manual de solicitudes de nuevos libros.

---

## Usuario/s afectados
* **Bibliotecarios y administradores:** Encargados de la gestión con accesos de administración seguros, recepción en tiempo real de reservas, gestión del inventario de la biblioteca.
* **Socios de la biblioteca:** Personas que consultan el catálogo, consumen recursos digitales (PDFs y audiolibros), sugieren compras.
* **Comunidad y ciudadanos:** Usuarios generales que consultan el catálogo público y acceden a novedades, servicios y actividades.

---

## Cómo se resuelve hoy
Se utilizan registros en papel, planillas de cálculo como Excel o sistemas web inestables y lentos, propensos a duplicar cobros, bloquear accesos administrativos y reportar desconexiones de red.

---

## Consecuencias o dificultades actuales
* Cobros erróneos a los socios (descuentos dobles o transacciones rechazadas sin motivo) que generan reclamos y desconfianza.
* Interrupción constante de tareas administrativas por bloqueos o fallas en la gestión de entradas y permisos de administradores.
* Pérdida de usuarios por lentitud extrema del sistema y alertas de "falta de WiFi".
* Pérdida de reservas solicitadas al no notificarse a la biblioteca y flujo de pago frustrante.
* Imposibilidad de buscar/consumir PDFs o audiolibros y sobrecarga de trabajo operativo por carga manual de solicitudes.

---

## Solución propuesta
Desarrollar **Mi Biblioteca Virtual**, una aplicación web centralizada, ágil y robusta para la gestión integral de bibliotecas locales. La plataforma ofrecerá un panel administrativo con autenticación segura y gestión precisa de roles, módulo de material digital (PDFs y audiolibros), técnicas de Lazy Load para la carga bajo demanda de elementos visuales (optimizando la velocidad), un sistema automatizado de notificaciones e integraciones (Telegram, WhatsApp o Email) para avisos de reservas, devoluciones y sugerencias, un módulo de promociones institucionales y catálogo comunal.

---

## MVP - funciones imprescindibles
* **Optimización visual con Lazy Load:** Carga diferida de portadas, imágenes y elementos multimedia a medida que el usuario se desplaza, garantizando máxima fluidez.
* **Sistema automatizado de notificaciones y avisos:** Envío de alertas automáticas de confirmación de reservas, recordatorios de devolución y avisos de sugerencias a través de Telegram, WhatsApp y Correo Electrónico (Mail).
* **Gestión de Promociones:** Módulo público e interno para publicar y difundir promociones, ofertas, campañas de asociación o beneficios especiales de la biblioteca.
* **Panel de Administración e Historial Centralizado:** Autenticación segura para administradores/bibliotecarios, control de roles y acceso exclusivo al historial de préstamos, devoluciones y actividad de los socios.
* **Gestión del catálogo multimedia:** Alta, baja y modificación de libros, incluyendo ISBN, título, autor, editorial, año, categoría, cantidad y sinopsis.
* **Motor de reservas sincrónico:** Recepción garantizada con alertas inmediatas al panel del bibliotecario y notificación al socio.
* **Consulta pública:** Buscador de libros y consulta de disponibilidad.
* **Administración e inventario:** Control de ejemplares disponibles, registro de libros perdidos o dañados y generación de reportes básicos.
* **Gestión de préstamos, devoluciones y socios:** Registro e inicio de sesión, consulta de libros en posesión, registro completo de fechas, gestión de reservas, renovaciones, alertas de vencimiento y perfil de socio.

---

## Funciones deseables / backlog futuro
* Módulo de cobranza e integración de pagos en línea: Pasarela de pagos integrada para abono de cuotas o multas en un solo paso (movido al backlog).
* Modelo de IA para recomendación de libros: Algoritmo inteligente de recomendación y sugerencias según historial de lectura.
* Importación masiva de datos mediante CSV/Excel.
* Sistema de reseñas, puntuaciones y listas de deseos.
* Carnet digital de socio con lector de códigos QR / códigos de barras.
* Agenda de eventos, talleres, chat interactivo y analíticas/estadísticas avanzadas.

---

## Datos principales que deberá manejar
* **Notificaciones e Integraciones:** Socio destinatario, canal de envío (Telegram ID, WhatsApp Phone, Email), tipo de mensaje (Reserva, Devolución, Sugerencia, Promoción), fecha/hora y estado de entrega.
* **Promociones:** Título de la promoción, descripción, fecha de vigencia, imagen promocional y condiciones.
* **Usuarios e Historial de Administración:** Credenciales encriptadas, permisos de roles, logs de acceso y consulta del historial detallado de cada socio (préstamos pasados, sanciones, libros leídos).
* **Libros y Recursos Digitales:** ISBN, título, autor, editorial, año, categoría, cantidad, portada, sinopsis, disponibilidad, PDF y audiolibro.
* **Reservas, Solicitudes de Adquisición y Socios:** Datos personales, estado del socio y registros de vigencia.

---

## Stack propuesto
HTML5, CSS3, JavaScript, PHP, CodeIgniter 4, MySQL, Git, GitHub y GitHub Issues.

* **HTML5, CSS3 y JavaScript:** Permitirán crear una interfaz liviana y rápida que elimine la lentitud del sistema anterior, gestione adecuadamente las peticiones asincrónicas (evitando alertas falsas de falta de WiFi) y ofrezca un flujo de pago fluido.
* **PHP y CodeIgniter 4:** Proporcionarán una lógica de backend segura bajo arquitectura MVC. Facilitarán la correcta gestión de autenticación/sesiones de administradores y el manejo de transacciones atómicas para que las reservas y pagos se procesen de forma exacta y no se dupliquen.
* **MySQL:** Permitirá optimizar consultas mediante índices y transacciones SQL, garantizando la velocidad de respuesta del sistema y la consistencia total en los registros financieros e inventarios.
* **Git, GitHub y GitHub Issues:** Facilitarán el control de versiones, trabajo en equipo y el seguimiento de pruebas técnicas enfocadas en usabilidad y corrección de errores.

---

## Riesgos o dudas iniciales
* **Integración de APIs de Mensajería:** Configurar y mantener las credenciales/costos operacionales de los servicios de envío masivo por WhatsApp, Telegram y Mail sin caer en bloqueos o filtros de SPAM.
* **Rendimiento con Lazy Load:** Asegurar que la carga diferida funcione de manera consistente en navegadores móviles antiguos o con conexiones débiles.
* **Seguridad en la privacidad del Historial:** Garantizar el cumplimiento normativo en la protección de datos personales al concentrar el historial de lectura de los usuarios en el panel de los administradores.
* **Seguridad en sesiones administrativas:** Definir políticas de expiración de token y control de acceso por roles para asegurar la estabilidad del panel de administración.
* **Reglas de negocio, alcance y mantenimiento:** Políticas de devolución, límites de préstamos digitales, soporte multi biblioteca y copias de seguridad de la base de datos.