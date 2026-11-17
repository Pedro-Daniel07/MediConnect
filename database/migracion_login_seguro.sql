-- Añade límite temporal de intentos de inicio de sesión.
-- Ejecutar una sola vez sobre la base de datos mediconnect.
USE mediconnect;

CREATE TABLE intentos_login (
    clave CHAR(64) PRIMARY KEY,
    fallos SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    inicio_ventana DATETIME(6) NOT NULL,
    bloqueado_hasta DATETIME(6) NULL,
    INDEX idx_intentos_limpieza (inicio_ventana, bloqueado_hasta)
) ENGINE=InnoDB;
