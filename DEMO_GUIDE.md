# Guía de Demostración - Sistema MediConnect
## 5 Minutos de Presentación

### MINUTO 1: Introducción y Login (45 seg)
**Mostrar:**
- Página de inicio elegante con logo MediConnect
- Explicar los 3 roles: Paciente, Médico, Administrador
- Mostrar credenciales de prueba en pantalla
- **Login como Paciente:** paciente1@mediconnect.com / Admin123!

**Decir:**
"Este es MediConnect, un sistema completo de gestión de salud. Tenemos 3 tipos de usuarios, cada uno con su propio panel. Vamos a empezar como paciente."

---

### MINUTO 2: Vista de Paciente (1 min)
**Mostrar:**
1. Dashboard con tarjetas de estadísticas (citas pendientes, completadas, consultas)
2. Lista de próximas citas
3. **Clic en "Agendar Cita":**
   - Seleccionar especialidad: Cardiología
   - Elegir médico: Dr. Martínez
   - Seleccionar fecha de mañana
   - ⭐ **VER HORARIOS DISPONIBLES EN TIEMPO REAL** (cargados con AJAX)
   - Escribir motivo: "Control de presión arterial"
   - Agendar
4. Ver confirmación y regresar a "Mis Citas"
5. Rápidamente mostrar:
   - Mi Historial (diagnósticos anteriores)
   - Mis Fórmulas (con medicamentos y opción de imprimir)

**Decir:**
"El paciente puede agendar citas fácilmente. El sistema solo muestra los horarios realmente disponibles. También puede ver todo su historial médico y fórmulas prescritas."

---

### MINUTO 3: Vista de Médico (1 min 15 seg)
**Mostrar:**
1. Logout → Login como dr.rodriguez@mediconnect.com / Admin123!
2. Dashboard del médico con citas del día
3. **Clic en "Atender" una cita:**
   - Ver datos del paciente (nombre, alergias destacadas en rojo)
   - Ver historial previo del paciente
   - Registrar signos vitales:
     - Peso: 75 kg
     - Altura: 170 cm
     - ⭐ **VER CÁLCULO AUTOMÁTICO DE IMC**
     - Presión: 130/85
   - Escribir diagnóstico: "Hipertensión arterial controlada"
   - Prescribir medicamento:
     - Losartán 50mg, cada 24 horas, 30 días
   - Guardar
4. Ver confirmación
5. Mostrar "Mis Pacientes" brevemente

**Decir:**
"El médico ve su agenda y puede atender citas registrando toda la información clínica. El sistema calcula automáticamente el IMC y permite prescribir medicamentos de forma estructurada."

---

### MINUTO 4: Vista de Administrador (50 seg)
**Mostrar:**
1. Logout → Login como admin@mediconnect.com / Admin123!
2. Dashboard con estadísticas generales
3. ⭐ **Gráficos interactivos con Chart.js:**
   - Gráfico de citas por estado (pendientes, confirmadas, completadas)
   - Top de especialidades más solicitadas
4. Clic rápido en:
   - Usuarios (tabla con búsqueda)
   - Médicos (listado completo)
   - Reportes (gráficos mensuales)

**Decir:**
"El administrador tiene control total del sistema con estadísticas en tiempo real, gráficos interactivos y gestión de todos los usuarios y médicos."

---

### MINUTO 5: Funciones Innovadoras (1 min 10 seg)
**Mostrar como Paciente (login rápido):**

1. **🩺 Orientador de Síntomas (25 seg):**
   - Ir a menú → Orientador de Síntomas
   - Marcar: Fiebre + Tos
   - Evaluar → Ver recomendación: "Medicina General"
   - ⚠️ **Mostrar disclaimer visible** de que NO es diagnóstico médico

2. **📊 Línea de Tiempo de Salud (15 seg):**
   - Ir a "Línea de Tiempo"
   - Ver gráficos de evolución de peso e IMC a lo largo del tiempo

3. **🌙 Modo Oscuro (10 seg):**
   - Menú usuario → Clic en "Modo Oscuro"
   - Ver transición suave a tema oscuro
   - Volver a claro

4. **📱 Responsive (10 seg):**
   - Reducir ventana del navegador
   - Mostrar menú hamburguesa
   - Ver adaptación perfecta a móvil

