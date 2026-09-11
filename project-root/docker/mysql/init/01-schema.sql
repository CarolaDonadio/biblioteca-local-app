CREATE DATABASE IF NOT EXISTS biblioteca_virtual
;

USE biblioteca_virtual;

-- =====================================================
-- TABLA: usuarios
-- =====================================================


-- ---------------------------------------------------------
-- Módulo: Panel de Administración (usuarios y roles)
-- ---------------------------------------------------------

CREATE TABLE usuarios (
  dni INT NOT NULL,
  nombre_completo VARCHAR(150) NOT NULL,
  telefono VARCHAR(30),
  mail VARCHAR(150) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  perfil ENUM('socio','bibliotecario') NOT NULL DEFAULT 'bibliotecario',
  estado ENUM('activo', 'suspendido') NOT NULL DEFAULT 'activo',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
  ultimo_login DATETIME NULL ,

  PRIMARY KEY (dni),

  UNIQUE KEY uk_usuarios_mail (mail),

  CONSTRAINT chk_usuarios_dni
      CHECK (dni BETWEEN 1000000 AND 99999999)

);

-- =====================================================
-- TABLA: libros
-- =====================================================

CREATE TABLE libros (
  id INT NOT NULL AUTO_INCREMENT,
  isbn VARCHAR(20),
  titulo VARCHAR(255) NOT NULL,
  autor VARCHAR(150) NOT NULL,
  editorial VARCHAR(150),
  anio YEAR,
  categoria VARCHAR(100),
  cantidad INT NOT NULL DEFAULT 1,
  sinopsis TEXT,
  disponible BOOLEAN NOT NULL DEFAULT TRUE,

  PRIMARY KEY (id),

  UNIQUE KEY uk_libros_isbn (isbn),

  CONSTRAINT chk_libros_cantidad
      CHECK (cantidad >= 0)
);

-- =====================================================
-- TABLA: registros (préstamos)
-- =====================================================

CREATE TABLE registros (
  id INT NOT NULL AUTO_INCREMENT,
  idlibro INT NOT NULL,
  dniUsuario INT NOT NULL,
  fechaPrestamo DATE NOT NULL,
  fechaVence DATE NOT NULL,
  fechaDevolucion DATE NULL,

  PRIMARY KEY (id),

  CONSTRAINT fk_registros_libro
      FOREIGN KEY (idlibro)
      REFERENCES libros(id)
      ON UPDATE CASCADE
      ON DELETE RESTRICT,

  CONSTRAINT fk_registros_usuario
      FOREIGN KEY (dniUsuario)
      REFERENCES usuarios(dni)
      ON UPDATE CASCADE
      ON DELETE RESTRICT
);

-- =====================================================
-- TABLA: reservas
-- =====================================================

CREATE TABLE reservas (
  id INT NOT NULL AUTO_INCREMENT,
  libro_id INT NOT NULL,
  socio_id INT NOT NULL,
  fecha_solicitud DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  estado ENUM('pendiente', 'confirmada', 'cancelada', 'completada') NOT NULL DEFAULT 'pendiente',
  fecha_confirmacion DATETIME NULL,
  fecha_cancelacion DATETIME NULL,
  fecha_completada DATETIME NULL,
  procesada_por INT NULL,

  PRIMARY KEY (id),
  KEY idx_reservas_socio_libro_estado (socio_id, libro_id, estado),
  KEY idx_reservas_estado_fecha (estado, fecha_solicitud),

  CONSTRAINT fk_reservas_libro
      FOREIGN KEY (libro_id)
      REFERENCES libros(id)
      ON UPDATE CASCADE
      ON DELETE RESTRICT,

  CONSTRAINT fk_reservas_socio
      FOREIGN KEY (socio_id)
      REFERENCES usuarios(dni)
      ON UPDATE CASCADE
      ON DELETE RESTRICT,

  CONSTRAINT fk_reservas_procesada_por
      FOREIGN KEY (procesada_por)
      REFERENCES usuarios(dni)
      ON UPDATE CASCADE
      ON DELETE SET NULL
);

