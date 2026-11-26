# Sistema de Gestión de Salud MediConnect

Sistema integral de gestión médica y hospitalaria desarrollado en PHP puro (arquitectura MVC sin frameworks), MySQL y JavaScript vanilla. Diseñado para digitalizar el ciclo completo de atención clínica con una interfaz de salud moderna y segura.

---

## 🌟 Características del Sistema

### 👤 Módulo del Paciente
- **Autenticación Segura y Perfil:** Registro con validaciones completas, inicio de sesión protegido y gestión de datos personales/médicos.
- **Gestión Integral de Citas:** Agendamiento en tiempo real con filtrado inteligente de médicos y horarios disponibles, cancelación con control de estado y reprogramación.
- **Historial Clínico Digital:** Consulta cronológica de atenciones, diagnósticos, síntomas, antecedentes y planes de tratamiento.
- **Fórmulas Médicas y Prescripciones:** Visualización de recetas con medicamentos prescritos (dosis, frecuencia y duración) y código QR único verificable.
- **Resultados de Laboratorio:** Consulta detallada de exámenes médicos con rangos de referencia y observaciones.
- **Signos Vitales y Puntaje de Salud:** Registro de parámetros biométricos (peso, altura, presión arterial, frecuencia cardíaca/respiratoria, temperatura, saturación de O2 y glucosa), cálculo automático de IMC y cálculo del Puntaje de Salud (0-100).
- **Carné Digital de Salud (Wallet):** Carné médico virtual imprimible con código QR dinámico, grupo sanguíneo, alergias y contacto de emergencia.
- **Carné Digital de Vacunación:** Registro del esquema de inmunización (dosis, fecha de aplicación, lote, profesional y alertas de próximas dosis).
- **Línea de Tiempo de Salud:** Seguimiento gráfico interactivo de la evolución antropométrica y cardiovascular con Chart.js.
- **Orientador de Síntomas:** Herramienta de orientación preliminar que evalúa síntomas y recomienda la especialidad adecuada (con descargo de responsabilidad médica visible).

### 👨‍⚕️ Módulo del Médico
- **Agenda y Programación Semanal:** Visualización organizada de citas programadas, pendientes y confirmadas por día y horario.
- **Atención de Consultas en Vivo:** Registro de atención clínica que incluye examen físico, signos vitales, diagnóstico, observaciones y emisión de recetas en una misma transacción.
- **Prescripción Farmacológica:** Generación de fórmulas médicas asociadas a la consulta con código QR único.
- **Directorio de Pacientes Atendidos:** Acceso protegido a la historia clínica de pacientes que hayan tenido consultas previas con el profesional.

### 👨‍💼 Módulo del Administrador
- **Centro de Mando y Auditoría en Vivo:** Monitoreo en tiempo real de eventos del sistema (inicios de sesión, acciones clínicas, exportaciones, asignaciones) con registro de IP y agente de usuario.
- **Gestión de Usuarios y Médicos:** Administración de roles, credenciales, información profesional, licencias médicas, tarifas y consultorios.
- **Gestión de Especialidades:** Alta, edición y activación/desactivación de especialidades médicas.
- **Lista de Espera Inteligente:** Asignación automática de cupos liberados tras cancelaciones y panel para asignación o descarte manual por prioridad (alta, media, baja).
- **Reportes y Estadísticas:** Métricas operativas consolidadas, distribución de citas por estado y gráficos de demanda por especialidad.
- **Exportación de Datos:** Descarga directa de reportes de citas y base de pacientes en formato CSV con codificación UTF-8 compatible con Microsoft Excel.

### ⚡ Funciones Transversales
- 💬 **Chat Clínico Paciente–Médico:** Mensajería segura bidireccional con persistencia en base de datos, autorización estricta de contactos y actualización continua vía AJAX.
- 📹 **Telemedicina (Videoconsulta WebRTC):** Sala virtual para consultas a distancia con transmisión cifrada de audio/video, control de cámara/micrófono, pantalla compartida y toma de notas clínicas en tiempo real.
- 🔔 **Sistema de Notificaciones:** Alertas contextuales para pacientes y médicos (confirmaciones, cancelaciones, asignación de cupos y avisos).
- 🌓 **Modo Oscuro / Claro:** Tema visual con persistencia en `localStorage` y paleta adaptada para sesiones prolongadas.
- 📱 **Diseño 100% Responsivo:** Interfaz adaptable a pantallas de escritorio, tablets y dispositivos móviles.

---

## 🛠️ Stack Tecnológico y Requisitos

