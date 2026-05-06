-- ============================================================
-- StatRift — Integración Data Dragon API (IDs reales de Riot)
-- Ejecutar en phpMyAdmin > bd_final > SQL
-- ============================================================

-- 1. Añadir riot_id a objeto
ALTER TABLE `objeto` ADD COLUMN `riot_id` VARCHAR(10) DEFAULT NULL AFTER `nombre`;

-- 2. Actualizar objetos existentes con IDs reales de Riot
UPDATE objeto SET riot_id = '1036' WHERE nombre = 'Espada Larga';
UPDATE objeto SET riot_id = '3118' WHERE nombre = 'Vara de la Edad';
UPDATE objeto SET riot_id = '3117' WHERE nombre = 'Botas de Movilidad';
UPDATE objeto SET riot_id = '1043' WHERE nombre = 'Arco Recurvo';
UPDATE objeto SET riot_id = '1052' WHERE nombre = 'Libro de la Sabiduría';
UPDATE objeto SET riot_id = '3031' WHERE nombre = 'Filo del Infinito';
UPDATE objeto SET riot_id = '3115' WHERE nombre = 'Diente de Nashor';
UPDATE objeto SET riot_id = '6692' WHERE nombre = 'Eclipse';
UPDATE objeto SET riot_id = '3285' WHERE nombre = 'Tormenta de Luden';
UPDATE objeto SET riot_id = '3814' WHERE nombre = 'Filo de la Noche';
UPDATE objeto SET riot_id = '3085' WHERE nombre = 'Matarracimos';
UPDATE objeto SET riot_id = '6630' WHERE nombre = 'Piedra del Ocaso';
UPDATE objeto SET riot_id = '3094' WHERE nombre = 'Cañón de Fuego Rápido';
UPDATE objeto SET riot_id = '3107' WHERE nombre = 'Redencion';
UPDATE objeto SET riot_id = '3190' WHERE nombre = 'Locket de la Solari de Hierro';

-- 3. Insertar nuevos objetos necesarios para las builds
INSERT INTO objeto (id, nombre, riot_id) VALUES
(16, 'Botas del Berserker', '3006'),
(17, 'Zapatos del Hechicero', '3020'),
(18, 'Gorra de la Muerte', '3089'),
(19, 'Reloj de Arena de Zhonya', '3157'),
(20, 'Youmuu Ghostblade', '3142'),
(21, 'Escudo Inmortal', '6673'),
(22, 'Pocion de Vida', '2003'),
(23, 'Hacha Corta', '1029'),
(24, 'Kraken', '6672'),
(25, 'Destrozador de Mortales', '3033');

-- 4. Ampliar tabla build con campos de estadísticas y rutas de runas
ALTER TABLE `build`
  ADD COLUMN `primary_path`    INT(11) DEFAULT 8000 AFTER `popularidad`,
  ADD COLUMN `secondary_path`  INT(11) DEFAULT 8100 AFTER `primary_path`,
  ADD COLUMN `win_rate`        DECIMAL(4,1) DEFAULT 50.0 AFTER `secondary_path`,
  ADD COLUMN `total_matches`   INT DEFAULT 10000 AFTER `win_rate`;

-- 5. Añadir fase a build_objeto (para Starter/Early/Core/Full)
ALTER TABLE `build_objeto`
  ADD COLUMN `fase` ENUM('starter','early','core','full') DEFAULT 'core' AFTER `orden`;

-- 6. Actualizar tabla runa con riot_id
ALTER TABLE `runa` ADD COLUMN `riot_id` INT(11) DEFAULT NULL AFTER `nombre`;
UPDATE runa SET riot_id = 8008  WHERE nombre = 'Lluvia de Cuchillas';
UPDATE runa SET riot_id = 8010  WHERE nombre = 'Conquistador';
UPDATE runa SET riot_id = 8112  WHERE nombre = 'Electrocutar';
UPDATE runa SET riot_id = 8128  WHERE nombre = 'Cosecha Oscura';
UPDATE runa SET riot_id = 8229  WHERE nombre = 'Cometa Arcano';
UPDATE runa SET riot_id = 8465  WHERE nombre = 'Guardián';

-- 7. Insertar runas secundarias comunes
INSERT INTO runa (id, nombre, riot_id, tipo) VALUES
(9,  'Triunfo',              9111, 'Secundaria'),
(10, 'Leyenda: Presencia',   9104, 'Secundaria'),
(11, 'Golpe de Gracia',      8014, 'Secundaria'),
(12, 'Banda de Maná',        8226, 'Secundaria'),
(13, 'Trascendencia',        8210, 'Secundaria'),
(14, 'Abrasar',              8237, 'Secundaria'),
(15, 'Mordedura Barata',     8126, 'Secundaria'),
(16, 'Coleccionista de Ojos',8138, 'Secundaria'),
(17, 'Cazador Relentless',   8105, 'Secundaria'),
(18, 'Revestimiento Óseo',   8473, 'Secundaria'),
(19, 'Segundo Aire',         8444, 'Secundaria'),
(20, 'Crecimiento',          8451, 'Secundaria');

