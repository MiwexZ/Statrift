-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 06-05-2026 a las 20:34:49
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `build`
--

CREATE TABLE `build` (
  `id` int(11) NOT NULL,
  `id_campeon` int(11) NOT NULL,
  `nombre_build` varchar(100) DEFAULT 'Build Popular',
  `popularidad` int(11) DEFAULT 80,
  `primary_path` int(11) DEFAULT 8000,
  `secondary_path` int(11) DEFAULT 8100,
  `win_rate` decimal(4,1) DEFAULT 50.0,
  `total_matches` int(11) DEFAULT 10000
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `build`
--

INSERT INTO `build` (`id`, `id_campeon`, `nombre_build`, `popularidad`, `primary_path`, `secondary_path`, `win_rate`, `total_matches`) VALUES
(1, 1, 'ADC Control', 85, 8000, 8200, 50.8, 22150),
(2, 2, 'Bruiser Tanque', 78, 8000, 8400, 51.9, 18900),
(3, 3, 'Lethality Burst', 90, 8100, 8200, 53.4, 41200),
(4, 4, 'Burst AP', 82, 8200, 8300, 52.6, 19800),
(5, 5, 'Conquistador Full', 88, 8000, 8400, 54.2, 31500),
(6, 6, 'Crit Carry', 92, 8000, 8400, 51.3, 34875),
(7, 7, 'AP Burst', 84, 8100, 8200, 52.1, 28430);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `build_objeto`
--

CREATE TABLE `build_objeto` (
  `id_build` int(11) NOT NULL,
  `id_objeto` int(11) NOT NULL,
  `orden` int(11) DEFAULT 1,
  `fase` enum('starter','early','core','full') DEFAULT 'core'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `build_objeto`
--

INSERT INTO `build_objeto` (`id_build`, `id_objeto`, `orden`, `fase`) VALUES
(1, 4, 2, 'early'),
(1, 6, 4, 'core'),
(1, 11, 3, 'core'),
(1, 13, 5, 'core'),
(1, 16, 6, 'full'),
(1, 22, 1, 'starter'),
(2, 1, 1, 'starter'),
(2, 3, 5, 'full'),
(2, 6, 3, 'core'),
(2, 12, 2, 'early'),
(2, 25, 4, 'core'),
(3, 1, 1, 'starter'),
(3, 3, 6, 'full'),
(3, 8, 3, 'core'),
(3, 10, 4, 'core'),
(3, 20, 2, 'early'),
(3, 25, 5, 'full'),
(4, 5, 1, 'starter'),
(4, 9, 2, 'early'),
(4, 17, 5, 'full'),
(4, 18, 3, 'core'),
(4, 19, 4, 'core'),
(5, 1, 1, 'starter'),
(5, 3, 5, 'full'),
(5, 6, 3, 'core'),
(5, 12, 2, 'early'),
(5, 25, 4, 'core'),
(6, 1, 2, 'starter'),
(6, 3, 6, 'full'),
(6, 4, 5, 'core'),
(6, 6, 4, 'core'),
(6, 21, 3, 'early'),
(6, 22, 1, 'starter'),
(6, 25, 7, 'full'),
(7, 2, 6, 'full'),
(7, 5, 1, 'starter'),
(7, 9, 3, 'early'),
(7, 17, 7, 'full'),
(7, 18, 4, 'core'),
(7, 19, 5, 'core'),
(7, 22, 2, 'starter');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `build_runa`
--

CREATE TABLE `build_runa` (
  `id_build` int(11) NOT NULL,
  `id_runa` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `build_runa`
--

INSERT INTO `build_runa` (`id_build`, `id_runa`) VALUES
(1, 1),
(1, 9),
(1, 10),
(1, 11),
(1, 13),
(1, 14),
(2, 2),
(2, 9),
(2, 10),
(2, 11),
(2, 18),
(2, 19),
(3, 4),
(3, 12),
(3, 13),
(3, 15),
(3, 16),
(3, 17),
(4, 5),
(4, 12),
(4, 13),
(4, 14),
(4, 15),
(4, 16),
(5, 2),
(5, 9),
(5, 10),
(5, 11),
(5, 18),
(5, 20),
(6, 2),
(6, 9),
(6, 10),
(6, 11),
(6, 18),
(6, 19),
(7, 3),
(7, 12),
(7, 13),
(7, 15),
(7, 16),
(7, 17);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `campeon`
--

CREATE TABLE `campeon` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `rol` varchar(30) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `q` varchar(50) DEFAULT NULL,
  `w` varchar(50) DEFAULT NULL,
  `e` varchar(50) DEFAULT NULL,
  `r` varchar(50) DEFAULT NULL,
  `foto` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `campeon`
--

INSERT INTO `campeon` (`id`, `nombre`, `rol`, `descripcion`, `q`, `w`, `e`, `r`, `foto`) VALUES
(1, 'Ashe', 'ADC', 'Ashe es una tiradora de control con gran utilidad. Su pasivo ralentiza con cada ataque básico, lo que la convierte en una excelente kiter. Su R es una de las mejores iniciaciones globales del juego, ideal para picks a larga distancia. Se juega en la línea inferior como ADC.', 'Descarga de Flechas', 'Barrido de Hielo', 'Flecha Envenenada', 'Flecha de Cristal Enfriada', 'https://ddragon.leagueoflegends.com/cdn/img/champion/splash/Ashe_0.jpg'),
(2, 'Garen', 'Top', 'Garen es un guerrero cuerpo a cuerpo extremadamente resistente y fácil de aprender. Su kit se basa en entrar en combate, absorber daño con su W y ejecutar enemigos con poca vida usando su R. Es un pick ideal para la top lane con un estilo de juego agresivo y directo.', 'Golpe Decisivo', 'Valor', 'Girar', 'Justicia Demaciana', 'https://ddragon.leagueoflegends.com/cdn/img/champion/splash/Garen_0.jpg'),
(3, 'Zed', 'Mid', 'Zed es un asesino basado en daño físico con una curva de habilidad muy alta. Su mecánica de sombras le permite engañar, esquivar y eliminar objetivos prioritarios en fracciones de segundo. Domina la mid lane y rota para asesinar carries enemigos en las peleas de equipo.', 'Shuriken de Sombra', 'Viento Cortante', 'Sombra Viviente', 'Marca de la Muerte', 'https://ddragon.leagueoflegends.com/cdn/img/champion/splash/Zed_0.jpg'),
(4, 'Lux', 'Mid / Support', 'Lux es una maga de largo alcance con un kit centrado en el control de zona y el burst de daño mágico. Su combinación Q + E + R puede eliminar a un enemigo squish desde una distancia segura. También es popular como support por su escudo grupal (W) y su capacidad de poke.', 'Luz Final', 'Púa Luminosa', 'Atadura Lucente', 'Barrera Prismática', 'https://ddragon.leagueoflegends.com/cdn/img/champion/splash/Lux_0.jpg'),
(5, 'Darius', 'Top', 'Darius es un bruiser dominante en la top lane que se especializa en peleas prolongadas. Su pasivo de Hemorragia acumula stacks que amplifican enormemente el daño de su R (Guillotina Noxiana), que puede resetear con cada kill. Es el rey de los duelos 1v1 en las fases tempranas.', 'Golpe de Hacha', 'Cosecha', 'Aplicar Hemorragia', 'Guillotina Noxiana', 'https://ddragon.leagueoflegends.com/cdn/img/champion/splash/Darius_0.jpg'),
(6, 'Yasuo', 'Mid', 'Yasuo es un espadachín de alta movilidad que escala exponencialmente con los objetos críticos. Su pasivo le da el doble de probabilidad crítica. El Muro de Viento (W) bloquea todos los proyectiles, y su R (Último Aliento) se activa sobre enemigos en el aire, lo que le da sinergia con equipos de knockup.', 'Tempestad de Acero', 'Muro de Viento', 'Hoja Cortante', 'Último Aliento', 'https://ddragon.leagueoflegends.com/cdn/img/champion/splash/Yasuo_0.jpg'),
(7, 'Ahri', 'Mid', 'Ahri es una asesina/maga versátil con gran movilidad gracias a su R. Su kit le permite hacer trades seguros en la fase de líneas con la Q, y en mid-late game puede flanquear y eliminar carries gracias al charm (E) que encanta al enemigo obligándole a caminar hacia ella.', 'Orbe del Engaño', 'Fuego Zorruno', 'Hechizo', 'Impulso Espiritual', 'https://ddragon.leagueoflegends.com/cdn/img/champion/splash/Ahri_0.jpg'),
(8, 'Lee Sin', 'Jungla', 'Lee Sin es un guerrero de la jungla con una de las mecánicas más complejas del juego. Su Q le da movilidad ofensiva, su W defensiva (salvaguarda a aliados/wards), y su R (Ira del Dragón) es una patada que puede reposicionar enemigos hacia tu equipo. Es el jungler por excelencia para jugadores mecánicamente habilidosos.', 'Onda Sónica', 'Salvaguarda', 'Tempestad', 'Ira del Dragón', 'https://ddragon.leagueoflegends.com/cdn/img/champion/splash/LeeSin_0.jpg'),
(9, 'Jinx', 'ADC', 'Jinx es una hypercarry de rango largo cuyo daño escala monstruosamente en late game. Su pasivo le da velocidad de movimiento y ataque tras cada kill/asistencia, convirtiéndola en una máquina de limpiar teamfights. Su R es un misil global que ejecuta enemigos con poca vida en cualquier punto del mapa.', '¡Cambio de Armas!', '¡Zap!', '¡Mascafuegos!', '¡Supermegacohete Mortal!', 'https://ddragon.leagueoflegends.com/cdn/img/champion/splash/Jinx_0.jpg'),
(10, 'Thresh', 'Support', 'Thresh es un support tanque con uno de los kits más completos del juego. Su Q (gancho) inicia peleas, su W (linterna) salva aliados, su E (Despellejar) interrumpe dashes enemigos y su R (La Caja) crea una zona de control. Es el support definitivo para jugadores que quieren impactar el mapa.', 'Sentencia de Muerte', 'Camino Oscuro', 'Despellejar', 'La Caja', 'https://ddragon.leagueoflegends.com/cdn/img/champion/splash/Thresh_0.jpg');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `comentario`
--

INSERT INTO `comentario` (`id`, `cuerpo`, `fecha`, `hora`, `id_publicación`, `id_jugador`) VALUES
(1, 'Totalmente de acuerdo, el control de T1 es de otro planeta.', '2024-03-01', '11:00:00', 4, 7),
(2, '¡Qué gran guía! Me ha servido mucho para salir de plata.', '2024-03-06', '09:20:00', 5, 2),
(3, 'Tú sí que sabes de backdoors, maestro.', '2024-03-10', '22:00:00', 6, 7);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `juega`
--

INSERT INTO `juega` (`id_partido`, `id_equipo`, `resultado`) VALUES
(1, 1, 'Victoria'),
(1, 2, 'Derrota'),
(2, 3, 'Victoria'),
(2, 4, 'Derrota'),
(3, 2, 'Victoria'),
(3, 4, 'Derrota');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `jugador`
--

INSERT INTO `jugador` (`id`, `nombre`, `apellidos`, `nick`, `pass`, `correo`, `tipo`, `activo`, `id_equipo_favorito`) VALUES
(1, 'Juan', 'González', 'JuanG', '1234', 'juangonzalez@example.com', 'Jugador Profesional', 1, 4),
(2, 'Ana', 'Martínez', 'Anita', '1234', 'anamartinez@example.com', 'Jugador Amateur', 1, 1),
(3, 'John', 'Doe', 'johndoe', '1234', 'johndoe@example.com', 'Jungler', 1, 2),
(4, 'Jane', 'Smith', 'janesmith', '1234', 'janesmith@example.com', 'Support', 1, 1),
(5, 'Michael', 'Johnson', 'mjohnson', '1234', 'mjohnson@example.com', 'Mid Laner', 1, 3),
(6, 'Lee', 'Sang-hyeok', 'Faker', '1234', 'faker@skt.com', 'Jugador Profesional', 1, 1),
(7, 'Rasmus', 'Winther', 'Caps', '1234', 'caps@g2.com', 'Jugador Profesional', 1, 2),
(8, 'Enrique', 'Cedeño', 'xPeke', '1234', 'xpeke@fnatic.com', 'Leyenda', 1, 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `liga`
--

CREATE TABLE `liga` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `logo` varchar(200) DEFAULT NULL,
  `id_equipos` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `liga`
--

INSERT INTO `liga` (`id`, `nombre`, `logo`, `id_equipos`) VALUES
(1, 'LEC', 'https://static.lolesports.com/leagues/1592516184297_LEC-01-FullonDark.png', NULL),
(2, 'LCK', 'https://static.lolesports.com/leagues/lck-color-on-black.png', NULL),
(3, 'LCS', 'https://static.lolesports.com/leagues/1592516115322_LCS-01-FullonDark.png', NULL),
(4, 'LPL', 'https://static.lolesports.com/leagues/1592516149174_LPL-01-FullonDark.png', NULL);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `nombre` varchar(50) DEFAULT NULL,
  `riot_id` varchar(10) DEFAULT NULL,
  `foto` varchar(300) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `objeto`
--

INSERT INTO `objeto` (`id`, `nombre`, `riot_id`, `foto`) VALUES
(1, 'Espada Larga', '1036', 'https://ddragon.leagueoflegends.com/cdn/14.9.1/img/item/1036.png'),
(2, 'Vara de la Edad', '3118', 'https://ddragon.leagueoflegends.com/cdn/14.9.1/img/item/3118.png'),
(3, 'Botas de Movilidad', '3117', 'https://ddragon.leagueoflegends.com/cdn/14.9.1/img/item/3117.png'),
(4, 'Arco Recurvo', '1043', 'https://ddragon.leagueoflegends.com/cdn/14.9.1/img/item/1043.png'),
(5, 'Libro de la Sabiduría', '1052', 'https://ddragon.leagueoflegends.com/cdn/14.9.1/img/item/1052.png'),
(6, 'Filo del Infinito', '3031', NULL),
(7, 'Diente de Nashor', '3115', NULL),
(8, 'Eclipse', '6692', NULL),
(9, 'Tormenta de Luden', '3285', NULL),
(10, 'Filo de la Noche', '3814', NULL),
(11, 'Matarracimos', '3085', NULL),
(12, 'Piedra del Ocaso', '6630', NULL),
(13, 'Cañón de Fuego Rápido', '3094', NULL),
(14, 'Redencion', '3107', NULL),
(15, 'Locket de la Solari de Hierro', '3190', NULL),
(16, 'Botas del Berserker', '3006', NULL),
(17, 'Zapatos del Hechicero', '3020', NULL),
(18, 'Gorra de la Muerte', '3089', NULL),
(19, 'Reloj de Arena de Zhonya', '3157', NULL),
(20, 'Youmuu Ghostblade', '3142', NULL),
(21, 'Escudo Inmortal', '6673', NULL),
(22, 'Pocion de Vida', '2003', NULL),
(23, 'Hacha Corta', '1029', NULL),
(24, 'Kraken', '6672', NULL),
(25, 'Destrozador de Mortales', '3033', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `partido`
--

CREATE TABLE `partido` (
  `id` int(11) NOT NULL,
  `fecha` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `partido`
--

INSERT INTO `partido` (`id`, `fecha`) VALUES
(1, '2026-05-06'),
(2, '2026-05-07'),
(3, '2026-05-08');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `publicaciones`
--

INSERT INTO `publicaciones` (`id`, `titulo`, `cuerpo`, `fecha`, `hora`, `id_jugador`) VALUES
(1, 'Campeonato mundial de League of Legends', 'El campeonato mundial de League of Legends es un torneo anual que reúne a los mejores equipos del mundo para competir por el título de campeón mundial. La edición de este año se llevará a cabo en China.', '2023-10-15', '14:00:00', 2),
(2, 'Nuevo personaje: Gwen', 'Gwen es la última campeona agregada a League of Legends. Es una espadachina y costará 7800 puntos de influencia. Se lanzará en la próxima actualización del juego.', '2023-05-30', '18:00:00', 1),
(3, 'Nuevo conjunto de aspectos de campeonato', 'Se han lanzado nuevos aspectos de campeonato para varios campeones populares de League of Legends. Estos aspectos solo están disponibles durante el campeonato mundial y cuestan 1350 puntos de Riot.', '2023-09-28', '09:30:00', 2),
(4, 'El macro game en 2024', '<p>He estado analizando los últimos partidos de la LCK y es increíble cómo <strong>T1</strong> sigue dominando el control de oleadas. ¿Qué opináis de la nueva ruta de jungla?</p>', '2024-03-01', '10:30:00', 6),
(5, 'Guía de Thresh para Supports', '<p>Para todos los que quieran subir elo, os dejo un consejo básico: <em>No tiréis la linterna directamente encima del ADC</em>. Ponedla en el camino hacia donde tiene que huir.</p><ul><li>Usa la E antes de la Q.</li><li>Guarda la linterna para escapes.</li></ul>', '2024-03-05', '16:45:00', 4),
(6, 'Resultados de este fin de semana', '<h1>¡Menudos partidos!</h1><p>La victoria de G2 frente a Fnatic ha sido espectacular. Ese backdoor me ha recordado a mí en la season 3.</p>', '2024-03-10', '21:15:00', 8);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reporte_comentario`
--

CREATE TABLE `reporte_comentario` (
  `id` int(11) NOT NULL,
  `fecha` date DEFAULT NULL,
  `estado` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reporte_jugador`
--

CREATE TABLE `reporte_jugador` (
  `id` int(11) NOT NULL,
  `fecha` date DEFAULT NULL,
  `estado` varchar(20) DEFAULT NULL,
  `id_jugador` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `runa`
--

CREATE TABLE `runa` (
  `id` int(11) NOT NULL,
  `nombre` varchar(80) NOT NULL,
  `riot_id` int(11) DEFAULT NULL,
  `tipo` enum('Primaria','Secundaria') DEFAULT 'Primaria',
  `icono` varchar(300) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `runa`
--

INSERT INTO `runa` (`id`, `nombre`, `riot_id`, `tipo`, `icono`) VALUES
(1, 'Lluvia de Cuchillas', 8008, 'Primaria', 'https://ddragon.leagueoflegends.com/cdn/img/perk-images/Styles/Precision/LethalTempo/LethalTempoTemp.png'),
(2, 'Conquistador', 8010, 'Primaria', 'https://ddragon.leagueoflegends.com/cdn/img/perk-images/Styles/Precision/Conqueror/Conqueror.png'),
(3, 'Electrocutar', 8112, 'Primaria', 'https://ddragon.leagueoflegends.com/cdn/img/perk-images/Styles/Domination/Electrocute/Electrocute.png'),
(4, 'Cosecha Oscura', 8128, 'Primaria', 'https://ddragon.leagueoflegends.com/cdn/img/perk-images/Styles/Domination/DarkHarvest/DarkHarvest.png'),
(5, 'Cometa Arcano', 8229, 'Primaria', 'https://ddragon.leagueoflegends.com/cdn/img/perk-images/Styles/Sorcery/ArcaneComet/ArcaneComet.png'),
(6, 'Guardián', 8465, 'Primaria', 'https://ddragon.leagueoflegends.com/cdn/img/perk-images/Styles/Resolve/Guardian/Guardian.png'),
(7, 'Golpe de Escudo', NULL, 'Secundaria', 'https://ddragon.leagueoflegends.com/cdn/img/perk-images/Styles/Inspiration/GlacialAugment/GlacialAugment.png'),
(8, 'Empuñadura Afilada', NULL, 'Secundaria', 'https://ddragon.leagueoflegends.com/cdn/img/perk-images/Styles/Domination/SuddenImpact/SuddenImpact.png'),
(9, 'Triunfo', 9111, 'Secundaria', NULL),
(10, 'Leyenda: Presencia', 9104, 'Secundaria', NULL),
(11, 'Golpe de Gracia', 8014, 'Secundaria', NULL),
(12, 'Banda de Maná', 8226, 'Secundaria', NULL),
(13, 'Trascendencia', 8210, 'Secundaria', NULL),
(14, 'Abrasar', 8237, 'Secundaria', NULL),
(15, 'Mordedura Barata', 8126, 'Secundaria', NULL),
(16, 'Coleccionista de Ojos', 8138, 'Secundaria', NULL),
(17, 'Cazador Relentless', 8105, 'Secundaria', NULL),
(18, 'Revestimiento Óseo', 8473, 'Secundaria', NULL),
(19, 'Segundo Aire', 8444, 'Secundaria', NULL),
(20, 'Crecimiento', 8451, 'Secundaria', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sigue`
--

CREATE TABLE `sigue` (
  `id_jugador` int(11) NOT NULL,
  `id_seguido` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Indices de la tabla `build`
--
ALTER TABLE `build`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `build_objeto`
--
ALTER TABLE `build_objeto`
  ADD PRIMARY KEY (`id_build`,`id_objeto`);

--
-- Indices de la tabla `build_runa`
--
ALTER TABLE `build_runa`
  ADD PRIMARY KEY (`id_build`,`id_runa`);

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
-- Indices de la tabla `runa`
--
ALTER TABLE `runa`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `build`
--
ALTER TABLE `build`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `runa`
--
ALTER TABLE `runa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

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
