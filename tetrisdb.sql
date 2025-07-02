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

CREATE TABLE `clasificacion` (
  `id_ranking` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `puntaje_maximo` int(11) NOT NULL,
  `fecha_ultima_actualizacion` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion`
--

CREATE TABLE `configuracion` (
  `id_config` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_idioma` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `idioma`
--

CREATE TABLE `idioma` (
  `id_idioma` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modojuego`
--

CREATE TABLE `modojuego` (
  `id_modo` int(11) NOT NULL,
  `nombre_modo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `modojuego`
--

INSERT INTO `modojuego` (`id_modo`, `nombre_modo`) VALUES
(1, 0),
(2, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `record`
--

CREATE TABLE `record` (
  `id_record` int(11) NOT NULL,
  `fecha_jugada` datetime NOT NULL,
  `puntaje` int(11) NOT NULL,
  `duracion` int(11) NOT NULL,
  `id_modo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `record`
--

INSERT INTO `record` (`id_record`, `fecha_jugada`, `puntaje`, `duracion`, `id_modo`) VALUES
(1, '2025-06-30 12:45:02', 9500, 300, 1),
(2, '2025-06-30 12:45:02', 9200, 280, 1),
(3, '2025-06-30 12:45:02', 9000, 290, 1),
(4, '2025-06-30 12:45:02', 8800, 270, 1),
(5, '2025-06-30 12:45:02', 8600, 260, 1),
(6, '2025-06-30 12:45:02', 8500, 250, 1),
(7, '2025-06-30 12:45:02', 8300, 240, 1),
(8, '2025-06-30 12:45:02', 8100, 230, 1),
(9, '2025-06-30 12:45:02', 8000, 220, 1),
(10, '2025-06-30 12:45:02', 7900, 210, 1),
(11, '2025-06-30 12:45:02', 7500, 200, 1),
(12, '2025-06-30 12:45:02', 7000, 190, 1),
(13, '2025-06-30 12:45:02', 9600, 310, 1),
(14, '2025-06-30 12:45:02', 8900, 275, 1),
(15, '2025-06-30 12:45:02', 9100, 285, 2),
(16, '2025-06-30 12:45:02', 8200, 235, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuariomodojuego`
--

CREATE TABLE `usuariomodojuego` (
  `id_usuario_modo` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_modo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuariomodojuego`
--

INSERT INTO `usuariomodojuego` (`id_usuario_modo`, `id_usuario`, `id_modo`) VALUES
(2, 1, 1),
(3, 1, 2),
(4, 2, 1),
(5, 3, 1),
(6, 3, 2),
(7, 4, 1),
(8, 5, 1),
(9, 6, 1),
(10, 7, 1),
(11, 8, 1),
(12, 9, 1),
(13, 10, 1),
(14, 11, 1),
(15, 12, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(6) NOT NULL AUTO_INCREMENT,
  `nombre_usuario` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `contraseña` varchar(255) NOT NULL,
  `fecha_registro` datetime NOT NULL,
  `es_admin` tinyint(1) NOT NULL,
  `usuario_activo` tinyint(1) NOT NULL,
  `ubicacion` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre_usuario`, `email`, `contraseña`, `fecha_registro`, `es_admin`, `usuario_activo`, `ubicacion`) VALUES
(1, 'JugadorUno', 'jugador1@example.com', 'pass123', '2025-06-30 12:42:33', 0, 1, 'Ciudad A'),
(2, 'JugadorDos', 'jugador2@example.com', 'pass123', '2025-06-30 12:42:33', 0, 1, 'Ciudad B'),
(3, 'JugadorTres', 'jugador3@example.com', 'pass123', '2025-06-30 12:42:33', 0, 1, 'Ciudad C'),
(4, 'JugadorCuatro', 'jugador4@example.com', 'pass123', '2025-06-30 12:42:33', 0, 1, 'Ciudad D'),
(5, 'JugadorCinco', 'jugador5@example.com', 'pass123', '2025-06-30 12:42:33', 0, 1, 'Ciudad E'),
(6, 'JugadorSeis', 'jugador6@example.com', 'pass123', '2025-06-30 12:42:33', 0, 1, 'Ciudad F'),
(7, 'JugadorSiete', 'jugador7@example.com', 'pass123', '2025-06-30 12:42:33', 0, 1, 'Ciudad G'),
(8, 'JugadorOcho', 'jugador8@example.com', 'pass123', '2025-06-30 12:42:33', 0, 1, 'Ciudad H'),
(9, 'JugadorNueve', 'jugador9@example.com', 'pass123', '2025-06-30 12:42:33', 0, 1, 'Ciudad I'),
(10, 'JugadorDiez', 'jugador10@example.com', 'pass123', '2025-06-30 12:42:33', 0, 1, 'Ciudad J'),
(11, 'JugadorOnce', 'jugador11@example.com', 'pass123', '2025-06-30 12:42:33', 0, 1, 'Ciudad K'),
(12, 'JugadorDoce', 'jugador12@example.com', 'pass123', '2025-06-30 12:42:33', 0, 1, 'Ciudad L');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `clasificacion`
--
ALTER TABLE `clasificacion`
  ADD PRIMARY KEY (`id_ranking`);

--
-- Indices de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  ADD PRIMARY KEY (`id_config`);

--
-- Indices de la tabla `idioma`
--
ALTER TABLE `idioma`
  ADD PRIMARY KEY (`id_idioma`);

--
-- Indices de la tabla `usuariomodojuego`
--
ALTER TABLE `usuariomodojuego`
  ADD PRIMARY KEY (`id_usuario_modo`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  MODIFY `id_config` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `idioma`
--
ALTER TABLE `idioma`
  MODIFY `id_idioma` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuariomodojuego`
--
ALTER TABLE `usuariomodojuego`
  MODIFY `id_usuario_modo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
