-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 30-06-2025 a las 17:47:43
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `tetrisdb`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clasificacion`
--

CREATE TABLE usuarios (
  id_usuario INT(6) NOT NULL AUTO_INCREMENT,
  nombre_usuario VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  contraseña VARCHAR(255) NOT NULL,
  fecha_registro DATETIME NOT NULL,
  es_admin TINYINT(1) NOT NULL,
  usuario_activo TINYINT(1) NOT NULL,
  ubicacion VARCHAR(255) NOT NULL,
  PRIMARY KEY (id_usuario)
) ENGINE=InnoDB;

CREATE TABLE idioma (
  id_idioma INT(11) NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(50) NOT NULL,
  PRIMARY KEY (id_idioma)
) ENGINE=InnoDB;

CREATE TABLE configuracion (
  id_config INT(11) NOT NULL AUTO_INCREMENT,
  id_usuario INT(11) NOT NULL,
  id_idioma INT(11) NOT NULL,
  PRIMARY KEY (id_config),
  CONSTRAINT fk_config_usuario FOREIGN KEY (id_usuario)
    REFERENCES usuarios(id_usuario)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  CONSTRAINT fk_config_idioma FOREIGN KEY (id_idioma)
    REFERENCES idioma(id_idioma)
    ON UPDATE CASCADE
    ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE modojuego (
  id_modo INT(11) NOT NULL AUTO_INCREMENT,
  nombre_modo VARCHAR(50) NOT NULL,
  PRIMARY KEY (id_modo)
) ENGINE=InnoDB;

CREATE TABLE usuariomodojuego (
  id_usuario_modo INT(11) NOT NULL AUTO_INCREMENT,
  id_usuario INT(11) NOT NULL,
  id_modo INT(11) NOT NULL,
  PRIMARY KEY (id_usuario_modo),
  CONSTRAINT fk_usuariomodo_usuario FOREIGN KEY (id_usuario)
    REFERENCES usuarios(id_usuario)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  CONSTRAINT fk_usuariomodo_modo FOREIGN KEY (id_modo)
    REFERENCES modojuego(id_modo)
    ON UPDATE CASCADE
    ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE record (
  id_record INT(11) NOT NULL AUTO_INCREMENT,
  id_usuario INT(11) NOT NULL,
  fecha_jugada DATETIME NOT NULL,
  puntaje INT(11) NOT NULL,
  duracion INT(11) NOT NULL,
  nivel INT(11) NOT NULL,
  lineas INT(11) NOT NULL,
  id_modo INT(11) NOT NULL,
  PRIMARY KEY (id_record),
  CONSTRAINT fk_record_usuario FOREIGN KEY (id_usuario)
    REFERENCES usuarios(id_usuario)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  CONSTRAINT fk_record_modo FOREIGN KEY (id_modo)
    REFERENCES modojuego(id_modo)
    ON UPDATE CASCADE
    ON DELETE CASCADE
) ENGINE=InnoDB;

-- Insertar modos de juego básicos
INSERT INTO modojuego (nombre_modo) VALUES 
('Clásico'),
('Carrera'),
('Excavar'),
('Chill-Out');

-- Insertar idiomas básicos
INSERT INTO idioma (nombre) VALUES 
('Español'),
('Inglés'),
('Francés'); 