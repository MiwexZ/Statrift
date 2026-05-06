-- ============================================================
-- StatRift — Actualización de imágenes con CDN oficial de Riot
-- Ejecutar en: bd_final  |  phpMyAdmin o cliente MySQL
-- ============================================================
-- VERIFICADO contra bd_final.sql:
--   campeon (id 1-5): tiene columna `foto` varchar(200) ✓
--   liga    (id 1-2): tiene columna `logo` varchar(200) ✓
--   objeto  (id 1-5): NO tiene columna `foto` → se crea abajo ✓
-- ============================================================


-- ------------------------------------------------------------
-- PASO 1: Añadir columna foto a la tabla objeto
-- (Si ya la añadiste antes, comenta esta línea)
-- ------------------------------------------------------------
ALTER TABLE `objeto` ADD COLUMN `foto` VARCHAR(300) DEFAULT NULL;


-- ------------------------------------------------------------
-- PASO 2: CAMPEONES — Splash arts panorámicos (16:9)
-- Tabla: campeon | Columna: foto | IDs: 1=Ashe, 2=Garen, 3=Zed, 4=Lux, 5=Darius
-- ------------------------------------------------------------
UPDATE `campeon` SET `foto` = 'https://ddragon.leagueoflegends.com/cdn/img/champion/splash/Ashe_0.jpg'
  WHERE `id` = 1;   -- Ashe

UPDATE `campeon` SET `foto` = 'https://ddragon.leagueoflegends.com/cdn/img/champion/splash/Garen_0.jpg'
  WHERE `id` = 2;   -- Garen

UPDATE `campeon` SET `foto` = 'https://ddragon.leagueoflegends.com/cdn/img/champion/splash/Zed_0.jpg'
  WHERE `id` = 3;   -- Zed

UPDATE `campeon` SET `foto` = 'https://ddragon.leagueoflegends.com/cdn/img/champion/splash/Lux_0.jpg'
  WHERE `id` = 4;   -- Lux

UPDATE `campeon` SET `foto` = 'https://ddragon.leagueoflegends.com/cdn/img/champion/splash/Darius_0.jpg'
  WHERE `id` = 5;   -- Darius


-- ------------------------------------------------------------
-- PASO 3: LIGAS — Logos oficiales
-- Tabla: liga | Columna: logo | IDs: 1=LEC, 2=LCK
-- ------------------------------------------------------------

-- LEC (logo llama cian transparente)
UPDATE `liga` SET `logo` = 'https://static.lolesports.com/leagues/1592516184297_LEC-01-FullonDark.png'
  WHERE `id` = 1;

-- LCK (logo estrella blanco/negro transparente)
UPDATE `liga` SET `logo` = 'https://static.lolesports.com/leagues/lck-color-on-black.png'
  WHERE `id` = 2;


-- ------------------------------------------------------------
-- PASO 4: OBJETOS — Iconos del item shop (Data Dragon 14.9.1)
-- Tabla: objeto | Columna: foto (recién creada arriba)
-- IDs: 1=Espada Larga, 2=Vara de la Edad, 3=Botas, 4=Arco, 5=Libro
-- ------------------------------------------------------------
UPDATE `objeto` SET `foto` = 'https://ddragon.leagueoflegends.com/cdn/14.9.1/img/item/1036.png'
  WHERE `id` = 1;   -- Espada Larga (Long Sword)

UPDATE `objeto` SET `foto` = 'https://ddragon.leagueoflegends.com/cdn/14.9.1/img/item/3118.png'
  WHERE `id` = 2;   -- Vara de la Edad / Rod of Ages (3118, no 3001 que es Abyssal Mask)

UPDATE `objeto` SET `foto` = 'https://ddragon.leagueoflegends.com/cdn/14.9.1/img/item/3117.png'
  WHERE `id` = 3;   -- Botas de Movilidad (Mobility Boots)

UPDATE `objeto` SET `foto` = 'https://ddragon.leagueoflegends.com/cdn/14.9.1/img/item/1043.png'
  WHERE `id` = 4;   -- Arco Recurvo (Recurve Bow)

UPDATE `objeto` SET `foto` = 'https://ddragon.leagueoflegends.com/cdn/14.9.1/img/item/1052.png'
  WHERE `id` = 5;   -- Libro de la Sabiduría (Amplifying Tome)