### Tecnologías Utilizadas
- **Backend:** PHP 8.0+ (Arquitectura MVC sin dependencias externas ni Composer).
- **Base de Datos:** MySQL 8.0+ / MariaDB con motor InnoDB y conexión PDO.
- **Frontend:** HTML5 semántico, CSS3 moderno (Variables CSS, Flexbox, Grid) y JavaScript ES6+ vanilla.
- **Librerías Locales (incluidas en el repositorio):**
  - Chart.js 3.9.1 (gráficos interactivos).
  - QRCode.js (generación de códigos QR).
- **Pruebas End-to-End:** Playwright con Node.js.

### Requisitos del Sistema
- PHP 8.0 o superior (extensiones requeridas: `pdo`, `pdo_mysql`, `session`, `json`).
- MySQL 8.0+ o MariaDB 10.4+.
- Servidor web: Apache (mediante XAMPP / LAMPP) o el servidor integrado CLI de PHP.
- Node.js 18+ (únicamente requerido si se desean ejecutar las pruebas automatizadas con Playwright).

---

## 🚀 Instalación y Puesta en Marcha

### 1. Clonar o Descargar el Proyecto
Ubica el repositorio en tu espacio de trabajo local:
```bash
git clone https://github.com/Pedro-Daniel/MediConnect.git
cd MediConnect
```

### 2. Configuración de Base de Datos
El sistema incluye detección automática para entornos locales (`localhost` / `127.0.0.1`) con usuario `root` y sin contraseña. Si tu entorno MySQL utiliza credenciales distintas, crea tu archivo local:
```bash
cp config/database.example.php config/database.local.php
```
Edita [config/database.local.php](config/database.local.php) con tus parámetros:
```php
return [
    'DB_HOST' => 'localhost',
    'DB_NAME' => 'mediconnect',
    'DB_USER' => 'tu_usuario',
    'DB_PASS' => 'tu_contraseña',
    'DB_CHARSET' => 'utf8mb4',
];
```

### 3. Importación de la Base de Datos
Importa los scripts SQL en el orden estricto detallado en la siguiente sección.

### 4. Iniciar el Servidor Web

Puedes ejecutar el proyecto utilizando cualquiera de las siguientes dos opciones:

#### Opción A: Servidor Integrado de PHP (Recomendada para desarrollo)
Desde la raíz del proyecto, ejecuta:
```bash
php -S localhost:8000 -t public
```
Abre en tu navegador:
```
http://localhost:8000/
```

#### Opción B: Apache mediante XAMPP / LAMPP
1. Si usas XAMPP en Windows, copia la carpeta del proyecto dentro de `C:\xampp\htdocs\mediconnect`. En Linux, colócala en `/opt/lampp/htdocs/mediconnect`.
2. Inicia los servicios de **Apache** y **MySQL** desde el panel de control de XAMPP.
3. Abre en tu navegador:
```
http://localhost/mediconnect/public/
```

---

## 🗄️ Base de Datos y Migraciones

La base de datos completa de MediConnect consta de **19 tablas** normalizadas con restricciones de clave foránea e índices de búsqueda.

### Orden Obligatorio de Ejecución

Para una instalación limpia y funcional con todas las capacidades del sistema, ejecuta los scripts en este orden exacto:

1. **[database/database.sql](database/database.sql)** *(Obligatorio)*:
   Crea la base de datos `mediconnect` y las **14 tablas base**:
   - `roles`, `intentos_login`, `usuarios`, `especialidades`, `medicos`, `pacientes`, `horarios_medicos`, `citas`, `historias_clinicas`, `medicamentos`, `formulas_medicas`, `formula_medicamentos`, `resultados_laboratorio` y `notificaciones`.
   - Incluye usuarios semilla iniciales (1 admin, 5 médicos, 10 pacientes) y horarios de atención.
   - Ya incorpora la tabla `intentos_login` y la columna virtual `hora_inicio_activa` con índice único para permitir reutilizar cupos cancelados.

2. **[database/migracion_fase_b.sql](database/migracion_fase_b.sql)** *(Recomendado para datos enriquecidos)*:
   Inserta un volumen abundante de datos de prueba clínicos: 7 especialidades médicas adicionales, 7 médicos extra, 24 pacientes de prueba, histórico semestral de citas atendidas, historias clínicas con diagnósticos y prescripciones.

