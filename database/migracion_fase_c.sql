-- =====================================================
-- MIGRACIÓN FASE C - Módulos Nuevos MediConnect
-- Idempotente y con datos de demostración clínicos
-- =====================================================

USE mediconnect;

SET FOREIGN_KEY_CHECKS = 0;

-- 1. Agregar código de afiliado y fecha a pacientes si no existen
ALTER TABLE pacientes
    ADD COLUMN IF NOT EXISTS codigo_afiliado VARCHAR(30) UNIQUE AFTER usuario_id,
    ADD COLUMN IF NOT EXISTS fecha_afiliacion DATE DEFAULT '2026-01-15' AFTER contacto_emergencia_telefono;

-- Actualizar códigos de afiliados existentes
UPDATE pacientes SET codigo_afiliado = CONCAT('MC-', LPAD(id, 6, '0')) WHERE codigo_afiliado IS NULL;

-- 2. Tabla de Mensajes de Chat (Persistencia en BD)
CREATE TABLE IF NOT EXISTS mensajes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    remitente_id INT NOT NULL,
    destinatario_id INT NOT NULL,
    mensaje TEXT NOT NULL,
    leido BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (remitente_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (destinatario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_chat_convo (remitente_id, destinatario_id),
    INDEX idx_dest_leido (destinatario_id, leido),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Tabla de Vacunaciones (Carné Digital de Vacunación)
CREATE TABLE IF NOT EXISTS vacunaciones (
    id INT PRIMARY KEY AUTO_INCREMENT,
    paciente_id INT NOT NULL,
    nombre_vacuna VARCHAR(120) NOT NULL,
    dosis VARCHAR(50) NOT NULL,
    fecha_aplicacion DATE NOT NULL,
    fecha_proxima_dosis DATE NULL,
    lote VARCHAR(50) NOT NULL,
    profesional VARCHAR(100) NOT NULL,
    centro_vacunacion VARCHAR(150) DEFAULT 'Centro Clínico MediConnect',
    estado ENUM('aplicada', 'pendiente', 'vencida') DEFAULT 'aplicada',
    observaciones TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (paciente_id) REFERENCES pacientes(id) ON DELETE CASCADE,
    INDEX idx_paciente_vacuna (paciente_id),
    INDEX idx_estado_vacuna (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Tabla de Signos Vitales y Puntaje de Salud
CREATE TABLE IF NOT EXISTS signos_vitales (
    id INT PRIMARY KEY AUTO_INCREMENT,
    paciente_id INT NOT NULL,
    medico_id INT NULL,
    peso DECIMAL(5,2) NOT NULL COMMENT 'kg',
    altura DECIMAL(5,2) NOT NULL COMMENT 'cm',
    presion_sistolica INT NOT NULL COMMENT 'mmHg',
    presion_diastolica INT NOT NULL COMMENT 'mmHg',
    frecuencia_cardiaca INT NOT NULL COMMENT 'ppm',
    frecuencia_respiratoria INT DEFAULT 18 COMMENT 'rpm',
    temperatura DECIMAL(4,2) DEFAULT 36.6 COMMENT 'Celsius',
    saturacion_oxigeno INT DEFAULT 98 COMMENT '% SpO2',
    glucosa INT DEFAULT 95 COMMENT 'mg/dL',
    puntaje_salud INT DEFAULT 90 COMMENT '0-100',
    imc DECIMAL(4,1) NOT NULL,
    clasificacion_imc VARCHAR(50) NOT NULL,
    notas TEXT NULL,
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (paciente_id) REFERENCES pacientes(id) ON DELETE CASCADE,
    FOREIGN KEY (medico_id) REFERENCES medicos(id) ON DELETE SET NULL,
    INDEX idx_paciente_signos (paciente_id),
    INDEX idx_fecha_signos (fecha_registro)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Tabla de Lista de Espera Inteligente
CREATE TABLE IF NOT EXISTS lista_espera (
    id INT PRIMARY KEY AUTO_INCREMENT,
    paciente_id INT NOT NULL,
    especialidad_id INT NOT NULL,
    medico_id INT NULL,
    fecha_solicitud DATE NOT NULL,
    prioridad ENUM('alta', 'media', 'baja') DEFAULT 'media',
    estado ENUM('esperando', 'asignado', 'descartado') DEFAULT 'esperando',
    cita_asignada_id INT NULL,
    motivo TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (paciente_id) REFERENCES pacientes(id) ON DELETE CASCADE,
    FOREIGN KEY (especialidad_id) REFERENCES especialidades(id) ON DELETE RESTRICT,
    FOREIGN KEY (medico_id) REFERENCES medicos(id) ON DELETE SET NULL,
    FOREIGN KEY (cita_asignada_id) REFERENCES citas(id) ON DELETE SET NULL,
    INDEX idx_espera_busqueda (estado, especialidad_id, prioridad)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Tabla de Auditoría en Vivo (Centro de Comando Admin)
CREATE TABLE IF NOT EXISTS auditoria_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT NULL,
    accion VARCHAR(60) NOT NULL,
    modulo VARCHAR(60) NOT NULL,
    descripcion TEXT NOT NULL,
    ip_address VARCHAR(45) DEFAULT '127.0.0.1',
    user_agent VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_auditoria_usuario (usuario_id),
    INDEX idx_auditoria_accion (accion),
    INDEX idx_auditoria_fecha (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- DATOS DE DEMOSTRACIÓN (INSERT IGNORE)
-- =====================================================

-- Conversaciones en Chat (Doctor 2 con Paciente 7, etc.)
INSERT IGNORE INTO mensajes (id, remitente_id, destinatario_id, mensaje, leido, created_at) VALUES
(1, 7, 2, 'Buenos días Dra. Rodríguez, tengo una consulta sobre la dosis del medicamento.', 1, '2026-11-05 09:15:00'),
(2, 2, 7, 'Buenos días Juan. Sí, claro, dime exactamente qué duda tienes con la receta.', 1, '2026-11-05 09:18:20'),
(3, 7, 2, '¿Debo tomar el paracetamol antes o después del desayuno?', 1, '2026-11-05 09:20:10'),
(4, 2, 7, 'Tómalo siempre después de ingerir alimentos para proteger tu estómago. Si persiste la molestia me avisas.', 1, '2026-11-05 09:22:45'),
(5, 7, 2, 'Entendido doctora, muchas gracias por la pronta respuesta!', 1, '2026-11-05 09:25:00'),
(6, 2, 7, 'Con gusto Juan. Recuerda registrar tus signos vitales en el sistema cada semana.', 0, '2026-11-08 08:30:00'),
(7, 8, 3, 'Doctor Martínez, ya tengo los resultados del electrocardiograma listos.', 0, '2026-11-08 10:12:00'),
(8, 9, 4, 'Dra. García, le apliqué la vacuna a Carlos ayer y tuvo un poco de fiebre leve.', 1, '2026-11-07 14:05:00'),
(9, 4, 9, 'Es una reacción normal en las primeras 24 horas. Manténlo bien hidratado.', 1, '2026-11-07 14:20:00');

-- Vacunaciones de Paciente 1 (id=1, usuario 7) y otros
INSERT IGNORE INTO vacunaciones (id, paciente_id, nombre_vacuna, dosis, fecha_aplicacion, fecha_proxima_dosis, lote, profesional, centro_vacunacion, estado, observaciones) VALUES
(1, 1, 'COVID-19 (Bivalente Moderna)', 'Refuerzo Anual', '2026-03-10', '2027-03-10', 'MOD-2026-B9', 'Enf. Patricia Ruiz', 'Centro Clínico MediConnect', 'aplicada', 'Sin efectos adversos inmediatos'),
(2, 1, 'Influenza Estacional', 'Dosis Anual', '2026-04-15', '2027-04-15', 'INF-8841-A', 'Enf. Patricia Ruiz', 'Centro Clínico MediConnect', 'aplicada', 'Campaña de vacunación respiratoria'),
(3, 1, 'Tétanos y Difteria (Td)', 'Refuerzo 10 años', '2022-08-20', '2032-08-20', 'TD-4401-X', 'Dr. Roberto Meza', 'Hospital Central', 'aplicada', 'Vigente hasta 2032'),
(4, 1, 'Hepatitis B', 'Dosis 3 (Completa)', '2021-02-14', NULL, 'HEP-9902', 'Enf. Elena Castro', 'Centro Clínico MediConnect', 'aplicada', 'Inmunidad de por vida confirmada'),
(5, 1, 'Fiebre Amarilla', 'Dosis Única', '2026-12-01', '2026-12-01', 'PEND-FA-2026', 'Por asignar', 'Centro Clínico MediConnect', 'pendiente', 'Recomendada previo a viaje a zona endémica'),
(6, 2, 'COVID-19 (Bivalente Moderna)', 'Refuerzo Anual', '2025-10-15', '2026-10-15', 'MOD-2025-A1', 'Enf. Patricia Ruiz', 'Centro Clínico MediConnect', 'vencida', 'Pendiente aplicación de dosis de actualización'),
(7, 3, 'Triple Viral (SRP)', 'Refuerzo Escolar', '2026-05-12', NULL, 'SRP-7712', 'Dra. Ana García', 'Clínica MediConnect Pediatría', 'aplicada', 'Excelente tolerancia');

-- Signos Vitales y Puntaje de Salud para Paciente 1 y otros
INSERT IGNORE INTO signos_vitales (id, paciente_id, medico_id, peso, altura, presion_sistolica, presion_diastolica, frecuencia_cardiaca, frecuencia_respiratoria, temperatura, saturacion_oxigeno, glucosa, puntaje_salud, imc, clasificacion_imc, notas, fecha_registro) VALUES
(1, 1, 1, 72.50, 175.00, 118, 76, 72, 16, 36.5, 99, 92, 96, 23.7, 'Normal', 'Parámetros cardiovasculares y metabólicos óptimos.', '2026-06-15 08:30:00'),
(2, 1, 1, 73.00, 175.00, 120, 78, 74, 17, 36.6, 98, 94, 95, 23.8, 'Normal', 'Control rutinario mensual. Todo estable.', '2026-07-20 09:10:00'),
(3, 1, 1, 72.80, 175.00, 119, 77, 70, 16, 36.4, 99, 90, 97, 23.8, 'Normal', 'Excelente adaptación a plan de actividad física.', '2026-09-05 10:15:00'),
(4, 1, 1, 72.00, 175.00, 117, 75, 68, 16, 36.5, 99, 89, 98, 23.5, 'Normal', 'Estado general sobresaliente. Función respiratoria perfecta.', '2026-11-02 08:45:00'),
(5, 2, 2, 85.00, 168.00, 138, 88, 82, 19, 36.7, 96, 115, 74, 30.1, 'Obesidad', 'Hipertensión leve bajo seguimiento. Dieta baja en sodio.', '2026-11-03 11:20:00'),
(6, 3, 3, 58.50, 162.00, 110, 70, 75, 18, 36.5, 99, 85, 94, 22.3, 'Normal', 'Paciente normopeso con parámetros ideales.', '2026-11-04 15:40:00');

-- Lista de Espera Inteligente
INSERT IGNORE INTO lista_espera (id, paciente_id, especialidad_id, medico_id, fecha_solicitud, prioridad, estado, motivo, created_at) VALUES
(1, 4, 2, 2, '2026-11-06', 'alta', 'esperando', 'Solicita adelantar cita de control por palpitaciones ocasionales.', '2026-11-06 10:00:00'),
(2, 5, 1, 1, '2026-11-07', 'media', 'esperando', 'Chequeo laboral urgente requerido antes de fin de mes.', '2026-11-07 11:30:00'),
(3, 6, 4, 4, '2026-11-07', 'baja', 'esperando', 'Revisión preventiva de lunares y manchas solares.', '2026-11-07 14:15:00'),
(4, 2, 2, 2, '2026-10-28', 'alta', 'asignado', 'Cupo asignado automáticamente tras cancelación de cita previa.', '2026-10-28 09:00:00');

-- Auditoría en Vivo (Logs de acciones del sistema)
INSERT IGNORE INTO auditoria_logs (id, usuario_id, accion, modulo, descripcion, ip_address, user_agent, created_at) VALUES
(1, 1, 'LOGIN_EXITOSO', 'Autenticación', 'Inicio de sesión administrativo con credenciales maestras', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0', '2026-11-08 07:45:12'),
(2, 7, 'LOGIN_EXITOSO', 'Autenticación', 'Paciente Juan Pérez inició sesión en el portal clínico', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0', '2026-11-08 08:10:05'),
(3, 2, 'TELEMEDICINA_INICIADA', 'Telemedicina', 'Dra. María Rodríguez inició sala de videoconsulta cifrada #CITA-44', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0', '2026-11-08 08:30:22'),
(4, 2, 'RECETA_EMITIDA', 'Fórmulas Médicas', 'Emisión de prescripción médica digital con código QR para paciente Juan Pérez', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0', '2026-11-08 08:45:00'),
(5, 7, 'CARNE_DIGITAL_CONSULTADO', 'Carné Digital', 'Generación de carné wallet con verificación de código QR', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0', '2026-11-08 08:50:11'),
(6, 1, 'LISTA_ESPERA_ASIGNADA', 'Lista de Espera', 'Asignación automática de cupo a paciente en lista de espera por cupo liberado', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0', '2026-11-08 09:12:44'),
(7, 3, 'SIGNOS_REGISTRADOS', 'Signos Vitales', 'Registro de métricas biométricas: TA 118/76, FC 72, SpO2 99%, Score Salud: 96/100', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0', '2026-11-08 09:30:19'),
(8, 1, 'EXPORTACION_CSV', 'Centro de Comando', 'Exportación de base de datos de pacientes a archivo seguro CSV/Excel', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0', '2026-11-08 10:05:03');

SET FOREIGN_KEY_CHECKS = 1;
