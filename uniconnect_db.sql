-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3307
-- Tiempo de generación: 14-05-2026 a las 18:17:23
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
-- Base de datos: `uniconnect_db`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_actualizar_actividad` (IN `p_id_actividad` INT, IN `p_titulo` VARCHAR(100), IN `p_tipo` VARCHAR(50), IN `p_descripcion` TEXT, IN `p_fecha` VARCHAR(30), IN `p_id_club` INT, IN `p_id_usuario` INT)   BEGIN
    UPDATE actividades 
    SET titulo = p_titulo,
        tipo_actividad = p_tipo,
        descripcion_actividad = p_descripcion,
        fecha_evento = p_fecha,
        id_club = p_id_club
    WHERE id_actividad = p_id_actividad 
      AND id_usuario = p_id_usuario;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_buscar_actividades` (IN `p_termino` VARCHAR(100))   BEGIN
    SELECT a.*, c.nombre_club, u.nombre AS nombre_creador
    FROM actividades a
    INNER JOIN clubes c ON a.id_club = c.id_club
    LEFT JOIN usuarios u ON a.id_usuario = u.id_usuario
    WHERE a.titulo LIKE CONCAT('%', p_termino, '%')
       OR a.descripcion_actividad LIKE CONCAT('%', p_termino, '%')
       OR a.tipo_actividad LIKE CONCAT('%', p_termino, '%')
       OR c.nombre_club LIKE CONCAT('%', p_termino, '%')
    ORDER BY a.fecha_evento ASC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_crear_actividad` (IN `p_titulo` VARCHAR(100), IN `p_tipo` VARCHAR(50), IN `p_descripcion` TEXT, IN `p_fecha` VARCHAR(30), IN `p_id_club` INT, IN `p_id_usuario` INT)   BEGIN
    INSERT INTO actividades (titulo, tipo_actividad, descripcion_actividad, fecha_evento, id_club, id_usuario)
    VALUES (p_titulo, p_tipo, p_descripcion, p_fecha, p_id_club, p_id_usuario);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_eliminar_actividad` (IN `p_id_actividad` INT, IN `p_id_usuario` INT)   BEGIN
    DELETE FROM actividades 
    WHERE id_actividad = p_id_actividad 
      AND id_usuario = p_id_usuario;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_registrar_usuario` (IN `p_nombre` VARCHAR(100), IN `p_email` VARCHAR(150), IN `p_password_hash` VARCHAR(255))   BEGIN
    INSERT INTO usuarios (nombre, email, password_hash)
    VALUES (p_nombre, p_email, p_password_hash);
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `actividades`
--

CREATE TABLE `actividades` (
  `id_actividad` int(11) NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `tipo_actividad` varchar(50) DEFAULT NULL,
  `descripcion_actividad` text DEFAULT NULL,
  `fecha_evento` datetime NOT NULL,
  `id_club` int(11) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `actividades`
--

INSERT INTO `actividades` (`id_actividad`, `titulo`, `tipo_actividad`, `descripcion_actividad`, `fecha_evento`, `id_club`, `id_usuario`) VALUES
(1, 'Taller de Arduino Uno', 'Taller', 'Introducción práctica a la programación de microcontroladores para principiantes.', '2026-04-10 10:00:00', 1, NULL),
(2, 'Torneo de Debate Interuniversitario', 'Competencia', 'Encuentro de oratoria sobre el impacto de la ética en la Inteligencia Artificial.', '2026-04-12 15:30:00', 2, NULL),
(3, 'Gran Torneo de Valorant', 'Evento Social', 'Competencia interna en equipos con premios para los tres primeros lugares.', '2026-04-15 18:00:00', 3, NULL),
(4, 'Análisis de \"Cien años de soledad\"', 'Reunión', 'Charla abierta sobre el realismo mágico y la importancia de García Márquez.', '2026-04-18 11:00:00', 4, NULL),
(5, 'Simulacro de Programación ICPC', 'Entrenamiento', 'Sesión de 4 horas resolviendo problemas de lógica y optimización de código.', '2026-04-25 09:00:00', 5, NULL),
(18, 'Cafe literario', 'Taller', 'Hablaremos sobre el libro de La Granja', '2026-10-20 10:20:00', 4, NULL),
(19, 'Hackathon', 'Torneo', 'El tema será sobre el medio ambiente', '2026-06-18 10:30:00', 5, NULL);

--
-- Disparadores `actividades`
--
DELIMITER $$
CREATE TRIGGER `tr_log_actividad_eliminada` BEFORE DELETE ON `actividades` FOR EACH ROW BEGIN
    INSERT INTO log_auditoria (tabla_afectada, accion, detalle)
    VALUES ('actividades', 'DELETE', 
            CONCAT('Actividad eliminada: "', OLD.titulo, '" (ID: ', OLD.id_actividad, ', Club ID: ', OLD.id_club, ')'));
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clubes`
--

