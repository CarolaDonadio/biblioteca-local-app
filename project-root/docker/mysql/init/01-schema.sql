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

  
  