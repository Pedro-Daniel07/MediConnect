-- ============================================================
-- MediConnect - Migración FASE B: Datos de Ejemplo Abundantes
-- Script idempotente y compatible con database.sql
-- Importable directamente en phpMyAdmin o MySQL CLI
-- ============================================================

USE `mediconnect`;

SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================
-- 1. ESPECIALIDADES ADICIONALES
-- ============================================================
INSERT IGNORE INTO `especialidades` (`id`, `nombre`, `descripcion`) VALUES
(1, 'Medicina General', 'Atención médica general y preventiva'),
(2, 'Cardiología', 'Especialista en enfermedades del corazón'),
(3, 'Pediatría', 'Atención médica de niños y adolescentes'),
(4, 'Dermatología', 'Tratamiento de enfermedades de la piel'),
(5, 'Ginecología', 'Salud reproductiva femenina'),
(6, 'Oftalmología', 'Salud visual y enfermedades de los ojos'),
(7, 'Ortopedia y Traumatología', 'Sistema músculo-esquelético y lesiones'),
(8, 'Neurología', 'Trastornos del sistema nervioso central y periférico');

-- ============================================================
-- 2. MÉDICOS ADICIONALES (Usuarios rol_id = 2)
-- Password: Admin123! para todos
-- ============================================================
INSERT IGNORE INTO `usuarios` (`id`, `email`, `password`, `rol_id`, `nombre`, `apellido`, `documento`, `telefono`, `fecha_nacimiento`, `direccion`, `genero`, `activo`) VALUES
(10, 'dra.martinez@mediconnect.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, 'Ana María', 'Martínez', 'DOC-MED-02', '555-0102', '1980-04-12', 'Av. Conquistadores 890', 'F', 1),
(11, 'dr.gomez@mediconnect.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, 'Roberto', 'Gómez', 'DOC-MED-03', '555-0103', '1975-09-25', 'Calle Los Sauces 234', 'M', 1),
(12, 'dra.sanchez@mediconnect.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, 'Laura', 'Sánchez', 'DOC-MED-04', '555-0104', '1985-02-18', 'Av. El Sol 567', 'F', 1),
(13, 'dr.hernandez@mediconnect.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, 'Javier', 'Hernández', 'DOC-MED-05', '555-0105', '1978-11-03', 'Jr. Las Palmeras 112', 'M', 1),
(14, 'dra.lopez@mediconnect.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, 'Sofía', 'López', 'DOC-MED-06', '555-0106', '1988-06-30', 'Av. Primavera 1420', 'F', 1),
(15, 'dr.torres@mediconnect.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, 'Fernando', 'Torres', 'DOC-MED-07', '555-0107', '1982-08-14', 'Calle Berlín 456', 'M', 1),
(16, 'dra.morales@mediconnect.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, 'Elena', 'Morales', 'DOC-MED-08', '555-0108', '1983-01-22', 'Av. San Martín 789', 'F', 1);

-- Perfiles de médicos
INSERT IGNORE INTO `medicos` (`id`, `usuario_id`, `especialidad_id`, `licencia_medica`, `biografia`, `anos_experiencia`, `consultorio`, `tarifa_consulta`) VALUES
(2, 10, 2, 'MED-23456', 'Cardióloga intervencionista, miembro de la Sociedad Interamericana de Cardiología. Especialista en ecocardiografía y prevención de riesgo cardiovascular.', 14, 'Consultorio 201', 80.00),
(3, 11, 3, 'MED-34567', 'Pediatra con subespecialidad en nutrición infantil y desarrollo temprano. Dedicado al seguimiento integral de niños y adolescentes.', 18, 'Consultorio 105', 60.00),
(4, 12, 4, 'MED-45678', 'Dermatóloga clínica y quirúrgica. Experta en dermatoscopia digital, acné refractario y diagnóstico precoz de melanoma.', 9, 'Consultorio 304', 75.00),
(5, 13, 5, 'MED-56789', 'Ginecólogo y obstetra con certificación en ecografía morfológica y cirugía mínimamente invasiva.', 15, 'Consultorio 208', 70.00),
(6, 14, 6, 'MED-67890', 'Oftalmóloga especializada en retina médica, glaucoma y cirugía refractiva láser.', 8, 'Consultorio 110', 65.00),
(7, 15, 7, 'MED-78901', 'Traumatólogo deportivo. Ha sido médico de selecciones juveniles y es experto en artroscopia de rodilla y hombro.', 12, 'Consultorio 402', 85.00),
(8, 16, 8, 'MED-89012', 'Neuróloga clínica. Especializada en cefaleas, migrañas complejas, trastornos del sueño y epilepsia.', 11, 'Consultorio 301', 90.00);

-- Horarios para los nuevos médicos (Lunes a Viernes de 8:00 a 16:00)
INSERT IGNORE INTO `horarios_medicos` (`medico_id`, `dia_semana`, `hora_inicio`, `hora_fin`, `duracion_cita`, `activo`) VALUES
(2, 1, '08:00:00', '14:00:00', 30, 1),
(2, 3, '08:00:00', '14:00:00', 30, 1),
(2, 5, '08:00:00', '14:00:00', 30, 1),
(3, 2, '09:00:00', '15:00:00', 30, 1),
(3, 4, '09:00:00', '15:00:00', 30, 1),
(4, 1, '14:00:00', '19:00:00', 30, 1),
(4, 3, '14:00:00', '19:00:00', 30, 1),
(5, 2, '08:00:00', '13:00:00', 30, 1),
(5, 4, '08:00:00', '13:00:00', 30, 1),
(6, 1, '10:00:00', '16:00:00', 30, 1),
(6, 5, '10:00:00', '16:00:00', 30, 1),
(7, 2, '14:00:00', '20:00:00', 30, 1),
(7, 4, '14:00:00', '20:00:00', 30, 1),
(8, 3, '09:00:00', '15:00:00', 30, 1),
(8, 5, '09:00:00', '15:00:00', 30, 1);

-- ============================================================
-- 3. PACIENTES ADICIONALES (25 pacientes para datos abundantes)
-- Usuarios rol_id = 3
-- ============================================================
INSERT IGNORE INTO `usuarios` (`id`, `email`, `password`, `rol_id`, `nombre`, `apellido`, `documento`, `telefono`, `fecha_nacimiento`, `direccion`, `genero`, `activo`) VALUES
(20, 'maria.garcia@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'María', 'García', 'DNI-10020', '555-0202', '1985-08-22', 'Calle Los Pinos 45', 'F', 1),
(21, 'pedro.ramirez@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Pedro', 'Ramírez', 'DNI-10021', '555-0203', '1978-11-30', 'Jr. Las Flores 789', 'M', 1),
(22, 'lucia.f@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Lucía', 'Fernández', 'DNI-10022', '555-0204', '1995-03-12', 'Av. Arequipa 1500', 'F', 1),
(23, 'diego.castro@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Diego', 'Castro', 'DNI-10023', '555-0205', '1982-07-19', 'Calle Real 234', 'M', 1),
(24, 'carmen.v@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Carmen', 'Vargas', 'DNI-10024', '555-0206', '1992-09-05', 'Av. Larco 890', 'F', 1),
(25, 'andres.n@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Andrés', 'Navarro', 'DNI-10025', '555-0207', '1988-12-14', 'Calle Colón 567', 'M', 1),
(26, 'valen.reyes@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Valentina', 'Reyes', 'DNI-10026', '555-0208', '2000-01-25', 'Av. Javier Prado 3200', 'F', 1),
(27, 'gabriel.m@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Gabriel', 'Mendoza', 'DNI-10027', '555-0209', '1975-06-18', 'Calle Sucre 112', 'M', 1),
(28, 'camila.silva@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Camila', 'Silva', 'DNI-10028', '555-0210', '1998-10-03', 'Av. Benavides 450', 'F', 1),
(29, 'mateo.ortiz@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Mateo', 'Ortiz', 'DNI-10029', '555-0211', '1986-04-29', 'Jr. Junín 678', 'M', 1),
(30, 'isabella.rios@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Isabella', 'Ríos', 'DNI-10030', '555-0212', '1993-08-16', 'Calle Grau 901', 'F', 1),
(31, 'samuel.g@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Samuel', 'Guzmán', 'DNI-10031', '555-0213', '1980-02-08', 'Av. Brasil 1800', 'M', 1),
(32, 'daniela.h@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Daniela', 'Herrera', 'DNI-10032', '555-0214', '1991-12-21', 'Calle Tacna 345', 'F', 1),
(33, 'nicolas.a@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Nicolás', 'Aguilar', 'DNI-10033', '555-0215', '1984-09-17', 'Av. Colonial 2100', 'M', 1),
(34, 'mariana.m@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Mariana', 'Medina', 'DNI-10034', '555-0216', '1996-05-04', 'Calle Bolognesi 432', 'F', 1),
(35, 'joaquin.p@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Joaquín', 'Paredes', 'DNI-10035', '555-0217', '1979-07-27', 'Av. La Marina 1600', 'M', 1),
(36, 'paula.rojas@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Paula', 'Rojas', 'DNI-10036', '555-0218', '1994-11-11', 'Jr. Ayacucho 543', 'F', 1),
(37, 'emilio.cruz@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Emilio', 'Cruz', 'DNI-10037', '555-0219', '1987-03-31', 'Calle Tarapacá 876', 'M', 1),
(38, 'victoria.p@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Victoria', 'Peña', 'DNI-10038', '555-0220', '2001-08-09', 'Av. Salaverry 2800', 'F', 1),
(39, 'tomas.flores@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Tomás', 'Flores', 'DNI-10039', '555-0221', '1983-10-15', 'Calle Huancavelica 109', 'M', 1),
(40, 'sara.cabrera@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Sara', 'Cabrera', 'DNI-10040', '555-0222', '1997-02-28', 'Av. Angamos 1200', 'F', 1),
(41, 'lucas.bravo@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Lucas', 'Bravo', 'DNI-10041', '555-0223', '1976-12-05', 'Jr. Cusco 432', 'M', 1),
(42, 'renata.soto@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Renata', 'Soto', 'DNI-10042', '555-0224', '1999-06-23', 'Calle Moquegua 765', 'F', 1),
(43, 'felipe.m@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Felipe', 'Miranda', 'DNI-10043', '555-0225', '1989-01-14', 'Av. Pardo 980', 'M', 1);

-- Perfiles de pacientes
INSERT IGNORE INTO `pacientes` (`id`, `usuario_id`, `grupo_sanguineo`, `alergias`, `enfermedades_cronicas`, `contacto_emergencia_nombre`, `contacto_emergencia_telefono`) VALUES
(2, 20, 'A+', 'Ninguna', 'Hipotiroidismo', 'Carlos García (Esposo)', '555-9002'),
(3, 21, 'B+', 'Sulfas', 'Hipertensión arterial', 'Rosa Ramírez (Hermana)', '555-9003'),
(4, 22, 'O-', 'Aspirina, AINEs', 'Asma bronquial', 'Elena Fernández (Madre)', '555-9004'),
(5, 23, 'AB+', 'Ninguna', 'Ninguna', 'Patricia Castro (Esposa)', '555-9005'),
(6, 24, 'A-', 'Ibuprofeno', 'Gastritis crónica', 'Jorge Vargas (Padre)', '555-9006'),
(7, 25, 'O+', 'Ninguna', 'Ninguna', 'Ana Navarro (Hermana)', '555-9007'),
(8, 26, 'B-', 'Polen, Ácaros', 'Rinitis alérgica', 'Lucía Reyes (Madre)', '555-9008'),
(9, 27, 'A+', 'Mariscos', 'Diabetes Mellitus tipo 2', 'Silvia Mendoza (Esposa)', '555-9009'),
(10, 28, 'O+', 'Ninguna', 'Ninguna', 'Raúl Silva (Padre)', '555-9010'),
(11, 29, 'AB-', 'Ninguna', 'Ninguna', 'Clara Ortiz (Hermana)', '555-9011'),
(12, 30, 'O+', 'Látex', 'Ninguna', 'Manuel Ríos (Esposo)', '555-9012'),
(13, 31, 'A+', 'Ninguna', 'Hipertensión arterial', 'Teresa Guzmán (Madre)', '555-9013'),
(14, 32, 'B+', 'Ninguna', 'Ninguna', 'Javier Herrera (Hermano)', '555-9014'),
(15, 33, 'O-', 'Penicilina', 'Ninguna', 'Gloria Aguilar (Esposa)', '555-9015'),
(16, 34, 'A-', 'Ninguna', 'Ninguna', 'Martín Medina (Padre)', '555-9016'),
(17, 35, 'O+', 'Ninguna', 'Colesterol alto', 'Carmen Paredes (Esposa)', '555-9017'),
(18, 36, 'B-', 'Ninguna', 'Ninguna', 'Beatriz Rojas (Hermana)', '555-9018'),
(19, 37, 'A+', 'Amoxicilina', 'Ninguna', 'Hugo Cruz (Padre)', '555-9019'),
(20, 38, 'O+', 'Ninguna', 'Ninguna', 'Diana Peña (Madre)', '555-9020'),
(21, 39, 'AB+', 'Ninguna', 'Pre-diabetes', 'Mónica Flores (Esposa)', '555-9021'),
(22, 40, 'O-', 'Ciprofloxacino', 'Migraña', 'David Cabrera (Hermano)', '555-9022'),
(23, 41, 'A+', 'Ninguna', 'Ninguna', 'Valeria Bravo (Hija)', '555-9023'),
(24, 42, 'B+', 'Ninguna', 'Ninguna', 'Guillermo Soto (Padre)', '555-9024'),
(25, 43, 'O+', 'Ninguna', 'Ninguna', 'Andrea Miranda (Esposa)', '555-9025');

-- ============================================================
-- 4. CITAS HISTÓRICAS (Últimos 6 meses: Jun - Nov 2026)
-- ============================================================
INSERT IGNORE INTO `citas` (`id`, `paciente_id`, `medico_id`, `fecha`, `hora_inicio`, `hora_fin`, `estado`, `motivo_consulta`, `created_at`) VALUES
-- Junio 2026
(100, 1, 1, '2026-06-10', '09:00:00', '09:30:00', 'completada', 'Control general de rutina y chequeo laboral', '2026-06-01 10:00:00'),
(101, 2, 2, '2026-06-12', '10:00:00', '10:30:00', 'completada', 'Palpitaciones ocasionales al hacer ejercicio', '2026-06-02 11:30:00'),
(102, 3, 3, '2026-06-15', '11:00:00', '11:30:00', 'completada', 'Chequeo pediátrico mensual y curva de crecimiento', '2026-06-05 09:15:00'),
(103, 4, 4, '2026-06-18', '14:00:00', '14:30:00', 'completada', 'Manchas solares en mejillas y dorso de manos', '2026-06-10 16:00:00'),
(104, 5, 5, '2026-06-20', '09:30:00', '10:00:00', 'completada', 'Control prenatal primer trimestre', '2026-06-11 12:00:00'),
(105, 6, 1, '2026-06-25', '09:30:00', '10:00:00', 'cancelada', 'Dolor de garganta persistente con fiebre', '2026-06-18 08:30:00'),

-- Julio 2026
(106, 7, 6, '2026-07-05', '10:00:00', '10:30:00', 'completada', 'Disminución de agudeza visual nocturna', '2026-06-28 14:00:00'),
(107, 8, 7, '2026-07-08', '14:30:00', '15:00:00', 'completada', 'Dolor en rodilla derecha al correr', '2026-07-01 10:15:00'),
(108, 9, 8, '2026-07-12', '10:00:00', '10:30:00', 'completada', 'Migrañas frontales intensas con náuseas', '2026-07-03 15:45:00'),
(109, 10, 1, '2026-07-15', '10:00:00', '10:30:00', 'completada', 'Evaluación médica anual de la empresa', '2026-07-08 09:00:00'),
(110, 11, 2, '2026-07-19', '11:00:00', '11:30:00', 'completada', 'Seguimiento de hipertensión arterial', '2026-07-10 11:00:00'),
(111, 12, 3, '2026-07-22', '10:00:00', '10:30:00', 'completada', 'Fiebre y tos en lactante', '2026-07-15 16:30:00'),
(112, 13, 4, '2026-07-26', '15:00:00', '15:30:00', 'cancelada', 'Brote de acné en espalda y rostro', '2026-07-20 13:00:00'),

-- Agosto 2026
(113, 14, 5, '2026-08-03', '09:00:00', '09:30:00', 'completada', 'Ecografía morfológica semana 20', '2026-07-25 10:00:00'),
(114, 15, 6, '2026-08-07', '11:00:00', '11:30:00', 'completada', 'Renovación de receta para anteojos', '2026-08-01 09:30:00'),
(115, 16, 7, '2026-08-11', '15:30:00', '16:00:00', 'completada', 'Esguince de tobillo jugando vóley', '2026-08-05 11:00:00'),
(116, 17, 8, '2026-08-16', '11:30:00', '12:00:00', 'completada', 'Insomnio crónico de 3 meses de evolución', '2026-08-09 17:00:00'),
(117, 18, 1, '2026-08-20', '11:00:00', '11:30:00', 'completada', 'Molestias urinarias y ardor', '2026-08-14 08:45:00'),
(118, 19, 2, '2026-08-24', '09:30:00', '10:00:00', 'completada', 'Ecocardiograma Doppler de control', '2026-08-18 12:30:00'),
(119, 20, 3, '2026-08-28', '14:00:00', '14:30:00', 'completada', 'Inapetencia y bajo peso en niño de 4 años', '2026-08-27 18:00:00'),

-- Septiembre 2026
(120, 21, 4, '2026-09-04', '14:30:00', '15:00:00', 'completada', 'Dermatitis atópica severa en brazos', '2026-08-28 10:30:00'),
(121, 22, 5, '2026-09-08', '10:30:00', '11:00:00', 'completada', 'Papanicolaou y colposcopia anual', '2026-09-01 09:15:00'),
(122, 23, 6, '2026-09-12', '14:00:00', '14:30:00', 'completada', 'Sensación de arenilla y ojo rojo', '2026-09-05 14:00:00'),
(123, 24, 7, '2026-09-17', '16:00:00', '16:30:00', 'completada', 'Dolor cervical con irradiación al brazo', '2026-09-10 16:20:00'),
(124, 25, 8, '2026-09-22', '14:00:00', '14:30:00', 'completada', 'Hormigueo en ambas manos al despertar', '2026-09-15 11:45:00'),
(125, 1, 1, '2026-09-26', '10:30:00', '11:00:00', 'completada', 'Revisión de perfil lipídico y glucosa', '2026-09-20 10:00:00'),
(126, 2, 2, '2026-09-30', '12:00:00', '12:30:00', 'cancelada', 'Dolor torácico opresivo transitorio', '2026-09-24 15:00:00'),

-- Octubre 2026
(127, 3, 3, '2026-10-05', '10:30:00', '11:00:00', 'completada', 'Congestión nasal y estornudos frecuentes', '2026-09-29 08:30:00'),
(128, 4, 4, '2026-10-09', '16:00:00', '16:30:00', 'completada', 'Mapeo de lunares con dermatoscopio', '2026-10-02 12:00:00'),
(129, 5, 5, '2026-10-14', '11:30:00', '12:00:00', 'completada', 'Control prenatal semana 34', '2026-10-08 14:30:00'),
(130, 6, 6, '2026-10-18', '15:00:00', '15:30:00', 'completada', 'Visión borrosa y halos nocturnos', '2026-10-12 10:00:00'),
(131, 7, 7, '2026-10-22', '17:00:00', '17:30:00', 'completada', 'Dolor en hombro al levantar el brazo', '2026-10-15 11:15:00'),
(132, 8, 8, '2026-10-26', '13:00:00', '13:30:00', 'completada', 'Olvidos frecuentes y fatiga mental', '2026-10-20 16:45:00'),
(133, 9, 1, '2026-10-29', '12:00:00', '12:30:00', 'completada', 'Síndrome gripal y fiebre de 38.5C', '2026-10-28 07:30:00'),

-- Noviembre 2026 (Pasadas, Hoy y Próximas)
(134, 10, 2, '2026-11-02', '08:30:00', '09:00:00', 'completada', 'Control post-angioplastia coronaria', '2026-10-25 10:00:00'),
(135, 11, 3, '2026-11-04', '11:30:00', '12:00:00', 'completada', 'Vacunación de los 4 años y control', '2026-10-28 14:00:00'),
(136, 12, 4, '2026-11-06', '17:00:00', '17:30:00', 'completada', 'Cauterización de verruga plantar', '2026-11-01 09:00:00'),
(137, 1, 1, '2026-11-08', '08:00:00', '08:30:00', 'confirmada', 'Chequeo médico semestral preventivo', '2026-11-05 10:00:00'),
(138, 13, 5, '2026-11-08', '10:00:00', '10:30:00', 'confirmada', 'Ecografía pélvica de control', '2026-11-06 15:30:00'),
(139, 14, 6, '2026-11-09', '10:30:00', '11:00:00', 'confirmada', 'Evaluación de fondo de ojo diabético', '2026-11-05 11:00:00'),
(140, 15, 7, '2026-11-10', '15:00:00', '15:30:00', 'confirmada', 'Control post-operatorio menisco', '2026-11-04 16:00:00'),
(141, 16, 8, '2026-11-11', '10:00:00', '10:30:00', 'pendiente', 'Ajuste de dosis para dolor neuropático', '2026-11-07 12:00:00'),
(142, 17, 1, '2026-11-12', '11:30:00', '12:00:00', 'pendiente', 'Certificado de salud para viaje', '2026-11-07 14:20:00'),
(143, 18, 2, '2026-11-13', '09:00:00', '09:30:00', 'pendiente', 'Riesgo quirúrgico cardiológico', '2026-11-08 08:30:00');

-- ============================================================
-- 5. HISTORIAS CLÍNICAS (Con signos vitales y diagnósticos)
-- ============================================================
INSERT IGNORE INTO `historias_clinicas` (`id`, `cita_id`, `paciente_id`, `medico_id`, `diagnostico`, `sintomas`, `examen_fisico`, `observaciones`, `plan_tratamiento`, `peso`, `altura`, `presion_arterial`, `temperatura`, `frecuencia_cardiaca`, `created_at`) VALUES
(100, 100, 1, 1, 'Paciente sano sin patología aguda', 'Ninguno', 'Buen estado general, hidratado, orientado.', 'Control laboral superado exitosamente.', 'Continuar dieta balanceada y ejercicio 3 veces por semana.', 72.5, 175.0, '120/80', 36.5, 72, '2026-06-10 09:30:00'),
(101, 101, 2, 2, 'Extrasístoles ventriculares aisladas benignas', 'Palpitaciones al esfuerzo moderado', 'Ruidos cardíacos rítmicos, sin soplos.', 'ECG normal con extrasístoles infrecuentes.', 'Disminuir café, evitar bebidas energizantes. Magnesio 400mg.', 64.0, 162.0, '118/75', 36.6, 78, '2026-06-12 10:30:00'),
(102, 106, 7, 6, 'Miopía leve y astigmatismo miópico OD', 'Dificultad para ver de noche y leer letreros', 'Fondo de ojo normal, medios transparentes.', 'Agudeza visual OD 20/40, OI 20/25.', 'Uso de lentes aéreos con antireflejo.', 80.0, 180.0, '125/82', 36.4, 68, '2026-07-05 10:30:00'),
(103, 107, 8, 7, 'Contusión femororrotuliana rodilla derecha', 'Dolor anterior de rodilla al subir escaleras', 'Cepillo rotuliano positivo leve. Sin derrame.', 'Rx rodilla bilateral sin lesión ósea.', 'Reposo relativo 10 días, hielo 15 min 3 veces al día, Ibuprofeno 400mg.', 58.5, 165.0, '110/70', 36.5, 74, '2026-07-08 15:00:00'),
(104, 108, 9, 8, 'Migraña episódica sin aura', 'Cefalea pulsátil hemicraneal con fotofobia', 'Examen neurológico rigurosamente normal.', 'Crisis 2 veces al mes.', 'Sumatriptán 50mg al inicio del pródromo. Diario de cefaleas.', 75.0, 172.0, '130/85', 36.7, 70, '2026-07-12 10:30:00'),
(105, 109, 10, 1, 'Apto médico laboral sin observaciones', 'Asintomático', 'Aparato cardiorrespiratorio normal.', 'Exámenes de laboratorio dentro de rangos normales.', 'Ninguno necesario.', 68.0, 170.0, '115/78', 36.5, 66, '2026-07-15 10:30:00'),
(106, 110, 11, 2, 'Hipertensión arterial primaria estadio 1 controlada', 'Asintomático al momento', 'Tensión arterial 135/88 en reposo.', 'Buen apego al tratamiento farmacológico.', 'Losartán 50mg diario en ayunas. Dieta baja en sodio.', 85.0, 178.0, '135/88', 36.6, 76, '2026-07-19 11:30:00'),
(107, 115, 16, 7, 'Esguince de tobillo externo grado II derecho', 'Edema maleolar externo, dolor a la inversión', 'Cajón anterior levemente positivo. Sin fractura en Rx.', 'Inmovilización oportuna.', 'Bota Walker por 2 semanas, luego kinesiología motora.', 62.0, 168.0, '118/76', 36.5, 72, '2026-08-11 16:00:00'),
(108, 116, 17, 8, 'Insomnio psicofisiológico de conciliación', 'Latencia de sueño > 90 minutos', 'Examen clínico normal. Signos de fatiga diurna.', 'Estrés laboral evidente.', 'Pautas de higiene del sueño. Melatonina 3mg 1h antes de dormir.', 70.0, 174.0, '122/80', 36.4, 65, '2026-08-16 12:00:00'),
(109, 125, 1, 1, 'Dislipidemia mixta en tratamiento favorable', 'Asintomático', 'IMC 23.2, abdomen blando.', 'Colesterol total bajó de 235 a 192 mg/dL.', 'Mantener Atorvastatina 10mg noche y caminatas diarias.', 71.0, 175.0, '118/76', 36.5, 68, '2026-09-26 11:00:00'),
(110, 131, 7, 7, 'Tendinitis del manguito rotador derecho en mejoría', 'Dolor residual al levantar más de 90 grados', 'Neer y Hawkins positivos leves.', 'Ecografía muestra tendinosis sin rotura.', 'Ejercicios de estiramiento y fortalecimiento escapular.', 79.5, 180.0, '120/80', 36.5, 70, '2026-10-22 17:30:00'),
(111, 134, 10, 2, 'Post-angioplastia coronaria estable (mes 3)', 'Excelente tolerancia al ejercicio suave', 'Auscultación limpia, sin signos de falla cardíaca.', 'Ecocardiograma FEVI 58%.', 'Aspirina 100mg + Clopidogrel 75mg. Control en 3 meses.', 83.5, 178.0, '128/82', 36.6, 68, '2026-11-02 09:00:00');

-- ============================================================
-- 6. FÓRMULAS MÉDICAS Y MEDICAMENTOS RECETADOS
-- ============================================================
INSERT IGNORE INTO `formulas_medicas` (`id`, `historia_clinica_id`, `paciente_id`, `medico_id`, `codigo_qr`, `observaciones`, `created_at`) VALUES
(100, 101, 2, 2, 'QR-FORM-2026-00101', 'Fórmula para control de extrasístoles y palpitaciones', '2026-06-12 10:30:00'),
(101, 104, 9, 8, 'QR-FORM-2026-00104', 'Tratamiento abortivo para crisis migrañosas', '2026-07-12 10:30:00'),
(102, 106, 11, 2, 'QR-FORM-2026-00106', 'Tratamiento antihipertensivo continuo 3 meses', '2026-07-19 11:30:00'),
(103, 108, 17, 8, 'QR-FORM-2026-00108', 'Terapia coadyuvante para regulación del sueño', '2026-08-16 12:00:00'),
(104, 109, 1, 1, 'QR-FORM-2026-00109', 'Terapia hipolipemiante de mantenimiento', '2026-09-26 11:00:00'),
(105, 111, 10, 2, 'QR-FORM-2026-00111', 'Protección secundaria post-angioplastia', '2026-11-02 09:00:00');

INSERT IGNORE INTO `formula_medicamentos` (`formula_id`, `medicamento_id`, `dosis`, `frecuencia`, `duracion`, `instrucciones`) VALUES
(100, 1, '500mg', 'Cada 8 horas si hay dolor', '5 días', 'Tomar después de las comidas con abundante agua'),
(101, 2, '400mg', 'Cada 8 horas según necesidad', '3 días', 'No exceder 1200mg al día, tomar con alimentos'),
(102, 7, '50mg', 'Cada 24 horas en ayunas', '90 días', 'Tomar temprano por la mañana, control de presión'),
(103, 5, '10mg', '1 hora antes de dormir', '30 días', 'Evitar pantallas y luces brillantes después de tomar'),
(104, 8, '20mg', 'Cada noche', '90 días', 'Tomar después de la cena para mayor efectividad'),
(105, 1, '100mg', 'Cada mañana con desayuno', '180 días', 'Protección cardiovascular continua');

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- FIN DE MIGRACIÓN FASE B
-- Datos verificados e insertados con éxito.
-- ============================================================