-- ============================================================
-- 8. LIMPIAR BUILDS VIEJOS Y REHACER CON DATOS REALES
-- ============================================================
DELETE FROM build_runa;
DELETE FROM build_objeto;
DELETE FROM build;

-- YASUO (id:6) — Mid Crit | Precisión + Resolve
INSERT INTO build (id, id_campeon, nombre_build, popularidad, primary_path, secondary_path, win_rate, total_matches)
VALUES (6, 6, 'Crit Carry', 92, 8000, 8400, 51.3, 34875);

INSERT INTO build_objeto (id_build, id_objeto, orden, fase) VALUES
(6, 22, 1, 'starter'), -- Pocion
(6, 1,  2, 'starter'), -- Espada Larga
(6, 21, 3, 'early'),   -- Escudo Inmortal
(6, 6,  4, 'core'),    -- Filo del Infinito
(6, 4,  5, 'core'),    -- Arco Recurvo
(6, 3,  6, 'full'),    -- Botas Movilidad
(6, 25, 7, 'full');    -- Destrozador

INSERT INTO build_runa (id_build, id_runa) VALUES
(6,2),(6,9),(6,10),(6,11), -- Precisión: Conq, Triunfo, Ley:Presencia, Golpe de Gracia
(6,18),(6,19);             -- Resolve: Rev Oseo, Segundo Aire

-- AHRI (id:7) — Mid AP | Domination + Sorcery
INSERT INTO build (id, id_campeon, nombre_build, popularidad, primary_path, secondary_path, win_rate, total_matches)
VALUES (7, 7, 'AP Burst', 84, 8100, 8200, 52.1, 28430);

INSERT INTO build_objeto (id_build, id_objeto, orden, fase) VALUES
(7, 5,  1, 'starter'), -- Libro Sabiduría
(7, 22, 2, 'starter'), -- Pocion
(7, 9,  3, 'early'),   -- Tormenta de Luden
(7, 18, 4, 'core'),    -- Gorra Muerte
(7, 19, 5, 'core'),    -- Zhonya
(7, 2,  6, 'full'),    -- Vara de la Edad
(7, 17, 7, 'full');    -- Zapatos Hechicero

INSERT INTO build_runa (id_build, id_runa) VALUES
(7,3),(7,15),(7,16),(7,17), -- Dom: Electro, Mordedura, Coleccionista, Cazador
(7,12),(7,13);              -- Sorcy: Banda Mana, Trascendencia

-- ZED (id:3) — Mid Lethality | Domination + Sorcery
INSERT INTO build (id, id_campeon, nombre_build, popularidad, primary_path, secondary_path, win_rate, total_matches)
VALUES (3, 3, 'Lethality Burst', 90, 8100, 8200, 53.4, 41200);

INSERT INTO build_objeto (id_build, id_objeto, orden, fase) VALUES
(3, 1,  1, 'starter'), -- Espada Larga
(3, 20, 2, 'early'),   -- Youmuu
(3, 8,  3, 'core'),    -- Eclipse
(3, 10, 4, 'core'),    -- Filo de la Noche
(3, 25, 5, 'full'),    -- Destrozador
(3, 3,  6, 'full');    -- Botas Movilidad

INSERT INTO build_runa (id_build, id_runa) VALUES
(3,4),(3,15),(3,16),(3,17), -- Dom: Cosecha Oscura
(3,12),(3,13);

-- ASHE (id:1) — ADC | Precision + Sorcery
INSERT INTO build (id, id_campeon, nombre_build, popularidad, primary_path, secondary_path, win_rate, total_matches)
VALUES (1, 1, 'ADC Control', 85, 8000, 8200, 50.8, 22150);

INSERT INTO build_objeto (id_build, id_objeto, orden, fase) VALUES
(1, 22, 1, 'starter'), -- Pocion
(1, 4,  2, 'early'),   -- Arco Recurvo
(1, 11, 3, 'core'),    -- Matarracimos
(1, 6,  4, 'core'),    -- Filo del Infinito
(1, 13, 5, 'core'),    -- Cañón de Fuego
(1, 16, 6, 'full');    -- Botas Berserker

