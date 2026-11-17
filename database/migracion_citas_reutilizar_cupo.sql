-- Permitir reutilizar el horario de una cita cancelada sin borrar su historial.
-- Ejecutar una sola vez sobre la base de datos mediconnect.
USE mediconnect;

ALTER TABLE citas
    ADD COLUMN hora_inicio_activa TIME
        GENERATED ALWAYS AS (CASE WHEN estado = 'cancelada' THEN NULL ELSE hora_inicio END) STORED,
    DROP INDEX unique_cita,
    ADD UNIQUE KEY unique_cita (medico_id, fecha, hora_inicio_activa);
