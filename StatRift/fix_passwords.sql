-- ============================================================
-- StatRift — Corrección de contraseñas de usuarios de prueba
-- Ejecutar en phpMyAdmin > bd_final > SQL
-- ============================================================

-- La contraseña "1234" está aquí en texto plano.
-- El login la acepta porque tiene un fallback de comparación directa.
-- Esto es SOLO para pruebas. En producción siempre usar password_hash().

UPDATE jugador SET pass = '1234' WHERE nick = 'Faker';
UPDATE jugador SET pass = '1234' WHERE nick = 'Caps';
UPDATE jugador SET pass = '1234' WHERE nick = 'xPeke';

-- También corregimos los usuarios originales que tienen pass vacía:
UPDATE jugador SET pass = '1234' WHERE nick = 'JuanG';
UPDATE jugador SET pass = '1234' WHERE nick = 'Anita';
UPDATE jugador SET pass = '1234' WHERE nick = 'johndoe';
UPDATE jugador SET pass = '1234' WHERE nick = 'janesmith';
UPDATE jugador SET pass = '1234' WHERE nick = 'mjohnson';

-- Verificar resultado:
SELECT nick, pass FROM jugador;
