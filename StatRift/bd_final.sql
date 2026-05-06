-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 30-05-2023 a las 17:53:03
-- Versión del servidor: 10.4.25-MariaDB
-- Versión de PHP: 7.4.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `bd_final`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bloqueo`
--

CREATE TABLE `bloqueo` (
  `id` int(11) NOT NULL,
  `f_ini` date DEFAULT NULL,
  `f_fin` date DEFAULT NULL,
  `id_jugador` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `campeon`
--

CREATE TABLE `campeon` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `q` varchar(50) DEFAULT NULL,
  `w` varchar(50) DEFAULT NULL,
  `e` varchar(50) DEFAULT NULL,
  `r` varchar(50) DEFAULT NULL,
  `foto` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `campeon`
--

INSERT INTO `campeon` (`id`, `nombre`, `q`, `w`, `e`, `r`, `foto`) VALUES
(1, 'Ashe', 'Descarga de Flechas', 'Barrido de Hielo', 'Flecha Envenenada', 'Flecha de Cristal Enfriada', 'ruta/de/la/foto/de/Ashe.jpg'),
(2, 'Garen', 'Golpe Decisivo', 'Valor', 'Girar', 'Justicia Demaciana', 'ruta/de/la/foto/de/Garen.jpg'),
(3, 'Zed', 'Shuriken de Sombra', 'Viento Cortante', 'Sombra Viviente', 'Marca de la Muerte', 'ruta/de/la/foto/de/Zed.jpg'),
(4, 'Lux', 'Luz Final', 'Púa Luminosa', 'Atadura Lucente', 'Barrera Prismática', 'ruta/de/la/foto/de/Lux.jpg'),
(5, 'Darius', 'Golpe de Hacha', 'Cosecha', 'Aplicar Hemorragia', 'Guillotina Noxiana', 'ruta/de/la/foto/de/Darius.jpg');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comentario`
--