-- =====================================================
-- TABLA: pagos
-- =====================================================

CREATE TABLE pagos (
  id INT NOT NULL AUTO_INCREMENT,
  dniUsuario INT NOT NULL,
  fecha DATE NOT NULL,

  PRIMARY KEY (id),

  CONSTRAINT fk_pagos_usuario
      FOREIGN KEY (dniUsuario)
      REFERENCES usuarios(dni)
      ON UPDATE CASCADE
      ON DELETE RESTRICT
);

-- Usuario admin de ejemplo (password: admin123 -> reemplazar hash en producción)
INSERT INTO usuarios
(dni, nombre_completo, telefono, mail, password_hash, perfil, estado)
VALUES(31001001, 'Administrador', '2241001001', 'admin@biblioteca.local', '$2y$10$E8T7eUceDI3uy6YSUwfLeuVvfCBFFCiJ9Oe3pNPYi.t2by/BVGzoS',
 'bibliotecario', 'activo');
INSERT INTO usuarios
(dni, nombre_completo, telefono, mail, password_hash, perfil, estado)
VALUES(31001002, 'socio', '2241001001', 'socio@biblioteca.local', '$2y$10$E8T7eUceDI3uy6YSUwfLeuVvfCBFFCiJ9Oe3pNPYi.t2by/BVGzoS',
 'socio', 'activo');


-- =====================================================
-- INSERTAR LIBROS
-- =====================================================
USE biblioteca_virtual;

INSERT INTO libros (isbn, titulo, autor, editorial, anio, categoria, cantidad, sinopsis, disponible) VALUES
('9780307474728', 'Cien Años de Soledad', 'Gabriel García Márquez', 'Sudamericana', 1967, 'Novela', 5, 'La historia de la familia Buendía en el pueblo ficticio de Macondo.', TRUE),
('9780451524935', '1984', 'George Orwell', 'Secker & Warburg', 1949, 'Ciencia Ficción', 3, 'Una distopía clásica sobre la vigilancia estatal y la manipulación de la verdad.', TRUE),
('9780156012195', 'El Principito', 'Antoine de Saint-Exupéry', 'Reynal & Hitchcock', 1943, 'Infantil / Fábula', 4, 'Un cuento poético acompañado de ilustraciones hechas por el propio autor.', TRUE),
('9788437604947', 'Don Quijote de la Mancha', 'Miguel de Cervantes', 'Espasa-Calpe', 1605, 'Clásico', 2, 'Las aventuras de un hidalgo pobre que de tanto leer libros de caballerías se vuelve loco.', TRUE),
('9789500700122', 'Ficciones', 'Jorge Luis Borges', 'Sur', 1944, 'Cuentos', 3, 'Una colección de cuentos que explotan laberintos, bibliotecas e infinitos.', TRUE),
('9788420658827', 'Fahrenheit 451', 'Ray Bradbury', 'Ballantine Books', 1953, 'Ciencia Ficción', 0, 'Un futuro donde los libros están prohibidos y los bomberos se dedican a quemarlos.', FALSE),
('9788437600895', 'Rayuela', 'Julio Cortázar', 'Editorial Sudamericana', 1963, 'Novela', 2, 'Una contranovela que puede leerse de múltiples maneras y secuencias.', TRUE),
('9789505112111', 'El Aleph', 'Jorge Luis Borges', 'Losada', 1949, 'Cuentos', 1, 'Colección de relatos donde destaca el punto que contiene todos los puntos del universo.', TRUE);

CREATE TABLE promociones (
  id INT NOT NULL AUTO_INCREMENT,
  titulo VARCHAR(255) NOT NULL,
  descripcion TEXT,
  fecha_inicio DATE NOT NULL,
  fecha_fin DATE NOT NULL,
  imagen_url VARCHAR(255),
  condiciones TEXT,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id)
);