3. **[database/migracion_fase_c.sql](database/migracion_fase_c.sql)** *(Obligatorio para módulos avanzados)*:
   Añade las columnas de afiliación a `pacientes` y crea las **5 tablas adicionales** requeridas por los módulos modernos:
   - `mensajes`: Persistencia del chat clínico paciente–médico.
   - `vacunaciones`: Carné digital de vacunación.
   - `signos_vitales`: Registro biométrico y puntajes de salud.
   - `lista_espera`: Motor de lista de espera inteligente.
   - `auditoria_logs`: Bitácora transaccional para el Centro de Mando administrativo.

> **Nota sobre migraciones históricas:** Los archivos [database/migracion_login_seguro.sql](database/migracion_login_seguro.sql) y [database/migracion_citas_reutilizar_cupo.sql](database/migracion_citas_reutilizar_cupo.sql) son parches incrementales que ya se encuentran fusionados dentro de [database/database.sql](database/database.sql). Solo se requieren si se actualiza una instalación antigua previa a noviembre de 2026.

#### Ejecución rápida desde línea de comandos (MySQL / XAMPP):
```bash
mysql -u root -e "DROP DATABASE IF EXISTS mediconnect;"
mysql -u root < database/database.sql
mysql -u root mediconnect < database/migracion_fase_b.sql
mysql -u root mediconnect < database/migracion_fase_c.sql
```

---

## 🔑 Usuarios de Prueba

Todos los usuarios de demostración tienen configurada la misma contraseña verificada en base de datos:
> **Contraseña para todos los usuarios:** `password`

| Rol | Correo Electrónico | Especialidad / Acceso |
| :--- | :--- | :--- |
| **Administrador** | `admin@mediconnect.com` | Acceso total al sistema y Centro de Mando |
| **Médico** | `dr.rodriguez@mediconnect.com` | Medicina General |
| **Médico** | `dr.martinez@mediconnect.com` | Cardiología |
| **Médico** | `dr.garcia@mediconnect.com` | Pediatría |
| **Médico** | `dr.lopez@mediconnect.com` | Dermatología |
| **Médico** | `dr.fernandez@mediconnect.com` | Ginecología |
| **Paciente** | `paciente1@mediconnect.com` | Laura Gómez (con historial completo) |
| **Paciente** | `paciente2@mediconnect.com` | Pedro Sánchez |
| **Paciente** | `paciente3@mediconnect.com` | Sofía Ramírez |

*(Al importar [database/migracion_fase_b.sql](database/migracion_fase_b.sql) se habilitan médicos y pacientes adicionales hasta el ID 43 con la misma contraseña).*

---

## 📁 Estructura del Proyecto

El código está estructurado bajo el patrón MVC con separación estricta de responsabilidades:
- **12 Controladores** en `src/controllers/`
- **5 Modelos** en `src/models/`
- **32 Vistas** en `src/views/`
- **19 Tablas** en base de datos

