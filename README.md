# Sistema de Gestión de Salud MediConnect

Sistema completo de gestión médica desarrollado en PHP puro, MySQL y JavaScript vanilla para proyecto universitario.

## 🚀 Características Principales

### Por Rol de Usuario

**👤 Pacientes:**
- ✅ Registro y autenticación segura
- 📅 Agendar, cancelar y reprogramar citas
- 📋 Ver historial clínico completo
- 💊 Consultar fórmulas médicas con código QR
- 🔬 Ver resultados de laboratorio
- 📊 Línea de tiempo de salud (peso, IMC, presión)
- 🩺 Orientador de síntomas con recomendaciones
- 🔔 Sistema de notificaciones

**👨‍⚕️ Médicos:**
- 📅 Gestión de agenda semanal
- 👥 Atención de citas con registro de historia clínica
- 💊 Prescripción de fórmulas médicas
- 📋 Ver historial de pacientes atendidos
- 📊 Dashboard con estadísticas

**👨‍💼 Administradores:**
- 👥 Gestión de usuarios, médicos y especialidades
- 📊 Reportes y estadísticas del sistema
- 📈 Gráficos de citas y especialidades
- ⚙️ Configuración general del sistema

### Funciones Innovadoras

- 🌓 **Modo oscuro automático** con preferencia guardada
- 📊 **Gráficos interactivos** con Chart.js (incluido localmente)
- 📱 **Diseño 100% responsive** (mobile-first)
- 🔒 **Seguridad completa**: CSRF, prepared statements, password_hash
- ⚡ **Horarios inteligentes**: solo muestra espacios disponibles
- 🎨 **Diseño moderno** con animaciones sutiles
- 🖨️ **Fórmulas imprimibles** con código QR

## 📋 Requisitos

- XAMPP (Apache + MySQL + PHP 8.0+)
- Navegador web moderno
- 50MB de espacio en disco

## 🔧 Instalación Paso a Paso

### 1. Copiar el Proyecto

```bash
### 1. Copiar proyecto y configurar BD
```bash
cp config/database.example.php config/database.local.php
# editar config/database.local.php con tus credenciales
```

### 2. Iniciar servidor
php -S localhost:8000 -t public
```

### 2. Importar la Base de Datos

1. Abrir XAMPP Control Panel
2. Iniciar **Apache** y **MySQL**
3. Abrir phpMyAdmin: `http://localhost/phpmyadmin`
4. Clic en "Nuevo" para crear base de datos (se creará automáticamente al importar)
5. Clic en "Importar" en el menú superior
6. Seleccionar archivo: `C:\xampp\htdocs\ruthxd\database\database.sql`
7. Clic en "Continuar"
8. ✅ La base de datos `mediconnect` se creará con todos los datos de prueba

### 3. Acceder al Sistema

Abrir en el navegador:
```
http://localhost/ruthxd/public/
```

## 🔑 Usuarios de Prueba

### Administrador
- **Email:** admin@mediconnect.com
- **Contraseña:** password
- **Acceso:** Gestión completa del sistema

### Médico
- **Email:** dr.rodriguez@mediconnect.com
- **Contraseña:** password
- **Especialidad:** Medicina General

### Paciente
- **Email:** paciente1@mediconnect.com
- **Contraseña:** password

**Nota:** Todos los usuarios de prueba usan la misma contraseña: `password`

## Acceso
http://localhost:8000/index.php?page=login

## 📁 Estructura del Proyecto

```
ruthxd/
├── config/
│   ├── config.php          # Configuración general
│   └── database.php        # Conexión a BD
├── database/
│   └── database.sql        # Script SQL completo
├── public/
│   └── index.php           # Punto de entrada
├── src/
│   ├── controllers/        # Lógica de negocio
│   ├── models/            # Acceso a datos
│   └── views/             # Plantillas HTML
│       ├── auth/          # Login, registro
│       ├── dashboard/     # Dashboards por rol
│       ├── citas/         # Gestión de citas
│       ├── paciente/      # Vistas del paciente
│       ├── medico/        # Vistas del médico
│       └── admin/         # Vistas del admin
├── assets/
│   ├── css/
│   │   └── style.css      # Estilos completos
│   ├── js/
│   │   ├── app.js         # JavaScript principal
│   │   └── chart.min.js   # Librería de gráficos
│   └── img/               # Imágenes
├── README.md              # Este archivo
└── PLAN.md               # Plan de desarrollo
```

## 🎯 Guía de Demostración (5 minutos)

### Minuto 1: Introducción y Login
1. Mostrar página de inicio de sesión
2. Explicar los 3 tipos de usuarios
3. Iniciar sesión como **Paciente**

