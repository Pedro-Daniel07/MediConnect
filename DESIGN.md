# Design System — MediConnect

<!-- impeccable:design-schema 1 -->

## Visual Identity

**Mundo:** Vidrio clínico futurista. Interfaz de salud de próxima generación que combina la precisión quirúrgica con materiales translúcidos y capas flotantes. Inspirado en dispositivos médicos de vanguardia: pantallas OLED de monitores de signos vitales, superficies de vidrio esterilizable, HUDs holográficos de quirófano.

**Personalidad:** Confianza técnica con calidez humana. Futurista sin ser frío, preciso sin ser intimidante. Como un asistente médico AI del año 2030: competente, claro, accesible.

**Paleta de color:** Estrategia Restrained con acento estratégico. Fondo oscuro por defecto (interfaz que acompaña sesiones largas bajo cualquier luz), con modo claro igualmente resuelto.

**Materiales:** Superficies de vidrio esmerilado con blur, bordes sutiles con brillo interno, sombras difusas tipo profundidad, gradientes apenas perceptibles que dan vida sin ser decorativos.

## Color

### Tema oscuro (por defecto)
```css
--glass-bg-primary: rgba(15, 23, 42, 0.85);      /* Vidrio sobre oscuro profundo */
--glass-bg-secondary: rgba(30, 41, 59, 0.75);    /* Tarjetas elevadas */
--glass-bg-tertiary: rgba(51, 65, 85, 0.65);     /* Controles hover */

--glass-border: rgba(148, 163, 184, 0.15);       /* Bordes sutiles */
--glass-glow: rgba(56, 189, 248, 0.25);          /* Resplandor interior */

--text-primary: rgba(248, 250, 252, 0.95);
--text-secondary: rgba(203, 213, 225, 0.80);
--text-tertiary: rgba(148, 163, 184, 0.65);

--accent-primary: #0ea5e9;        /* Cian clínico, saturado */
--accent-hover: #38bdf8;
--accent-active: #0284c7;

--success: #10b981;
--warning: #f59e0b;
--error: #ef4444;
--info: #06b6d4;

--backdrop: #0f172a;              /* Fondo profundo detrás del vidrio */
--surface-base: #1e293b;          /* Superficie sólida para contraste AA */
```

### Tema claro
```css
--glass-bg-primary: rgba(255, 255, 255, 0.90);
--glass-bg-secondary: rgba(248, 250, 252, 0.85);
--glass-bg-tertiary: rgba(241, 245, 249, 0.80);

--glass-border: rgba(100, 116, 139, 0.12);
--glass-glow: rgba(14, 165, 233, 0.20);

--text-primary: rgba(15, 23, 42, 0.95);
--text-secondary: rgba(51, 65, 85, 0.85);
--text-tertiary: rgba(100, 116, 139, 0.70);

--accent-primary: #0284c7;
--accent-hover: #0369a1;
--accent-active: #075985;

--backdrop: #f8fafc;
--surface-base: #ffffff;
```

### Estados de citas (reutilizados, ahora con coherencia)
```css
--status-pending: #f59e0b;
--status-confirmed: #0ea5e9;
--status-completed: #10b981;
--status-cancelled: #ef4444;
```

## Tipografía

**System Stack (primario):**  
`-apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif`  
— Aprovecha la claridad de los sistemas nativos. En Windows, Segoe UI es la cara de precisión médica: quirúrgica, legible, confiable.

**Mono (datos y códigos):**  
`ui-monospace, 'SF Mono', 'Cascadia Code', 'Consolas', monospace`  
— Para IDs de cita, códigos QR, timestamps, valores de signos vitales.

**Escala tipográfica:**
```css
--text-xs: 0.75rem;      /* 12px - Labels pequeños, metadatos */
--text-sm: 0.875rem;     /* 14px - Cuerpo secundario */
--text-base: 1rem;       /* 16px - Cuerpo principal */
--text-lg: 1.125rem;     /* 18px - Énfasis */
--text-xl: 1.25rem;      /* 20px - Subtítulos */
--text-2xl: 1.5rem;      /* 24px - Títulos de sección */
--text-3xl: 1.875rem;    /* 30px - Títulos de página */
--text-4xl: 2.25rem;     /* 36px - Display, dashboards */

--weight-normal: 400;
--weight-medium: 500;
--weight-semibold: 600;
--weight-bold: 700;

--line-tight: 1.25;
--line-normal: 1.5;
--line-relaxed: 1.75;
```

## Espaciado y Ritmo

