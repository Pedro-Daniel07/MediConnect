# Product

<!-- impeccable:product-schema 1 -->

## Platform

web

## Users

Sistema multi-rol que sirve simultáneamente a tres grupos de usuarios:

**Pacientes:** Personas que necesitan gestionar su salud digitalmente desde cualquier lugar. Agenda de citas, consulta de historial clínico, seguimiento de tratamientos, visualización de resultados de laboratorio y fórmulas médicas.

**Médicos:** Profesionales de la salud que digitalizan su práctica. Gestión de agenda, atención de citas, registro de historias clínicas, prescripción de medicamentos y seguimiento de pacientes.

**Administradores:** Personal que supervisa el funcionamiento del sistema. Gestión de usuarios, médicos y especialidades, reportes estadísticos y configuración general.

## Product Purpose

MediConnect es un sistema integral de gestión de salud que digitaliza el ciclo completo de atención médica: desde que un paciente agenda una cita hasta que recibe seguimiento de su tratamiento. Existe para demostrar que un sistema médico funcional y seguro puede entregarse con una experiencia visual futurista que inspira confianza y modernidad. El éxito se mide en la exposición universitaria: sorprender con un diseño de próxima generación mientras se demuestra dominio técnico completo.

## Positioning

Combinación de experiencia visual futurista (interfaz de salud de 2030: neón, vidrio, luz, datos vivos) con un sistema médico completamente funcional. Mientras otros proyectos universitarios priorizan función o estética, MediConnect demuestra ambos sin compromiso: seguridad real (CSRF, PDO, hashing), funciones innovadoras (orientador de síntomas, líneas de tiempo de salud, gráficos interactivos), y un diseño que se siente de otra década, todo construido en PHP puro + MySQL + JS vanilla sin frameworks.

## Operating Context

**Entorno de uso:** XAMPP local (Apache + MySQL + PHP 8.0+) en navegadores modernos. Presentación universitaria donde profesores y compañeros evalúan tanto funcionalidad como diseño. El proyecto debe ser explicable línea por línea durante la exposición.

**Flujos clave:**
- Paciente: registro → agendar cita → consultar historial → ver fórmulas/laboratorios
- Médico: login → revisar agenda → atender cita → registrar historia clínica → prescribir medicamentos
- Administrador: supervisar sistema → gestionar usuarios/médicos → revisar estadísticas → configurar

**Materiales de demostración:** Datos ficticios abundantes para mostrar el sistema lleno y vivo durante la demo de 5 minutos.

## Capabilities and Constraints

**Capacidades confirmadas:**
- Autenticación segura con tres roles diferenciados y control de acceso
- Gestión completa del ciclo de citas (agendar, cancelar, reprogramar, atender)
- Historial clínico con signos vitales, diagnósticos y tratamientos
- Fórmulas médicas con código QR imprimibles
- Resultados de laboratorio
- Orientador de síntomas con recomendaciones (incluye disclaimer médico)
- Línea de tiempo de salud con gráficos de evolución (peso, IMC, presión)
- Dashboard con estadísticas y gráficos interactivos (Chart.js)
- Sistema de notificaciones
- Modo oscuro con preferencia persistente
- Diseño 100% responsive

**Restricciones técnicas:**
- PHP 8+ puro sin frameworks (Composer prohibido)
- MySQL con PDO y consultas preparadas
- HTML/CSS/JS vanilla (sin React, Vue, etc.)
- Chart.js ya incluido localmente
- Sin servicios externos (email simulado, sin CDNs para funciones críticas)
- Código comentado en español para facilitar explicación en exposición
- Base de datos importable en un solo archivo SQL

**Seguridad implementada:**
- Contraseñas con password_hash (bcrypt)
- Protección CSRF en todos los formularios
- Prepared statements contra SQL injection
- Escape de salidas con htmlspecialchars
- Sesiones seguras con regeneración de ID
- Control de acceso por roles

## Brand Commitments

Libertad creativa total. El nombre "MediConnect" puede evolucionar. No hay logos, colores o assets que deban preservarse. La identidad visual se construirá desde cero con personalidad clínica futurista: calidez humana + tecnología de vanguardia.

**Anti-referencias explícitas:** azul genérico de sistemas médicos, degradados morados típicos de IA, emojis como iconos, tarjetas idénticas sin jerarquía.

## Evidence on Hand

- Sistema funcional completo en `C:\xampp\htdocs\ruthxd\`
- Base de datos poblada con datos de prueba en `database/database.sql`
- Tres usuarios de prueba activos (uno por rol)
- README.md con guía de instalación y demostración
- Estructura MVC implementada
- Sistema de rutas funcional con `?page=` pattern
- Helper `asset()` para recursos en `public/assets/{css,js,img}`
- Chart.js 3.9.1 incluido localmente
- Algunos gráficos ya dentro de `.chart-box` con altura fija

**Ausencias confirmadas:** 
- No hay capa visual futurista implementada aún (aurora, vidrio, luz cursor mencionados en la instrucción son expectativas)
- Los iconos actuales son emojis básicos
- Muchas vistas son listas de texto simples sin riqueza visual
- Datos de ejemplo insuficientes (dashboards se ven vacíos)

## Product Principles

1. **Funcionalidad real primero, efectos después:** Cada función debe ser completamente operativa antes de agregar animación o detalle visual. Nada meramente decorativo.

2. **Explicabilidad técnica:** El código debe ser legible y comentado en español para poder explicar cada decisión durante la exposición universitaria. Claridad sobre cleverness.

3. **Impacto visual inmediato:** El primer viewport debe comunicar "esto es diferente" — la interfaz debe verse y sentirse como una aplicación de salud de 2030, no de 2020.

4. **Seguridad sin excepciones:** Todo input validado, todo query preparado, todo output escapado. La demo incluye mostrar que el sistema es seguro.

5. **Datos vivos y abundantes:** Los dashboards, gráficos y listas deben verse poblados y realistas. Estados vacíos solo cuando tienen propósito pedagógico.

## Accessibility & Inclusion

- Contraste AA mínimo en modo claro y oscuro
- Navegación completa por teclado
- Labels en formularios
- Estados de error claros y en lenguaje natural
- Disclaimer médico visible en orientador de síntomas: "NO es un diagnóstico médico"
- Responsive desde 390px (móvil) hasta escritorio
