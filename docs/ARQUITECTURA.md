# MediConnect – Resumen de Controladores, Modelos y Config

## Controladores

*AdminController.php*  
Purpose: admin panel. Class AdminController with Usuario & Medico models.  
Key methods: usuarios() / medicos() list with role guard; especialidades() CRUD specialties; reportes() stats + citas/especialidades queries; auditoria() live audit log viewer; listaEspera() smart waitlist assign/discard; exportarCitas() / exportarPacientes() CSV exports with UTF-8 BOM.

*ApiController.php*  
Purpose: AJAX endpoints. Class ApiController with Cita model.  
obtenerHorarios() returns free slots for doctor/date; contarNotificaciones() returns unread count for session user.

*AuthController.php*  
Purpose: authentication. Class AuthController with Usuario, Paciente, IntentoLogin.  
login() / procesarLogin() with CSRF, rate-limit via IntentoLogin, session_regenerate_id, last access update; registro() / procesarRegistro() validates, checks duplicates, creates user + patient; logout() POST+CSRF destroys session; recuperar() stub.

*ChatController.php*  
Purpose: clinical messaging patient-doctor. Class ChatController.  
index() builds contacts per role, marks read, loads history; apiMensajes() GET polling; apiEnviar() POST send with CSRF and contact authorization; usuarioPuedeContactar() enforces médico↔paciente relationship and prior cita/message.

*CitaController.php*  
Purpose: appointment workflow for patients. Class CitaController with Cita, Paciente, Medico.  
misCitas() shows list and handles cancel with transaction: cancel + audit + auto-assign from lista_espera + notification; agendar() / procesarAgendarCita() validates CSRF, future date, availability and creates notification.

*DashboardController.php*  
Purpose: role dashboards. Class DashboardController.  
index() dispatches to dashboardPaciente(), dashboardMedico(), dashboardAdmin(). Each aggregates upcoming/pending/completed citas and stats via models.

*MedicoController.php*  
Purpose: doctor UI. Class MedicoController with Medico, Cita.  
agenda() week view; atenderCita() loads cita + history, processes atención creating historias_clinicas, optional formulas_medicas + formula_medicamentos in transaction, then completar cita; pacientes() list attended patients; verHistoria() secured history view.

*NotificacionController.php*  
Purpose: notifications. Class NotificacionController.  
index() lists user notifications; POST marcar_leidas with CSRF updates leida=1.

*OrientadorController.php*  
Purpose: symptom triage. Class OrientadorController.  
index() displays form, evaluates sintomas via heuristic rules; evaluarSintomas() returns especialidad, urgencia and recomendación for urgent / cardiology / dermatology / general cases.

*PacienteController.php*  
Purpose: patient features. Class PacienteController with Paciente model.  
historial(), formulas(), resultados(), lineaTiempo() with IMC calc; carneDigital() / vacunacion() with audit and counters; signosVitales() shows history and POST guardar_signos creates signos_vitales with IMC, clasificación and puntaje_salud, audit. obtenerPacienteAutorizado() enforces médico/admin permission.

*PerfilController.php*  
Purpose: profile management. Class PerfilController with Usuario, Paciente.  
index() shows profile; procesarActualizacion() handles datos_personales, datos_medicos and cambiar_password with CSRF and verification.

*TelemedicinaController.php*  
Purpose: virtual consultation. Class TelemedicinaController.  
index() validates cita ownership per role, loads cita data for sala; guardarNotas() AJAX saves notas_medicas for doctor-owned cita with audit.

## Modelos

*Cita.php* – Class Cita. crear, verificarDisponibilidad with overlap check, obtenerPorPaciente / obtenerPorMedico with joins, obtenerPorId with security param, actualizarEstado, cancelar with state guard, confirmar, completar, contarPorEstado, obtenerHorariosDisponibles generating slots from horarios_medicos minus occupied citas.

*IntentoLogin.php* – Class IntentoLogin. Rate limiting. tiempoEspera(), registrarFallo() with ON DUPLICATE KEY UPDATE, limpiar(), obtenerClaves() SHA-256 of email/ip. Config: MAX_FALLOS 5, VENTANA 300s, BLOQUEO 30s. Fail-closed on error.

*Medico.php* – Class Medico. obtenerPorUsuarioId / obtenerPorId with joins to usuarios & especialidades; listar with filters; obtenerEspecialidades; obtenerPacientes distinct patients with last cita.

*Paciente.php* – Class Paciente. crear, obtenerPorUsuarioId / obtenerPorId with joins, actualizar medical data, tieneCitaConMedico check, obtenerHistoriaClinica with limit, obtenerLineaTiempo, listar con paginación.

*Usuario.php* – Class Usuario. crear with password_hash, buscarPorEmail / buscarPorId with role join, emailExiste / documentoExiste, actualizarUltimoAcceso, actualizar, cambiarPassword, listar / contar with filters, obtenerRolId.

## Config

*config/database.php*  
Singleton Database class using PDO MySQL. Loads DB constants from database.local.php if present, else defaults for localhost. Defines DSN, ERRMODE_EXCEPTION, FETCH_ASSOC, EMULATE_PREPARES false. getInstance()/getConnection().

*config/config.php*  
Site constants, BASE_URL, asset() helper, America/Bogota timezone, secure session settings, periodic session_regenerate_id, CSRF token generation. Helpers: verificar_csrf, esta_autenticado, tiene_rol, redirigir, e, formatear_fecha, calcular_edad, calcular_imc, clasificar_imc, calcular_puntaje_salud, registrar_auditoria.

## Alcance y límites

- **No se revisó directamente:** `database.local.php`, `database.example.php`, archivos de vistas, migraciones (`migracion_fase_b.sql`, `migracion_fase_c.sql`, `migracion_login_seguro.sql`, `migracion_citas_reutilizar_cupo.sql`, `database.sql`), ni el esquema real de tablas.
- **Deducción de esquema:** Los nombres de tablas y columnas se deducen del código PHP (consultas SQL embebidas) e inteligencia del código. Cualquier desajuste con el esquema real de la base de datos podría causar errores de ejecución.