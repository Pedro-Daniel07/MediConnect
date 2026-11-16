-- =====================================================
-- Base de Datos: Sistema de Salud MediConnect
-- =====================================================

DROP DATABASE IF EXISTS mediconnect;
CREATE DATABASE IF NOT EXISTS mediconnect
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE mediconnect;

-- =====================================================
-- Tabla: roles
-- =====================================================
CREATE TABLE roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    descripcion VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO roles (nombre, descripcion) VALUES
('administrador', 'Acceso total al sistema'),
('medico', 'Personal médico autorizado'),
('paciente', 'Usuario paciente del sistema');

-- =====================================================
-- Tabla: usuarios
-- =====================================================
CREATE TABLE usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol_id INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    documento VARCHAR(20) NOT NULL UNIQUE,
    telefono VARCHAR(20),
    fecha_nacimiento DATE,
    direccion VARCHAR(255),
    genero ENUM('M', 'F', 'Otro') DEFAULT 'Otro',
    activo BOOLEAN DEFAULT TRUE,
    token_recuperacion VARCHAR(100),
    token_expiracion DATETIME,
    ultimo_acceso DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (rol_id) REFERENCES roles(id) ON DELETE RESTRICT,
    INDEX idx_email (email),
    INDEX idx_documento (documento),
    INDEX idx_rol (rol_id)
) ENGINE=InnoDB;

-- =====================================================
-- Tabla: especialidades
-- =====================================================
CREATE TABLE especialidades (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    descripcion TEXT,
    activo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO especialidades (nombre, descripcion) VALUES
('Medicina General', 'Atención médica general y preventiva'),
('Cardiología', 'Especialista en enfermedades del corazón'),
('Pediatría', 'Atención médica de niños y adolescentes'),
('Dermatología', 'Tratamiento de enfermedades de la piel'),
('Ginecología', 'Salud reproductiva femenina');

-- =====================================================
-- Tabla: medicos
-- =====================================================
CREATE TABLE medicos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT NOT NULL UNIQUE,
    especialidad_id INT NOT NULL,
    licencia_medica VARCHAR(50) NOT NULL UNIQUE,
    biografia TEXT,
    anos_experiencia INT DEFAULT 0,
    consultorio VARCHAR(50),
    tarifa_consulta DECIMAL(10,2) DEFAULT 0.00,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (especialidad_id) REFERENCES especialidades(id) ON DELETE RESTRICT,
    INDEX idx_especialidad (especialidad_id)
) ENGINE=InnoDB;

-- =====================================================
-- Tabla: pacientes
-- =====================================================
CREATE TABLE pacientes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT NOT NULL UNIQUE,
    grupo_sanguineo VARCHAR(5),
    alergias TEXT,
    enfermedades_cronicas TEXT,
    contacto_emergencia_nombre VARCHAR(100),
    contacto_emergencia_telefono VARCHAR(20),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =====================================================
-- Tabla: horarios_medicos
-- =====================================================
CREATE TABLE horarios_medicos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    medico_id INT NOT NULL,
    dia_semana TINYINT NOT NULL COMMENT '1=Lunes, 7=Domingo',
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL,
    duracion_cita INT DEFAULT 30 COMMENT 'Minutos por cita',
    activo BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (medico_id) REFERENCES medicos(id) ON DELETE CASCADE,
    INDEX idx_medico_dia (medico_id, dia_semana)
) ENGINE=InnoDB;

-- =====================================================
-- Tabla: citas
-- =====================================================
CREATE TABLE citas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    paciente_id INT NOT NULL,
    medico_id INT NOT NULL,
    fecha DATE NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL,
    estado ENUM('pendiente', 'confirmada', 'completada', 'cancelada') DEFAULT 'pendiente',
    motivo_consulta TEXT,
    motivo_cancelacion TEXT,
    notas_medicas TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (paciente_id) REFERENCES pacientes(id) ON DELETE CASCADE,
    FOREIGN KEY (medico_id) REFERENCES medicos(id) ON DELETE CASCADE,
    INDEX idx_paciente (paciente_id),
    INDEX idx_medico (medico_id),
    INDEX idx_fecha (fecha),
    INDEX idx_estado (estado),
    UNIQUE KEY unique_cita (medico_id, fecha, hora_inicio)
) ENGINE=InnoDB;