Sistema de 4px base:
```css
--space-1: 0.25rem;   /* 4px */
--space-2: 0.5rem;    /* 8px */
--space-3: 0.75rem;   /* 12px */
--space-4: 1rem;      /* 16px */
--space-5: 1.25rem;   /* 20px */
--space-6: 1.5rem;    /* 24px */
--space-8: 2rem;      /* 32px */
--space-10: 2.5rem;   /* 40px */
--space-12: 3rem;     /* 48px */
--space-16: 4rem;     /* 64px */
--space-20: 5rem;     /* 80px */
```

**Principio:** Más espacio arriba de un encabezado que abajo. Densidad variable por sección: un panel denso de datos gana espacio para respirar después.

## Efectos de Vidrio

```css
--blur-sm: blur(4px);
--blur-md: blur(8px);
--blur-lg: blur(16px);
--blur-xl: blur(24px);

--shadow-glass-sm: 0 2px 8px rgba(0, 0, 0, 0.08),
                   0 1px 2px rgba(0, 0, 0, 0.12);

--shadow-glass-md: 0 4px 16px rgba(0, 0, 0, 0.12),
                   0 2px 4px rgba(0, 0, 0, 0.16),
                   inset 0 0 0 1px var(--glass-border);

--shadow-glass-lg: 0 8px 32px rgba(0, 0, 0, 0.16),
                   0 4px 8px rgba(0, 0, 0, 0.20),
                   inset 0 0 0 1px var(--glass-border);

--shadow-glow: 0 0 16px var(--glass-glow),
               0 0 4px var(--glass-glow);
```

**Técnica de vidrio esmerilado:**
```css
.glass-surface {
  background: var(--glass-bg-primary);
  backdrop-filter: var(--blur-md);
  -webkit-backdrop-filter: var(--blur-md);
  border: 1px solid var(--glass-border);
  box-shadow: var(--shadow-glass-md);
}
```

## Bordes y Radios

```css
--radius-sm: 6px;
--radius-md: 10px;
--radius-lg: 14px;
--radius-xl: 20px;
--radius-full: 9999px;

--border-width: 1px;
--border-width-thick: 2px;
```

Bordes translúcidos para no romper el vidrio. Radios generosos (10-14px base) para sensación moderna sin exagerar.

## Componentes Core

### Botón

**Primario (acción principal):**
```css
background: linear-gradient(135deg, var(--accent-primary), var(--accent-hover));
color: white;
padding: var(--space-3) var(--space-6);
border-radius: var(--radius-md);
font-weight: var(--weight-semibold);
box-shadow: 0 2px 8px rgba(14, 165, 233, 0.3);
transition: all 180ms cubic-bezier(0.4, 0, 0.2, 1);
```
— Hover: eleva sombra y aumenta brillo.  
— Active: escala a 0.98, sombra interior.

**Secundario (vidrio):**
```css
background: var(--glass-bg-secondary);
backdrop-filter: var(--blur-sm);
border: 1px solid var(--glass-border);
color: var(--text-primary);
```
— Hover: border glow, fondo más opaco.

**Fantasma (texto con hover):**
```css
background: transparent;
color: var(--accent-primary);
```
— Hover: fondo glass-bg-tertiary.

### Input y Textarea

```css
background: var(--glass-bg-secondary);
backdrop-filter: var(--blur-sm);
border: 1px solid var(--glass-border);
border-radius: var(--radius-md);
padding: var(--space-3) var(--space-4);
color: var(--text-primary);
transition: border-color 150ms ease, box-shadow 150ms ease;

/* Focus */
border-color: var(--accent-primary);
box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.15);
outline: none;

/* Error */
border-color: var(--error);
box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15);
```

### Card (tarjeta de vidrio)

```css
background: var(--glass-bg-primary);
backdrop-filter: var(--blur-md);
border: 1px solid var(--glass-border);
border-radius: var(--radius-lg);
padding: var(--space-6);
box-shadow: var(--shadow-glass-md);
transition: transform 200ms ease, box-shadow 200ms ease;

/* Hover (cuando es interactiva) */
transform: translateY(-2px);
box-shadow: var(--shadow-glass-lg);
border-color: var(--glass-glow);
```

### Badge (estado)

```css
display: inline-flex;
align-items: center;
padding: var(--space-1) var(--space-3);
border-radius: var(--radius-full);
font-size: var(--text-xs);
font-weight: var(--weight-semibold);
text-transform: uppercase;
letter-spacing: 0.05em;

/* Variantes por estado */
.badge-pending { 
  background: rgba(245, 158, 11, 0.15);
  color: #f59e0b;
  border: 1px solid rgba(245, 158, 11, 0.3);
}
```