### Minuto 2: Vista de Paciente
1. Mostrar dashboard con estadísticas
2. Ver citas próximas
3. **Agendar nueva cita:**
   - Seleccionar especialidad → Cardiología
   - Elegir médico → Dr. Martínez
   - Seleccionar fecha → Mañana
   - Ver horarios disponibles en tiempo real ✨
   - Completar motivo y agendar
4. Ver historial clínico con detalles médicos
5. Mostrar fórmulas médicas con medicamentos

### Minuto 3: Vista de Médico
1. Cerrar sesión y entrar como **Médico** (dr.rodriguez@mediconnect.com)
2. Dashboard con citas de hoy
3. Ver agenda de la semana
4. **Atender una cita:**
   - Clic en "Atender"
   - Ver datos del paciente (alergias, historial)
   - Registrar signos vitales (peso, presión, temperatura)
   - Escribir diagnóstico
   - Prescribir medicamentos con dosis y frecuencia
   - Guardar historia clínica
5. Mostrar listado de pacientes atendidos

### Minuto 4: Vista de Administrador
1. Cerrar sesión y entrar como **Admin**
2. Dashboard con estadísticas generales
3. **Gráficos interactivos** de citas por estado 📊
4. Ver especialidades más solicitadas
5. Gestión de usuarios y médicos
6. Reportes del sistema

### Minuto 5: Funciones Innovadoras
1. **Orientador de síntomas** 🩺:
   - Seleccionar síntomas
   - Obtener recomendación de especialidad
2. **Línea de tiempo de salud** 📈:
   - Gráficos de evolución de peso e IMC
3. **Modo oscuro** 🌙:
   - Toggle desde el menú
4. **Responsive** 📱:
   - Mostrar en ventana reducida/móvil
5. **Notificaciones** 🔔:
   - Sistema de recordatorios

## ⚙️ Configuración Opcional

### Cambiar Nombre del Sistema

Editar `config/config.php`:
```php
define('SITE_NAME', 'TuNombre');
```

### Cambiar Conexión de BD

Editar `config/database.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'mediconnect');
define('DB_USER', 'root');
define('DB_PASS', '');
```

## 🔒 Seguridad Implementada

- ✅ **Contraseñas hasheadas** con `password_hash()` (bcrypt)
- ✅ **Protección CSRF** en todos los formularios
- ✅ **Prepared statements** (PDO) contra SQL injection
- ✅ **Escape de salidas** con `htmlspecialchars()`
- ✅ **Validación en servidor y cliente**
- ✅ **Sesiones seguras** con regeneración de ID
- ✅ **Control de acceso por roles**

## 🎨 Tecnologías Utilizadas

- **Backend:** PHP 8+ (sin frameworks)
- **Base de Datos:** MySQL 8.0 con PDO
- **Frontend:** HTML5, CSS3 (Variables CSS), JavaScript ES6+
- **Gráficos:** Chart.js 3.9.1 (incluido localmente)
- **Arquitectura:** MVC simple
- **Patrón:** Singleton para conexión BD

## 🐛 Solución de Problemas

### Error: "La base de datos no existe"
- Importar nuevamente `database/database.sql` en phpMyAdmin

### Error: "No se puede conectar"
- Verificar que Apache y MySQL estén corriendo en XAMPP
- Revisar credenciales en `config/database.php`

### Los estilos no cargan
- Verificar que la ruta sea: `http://localhost/ruthxd/public/`
- Limpiar caché del navegador (Ctrl+F5)

### Horarios no se muestran al agendar
- Verificar que JavaScript esté habilitado
- Abrir consola del navegador (F12) para ver errores

## 📝 Notas para la Exposición

1. **Datos ficticios:** Todos los pacientes y médicos son inventados
2. **Recuperación de contraseña:** Simulada (no envía email real)
3. **Código QR:** Generado como código único, listo para QR real
4. **Orientador de síntomas:** ⚠️ Incluye disclaimer visible de que NO es diagnóstico médico
5. **Sin frameworks:** Código PHP puro para fácil explicación

## ✨ Puntos Destacados para Calificación

- ✅ Sistema completo funcional de inicio a fin
- ✅ 3 roles con permisos diferenciados
- ✅ Base de datos normalizada con relaciones
- ✅ Seguridad implementada correctamente
- ✅ Diseño moderno y profesional
- ✅ 100% responsive
- ✅ Código limpio y comentado en español
- ✅ Funciones innovadoras (gráficos, orientador, modo oscuro)
- ✅ Instalación simple (1 archivo SQL)

## 👨‍💻 Autor

Proyecto universitario - Sistema de Gestión de Salud MediConnect
Versión 1.0 - Noviembre 2026

---

**🎓 Listo para presentar y demostrar**