-- =====================================================
-- Tabla: historias_clinicas
-- =====================================================
CREATE TABLE historias_clinicas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    cita_id INT NOT NULL,
    paciente_id INT NOT NULL,
    medico_id INT NOT NULL,
    diagnostico TEXT NOT NULL,
    sintomas TEXT,
    examen_fisico TEXT,
    observaciones TEXT,
    plan_tratamiento TEXT,
    peso DECIMAL(5,2) COMMENT 'kg',
    altura DECIMAL(5,2) COMMENT 'cm',
    presion_arterial VARCHAR(20),
    temperatura DECIMAL(4,2) COMMENT 'Celsius',
    frecuencia_cardiaca INT COMMENT 'ppm',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cita_id) REFERENCES citas(id) ON DELETE CASCADE,
    FOREIGN KEY (paciente_id) REFERENCES pacientes(id) ON DELETE CASCADE,
    FOREIGN KEY (medico_id) REFERENCES medicos(id) ON DELETE CASCADE,
    INDEX idx_paciente (paciente_id),
    INDEX idx_fecha (created_at)
) ENGINE=InnoDB;

-- =====================================================
-- Tabla: medicamentos
-- =====================================================
CREATE TABLE medicamentos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(200) NOT NULL,
    principio_activo VARCHAR(200),
    concentracion VARCHAR(100),
    presentacion VARCHAR(100),
    activo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_nombre (nombre)
) ENGINE=InnoDB;

INSERT INTO medicamentos (nombre, principio_activo, concentracion, presentacion) VALUES
('Acetaminofén', 'Paracetamol', '500mg', 'Tabletas'),
('Ibuprofeno', 'Ibuprofeno', '400mg', 'Tabletas'),
('Amoxicilina', 'Amoxicilina', '500mg', 'Cápsulas'),
('Omeprazol', 'Omeprazol', '20mg', 'Cápsulas'),
('Loratadina', 'Loratadina', '10mg', 'Tabletas'),
('Metformina', 'Metformina', '850mg', 'Tabletas'),
('Losartán', 'Losartán', '50mg', 'Tabletas'),
('Atorvastatina', 'Atorvastatina', '20mg', 'Tabletas'),
('Salbutamol', 'Salbutamol', '100mcg', 'Inhalador'),
('Diclofenaco', 'Diclofenaco', '50mg', 'Tabletas');

-- =====================================================
-- Tabla: formulas_medicas
-- =====================================================
CREATE TABLE formulas_medicas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    historia_clinica_id INT NOT NULL,
    paciente_id INT NOT NULL,
    medico_id INT NOT NULL,
    codigo_qr VARCHAR(255),
    observaciones TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (historia_clinica_id) REFERENCES historias_clinicas(id) ON DELETE CASCADE,
    FOREIGN KEY (paciente_id) REFERENCES pacientes(id) ON DELETE CASCADE,
    FOREIGN KEY (medico_id) REFERENCES medicos(id) ON DELETE CASCADE,
    INDEX idx_paciente (paciente_id),
    INDEX idx_codigo_qr (codigo_qr)
) ENGINE=InnoDB;