### Tabla

Cabecera sticky con vidrio, filas con separador sutil:
```css
thead {
  position: sticky;
  top: 0;
  background: var(--glass-bg-secondary);
  backdrop-filter: var(--blur-md);
  z-index: 10;
}

th {
  padding: var(--space-3) var(--space-4);
  text-align: left;
  font-weight: var(--weight-semibold);
  font-size: var(--text-sm);
  color: var(--text-secondary);
  border-bottom: 1px solid var(--glass-border);
}

td {
  padding: var(--space-4);
  border-bottom: 1px solid rgba(148, 163, 184, 0.08);
}

tbody tr:hover {
  background: var(--glass-bg-tertiary);
}
```

## Layout Autenticado

**Estructura:**
```
┌─────────────────────────────────────────┐
│ [Sidebar]  [Main Area]                  │
│            ┌───────────────────────────┐│
│            │ Topbar: search, bell, user││
│            ├───────────────────────────┤│
│            │                            ││
│            │ Content                    ││
│            │                            ││
│            └───────────────────────────┘│
└─────────────────────────────────────────┘
```

**Sidebar:**
- Ancho: 240px (expandido), 64px (colapsado)
- Vidrio con blur fuerte
- Logo arriba, navegación por rol, toggle abajo
- Iconos SVG + texto (expandido) o solo iconos (colapsado)
- Indicador activo: borde izquierdo accent + fondo glass-bg-tertiary

**Topbar:**
- Altura: 64px
- Vidrio con blur, sticky
- Campo de búsqueda con Ctrl+K hint
- Campana con contador badge
- Avatar + nombre + menú dropdown

**Móvil (< 768px):**
- Sidebar se convierte en overlay modal (desde la izquierda)
- Topbar permanece con hamburger menu
- Alternativa: barra inferior fija con 4-5 acciones principales

## Iconografía

**Sistema de iconos SVG:** sprite en `public/assets/img/icons.svg` con símbolos coherentes:

- `home` — Dashboard
- `calendar` — Citas
- `users` — Pacientes
- `user-md` — Médico
- `heart` — Salud
- `pills` — Recetas
- `flask` — Laboratorio
- `bell` — Notificaciones
- `chart-line` — Gráficos / Estadísticas
- `message` — Chat
- `video` — Videollamada
- `syringe` — Vacunas
- `shield` — Seguridad
- `qrcode` — QR
- `cog` — Ajustes
- `search` — Búsqueda
- `chevron-*` — Navegación
- `x` — Cerrar
- `check` — Confirmar
- `alert-triangle` — Advertencia
- `info` — Información

**Tamaño base:** 20px, escalable. Trazo de 2px para consistencia. Estilo: geométrico, limpio, sin rellenos innecesarios.

**Uso:**
```html
<svg class="icon" aria-hidden="true">
  <use href="/assets/img/icons.svg#calendar"></use>
</svg>
```

## Movimiento

**Timing functions:**
```css
--ease-smooth: cubic-bezier(0.4, 0.0, 0.2, 1);  /* Material smooth */
--ease-bounce: cubic-bezier(0.68, -0.55, 0.265, 1.55);
--ease-out: cubic-bezier(0.0, 0.0, 0.2, 1);
```

**Duraciones:**
```css
--duration-instant: 100ms;
--duration-fast: 150ms;
--duration-normal: 200ms;
--duration-slow: 300ms;
--duration-enter: 250ms;
```

**Principios:**
- Transiciones de estado: 150-200ms
- Feedback de interacción: 100ms
- Entradas escalonadas: delay incremental de 50ms entre elementos
- Propiedades animables: `transform`, `opacity`, `filter` (nunca `width`, `height`, `top`, `left`)
- Respeta `prefers-reduced-motion`

**Entrada escalonada (listas, cards):**
```css
.stagger-item {
  opacity: 0;
  transform: translateY(8px);
  animation: enter-up var(--duration-enter) var(--ease-smooth) forwards;
}

.stagger-item:nth-child(1) { animation-delay: 0ms; }
.stagger-item:nth-child(2) { animation-delay: 50ms; }
.stagger-item:nth-child(3) { animation-delay: 100ms; }
/* ... */

@keyframes enter-up {
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
```

## Gráficos (Chart.js)