5. **🔔 Notificaciones (10 seg):**
   - Clic en campana → Ver recordatorios de citas y medicamentos

**Decir:**
"Y las funciones innovadoras: un orientador de síntomas inteligente con avisos claros de que no reemplaza al médico, seguimiento gráfico de salud, modo oscuro, diseño completamente responsive y un sistema de notificaciones."

---

### CIERRE (10 seg)
**Mostrar:**
- Regresar al dashboard principal

**Decir:**
"Todo el sistema funciona sin frameworks, es seguro con protección CSRF y encriptación de contraseñas, y se instala con un solo archivo SQL. Listo para producción."

---

## Tips para la Demo

### Antes de Empezar:
- ✅ Tener 3 pestañas del navegador abiertas, una por cada rol
- ✅ Tener los logins escritos en un notepad a la vista
- ✅ Probar que Chart.js carga (verificar que assets/js/chart.min.js existe)
- ✅ Verificar que hay datos de ejemplo en la BD

### Durante la Demo:
- 🎯 Hablar con confianza y fluidez
- ⚡ Movimientos rápidos pero precisos (ensayar antes)
- 👁️ Resaltar las funciones estrella (horarios dinámicos, IMC automático, gráficos)
- 🚨 Mencionar la seguridad implementada

### Qué Destacar:
1. **Sin frameworks** = código explicable
2. **Horarios disponibles en tiempo real** = AJAX funcional
3. **Cálculo automático de IMC** = validación y cálculos
4. **Gráficos con Chart.js** = visualización de datos
5. **Orientador con disclaimer** = ética médica
6. **Modo oscuro** = experiencia de usuario
7. **100% responsive** = diseño moderno
8. **Seguridad completa** = calidad profesional

---

## Estructura de Tiempo Detallada

| Sección | Tiempo | Acción Clave |
|---------|--------|--------------|
| Intro + Login Paciente | 0:00 - 0:45 | Mostrar credenciales, entrar |
| Dashboard Paciente | 0:45 - 1:00 | Ver stats y citas |
| Agendar Cita | 1:00 - 1:30 | ⭐ Horarios dinámicos |
| Historial/Fórmulas | 1:30 - 2:00 | Vista rápida |
| Login Médico | 2:00 - 2:10 | Cambiar de rol |
| Dashboard Médico | 2:10 - 2:20 | Ver agenda |
| Atender Cita | 2:20 - 3:00 | ⭐ IMC automático, fórmula |
| Pacientes | 3:00 - 3:15 | Listado |
| Login Admin | 3:15 - 3:25 | Cambiar de rol |
| Dashboard Admin | 3:25 - 3:45 | ⭐ Gráficos Chart.js |
| Gestión | 3:45 - 4:05 | Usuarios, médicos |
| Orientador | 4:05 - 4:30 | ⭐ Con disclaimer |
| Línea de Tiempo | 4:30 - 4:45 | Gráficos salud |
| Modo Oscuro | 4:45 - 4:55 | Toggle theme |
| Responsive | 4:55 - 5:05 | Resize window |
| Notificaciones | 5:05 - 5:15 | Campana |
| Cierre | 5:15 - 5:25 | Resumen |

---

## Posibles Preguntas y Respuestas

**P: ¿Por qué sin frameworks?**
R: Para facilitar la explicación del código en clase y demostrar comprensión de PHP puro.

**P: ¿Es seguro?**
R: Sí, implementa password_hash, protección CSRF, prepared statements y validación de datos.

**P: ¿Funciona en móvil?**
R: Totalmente responsive con diseño mobile-first.

**P: ¿Cómo se instala?**
R: Copiar carpeta a htdocs, importar un archivo SQL en phpMyAdmin, listo.

**P: ¿Los gráficos requieren internet?**
R: No, Chart.js está incluido localmente en el proyecto.

**P: ¿El orientador de síntomas es confiable médicamente?**
R: No, es educativo. Por eso tiene un disclaimer prominente de que NO reemplaza consulta médica.

---

**🎯 Objetivo: Demostrar un sistema COMPLETO, FUNCIONAL y PROFESIONAL en 5 minutos.**
