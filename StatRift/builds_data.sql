-- ============================================================
-- StatRift — Datos de Builds, Runas y Descripciones Técnicas
-- Ejecutar en: bd_final (phpMyAdmin > SQL)
-- ============================================================

-- 1. Ampliar la tabla campeon con descripción técnica y rol
ALTER TABLE `campeon` 
ADD COLUMN `rol` VARCHAR(30) DEFAULT NULL AFTER `nombre`,
ADD COLUMN `descripcion` TEXT DEFAULT NULL AFTER `rol`;

-- 2. Tabla de runas
CREATE TABLE IF NOT EXISTS `runa` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `nombre` VARCHAR(80) NOT NULL,
    `tipo` ENUM('Primaria','Secundaria') DEFAULT 'Primaria',
    `icono` VARCHAR(300) DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Tabla de builds (vincula campeón con objetos y runas)
CREATE TABLE IF NOT EXISTS `build` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `id_campeon` INT(11) NOT NULL,
    `nombre_build` VARCHAR(100) DEFAULT 'Build Popular',
    `popularidad` INT DEFAULT 80,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `build_objeto` (
    `id_build` INT(11) NOT NULL,
    `id_objeto` INT(11) NOT NULL,
    `orden` INT(11) DEFAULT 1,
    PRIMARY KEY (`id_build`, `id_objeto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `build_runa` (
    `id_build` INT(11) NOT NULL,
    `id_runa` INT(11) NOT NULL,
    PRIMARY KEY (`id_build`, `id_runa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- 4. RELLENAR DATOS
-- ============================================================

-- 4a. Actualizar descripciones técnicas de los campeones existentes
UPDATE campeon SET rol = 'ADC', descripcion = 'Ashe es una tiradora de control con gran utilidad. Su pasivo ralentiza con cada ataque básico, lo que la convierte en una excelente kiter. Su R es una de las mejores iniciaciones globales del juego, ideal para picks a larga distancia. Se juega en la línea inferior como ADC.' WHERE id = 1;
UPDATE campeon SET rol = 'Top', descripcion = 'Garen es un guerrero cuerpo a cuerpo extremadamente resistente y fácil de aprender. Su kit se basa en entrar en combate, absorber daño con su W y ejecutar enemigos con poca vida usando su R. Es un pick ideal para la top lane con un estilo de juego agresivo y directo.' WHERE id = 2;
UPDATE campeon SET rol = 'Mid', descripcion = 'Zed es un asesino basado en daño físico con una curva de habilidad muy alta. Su mecánica de sombras le permite engañar, esquivar y eliminar objetivos prioritarios en fracciones de segundo. Domina la mid lane y rota para asesinar carries enemigos en las peleas de equipo.' WHERE id = 3;
UPDATE campeon SET rol = 'Mid / Support', descripcion = 'Lux es una maga de largo alcance con un kit centrado en el control de zona y el burst de daño mágico. Su combinación Q + E + R puede eliminar a un enemigo squish desde una distancia segura. También es popular como support por su escudo grupal (W) y su capacidad de poke.' WHERE id = 4;
UPDATE campeon SET rol = 'Top', descripcion = 'Darius es un bruiser dominante en la top lane que se especializa en peleas prolongadas. Su pasivo de Hemorragia acumula stacks que amplifican enormemente el daño de su R (Guillotina Noxiana), que puede resetear con cada kill. Es el rey de los duelos 1v1 en las fases tempranas.' WHERE id = 5;
UPDATE campeon SET rol = 'Mid', descripcion = 'Yasuo es un espadachín de alta movilidad que escala exponencialmente con los objetos críticos. Su pasivo le da el doble de probabilidad crítica. El Muro de Viento (W) bloquea todos los proyectiles, y su R (Último Aliento) se activa sobre enemigos en el aire, lo que le da sinergia con equipos de knockup.' WHERE id = 6;
UPDATE campeon SET rol = 'Mid', descripcion = 'Ahri es una asesina/maga versátil con gran movilidad gracias a su R. Su kit le permite hacer trades seguros en la fase de líneas con la Q, y en mid-late game puede flanquear y eliminar carries gracias al charm (E) que encanta al enemigo obligándole a caminar hacia ella.' WHERE id = 7;
UPDATE campeon SET rol = 'Jungla', descripcion = 'Lee Sin es un guerrero de la jungla con una de las mecánicas más complejas del juego. Su Q le da movilidad ofensiva, su W defensiva (salvaguarda a aliados/wards), y su R (Ira del Dragón) es una patada que puede reposicionar enemigos hacia tu equipo. Es el jungler por excelencia para jugadores mecánicamente habilidosos.' WHERE id = 8;
UPDATE campeon SET rol = 'ADC', descripcion = 'Jinx es una hypercarry de rango largo cuyo daño escala monstruosamente en late game. Su pasivo le da velocidad de movimiento y ataque tras cada kill/asistencia, convirtiéndola en una máquina de limpiar teamfights. Su R es un misil global que ejecuta enemigos con poca vida en cualquier punto del mapa.' WHERE id = 9;
UPDATE campeon SET rol = 'Support', descripcion = 'Thresh es un support tanque con uno de los kits más completos del juego. Su Q (gancho) inicia peleas, su W (linterna) salva aliados, su E (Despellejar) interrumpe dashes enemigos y su R (La Caja) crea una zona de control. Es el support definitivo para jugadores que quieren impactar el mapa.' WHERE id = 10;

-- 4b. Insertar objetos adicionales con iconos del CDN oficial
INSERT INTO `objeto` (`id`, `nombre`) VALUES
(6, 'Filo del Infinito'),
(7, 'Diente de Nashor'),
(8, 'Eclipse'),
(9, 'Tormenta de Luden'),
(10, 'Filo de la Noche'),
(11, 'Matarracimos'),
(12, 'Piedra del Ocaso'),
(13, 'Cañón de Fuego Rápido'),
(14, 'Redencion'),
(15, 'Locket de la Solari de Hierro');

-- 4c. Insertar runas
INSERT INTO `runa` (`id`, `nombre`, `tipo`, `icono`) VALUES
(1, 'Lluvia de Cuchillas', 'Primaria', 'https://ddragon.leagueoflegends.com/cdn/img/perk-images/Styles/Precision/LethalTempo/LethalTempoTemp.png'),
(2, 'Conquistador', 'Primaria', 'https://ddragon.leagueoflegends.com/cdn/img/perk-images/Styles/Precision/Conqueror/Conqueror.png'),
(3, 'Electrocutar', 'Primaria', 'https://ddragon.leagueoflegends.com/cdn/img/perk-images/Styles/Domination/Electrocute/Electrocute.png'),
(4, 'Cosecha Oscura', 'Primaria', 'https://ddragon.leagueoflegends.com/cdn/img/perk-images/Styles/Domination/DarkHarvest/DarkHarvest.png'),
(5, 'Cometa Arcano', 'Primaria', 'https://ddragon.leagueoflegends.com/cdn/img/perk-images/Styles/Sorcery/ArcaneComet/ArcaneComet.png'),
(6, 'Guardián', 'Primaria', 'https://ddragon.leagueoflegends.com/cdn/img/perk-images/Styles/Resolve/Guardian/Guardian.png'),
(7, 'Golpe de Escudo', 'Secundaria', 'https://ddragon.leagueoflegends.com/cdn/img/perk-images/Styles/Inspiration/GlacialAugment/GlacialAugment.png'),
(8, 'Empuñadura Afilada', 'Secundaria', 'https://ddragon.leagueoflegends.com/cdn/img/perk-images/Styles/Domination/SuddenImpact/SuddenImpact.png');

-- 4d. Crear builds populares para cada campeón
-- Ashe (ADC)
INSERT INTO build (id, id_campeon, nombre_build, popularidad) VALUES (1, 1, 'ADC Crítico', 85);
INSERT INTO build_objeto VALUES (1, 4, 1), (1, 13, 2), (1, 6, 3);
INSERT INTO build_runa VALUES (1, 1), (1, 8);

-- Garen (Top)
INSERT INTO build (id, id_campeon, nombre_build, popularidad) VALUES (2, 2, 'Bruiser Tanque', 78);
INSERT INTO build_objeto VALUES (2, 1, 1), (2, 3, 2), (2, 12, 3);
INSERT INTO build_runa VALUES (2, 2), (2, 7);

-- Zed (Mid Asesino)
INSERT INTO build (id, id_campeon, nombre_build, popularidad) VALUES (3, 3, 'Lethality Burst', 90);
INSERT INTO build_objeto VALUES (3, 8, 1), (3, 10, 2), (3, 1, 3);
INSERT INTO build_runa VALUES (3, 3), (3, 8);

-- Lux (Mid/Support Maga)
INSERT INTO build (id, id_campeon, nombre_build, popularidad) VALUES (4, 4, 'Burst AP', 82);
INSERT INTO build_objeto VALUES (4, 9, 1), (4, 2, 2), (4, 5, 3);
INSERT INTO build_runa VALUES (4, 5), (4, 7);

-- Darius (Top Bruiser)
INSERT INTO build (id, id_campeon, nombre_build, popularidad) VALUES (5, 5, 'Conquistador Full', 88);
INSERT INTO build_objeto VALUES (5, 1, 1), (5, 12, 2), (5, 3, 3);
INSERT INTO build_runa VALUES (5, 2), (5, 8);

-- Yasuo (Mid Crit)
INSERT INTO build (id, id_campeon, nombre_build, popularidad) VALUES (6, 6, 'Crit Carry', 92);
INSERT INTO build_objeto VALUES (6, 6, 1), (6, 4, 2), (6, 3, 3);
INSERT INTO build_runa VALUES (6, 1), (6, 8);

-- Ahri (Mid Maga)
INSERT INTO build (id, id_campeon, nombre_build, popularidad) VALUES (7, 7, 'AP Burst', 84);
INSERT INTO build_objeto VALUES (7, 9, 1), (7, 7, 2), (7, 5, 3);
INSERT INTO build_runa VALUES (7, 3), (7, 7);

-- Lee Sin (Jungla)
INSERT INTO build (id, id_campeon, nombre_build, popularidad) VALUES (8, 8, 'Bruiser Jungla', 86);
INSERT INTO build_objeto VALUES (8, 8, 1), (8, 1, 2), (8, 3, 3);
INSERT INTO build_runa VALUES (8, 2), (8, 8);

-- Jinx (ADC Hypercarry)
INSERT INTO build (id, id_campeon, nombre_build, popularidad) VALUES (9, 9, 'Hypercarry Crit', 91);
INSERT INTO build_objeto VALUES (9, 4, 1), (9, 6, 2), (9, 13, 3);
INSERT INTO build_runa VALUES (9, 1), (9, 7);

-- Thresh (Support)
INSERT INTO build (id, id_campeon, nombre_build, popularidad) VALUES (10, 10, 'Support Tanque', 80);
INSERT INTO build_objeto VALUES (10, 15, 1), (10, 14, 2), (10, 3, 3);
INSERT INTO build_runa VALUES (10, 6), (10, 7);