CREATE TABLE `clubes` (
  `id_club` int(11) NOT NULL,
  `nombre_club` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `categoria` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `clubes`
--

INSERT INTO `clubes` (`id_club`, `nombre_club`, `descripcion`, `categoria`) VALUES
(1, 'Club de Robótica y Automatización', 'Diseño y construcción de prototipos autónomos y drones de competición.', 'Tecnología'),
(2, 'Sociedad de Oratoria y Debate', 'Espacio para desarrollar habilidades de argumentación y hablar en público con fluidez.', 'Académico'),
(3, 'Gamer Guild Uni', 'Comunidad dedicada a los deportes electrónicos (eSports) y organización de torneos internos.', 'Recreativo'),
(4, 'Círculo de Literatura Hispana', 'Análisis y discusión de obras literarias clásicas y contemporáneas en español.', 'Cultural'),
(5, 'Peña de Programación Competitiva', 'Entrenamiento intensivo en algoritmos y estructuras de datos para concursos internacionales.', 'Tecnología');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `log_auditoria`
--

CREATE TABLE `log_auditoria` (
  `id_log` int(11) NOT NULL,
  `tabla_afectada` varchar(50) NOT NULL,
  `accion` varchar(20) NOT NULL,
  `detalle` text DEFAULT NULL,
  `fecha` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `log_auditoria`
--

INSERT INTO `log_auditoria` (`id_log`, `tabla_afectada`, `accion`, `detalle`, `fecha`) VALUES
(1, 'actividades', 'DELETE', 'Actividad eliminada: \"dada\" (ID: 24, Club ID: 4)', '2026-05-11 17:22:14'),
(2, 'usuarios', 'INSERT', 'Nuevo usuario registrado: Usuario Prueba (prueba_rubrica@test.com)', '2026-05-11 17:29:48'),
(3, 'actividades', 'DELETE', 'Actividad eliminada: \"Reunion Editada con Exito\" (ID: 25, Club ID: 4)', '2026-05-11 17:39:56'),
(4, 'usuarios', 'INSERT', 'Nuevo usuario registrado: Carla Perez (bethgoca@gmail.com)', '2026-05-11 22:37:45'),
(5, 'actividades', 'DELETE', 'Actividad eliminada: \"Lectura de Orgullo y Prejuicio\" (ID: 26, Club ID: 4)', '2026-05-11 22:48:10');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `miembros_club`
--

CREATE TABLE `miembros_club` (
  `id_miembro` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_club` int(11) NOT NULL,
  `fecha_union` datetime DEFAULT current_timestamp(),
  `rol` varchar(30) DEFAULT 'miembro'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `miembros_club`
--

INSERT INTO `miembros_club` (`id_miembro`, `id_usuario`, `id_club`, `fecha_union`, `rol`) VALUES
(2, 1, 4, '2026-05-11 13:11:24', 'miembro'),
(3, 1, 1, '2026-05-11 13:13:19', 'miembro'),
(4, 2, 4, '2026-05-11 13:38:50', 'miembro'),
(5, 2, 3, '2026-05-11 13:39:51', 'miembro'),
(6, 3, 4, '2026-05-11 17:34:10', 'miembro'),
(7, 4, 4, '2026-05-11 22:42:08', 'miembro');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `email`, `password_hash`, `fecha_registro`) VALUES
(1, 'Elizabeth Gomez', 'elizabeth@test.com', '$2y$10$V6Ei13NQypnEdp9vffCmcu7t5kn3w3wXJm/AYM7SDK8YWuNJNQqIq', '2026-05-11 13:05:06'),
(2, 'Pedro Perez', '1@gmail.com', '$2y$10$JEzni2soaftVoLZnx7eRqu1ym8/GVqIquj1LXPOiQOdZ3Pn.F3mvu', '2026-05-11 13:37:04'),
(3, 'Usuario Prueba', 'prueba_rubrica@test.com', '$2y$10$/yspB0cbrgEyM8GSmbBnXeGwa2jRejJXLX757usb2RFu7AYSPfUWG', '2026-05-11 17:29:48'),
(4, 'Carla Perez', 'bethgoca@gmail.com', '$2y$10$XmRw7jDqbRWcs9DSPI61HuTj0bxm7cZy0SL0q5LEoIGpuq24Emw9y', '2026-05-11 22:37:45');

--
-- Disparadores `usuarios`
--
DELIMITER $$
CREATE TRIGGER `tr_log_nuevo_usuario` AFTER INSERT ON `usuarios` FOR EACH ROW BEGIN
    INSERT INTO log_auditoria (tabla_afectada, accion, detalle)
    VALUES ('usuarios', 'INSERT', 
            CONCAT('Nuevo usuario registrado: ', NEW.nombre, ' (', NEW.email, ')'));
END
$$
DELIMITER ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `actividades`
--
ALTER TABLE `actividades`
  ADD PRIMARY KEY (`id_actividad`),
  ADD KEY `id_club` (`id_club`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `clubes`
--
ALTER TABLE `clubes`
  ADD PRIMARY KEY (`id_club`);

--
-- Indices de la tabla `log_auditoria`
--
ALTER TABLE `log_auditoria`
  ADD PRIMARY KEY (`id_log`);

--
-- Indices de la tabla `miembros_club`
--
ALTER TABLE `miembros_club`
  ADD PRIMARY KEY (`id_miembro`),
  ADD UNIQUE KEY `unique_membresia` (`id_usuario`,`id_club`),
  ADD KEY `id_club` (`id_club`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `actividades`
--
ALTER TABLE `actividades`
  MODIFY `id_actividad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de la tabla `clubes`
--
ALTER TABLE `clubes`
  MODIFY `id_club` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `log_auditoria`
--
ALTER TABLE `log_auditoria`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `miembros_club`
--
ALTER TABLE `miembros_club`
  MODIFY `id_miembro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `actividades`
--
ALTER TABLE `actividades`
  ADD CONSTRAINT `actividades_ibfk_1` FOREIGN KEY (`id_club`) REFERENCES `clubes` (`id_club`) ON DELETE CASCADE,
  ADD CONSTRAINT `actividades_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE SET NULL;

--
-- Filtros para la tabla `miembros_club`
--
ALTER TABLE `miembros_club`
  ADD CONSTRAINT `miembros_club_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `miembros_club_ibfk_2` FOREIGN KEY (`id_club`) REFERENCES `clubes` (`id_club`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