```
MediConnect/
├── config/                         # Ajustes globales y conexión
│   ├── config.php                  # Constantes, helpers, sesiones, CSRF y auditoría
│   ├── database.php                # Conexión PDO Singleton
│   ├── database.example.php        # Plantilla de credenciales locales
│   └── database.local.php          # Credenciales locales (ignorado en git)
├── database/                       # Esquemas y migraciones SQL
│   ├── database.sql                # Esquema base (14 tablas iniciales + datos semilla)
│   ├── migracion_fase_b.sql        # Datos clínicos abundantes de demostración
│   ├── migracion_fase_c.sql        # Tablas para Chat, Vacunación, Signos, Espera y Auditoría
│   ├── migracion_login_seguro.sql  # (Histórico) Parche para intentos_login
│   ├── migracion_citas_reutilizar_cupo.sql # (Histórico) Parche para hora_inicio_activa
│   └── INSTALACION.md              # Resumen técnico de instalación SQL
├── docs/                           # Documentación interna de arquitectura
│   └── ARQUITECTURA.md             # Especificación técnica de controladores y modelos
├── public/                         # Raíz pública expuesta en el servidor web
│   ├── index.php                   # Front Controller y enrutador principal (?page=...)
│   └── assets/                     # Recursos estáticos servidos al navegador
│       ├── css/
│       │   └── style.css           # Estilos completos del sistema y temas oscuro/claro
│       ├── img/
│       │   └── icons.svg           # Sprite de iconos vectoriales SVG
│       └── js/
│           ├── app.js              # Lógica del cliente, validaciones y temas
│           ├── chart.min.js        # Motor de gráficos clínicos interactivos
│           └── qrcode.min.js       # Generador de códigos QR para carné y recetas
├── src/                            # Núcleo de la aplicación (MVC)
│   ├── controllers/                # 12 Controladores del sistema
│   │   ├── AdminController.php     # Gestión de usuarios, especialidades, auditoría y exportación CSV
│   │   ├── ApiController.php       # Endpoints AJAX (horarios disponibles, notificaciones)
│   │   ├── AuthController.php      # Login, registro, recuperación y rate limiting
│   │   ├── ChatController.php      # Mensajería clínica en tiempo real paciente–médico
│   │   ├── CitaController.php      # Agendamiento, cancelación y reasignación de cupos
│   │   ├── DashboardController.php # Paneles de control por rol
│   │   ├── MedicoController.php    # Agenda, atención de consultas e historial clínico
│   │   ├── NotificacionController.php # Gestión y lectura de notificaciones
│   │   ├── OrientadorController.php   # Triage heurístico y recomendación de especialidades
│   │   ├── PacienteController.php  # Historial, carné digital, vacunación y signos vitales
│   │   ├── PerfilController.php    # Actualización de perfil y cambio de contraseña
│   │   └── TelemedicinaController.php # Sala virtual WebRTC y bitácora clínica
│   ├── models/                     # 5 Modelos de acceso a datos
│   │   ├── Cita.php                # Operaciones y disponibilidad de citas médicas
│   │   ├── IntentoLogin.php        # Control de fuerza bruta y bloqueo de accesos
│   │   ├── Medico.php              # Información profesional y agendas médicas
│   │   ├── Paciente.php            # Ficha clínica y datos del paciente
│   │   └── Usuario.php             # Gestión de cuentas, roles y credenciales
│   └── views/                      # 32 Plantillas de renderizado
│       ├── admin/                  # Vistas: auditoria, especialidades, lista-espera, medicos, reportes, usuarios
│       ├── auth/                   # Vistas: login, recuperar, registro
│       ├── chat/                   # Vista: index (interfaz de chat)
│       ├── citas/                  # Vistas: agendar, mis-citas
│       ├── dashboard/              # Vistas: admin, medico, paciente
│       ├── layout/                 # Plantillas base: header, footer
│       ├── medico/                 # Vistas: agenda, atender, historia-paciente, pacientes
│       ├── notificaciones/         # Vista: index
│       ├── orientador/             # Vista: index
│       ├── paciente/               # Vistas: carne-digital, formulas, historial, linea-tiempo, resultados, signos-vitales, vacunacion
│       ├── perfil/                 # Vista: index
│       └── telemedicina/           # Vista: sala (videollamada WebRTC)
├── tests/                          # Suite automatizada de pruebas E2E
│   ├── flujos.spec.js              # 8 flujos críticos de negocio y validación de seguridad
│   └── README.md                   # Guía de ejecución con Playwright
├── tools/                          # Herramientas de soporte y verificación
│   ├── verificar.sh                # Verificación de sintaxis PHP y consistencia
│   └── RESUMEN_FASE2.md            # Registro de avances técnicos de Fase 2
├── playwright.config.js            # Configuración de Playwright con servidor PHP integrado
├── package.json                    # Dependencias de testing (Playwright)
├── DEMO_GUIDE.md                   # Guía cronometrada de demostración
├── DESIGN.md                       # Especificación del sistema de diseño visual
├── PRODUCT.md                      # Definición y alcance del producto
└── README.md                       # Documentación principal del proyecto
```

---

## 🔒 Seguridad Implementada

El sistema adopta buenas prácticas de desarrollo seguro sin depender de frameworks:

1. **Protección contra Fuerza Bruta (Rate Limiting):**
   - Implementado en [src/models/IntentoLogin.php](src/models/IntentoLogin.php).
   - Registra intentos por hash SHA-256 de usuario e IP. Bloquea el acceso temporalmente (30 segundos) tras alcanzar 5 intentos fallidos en una ventana de 5 minutos.
2. **Protección contra Falsificación de Peticiones en Sitios Cruzados (CSRF):**
   - Generación de tokens criptográficos por sesión en todos los formularios POST.
   - Validación estricta con respuesta de error explícita ante tokens ausentes o manipulados.
3. **Prevención de Inyección SQL:**
   - Consultas preparadas exclusivamente con PDO en todos los modelos y controladores.
   - Emulación de prepared statements deshabilitada (`PDO::ATTR_EMULATE_PREPARES => false`).
4. **Almacenamiento Seguro de Contraseñas:**
   - Hasheo mediante el algoritmo bcrypt nativo a través de `password_hash()` con costo de trabajo adecuado y validación con `password_verify()`.
5. **Prevención de Cross-Site Scripting (XSS):**
   - Sanitización y escape contextual de todas las salidas dinámicas con `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')` mediante el helper `e()`.