-- =====================================================
-- Tabla: formula_medicamentos (detalle de fórmula)
-- =====================================================
CREATE TABLE formula_medicamentos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    formula_id INT NOT NULL,
    medicamento_id INT NOT NULL,
    dosis VARCHAR(100) NOT NULL,
    frecuencia VARCHAR(100) NOT NULL,
    duracion VARCHAR(100) NOT NULL,
    instrucciones TEXT,
    FOREIGN KEY (formula_id) REFERENCES formulas_medicas(id) ON DELETE CASCADE,
    FOREIGN KEY (medicamento_id) REFERENCES medicamentos(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- =====================================================
-- Tabla: resultados_laboratorio
-- =====================================================
CREATE TABLE resultados_laboratorio (
    id INT PRIMARY KEY AUTO_INCREMENT,
    paciente_id INT NOT NULL,
    medico_id INT NOT NULL,
    tipo_examen VARCHAR(200) NOT NULL,
    resultado TEXT NOT NULL,
    valores_referencia TEXT,
    observaciones TEXT,
    archivo_url VARCHAR(255),
    fecha_examen DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (paciente_id) REFERENCES pacientes(id) ON DELETE CASCADE,
    FOREIGN KEY (medico_id) REFERENCES medicos(id) ON DELETE CASCADE,
    INDEX idx_paciente (paciente_id),
    INDEX idx_fecha (fecha_examen)
) ENGINE=InnoDB;

-- =====================================================
-- Tabla: notificaciones
-- =====================================================
CREATE TABLE notificaciones (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT NOT NULL,
    tipo ENUM('cita', 'medicamento', 'resultado', 'general') DEFAULT 'general',
    titulo VARCHAR(255) NOT NULL,
    mensaje TEXT NOT NULL,
    leida BOOLEAN DEFAULT FALSE,
    fecha_evento DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_usuario_leida (usuario_id, leida),
    INDEX idx_fecha_evento (fecha_evento)
) ENGINE=InnoDB;

-- =====================================================
-- DATOS DE EJEMPLO
-- =====================================================

-- Usuario Administrador
INSERT INTO usuarios (email, password, rol_id, nombre, apellido, documento, telefono, fecha_nacimiento, genero) VALUES
('admin@mediconnect.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 'Carlos', 'Administrador', '1000000000', '3001234567', '1985-05-15', 'M');
-- Password: Admin123!

-- Usuarios Médicos
INSERT INTO usuarios (email, password, rol_id, nombre, apellido, documento, telefono, fecha_nacimiento, genero) VALUES
('dr.rodriguez@mediconnect.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, 'María', 'Rodríguez', '1001000000', '3101234567', '1980-03-20', 'F'),
('dr.martinez@mediconnect.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, 'José', 'Martínez', '1002000000', '3102234567', '1975-07-10', 'M'),
('dr.garcia@mediconnect.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, 'Ana', 'García', '1003000000', '3103234567', '1988-11-25', 'F'),
('dr.lopez@mediconnect.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, 'Luis', 'López', '1004000000', '3104234567', '1982-09-05', 'M'),
('dr.fernandez@mediconnect.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, 'Carmen', 'Fernández', '1005000000', '3105234567', '1990-01-30', 'F');

-- Médicos con especialidades
INSERT INTO medicos (usuario_id, especialidad_id, licencia_medica, biografia, anos_experiencia, consultorio, tarifa_consulta) VALUES
(2, 1, 'LM-2024-001', 'Médica general con enfoque en medicina preventiva y atención primaria.', 15, 'Consultorio 101', 80000.00),
(3, 2, 'LM-2024-002', 'Cardiólogo especializado en hipertensión y enfermedades coronarias.', 20, 'Consultorio 201', 150000.00),
(4, 3, 'LM-2024-003', 'Pediatra con experiencia en desarrollo infantil y vacunación.', 12, 'Consultorio 102', 100000.00),
(5, 4, 'LM-2024-004', 'Dermatólogo especializado en tratamientos estéticos y médicos.', 10, 'Consultorio 202', 120000.00),
(6, 5, 'LM-2024-005', 'Ginecóloga con experiencia en salud reproductiva y embarazo.', 8, 'Consultorio 103', 110000.00);

-- Horarios médicos (Lunes a Viernes, 8:00-17:00)
INSERT INTO horarios_medicos (medico_id, dia_semana, hora_inicio, hora_fin, duracion_cita) VALUES
-- Dra. Rodríguez (Medicina General)
(1, 1, '08:00:00', '12:00:00', 30), (1, 1, '14:00:00', '17:00:00', 30),
(1, 2, '08:00:00', '12:00:00', 30), (1, 2, '14:00:00', '17:00:00', 30),
(1, 3, '08:00:00', '12:00:00', 30), (1, 3, '14:00:00', '17:00:00', 30),
(1, 4, '08:00:00', '12:00:00', 30), (1, 4, '14:00:00', '17:00:00', 30),
(1, 5, '08:00:00', '12:00:00', 30),
-- Dr. Martínez (Cardiología)
(2, 1, '09:00:00', '13:00:00', 45), (2, 3, '09:00:00', '13:00:00', 45), (2, 5, '09:00:00', '13:00:00', 45),
-- Dra. García (Pediatría)
(3, 1, '08:00:00', '12:00:00', 30), (3, 2, '08:00:00', '12:00:00', 30),
(3, 3, '08:00:00', '12:00:00', 30), (3, 4, '08:00:00', '12:00:00', 30),
(3, 5, '14:00:00', '17:00:00', 30),
-- Dr. López (Dermatología)
(4, 2, '10:00:00', '14:00:00', 40), (4, 4, '10:00:00', '14:00:00', 40),
-- Dra. Fernández (Ginecología)
(5, 1, '14:00:00', '17:00:00', 45), (5, 3, '14:00:00', '17:00:00', 45), (5, 5, '14:00:00', '17:00:00', 45);

-- Usuarios Pacientes
INSERT INTO usuarios (email, password, rol_id, nombre, apellido, documento, telefono, fecha_nacimiento, genero) VALUES
('paciente1@mediconnect.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Laura', 'Gómez', '2001000000', '3201234567', '1995-06-12', 'F'),
('paciente2@mediconnect.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Pedro', 'Sánchez', '2002000000', '3202234567', '1988-08-20', 'M'),
('paciente3@mediconnect.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Sofía', 'Ramírez', '2003000000', '3203234567', '2010-02-14', 'F'),
('paciente4@mediconnect.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Diego', 'Torres', '2004000000', '3204234567', '1992-04-05', 'M'),
('paciente5@mediconnect.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Isabel', 'Morales', '2005000000', '3205234567', '1978-12-18', 'F'),
('paciente6@mediconnect.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Miguel', 'Castro', '2006000000', '3206234567', '1985-10-22', 'M'),
('paciente7@mediconnect.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Valentina', 'Ruiz', '2007000000', '3207234567', '2015-03-08', 'F'),
('paciente8@mediconnect.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Andrés', 'Herrera', '2008000000', '3208234567', '1990-07-30', 'M'),
('paciente9@mediconnect.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Carolina', 'Díaz', '2009000000', '3209234567', '1983-11-11', 'F'),
('paciente10@mediconnect.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Ricardo', 'Vargas', '2010000000', '3210234567', '1975-05-25', 'M');

-- Pacientes con información médica
INSERT INTO pacientes (usuario_id, grupo_sanguineo, alergias, enfermedades_cronicas, contacto_emergencia_nombre, contacto_emergencia_telefono) VALUES
(7, 'O+', 'Penicilina', NULL, 'Roberto Gómez', '3301234567'),
(8, 'A+', NULL, 'Hipertensión', 'María Sánchez', '3302234567'),
(9, 'B+', 'Polen', NULL, 'Andrea Ramírez', '3303234567'),
(10, 'AB+', NULL, 'Diabetes Tipo 2', 'Luz Torres', '3304234567'),
(11, 'O-', 'Ibuprofeno', 'Artritis', 'Fernando Morales', '3305234567'),
(12, 'A-', NULL, NULL, 'Claudia Castro', '3306234567'),
(13, 'B-', NULL, NULL, 'Diana Ruiz', '3307234567'),
(14, 'O+', 'Mariscos', NULL, 'Patricia Herrera', '3308234567'),
(15, 'A+', NULL, 'Asma', 'Carlos Díaz', '3309234567'),
(16, 'O+', NULL, 'Colesterol alto', 'Elena Vargas', '3310234567');

-- Citas pasadas (completadas)
INSERT INTO citas (paciente_id, medico_id, fecha, hora_inicio, hora_fin, estado, motivo_consulta, notas_medicas) VALUES
(1, 1, '2026-10-15', '09:00:00', '09:30:00', 'completada', 'Control general de salud', 'Paciente en buen estado general'),
(2, 2, '2026-10-18', '10:00:00', '10:45:00', 'completada', 'Control de presión arterial', 'Ajuste de medicación para hipertensión'),
(3, 3, '2026-10-20', '08:30:00', '09:00:00', 'completada', 'Control de crecimiento', 'Desarrollo acorde a la edad'),
(4, 1, '2026-10-22', '14:30:00', '15:00:00', 'completada', 'Dolor de garganta', 'Faringitis viral, tratamiento sintomático'),
(5, 5, '2026-10-25', '15:00:00', '15:45:00', 'completada', 'Control ginecológico anual', 'Resultados normales'),
(6, 4, '2026-10-28', '11:00:00', '11:40:00', 'completada', 'Revisión de lunares', 'Sin hallazgos preocupantes'),
(7, 3, '2026-11-01', '15:00:00', '15:30:00', 'completada', 'Vacunación', 'Aplicación de vacuna Triple Viral');

-- Citas futuras
INSERT INTO citas (paciente_id, medico_id, fecha, hora_inicio, hora_fin, estado, motivo_consulta) VALUES
(1, 1, '2026-11-15', '10:00:00', '10:30:00', 'confirmada', 'Control de seguimiento'),
(8, 2, '2026-11-12', '09:00:00', '09:45:00', 'confirmada', 'Evaluación cardiológica'),
(9, 1, '2026-11-13', '08:00:00', '08:30:00', 'pendiente', 'Consulta por gripe'),
(10, 4, '2026-11-14', '10:00:00', '10:40:00', 'pendiente', 'Consulta dermatológica'),
(2, 1, '2026-11-18', '09:30:00', '10:00:00', 'confirmada', 'Renovación de fórmula médica');

-- Historias clínicas
INSERT INTO historias_clinicas (cita_id, paciente_id, medico_id, diagnostico, sintomas, examen_fisico, peso, altura, presion_arterial, temperatura, frecuencia_cardiaca, plan_tratamiento) VALUES
(1, 1, 1, 'Estado de salud normal', 'Ninguno reportado', 'Buen estado general', 68.5, 165, '120/80', 36.5, 72, 'Continuar con estilo de vida saludable'),
(2, 2, 2, 'Hipertensión arterial controlada', 'Ocasionales dolores de cabeza', 'Presión elevada', 85.0, 178, '145/95', 36.8, 78, 'Ajuste de dosis de Losartán, seguimiento en 1 mes'),
(3, 3, 3, 'Desarrollo normal para la edad', 'Ninguno', 'Exploración física normal', 42.0, 145, '100/65', 36.3, 88, 'Continuar con alimentación balanceada'),
(4, 4, 1, 'Faringitis viral aguda', 'Dolor de garganta, fiebre leve', 'Faringe eritematosa', 72.0, 170, '118/75', 37.8, 82, 'Tratamiento sintomático, reposo e hidratación'),
(5, 5, 5, 'Control ginecológico normal', 'Ninguno', 'Exploración normal', 62.0, 158, '115/70', 36.4, 68, 'Control anual'),
(6, 6, 4, 'Nevus melanocíticos benignos', 'Preocupación por lunares', 'Múltiples nevus sin signos de malignidad', 78.0, 175, '125/82', 36.6, 74, 'Autoexamen mensual, control anual'),
(7, 7, 3, 'Paciente sana - Inmunización', 'Ninguno', 'Buen estado general', 18.5, 110, '95/60', 36.2, 95, 'Completar esquema de vacunación');

-- Fórmulas médicas
INSERT INTO formulas_medicas (historia_clinica_id, paciente_id, medico_id, codigo_qr, observaciones) VALUES
(2, 2, 2, 'FM-2026-001-QR', 'Tratamiento para hipertensión arterial'),
(4, 4, 1, 'FM-2026-002-QR', 'Tratamiento sintomático para faringitis');

-- Medicamentos en fórmulas
INSERT INTO formula_medicamentos (formula_id, medicamento_id, dosis, frecuencia, duracion, instrucciones) VALUES
(1, 7, '50mg', 'Cada 24 horas', '30 días', 'Tomar en ayunas con abundante agua'),
(2, 1, '500mg', 'Cada 8 horas', '5 días', 'Tomar con alimentos'),
(2, 5, '10mg', 'Cada 24 horas', '7 días', 'Tomar en la noche');

-- Resultados de laboratorio
INSERT INTO resultados_laboratorio (paciente_id, medico_id, tipo_examen, resultado, valores_referencia, fecha_examen) VALUES
(2, 2, 'Perfil lipídico', 'Colesterol total: 210 mg/dL\nHDL: 45 mg/dL\nLDL: 140 mg/dL\nTriglicéridos: 180 mg/dL', 'Colesterol total: <200 mg/dL\nHDL: >40 mg/dL\nLDL: <130 mg/dL\nTriglicéridos: <150 mg/dL', '2026-10-10'),
(4, 1, 'Hemograma completo', 'Leucocitos: 8,500/μL\nEritrocitos: 4.8 mill/μL\nHemoglobina: 14.2 g/dL\nPlaquetas: 250,000/μL', 'Leucocitos: 4,000-11,000/μL\nEritrocitos: 4.5-5.5 mill/μL\nHemoglobina: 13-17 g/dL\nPlaquetas: 150,000-400,000/μL', '2026-10-20'),
(5, 5, 'Citología cervical', 'Negativo para lesión intraepitelial o malignidad', 'Negativo', '2026-10-24');

-- Notificaciones
INSERT INTO notificaciones (usuario_id, tipo, titulo, mensaje, fecha_evento, leida) VALUES
(7, 'cita', 'Recordatorio de Cita', 'Tienes una cita el 15 de noviembre a las 10:00 AM con Dra. María Rodríguez', '2026-11-15 10:00:00', FALSE),
(8, 'cita', 'Recordatorio de Cita', 'Tienes una cita el 12 de noviembre a las 09:00 AM con Dr. José Martínez', '2026-11-12 09:00:00', FALSE),
(8, 'medicamento', 'Recordatorio de Medicamento', 'Es hora de tomar tu medicamento: Losartán 50mg', '2026-11-08 08:00:00', FALSE),
(10, 'resultado', 'Resultados de Laboratorio', 'Tus resultados de Hemograma están disponibles', '2026-10-20 00:00:00', TRUE),
(7, 'general', 'Bienvenida a MediConnect', 'Gracias por registrarte en nuestro sistema de salud', '2026-10-01 00:00:00', TRUE);

-- =====================================================
-- FIN DE LA BASE DE DATOS
-- =====================================================