CREATE TABLE `comentario` (
  `id` int(11) NOT NULL,
  `cuerpo` text DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `id_publicación` int(11) DEFAULT NULL,
  `id_jugador` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `equipo`
--

CREATE TABLE `equipo` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `Jugadores` varchar(100) DEFAULT NULL,
  `Logo` varchar(200) DEFAULT NULL,
  `id_liga` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `equipo`
--

INSERT INTO `equipo` (`id`, `nombre`, `Jugadores`, `Logo`, `id_liga`) VALUES
(1, 'SK Telecom T1', 'Faker, Teddy, Effort, Clid, Roach', 'skt_logo.png', 1),
(2, 'G2 Esports', 'Caps, Rekkles, Jankos, Wunder, Mikyx', 'g2_logo.png', 1),
(3, 'Team Liquid', 'Jensen, Tactical, Alphari, CoreJJ, Santorin', 'tl_logo.png', 2),
(4, 'Fnatic', 'Bwipo, Upset, Selfmade, Hylissang, Nisqy', 'fnatic_logo.png', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `juega`
--

CREATE TABLE `juega` (
  `id_partido` int(11) NOT NULL,
  `id_equipo` int(11) NOT NULL,
  `resultado` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jugador`
--

CREATE TABLE `jugador` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `apellidos` varchar(50) DEFAULT NULL,
  `nick` varchar(20) DEFAULT NULL,
  `pass` varchar(50) NOT NULL,
  `correo` varchar(50) DEFAULT NULL,
  `tipo` varchar(20) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT NULL,
  `id_equipo_favorito` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `jugador`
--

INSERT INTO `jugador` (`id`, `nombre`, `apellidos`, `nick`, `pass`, `correo`, `tipo`, `activo`, `id_equipo_favorito`) VALUES
(1, 'Juan', 'González', 'JuanG', '', 'juangonzalez@example.com', 'Jugador Profesional', 1, 2),
(2, 'Ana', 'Martínez', 'Anita', '', 'anamartinez@example.com', 'Jugador Amateur', 1, 1),
(3, 'John', 'Doe', 'johndoe', '', 'johndoe@example.com', 'Jungler', 1, 2),
(4, 'Jane', 'Smith', 'janesmith', '', 'janesmith@example.com', 'Support', 1, 1),
(5, 'Michael', 'Johnson', 'mjohnson', '', 'mjohnson@example.com', 'Mid Laner', 1, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `liga`
--

CREATE TABLE `liga` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `logo` varchar(200) DEFAULT NULL,
  `id_equipos` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `liga`
--

INSERT INTO `liga` (`id`, `nombre`, `logo`, `id_equipos`) VALUES
(1, 'LEC', NULL, NULL),
(2, 'LCK', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `noticia`
--

CREATE TABLE `noticia` (
  `id` int(11) NOT NULL,
  `titulo` varchar(100) DEFAULT NULL,
  `cuerpo` text DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `hashtags` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `noticia`
--

INSERT INTO `noticia` (`id`, `titulo`, `cuerpo`, `fecha`, `hashtags`) VALUES
(1, 'Nuevo Campeón Revelado: Viego, el Rey Arruinado', 'Riot Games ha revelado su nuevo campeón, Viego, el Rey Arruinado. Con una habilidad pasiva que le permite poseer a los enemigos que mata, Viego promete ser un campeón emocionante y desafiante.', '2021-01-08', '#Viego #NuevoCampeón #LeagueOfLegends'),
(2, 'Riot anuncia un nuevo modo de juego: Lucha en la Grieta', 'Riot Games ha anunciado un nuevo modo de juego llamado Lucha en la Grieta. Este modo se centrará en combates en equipo y estará disponible por tiempo limitado.', '2021-02-15', '#LuchaEnLaGrieta #NuevoModoDeJuego #LeagueOfLegends'),
(3, 'League of Legends se expande a dispositivos móviles', 'Riot Games ha anunciado que League of Legends llegará a dispositivos móviles en 2021. El juego será gratuito para descargar y jugar, y contará con todos los campeones y modos de juego de la versión de PC.', '2021-03-23', '#LeagueOfLegendsMobile #JuegosMóviles #Noticias'),
(4, 'Finales Mundiales de League of Legends 2021 se celebrarán en Islandia', 'Riot Games ha anunciado que las Finales Mundiales de League of Legends 2021 se celebrarán en Islandia. Debido a la pandemia de COVID-19, el evento se llevará a cabo sin audiencia en vivo.', '2021-09-01', '#FinalesMundiales #LeagueOfLegends #Islandia'),
(5, 'Riot anuncia cambios en la clasificación de League of Legends', 'Riot Games ha anunciado cambios en el sistema de clasificación de League of Legends para la temporada 12. Estos cambios incluyen ajustes en el MMR, restricciones de duo queue en algunas ligas y nuevas recompensas para los jugadores.', '2022-01-20', '#SistemaDeClasificación #Temporada12 #LeagueOfLegends');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `objeto`
--

CREATE TABLE `objeto` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `objeto`
--

INSERT INTO `objeto` (`id`, `nombre`) VALUES
(1, 'Espada Larga'),
(2, 'Vara de la Edad'),
(3, 'Botas de Movilidad'),
(4, 'Arco Recurvo'),
(5, 'Libro de la Sabiduría');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `partido`
--

CREATE TABLE `partido` (
  `id` int(11) NOT NULL,
  `fecha` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `publicaciones`
--

CREATE TABLE `publicaciones` (
  `id` int(11) NOT NULL,
  `titulo` varchar(100) DEFAULT NULL,
  `cuerpo` text DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `id_jugador` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `publicaciones`
--

INSERT INTO `publicaciones` (`id`, `titulo`, `cuerpo`, `fecha`, `hora`, `id_jugador`) VALUES
(1, 'Campeonato mundial de League of Legends', 'El campeonato mundial de League of Legends es un torneo anual que reúne a los mejores equipos del mundo para competir por el título de campeón mundial. La edición de este año se llevará a cabo en China.', '2023-10-15', '14:00:00', 2),
(2, 'Nuevo personaje: Gwen', 'Gwen es la última campeona agregada a League of Legends. Es una espadachina y costará 7800 puntos de influencia. Se lanzará en la próxima actualización del juego.', '2023-05-30', '18:00:00', 1),
(3, 'Nuevo conjunto de aspectos de campeonato', 'Se han lanzado nuevos aspectos de campeonato para varios campeones populares de League of Legends. Estos aspectos solo están disponibles durante el campeonato mundial y cuestan 1350 puntos de Riot.', '2023-09-28', '09:30:00', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reporte_comentario`
--

CREATE TABLE `reporte_comentario` (
  `id` int(11) NOT NULL,
  `fecha` date DEFAULT NULL,
  `estado` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reporte_jugador`
--

CREATE TABLE `reporte_jugador` (
  `id` int(11) NOT NULL,
  `fecha` date DEFAULT NULL,
  `estado` varchar(20) DEFAULT NULL,
  `id_jugador` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sigue`
--

CREATE TABLE `sigue` (
  `id_jugador` int(11) NOT NULL,
  `id_seguido` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `sigue`
--

INSERT INTO `sigue` (`id_jugador`, `id_seguido`) VALUES
(2, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `video`
--

CREATE TABLE `video` (
  `id` int(11) NOT NULL,
  `url` varchar(200) DEFAULT NULL,
  `id_jugador` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `video`
--

INSERT INTO `video` (`id`, `url`, `id_jugador`) VALUES
(1, 'https://www.youtube.com/watch?v=4zzibYbYz10', 1),
(2, 'https://www.youtube.com/watch?v=moFZzPZXXcA', 2);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `bloqueo`
--
ALTER TABLE `bloqueo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_jugador` (`id_jugador`);

--
-- Indices de la tabla `campeon`
--
ALTER TABLE `campeon`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `comentario`
--
ALTER TABLE `comentario`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_publicación` (`id_publicación`),
  ADD KEY `id_jugador` (`id_jugador`);

--
-- Indices de la tabla `equipo`
--
ALTER TABLE `equipo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_liga` (`id_liga`);

--
-- Indices de la tabla `juega`
--
ALTER TABLE `juega`
  ADD PRIMARY KEY (`id_partido`,`id_equipo`),
  ADD KEY `id_equipo` (`id_equipo`);

--
-- Indices de la tabla `jugador`
--
ALTER TABLE `jugador`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_idjugador_idequipo` (`id_equipo_favorito`);

--
-- Indices de la tabla `liga`
--
ALTER TABLE `liga`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_equipos` (`id_equipos`);

--
-- Indices de la tabla `noticia`
--
ALTER TABLE `noticia`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `objeto`
--
ALTER TABLE `objeto`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `partido`
--
ALTER TABLE `partido`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `publicaciones`
--
ALTER TABLE `publicaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_jugador` (`id_jugador`);

--
-- Indices de la tabla `reporte_comentario`
--
ALTER TABLE `reporte_comentario`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `reporte_jugador`
--
ALTER TABLE `reporte_jugador`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_jugador` (`id_jugador`);

--
-- Indices de la tabla `sigue`
--
ALTER TABLE `sigue`
  ADD PRIMARY KEY (`id_jugador`,`id_seguido`),
  ADD KEY `id_seguido` (`id_seguido`);

--
-- Indices de la tabla `video`
--
ALTER TABLE `video`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_jugador` (`id_jugador`);

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `bloqueo`
--
ALTER TABLE `bloqueo`
  ADD CONSTRAINT `bloqueo_ibfk_1` FOREIGN KEY (`id_jugador`) REFERENCES `jugador` (`id`);

--
-- Filtros para la tabla `comentario`
--
ALTER TABLE `comentario`
  ADD CONSTRAINT `comentario_ibfk_1` FOREIGN KEY (`id_publicación`) REFERENCES `publicaciones` (`id`),
  ADD CONSTRAINT `comentario_ibfk_2` FOREIGN KEY (`id_jugador`) REFERENCES `jugador` (`id`);

--
-- Filtros para la tabla `equipo`
--
ALTER TABLE `equipo`
  ADD CONSTRAINT `equipo_ibfk_1` FOREIGN KEY (`id_liga`) REFERENCES `liga` (`id`);

--
-- Filtros para la tabla `juega`
--
ALTER TABLE `juega`
  ADD CONSTRAINT `juega_ibfk_1` FOREIGN KEY (`id_partido`) REFERENCES `partido` (`id`),
  ADD CONSTRAINT `juega_ibfk_2` FOREIGN KEY (`id_equipo`) REFERENCES `equipo` (`id`);

--
-- Filtros para la tabla `jugador`
--
ALTER TABLE `jugador`
  ADD CONSTRAINT `fk_idjugador_idequipo` FOREIGN KEY (`id_equipo_favorito`) REFERENCES `equipo` (`id`);

--
-- Filtros para la tabla `liga`
--
ALTER TABLE `liga`
  ADD CONSTRAINT `liga_ibfk_1` FOREIGN KEY (`id_equipos`) REFERENCES `equipo` (`id`);

--
-- Filtros para la tabla `publicaciones`
--
ALTER TABLE `publicaciones`
  ADD CONSTRAINT `publicaciones_ibfk_1` FOREIGN KEY (`id_jugador`) REFERENCES `jugador` (`id`);

--
-- Filtros para la tabla `reporte_jugador`
--
ALTER TABLE `reporte_jugador`
  ADD CONSTRAINT `reporte_jugador_ibfk_1` FOREIGN KEY (`id_jugador`) REFERENCES `jugador` (`id`);

--
-- Filtros para la tabla `sigue`
--
ALTER TABLE `sigue`
  ADD CONSTRAINT `sigue_ibfk_1` FOREIGN KEY (`id_jugador`) REFERENCES `jugador` (`id`),
  ADD CONSTRAINT `sigue_ibfk_2` FOREIGN KEY (`id_seguido`) REFERENCES `jugador` (`id`);

--
-- Filtros para la tabla `video`
--
ALTER TABLE `video`
  ADD CONSTRAINT `video_ibfk_1` FOREIGN KEY (`id_jugador`) REFERENCES `jugador` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