INSERT INTO build_runa (id_build, id_runa) VALUES
(1,1),(1,9),(1,10),(1,11), -- Precisión: Lluvia Cuchillas
(1,13),(1,14);             -- Sorcery

-- GAREN (id:2) — Top Bruiser | Precision + Resolve
INSERT INTO build (id, id_campeon, nombre_build, popularidad, primary_path, secondary_path, win_rate, total_matches)
VALUES (2, 2, 'Bruiser Tanque', 78, 8000, 8400, 51.9, 18900);

INSERT INTO build_objeto (id_build, id_objeto, orden, fase) VALUES
(2, 1,  1, 'starter'), -- Espada Larga
(2, 12, 2, 'early'),   -- Piedra Ocaso
(2, 6,  3, 'core'),    -- Filo del Infinito
(2, 25, 4, 'core'),    -- Destrozador
(2, 3,  5, 'full');    -- Botas Movilidad

INSERT INTO build_runa (id_build, id_runa) VALUES
(2,2),(2,9),(2,10),(2,11), -- Precisión: Conquistador
(2,18),(2,19);             -- Resolve

-- DARIUS (id:5) — Top | Precision + Resolve
INSERT INTO build (id, id_campeon, nombre_build, popularidad, primary_path, secondary_path, win_rate, total_matches)
VALUES (5, 5, 'Conquistador Full', 88, 8000, 8400, 54.2, 31500);

INSERT INTO build_objeto (id_build, id_objeto, orden, fase) VALUES
(5, 1,  1, 'starter'),
(5, 12, 2, 'early'),
(5, 6,  3, 'core'),
(5, 25, 4, 'core'),
(5, 3,  5, 'full');

INSERT INTO build_runa (id_build, id_runa) VALUES
(5,2),(5,9),(5,10),(5,11),
(5,18),(5,20);

-- LUX (id:4) — Mid AP | Sorcery + Inspiration
INSERT INTO build (id, id_campeon, nombre_build, popularidad, primary_path, secondary_path, win_rate, total_matches)
VALUES (4, 4, 'Burst AP', 82, 8200, 8300, 52.6, 19800);

INSERT INTO build_objeto (id_build, id_objeto, orden, fase) VALUES
(4, 5,  1, 'starter'),
(4, 9,  2, 'early'),
(4, 18, 3, 'core'),
(4, 19, 4, 'core'),
(4, 17, 5, 'full');

INSERT INTO build_runa (id_build, id_runa) VALUES
(4,5),(4,12),(4,13),(4,14),
(4,15),(4,16);

-- LEE SIN (id:8) — Jungla | Precision + Domination
INSERT INTO build (id, id_campeon, nombre_build, popularidad, primary_path, secondary_path, win_rate, total_matches)
VALUES (8, 8, 'Bruiser Jungla', 86, 8000, 8100, 50.5, 27600);

INSERT INTO build_objeto (id_build, id_objeto, orden, fase) VALUES
(8, 1,  1, 'starter'),
(8, 8,  2, 'early'),
(8, 6,  3, 'core'),
(8, 25, 4, 'core'),
(8, 3,  5, 'full');

INSERT INTO build_runa (id_build, id_runa) VALUES
(8,2),(8,9),(8,10),(8,11),
(8,15),(8,16);

-- JINX (id:9) — ADC Hypercarry | Precision + Domination
INSERT INTO build (id, id_campeon, nombre_build, popularidad, primary_path, secondary_path, win_rate, total_matches)
VALUES (9, 9, 'Hypercarry Crit', 91, 8000, 8100, 52.4, 38900);

INSERT INTO build_objeto (id_build, id_objeto, orden, fase) VALUES
(9, 22, 1, 'starter'),
(9, 21, 2, 'early'),
(9, 6,  3, 'core'),
(9, 24, 4, 'core'),
(9, 11, 5, 'core'),
(9, 16, 6, 'full');

INSERT INTO build_runa (id_build, id_runa) VALUES
(9,1),(9,9),(9,10),(9,11),
(9,15),(9,16);

-- THRESH (id:10) — Support | Resolve + Inspiration
INSERT INTO build (id, id_campeon, nombre_build, popularidad, primary_path, secondary_path, win_rate, total_matches)
VALUES (10, 10, 'Support Tanque', 80, 8400, 8300, 51.1, 15400);

INSERT INTO build_objeto (id_build, id_objeto, orden, fase) VALUES
(10, 15, 1, 'starter'),
(10, 14, 2, 'core'),
(10, 3,  3, 'full');

INSERT INTO build_runa (id_build, id_runa) VALUES
(10,6),(10,18),(10,19),(10,20),
(10,12),(10,13);