Todos los gráficos dentro de contenedor `.chart-box` con altura fija:
```css
.chart-box {
  position: relative;
  height: 300px;  /* o 240px para charts compactos */
  background: var(--glass-bg-secondary);
  border-radius: var(--radius-lg);
  padding: var(--space-4);
}
```

**Paleta de datos (categóricos, máximo 8 series):**
```js
const dataColors = [
  '#0ea5e9',  // Cian primario
  '#10b981',  // Verde éxito
  '#f59e0b',  // Ámbar
  '#ef4444',  // Rojo
  '#8b5cf6',  // Violeta
  '#06b6d4',  // Cian info
  '#f97316',  // Naranja
  '#ec4899',  // Rosa
];
```

Configuración global:
```js
Chart.defaults.color = 'rgba(203, 213, 225, 0.8)';  // texto secundario
Chart.defaults.borderColor = 'rgba(148, 163, 184, 0.15)';  // grid
Chart.defaults.font.family = 'system-ui, sans-serif';
```

## Responsive

**Breakpoints:**
```css
--screen-sm: 640px;   /* Móvil grande */
--screen-md: 768px;   /* Tablet */
--screen-lg: 1024px;  /* Desktop pequeño */
--screen-xl: 1280px;  /* Desktop */
--screen-2xl: 1536px; /* Desktop grande */
```

**Estrategia:**
- Mobile-first: CSS base para móvil, `@media (min-width: ...)` para escritorio
- Sidebar: colapsado automáticamente en < 1024px
- Gutter lateral: 16px en móvil, 24px en tablet, 32px en desktop
- Gráficos: altura fija se mantiene, ancho fluido
- Tablas: scroll horizontal con sombra de desborde en móvil
- Texto: escala base 16px, crece a 18px en > 1280px si mejora legibilidad

## Accesibilidad

- **Contraste:** AA en todos los textos (4.5:1 body, 3:1 large text)
- **Focus visible:** anillo accent con box-shadow, nunca `outline: none` sin reemplazo
- **Skip links:** "Saltar al contenido" oculto hasta focus
- **ARIA labels:** en iconos sin texto, controles personalizados
- **Keyboard nav:** Tab order lógico, Escape cierra modales, Enter/Space activa
- **Reduced motion:** `@media (prefers-reduced-motion: reduce)` deshabilita animaciones

## Estados Vacíos

Ilustración SVG inline simple (geométrica, monocromática) + mensaje + botón de acción:
```html
<div class="empty-state">
  <svg class="empty-illustration" width="120" height="120">...</svg>
  <h3>No hay citas agendadas</h3>
  <p>Agenda tu primera cita con un especialista</p>
  <button class="btn-primary">Agendar cita</button>
</div>
```

Estilo minimalista, sin ilustraciones complejas o decorativas innecesarias.

## Modo Oscuro / Claro

Toggle persistente en localStorage como `theme`:
```js
// Aplicar al cargar
const theme = localStorage.getItem('theme') || 
              (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
document.documentElement.setAttribute('data-theme', theme);

// Toggle
function toggleTheme() {
  const current = document.documentElement.getAttribute('data-theme');
  const next = current === 'dark' ? 'light' : 'dark';
  document.documentElement.setAttribute('data-theme', next);
  localStorage.setItem('theme', next);
}
```

Icono: sol/luna, ubicado en topbar o menú de usuario.

## Paleta de Comandos (Ctrl+K / Cmd+K)

Modal centrado con:
- Input de búsqueda con autofocus
- Lista de comandos con iconos: páginas, acciones, tema
- Búsqueda difusa sobre títulos
- Navegación: ↑↓ para seleccionar, Enter para ejecutar, Escape para cerrar
- Fondo backdrop con blur

Ejemplo de comandos:
- "Agendar cita" → `/paciente/citas.php?nueva`
- "Ver pacientes" → `/admin/pacientes.php`
- "Cambiar tema" → toggle
- "Cerrar sesión" → logout

## Notas de Implementación

- **Sin CDNs externos para funcionalidad crítica:** Chart.js, iconos, todo local
- **QR codes:** librería local o generación server-side, nunca API externa
- **Animaciones:** CSS puro o JS vanilla, sin librerías de animación
- **Vidrio funciona en Safari:** `-webkit-backdrop-filter` incluido
- **Fallback de blur:** si no soporta backdrop-filter, fondo más opaco
- **Assets:** todos en `public/assets/` con helper `asset()` para rutas

---

**Fin de DESIGN.md — Versión 1**  
Sistema de diseño para MediConnect, proyecto universitario PHP puro.  
Última actualización: 2026-11-08
