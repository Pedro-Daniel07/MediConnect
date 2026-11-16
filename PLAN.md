# Plan de Desarrollo - Sistema de Salud [NOMBRE]

## Estado Actual
- Directorio vacío, comenzando desde cero
- No hay código legacy de Sanitas que reutilizar

## Arquitectura Decidida
- **Stack:** PHP 8 + MySQL (PDO) + HTML/CSS/JS vanilla
- **Estructura:** MVC simple sin frameworks
- **Autenticación:** Sesiones PHP + password_hash
- **Base de datos:** Un solo archivo SQL importable

## Estructura de Carpetas
```
/config         - Configuración de BD
/database       - SQL de instalación
/public         - Punto de entrada (index.php)
/src
  /controllers  - Lógica de negocio
  /models       - Acceso a datos
  /views        - Templates HTML
/assets
  /css          - Estilos
  /js           - JavaScript (Chart.js local)
  /img          - Imágenes y logo
/tests          - Tests con Playwright
```

## Decisiones de Simplicidad (Karpathy Principle)
1. **Sin router complejo:** index.php + parámetro ?page=
2. **Sin ORM:** PDO directo con prepared statements
3. **Sin API REST:** Formularios tradicionales + AJAX selectivo
4. **Chart.js local:** Descargarlo y guardarlo, sin CDN
5. **Modo oscuro:** CSS variables + toggle en localStorage
6. **CSRF:** Token en sesión, validado en cada POST

## Fases de Ejecución

### Fase 1: Base de Datos ✓ (siguiente)
- Crear database.sql con todas las tablas
- Datos ficticios realistas: 1 admin, 5 médicos, 10 pacientes
- Configuración en config/database.php

### Fase 2: Autenticación
- Login/logout con sesiones
- Registro de pacientes
- Middleware de roles (paciente/médico/admin)
- Protección CSRF

### Fase 3: Módulos por Rol
- **Paciente:** agendar cita, ver historial, fórmulas, resultados
- **Médico:** agenda, atender cita, registrar diagnóstico
- **Admin:** gestión completa + reportes

### Fase 4: Funciones Innovadoras
- Dashboard con Chart.js (gráficos de citas, pacientes, etc)
- Línea de tiempo de salud (peso, presión, IMC)
- Recordatorios internos (tabla de notificaciones)
- Orientador de síntomas (árbol de decisiones simple)
- Fórmula con código QR

### Fase 5: Diseño
- Aplicar principios de diseño moderno
- Responsive (mobile-first)
- Animaciones sutiles
- Estados vacíos bien diseñados

### Fase 6: Pruebas
- Playwright para flujos principales
- Validación de formularios
- Prueba de roles y permisos

## Nombre del Sistema
Usaré **"MediConnect"** como marca temporal - reemplazable fácilmente después.

## Credenciales de Prueba (a crear)
- Admin: admin@mediconnect.com / Admin123!
- Médico: dr.rodriguez@mediconnect.com / Medico123!
- Paciente: paciente1@mediconnect.com / Paciente123!