6. **Gestión Segura de Sesiones:**
   - Parámetros `httponly`, `samesite=Lax` y regeneración periódica de identificador de sesión con `session_regenerate_id()`.
   - Método HTTP estricto (POST con CSRF) para el cierre de sesión (`logout`), rechazando llamadas GET indebidas con código HTTP 405.
7. **Control de Acceso Basado en Roles (RBAC) y Aislamiento:**
   - Validación de sesión y rol en cada controlador (`tiene_rol()`).
   - Verificación de propiedad: los pacientes solo pueden ver sus propios registros, y los médicos únicamente pueden consultar historias clínicas y carnés de pacientes con quienes tengan citas previas asociadas.
8. **Bitácora de Auditoría Transaccional:**
   - Registro automático en `auditoria_logs` para acciones sensibles (accesos, emisión de fórmulas, atención de citas, asignaciones de lista de espera, consultas de carné y exportaciones).

---

## 🧪 Pruebas Automatizadas (Playwright)

El proyecto cuenta con una suite completa de pruebas End-to-End automatizadas con Playwright que cubren los flujos más sensibles del sistema.

### 1. Preparar la Base de Datos Limpia
Antes de correr las pruebas, restablece la base de datos a su estado inicial:
```bash
mysql -u root -e "DROP DATABASE IF EXISTS mediconnect;"
mysql -u root < database/database.sql
mysql -u root mediconnect < database/migracion_fase_b.sql
mysql -u root mediconnect < database/migracion_fase_c.sql
```

### 2. Ejecutar la Suite
1. Asegúrate de tener el servicio MySQL activo (por ejemplo con `sudo /opt/lampp/lampp startmysql` o desde XAMPP).
2. Ejecuta Playwright:
```bash
npx playwright test --reporter=list
```
> **Nota:** No necesitas iniciar manualmente el servidor PHP para las pruebas. Playwright inicia y gestiona automáticamente el servidor web integrado de PHP en el puerto 8000 mediante la directiva `webServer` de [playwright.config.js](playwright.config.js).

### Flujos Validados en `tests/flujos.spec.js`:
1. **Registro único, login y dashboard del paciente:** Registro con credenciales únicas, verificación de inicio de sesión exitoso y acceso al dashboard.
2. **Agendamiento de citas:** Selección de médico y franja horaria disponible, confirmación de cita y verificación en el listado de citas activas.
3. **Aislamiento de carné digital:** Valida que un paciente no pueda ver el carné de otro modificando el ID en la URL.
4. **Protección de carné por médico no autorizado:** Verifica que un médico no pueda consultar el carné de un paciente sin consultas previas.
5. **Control de acceso a rutas administrativas:** Comprueba que un paciente que intenta entrar a módulos de administración sea redirigido con seguridad.
6. **Protección de historia clínica médica:** Valida que un médico sin atención previa sobre un paciente no pueda abrir su historia clínica.
7. **Método estricto en logout (HTTP 405):** Verifica que peticiones GET hacia la acción de logout sean rechazadas con código 405 sin cerrar la sesión.
8. **Inmutabilidad de citas completadas:** Comprueba que intentar cancelar mediante POST una cita en estado "completada" sea rechazado manteniendo el estado original.

---

## 📚 Documentación Adicional

- [docs/ARQUITECTURA.md](docs/ARQUITECTURA.md): Documentación técnica profunda sobre controladores, modelos, métodos y configuración.
- [tests/README.md](tests/README.md): Instrucciones detalladas y configuración de la suite de pruebas E2E.
- [database/INSTALACION.md](database/INSTALACION.md): Especificación técnica de importación de base de datos.
- [PRODUCT.md](PRODUCT.md): Definición del producto, usuarios objetivo y especificación funcional.
- [DESIGN.md](DESIGN.md): Sistema de diseño visual, paletas de colores y componentes.
- [DEMO_GUIDE.md](DEMO_GUIDE.md): Guía paso a paso para presentaciones y demostraciones en vivo de 5 minutos.
- [PROYECTO_COMPLETADO.md](PROYECTO_COMPLETADO.md): Resumen de hitos y desarrollo del sistema.

---

## 👥 Créditos y Contexto Académico

**Proyecto Universitario:** Sistema de Gestión de Salud MediConnect  
**Institución:** Fundación de Estudios Superiores Comfanorte (FESC)  
**Grupo de Investigación / Semillero:** Semillero IDSE (Investigación y Desarrollo de Software)  
**Fecha:** Noviembre de 2026
