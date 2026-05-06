-- ============================================================
-- StatRift — Datos de Prueba y Expansión
-- Ejecutar en: bd_final
-- ============================================================

-- ------------------------------------------------------------
-- 1. MÁS CAMPEONES (Nuevos IDs: 6 al 10)
-- ------------------------------------------------------------
INSERT INTO `campeon` (`id`, `nombre`, `q`, `w`, `e`, `r`, `foto`) VALUES
(6, 'Yasuo', 'Tempestad de Acero', 'Muro de Viento', 'Hoja Cortante', 'Último Aliento', 'https://ddragon.leagueoflegends.com/cdn/img/champion/splash/Yasuo_0.jpg'),
(7, 'Ahri', 'Orbe del Engaño', 'Fuego Zorruno', 'Hechizo', 'Impulso Espiritual', 'https://ddragon.leagueoflegends.com/cdn/img/champion/splash/Ahri_0.jpg'),
(8, 'Lee Sin', 'Onda Sónica', 'Salvaguarda', 'Tempestad', 'Ira del Dragón', 'https://ddragon.leagueoflegends.com/cdn/img/champion/splash/LeeSin_0.jpg'),
(9, 'Jinx', '¡Cambio de Armas!', '¡Zap!', '¡Mascafuegos!', '¡Supermegacohete Mortal!', 'https://ddragon.leagueoflegends.com/cdn/img/champion/splash/Jinx_0.jpg'),
(10, 'Thresh', 'Sentencia de Muerte', 'Camino Oscuro', 'Despellejar', 'La Caja', 'https://ddragon.leagueoflegends.com/cdn/img/champion/splash/Thresh_0.jpg');

-- ------------------------------------------------------------
-- 2. MÁS LIGAS (Nuevos IDs: 3 y 4)
-- ------------------------------------------------------------
INSERT INTO `liga` (`id`, `nombre`, `logo`, `id_equipos`) VALUES
(3, 'LCS', 'https://static.lolesports.com/leagues/1592516115322_LCS-01-FullonDark.png', NULL),
(4, 'LPL', 'https://static.lolesports.com/leagues/1592516149174_LPL-01-FullonDark.png', NULL);

-- ------------------------------------------------------------
-- 3. MÁS USUARIOS
-- ------------------------------------------------------------
INSERT INTO `jugador` (`id`, `nombre`, `apellidos`, `nick`, `pass`, `correo`, `tipo`, `activo`, `id_equipo_favorito`) VALUES
(6, 'Lee', 'Sang-hyeok', 'Faker', '$2y$10$w8k2rE2O/wQo/y7cZ0sSre9n8p.T.W0/tQ0Z5V/qP1mQeQ6kQ1mCq', 'faker@skt.com', 'Jugador Profesional', 1, 1),
(7, 'Rasmus', 'Winther', 'Caps', '$2y$10$w8k2rE2O/wQo/y7cZ0sSre9n8p.T.W0/tQ0Z5V/qP1mQeQ6kQ1mCq', 'caps@g2.com', 'Jugador Profesional', 1, 2),
(8, 'Enrique', 'Cedeño', 'xPeke', '$2y$10$w8k2rE2O/wQo/y7cZ0sSre9n8p.T.W0/tQ0Z5V/qP1mQeQ6kQ1mCq', 'xpeke@fnatic.com', 'Leyenda', 1, 4);
-- Nota: La contraseña para todos estos usuarios nuevos es "1234" cifrada con password_hash().

-- ------------------------------------------------------------
-- 4. PARTIDOS Y RESULTADOS (Para la sección de partidos)
-- ------------------------------------------------------------
-- Insertar los Partidos en la semana actual para que aparezcan en el Index
INSERT INTO `partido` (`id`, `fecha`) VALUES
(1, CURDATE()), -- Partido hoy
(2, DATE_ADD(CURDATE(), INTERVAL 1 DAY)), -- Partido mañana
(3, DATE_ADD(CURDATE(), INTERVAL 2 DAY)); -- Partido pasado mañana

-- Insertar los resultados en la tabla "juega" (2 equipos por partido)
INSERT INTO `juega` (`id_partido`, `id_equipo`, `resultado`) VALUES
(1, 1, 'Victoria'), -- SKT (equipo 1) gana
(1, 2, 'Derrota'),  -- G2 (equipo 2) pierde

(2, 3, 'Victoria'), -- Team Liquid (equipo 3) gana
(2, 4, 'Derrota'),  -- Fnatic (equipo 4) pierde

(3, 2, 'Victoria'), -- G2 gana
(3, 4, 'Derrota');  -- Fnatic pierde

-- ------------------------------------------------------------
-- 5. MÁS PUBLICACIONES PARA LA COMUNIDAD
-- ------------------------------------------------------------
INSERT INTO `publicaciones` (`id`, `titulo`, `cuerpo`, `fecha`, `hora`, `id_jugador`) VALUES
(4, 'El macro game en 2024', '<p>He estado analizando los últimos partidos de la LCK y es increíble cómo <strong>T1</strong> sigue dominando el control de oleadas. ¿Qué opináis de la nueva ruta de jungla?</p>', '2024-03-01', '10:30:00', 6),
(5, 'Guía de Thresh para Supports', '<p>Para todos los que quieran subir elo, os dejo un consejo básico: <em>No tiréis la linterna directamente encima del ADC</em>. Ponedla en el camino hacia donde tiene que huir.</p><ul><li>Usa la E antes de la Q.</li><li>Guarda la linterna para escapes.</li></ul>', '2024-03-05', '16:45:00', 4),
(6, 'Resultados de este fin de semana', '<h1>¡Menudos partidos!</h1><p>La victoria de G2 frente a Fnatic ha sido espectacular. Ese backdoor me ha recordado a mí en la season 3.</p>', '2024-03-10', '21:15:00', 8);

-- ------------------------------------------------------------
-- 6. COMENTARIOS PARA LAS NUEVAS PUBLICACIONES
-- ------------------------------------------------------------
INSERT INTO `comentario` (`id`, `cuerpo`, `fecha`, `hora`, `id_publicación`, `id_jugador`) VALUES
(1, 'Totalmente de acuerdo, el control de T1 es de otro planeta.', '2024-03-01', '11:00:00', 4, 7),
(2, '¡Qué gran guía! Me ha servido mucho para salir de plata.', '2024-03-06', '09:20:00', 5, 2),
(3, 'Tú sí que sabes de backdoors, maestro.', '2024-03-10', '22:00:00', 6, 7);
