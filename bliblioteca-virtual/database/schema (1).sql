-- =========================================================
-- Mi Biblioteca Virtual - Esquema de base de datos (MySQL 8+)
-- =========================================================
CREATE DATABASE IF NOT EXISTS biblioteca_virtual
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE biblioteca_virtual;

-- ---------------------------------------------------------
-- Módulo: Panel de Administración (usuarios y roles)
-- ---------------------------------------------------------
CREATE TABLE usuarios_admin (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(120) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  rol ENUM('superadmin','bibliotecario') NOT NULL DEFAULT 'bibliotecario',
  activo TINYINT(1) NOT NULL DEFAULT 1,
  ultimo_login DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE logs_acceso (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  usuario_admin_id INT UNSIGNED NOT NULL,
  accion VARCHAR(150) NOT NULL,
  ip VARCHAR(45) NULL,
  fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_admin_id) REFERENCES usuarios_admin(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Módulo: Gestión de catálogo (libros)
-- ---------------------------------------------------------
CREATE TABLE libros (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  isbn VARCHAR(20) NOT NULL UNIQUE,
  titulo VARCHAR(250) NOT NULL,
  autor VARCHAR(150) NOT NULL,
  editorial VARCHAR(150) NULL,
  anio SMALLINT UNSIGNED NULL,
  categoria VARCHAR(100) NULL,
  sinopsis TEXT NULL,
  portada_url VARCHAR(255) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_titulo (titulo),
  INDEX idx_autor (autor),
  INDEX idx_categoria (categoria)
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Módulo: Administración e inventario (ejemplares físicos)
-- ---------------------------------------------------------
CREATE TABLE ejemplares (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  libro_id INT UNSIGNED NOT NULL,
  codigo_inventario VARCHAR(40) NOT NULL UNIQUE,
  estado ENUM('disponible','prestado','reservado','perdido','danado','baja') NOT NULL DEFAULT 'disponible',
  ubicacion VARCHAR(100) NULL,
  observaciones TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (libro_id) REFERENCES libros(id) ON DELETE CASCADE,
  INDEX idx_estado (estado)
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Módulo: Multimedia (PDF / audiolibros)
-- ---------------------------------------------------------
CREATE TABLE multimedia (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  libro_id INT UNSIGNED NOT NULL,
  tipo ENUM('pdf','audiolibro') NOT NULL,
  archivo_url VARCHAR(255) NOT NULL,
  tamano_kb INT UNSIGNED NULL,
  duracion_seg INT UNSIGNED NULL COMMENT 'solo audiolibros',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (libro_id) REFERENCES libros(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Módulo: Socios
-- ---------------------------------------------------------
CREATE TABLE socios (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(120) NOT NULL,
  apellido VARCHAR(120) NOT NULL,
  dni VARCHAR(20) NOT NULL UNIQUE,
  email VARCHAR(150) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  telefono VARCHAR(30) NULL,
  telegram_chat_id VARCHAR(60) NULL,
  whatsapp_numero VARCHAR(30) NULL,
  estado ENUM('activo','suspendido','vencido') NOT NULL DEFAULT 'activo',
  fecha_alta DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_estado_socio (estado)
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Módulo: Préstamos y devoluciones
-- ---------------------------------------------------------
CREATE TABLE prestamos (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ejemplar_id INT UNSIGNED NOT NULL,
  socio_id INT UNSIGNED NOT NULL,
  admin_id INT UNSIGNED NULL COMMENT 'quien registró el préstamo',
  fecha_prestamo DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  fecha_vencimiento DATE NOT NULL,
  fecha_devolucion DATETIME NULL,
  estado ENUM('activo','devuelto','vencido','perdido') NOT NULL DEFAULT 'activo',
  renovaciones TINYINT UNSIGNED NOT NULL DEFAULT 0,
  FOREIGN KEY (ejemplar_id) REFERENCES ejemplares(id),
  FOREIGN KEY (socio_id) REFERENCES socios(id),
  FOREIGN KEY (admin_id) REFERENCES usuarios_admin(id),
  INDEX idx_estado_prestamo (estado),
  INDEX idx_vencimiento (fecha_vencimiento)
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Módulo: Motor de reservas (sincrónico, con cola)
-- ---------------------------------------------------------
CREATE TABLE reservas (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  libro_id INT UNSIGNED NOT NULL,
  socio_id INT UNSIGNED NOT NULL,
  fecha_reserva DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  posicion_cola INT UNSIGNED NOT NULL DEFAULT 1,
  estado ENUM('pendiente','disponible_para_retiro','completada','cancelada','vencida') NOT NULL DEFAULT 'pendiente',
  fecha_limite_retiro DATETIME NULL,
  FOREIGN KEY (libro_id) REFERENCES libros(id),
  FOREIGN KEY (socio_id) REFERENCES socios(id),
  INDEX idx_libro_estado (libro_id, estado),
  UNIQUE KEY uniq_reserva_activa (libro_id, socio_id, estado)
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Módulo: Notificaciones automatizadas (Telegram/WhatsApp/Email)
-- ---------------------------------------------------------
CREATE TABLE notificaciones (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  socio_id INT UNSIGNED NOT NULL,
  canal ENUM('telegram','whatsapp','email') NOT NULL,
  tipo ENUM('reserva','devolucion','sugerencia','promocion','vencimiento') NOT NULL,
  mensaje TEXT NOT NULL,
  referencia_id INT UNSIGNED NULL COMMENT 'id de reserva/prestamo relacionado',
  estado_entrega ENUM('pendiente','enviado','fallido') NOT NULL DEFAULT 'pendiente',
  fecha_envio DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (socio_id) REFERENCES socios(id) ON DELETE CASCADE,
  INDEX idx_estado_envio (estado_entrega)
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Módulo: Promociones institucionales
-- ---------------------------------------------------------
CREATE TABLE promociones (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  titulo VARCHAR(200) NOT NULL,
  descripcion TEXT NULL,
  imagen_url VARCHAR(255) NULL,
  fecha_inicio DATE NOT NULL,
  fecha_fin DATE NOT NULL,
  activo TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Backlog ya contemplado en el esquema: sugerencias de compra
-- ---------------------------------------------------------
CREATE TABLE sugerencias (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  socio_id INT UNSIGNED NOT NULL,
  titulo_sugerido VARCHAR(250) NOT NULL,
  autor_sugerido VARCHAR(150) NULL,
  comentario TEXT NULL,
  estado ENUM('pendiente','en_evaluacion','aprobada','rechazada') NOT NULL DEFAULT 'pendiente',
  fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (socio_id) REFERENCES socios(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Usuario admin de ejemplo (password: admin123 -> reemplazar hash en producción)
INSERT INTO usuarios_admin (nombre, email, password_hash, rol)
VALUES ('Administrador General', 'admin@biblioteca.local', '$2y$10$92IXUNpkjO0rOQ5byMi.YeIA0Bp6R3xW2eD7g1z1z1z1z1z1z1z1u', 'superadmin');
