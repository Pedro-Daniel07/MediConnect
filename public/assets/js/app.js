/**
 * Sistema MediConnect - JavaScript Principal
 */

// Variables globales
let theme = 'auto';

// Inicialización al cargar el DOM
document.addEventListener('DOMContentLoaded', function() {
    initTheme();
    initMenu();
    initNotifications();
    initForms();
    initCommandPalette();
});

/**
 * Inicializar tema (modo oscuro/claro)
 */
function initTheme() {
    try {
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'dark' || savedTheme === 'light') {
            theme = savedTheme;
            document.documentElement.setAttribute('data-theme', savedTheme);
        }
    } catch (error) {
        console.warn('No se pudo recuperar el tema guardado:', error);
    }
}

/**
 * Alternar tema
 */
function toggleTheme() {
    const newTheme = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';

    theme = newTheme;
    document.documentElement.setAttribute('data-theme', newTheme);
    try {
        localStorage.setItem('theme', newTheme);
    } catch (error) {
        console.warn('No se pudo guardar la preferencia de tema:', error);
    }

    showToast('Tema cambiado a ' + (newTheme === 'dark' ? 'oscuro' : 'claro'));
}

/**
 * Inicializar menú móvil
 */
function initMenu() {
    const toggle = document.querySelector('.nav-toggle');
    const menu = document.querySelector('.nav-menu');

    if (toggle && menu) {
        toggle.addEventListener('click', function() {
            menu.classList.toggle('active');
            toggle.setAttribute('aria-expanded', menu.classList.contains('active') ? 'true' : 'false');
        });

        // Cerrar menú al hacer clic fuera
        document.addEventListener('click', function(e) {
            if (!toggle.contains(e.target) && !menu.contains(e.target)) {
                menu.classList.remove('active');
                toggle.setAttribute('aria-expanded', 'false');
            }
        });
    }
}

/**
 * Inicializar notificaciones
 */
function initNotifications() {
    // Cargar contador de notificaciones
    loadNotificationCount();

    // Actualizar cada 30 segundos
    setInterval(loadNotificationCount, 30000);
}

/**
 * Cargar contador de notificaciones
 */
function loadNotificationCount() {
    const badge = document.getElementById('notif-count');
    if (!badge) return;

    fetch('index.php?page=api-notificaciones')
        .then(response => response.json())
        .then(data => {
            if (data.count > 0) {
                badge.textContent = data.count;
                badge.style.display = 'block';
            } else {
                badge.style.display = 'none';
            }
        })
        .catch(error => console.error('Error al cargar notificaciones:', error));
}

/**
 * Inicializar validación de formularios
 */
function initForms() {
    // Validación de contraseñas en registro
    const passwordForm = document.querySelector('form[action*="registro"]');
    if (passwordForm) {
        passwordForm.addEventListener('submit', function(e) {
            const password = document.getElementById('password');
            const confirm = document.getElementById('password_confirm');

            if (password && confirm && password.value !== confirm.value) {
                e.preventDefault();
                showToast('Las contraseñas no coinciden', 'error');
                confirm.focus();
            }
        });
    }

    // Validación de fechas en agendar cita
    const citaForm = document.querySelector('form[action*="agendar-cita"]');
    if (citaForm) {
        const fechaInput = document.getElementById('fecha');
        if (fechaInput) {
            // Establecer fecha mínima como mañana
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            fechaInput.min = tomorrow.toISOString().split('T')[0];
        }
    }
}

/**
 * Mostrar toast/notificación
 */
function showToast(message, type = 'info') {
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.setAttribute('aria-live', 'polite');
        container.setAttribute('aria-relevant', 'additions');
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    const tone = ['success', 'error', 'warning'].includes(type) ? type : 'info';
    toast.className = `toast toast-${tone}`;
    toast.setAttribute('role', tone === 'error' ? 'alert' : 'status');
    toast.textContent = message;
    container.appendChild(toast);

    setTimeout(() => {
        toast.classList.add('toast-leaving');
        setTimeout(() => toast.remove(), 250);
    }, 3500);
}

/**
 * Confirmar acción
 */
function confirmar(mensaje, callback) {
    if (confirm(mensaje)) {
        callback();
    }
}

/**
 * Cancelar cita
 */
function cancelarCita(citaId) {
    const motivo = prompt('¿Por qué deseas cancelar esta cita?');
    if (motivo && motivo.trim()) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'index.php?page=mis-citas';

        const inputAction = document.createElement('input');
        inputAction.type = 'hidden';
        inputAction.name = 'action';
        inputAction.value = 'cancelar';

        const inputId = document.createElement('input');
        inputId.type = 'hidden';
        inputId.name = 'cita_id';
        inputId.value = citaId;

        const inputMotivo = document.createElement('input');
        inputMotivo.type = 'hidden';
        inputMotivo.name = 'motivo';
        inputMotivo.value = motivo;

        const inputCsrf = document.createElement('input');
        inputCsrf.type = 'hidden';
        inputCsrf.name = 'csrf_token';
        inputCsrf.value = document.querySelector('meta[name="csrf-token"]')?.content || '';

        form.appendChild(inputAction);
        form.appendChild(inputId);
        form.appendChild(inputMotivo);
        form.appendChild(inputCsrf);

        document.body.appendChild(form);
        form.submit();
    }
}

/**
 * Cargar horarios disponibles para agendar cita
 */
function cargarHorariosDisponibles() {
    const medicoSelect = document.getElementById('medico_id');
    const fechaInput = document.getElementById('fecha');
    const horariosContainer = document.getElementById('horarios-disponibles');

    if (!medicoSelect || !fechaInput || !horariosContainer) return;

    const medicoId = medicoSelect.value;
    const fecha = fechaInput.value;

    if (!medicoId || !fecha) {
        horariosContainer.innerHTML = '<p class="text-muted">Selecciona un médico y una fecha</p>';
        return;
    }

    horariosContainer.setAttribute('aria-live', 'polite');
    horariosContainer.innerHTML = '<p class="text-muted">Cargando horarios...</p>';

    fetch(`index.php?page=api-horarios&medico_id=${encodeURIComponent(medicoId)}&fecha=${encodeURIComponent(fecha)}`)
        .then(response => {
            if (!response.ok) throw new Error('No se pudieron consultar los horarios');
            return response.json();
        })
        .then(data => {
            if (data.horarios && data.horarios.length > 0) {
                const grid = document.createElement('div');
                grid.className = 'horarios-grid';

                data.horarios.forEach(horario => {
                    const label = document.createElement('label');
                    label.className = 'horario-item';
                    const radio = document.createElement('input');
                    radio.type = 'radio';
                    radio.name = 'hora_inicio';
                    radio.value = horario.hora_inicio;
                    radio.dataset.horaFin = horario.hora_fin;
                    radio.required = true;
                    radio.addEventListener('change', function() {
                        const horaFinInput = document.getElementById('hora_fin');
                        if (horaFinInput) horaFinInput.value = this.dataset.horaFin;
                    });
                    const time = document.createElement('span');
                    time.textContent = horario.hora_display;
                    label.append(radio, time);
                    grid.appendChild(label);
                });
                horariosContainer.replaceChildren(grid);
            } else {
                horariosContainer.innerHTML = '<p class="text-muted">No hay horarios disponibles para esta fecha</p>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            horariosContainer.innerHTML = '<p class="text-danger">No se pudieron cargar los horarios. Intenta de nuevo.</p>';
        });
}

/**
 * Filtrar médicos por especialidad
 */
function filtrarMedicosPorEspecialidad() {
    const especialidadSelect = document.getElementById('especialidad_id');
    const medicoSelect = document.getElementById('medico_id');

    if (!especialidadSelect || !medicoSelect) return;

    const especialidadId = especialidadSelect.value;
    const opciones = medicoSelect.querySelectorAll('option');

    opciones.forEach(option => {
        if (option.value === '') {
            option.style.display = 'block';
            return;
        }

        if (!especialidadId || option.dataset.especialidad === especialidadId) {
            option.style.display = 'block';
        } else {
            option.style.display = 'none';
        }
    });

    // Reset selección de médico
    medicoSelect.value = '';
}

/**
 * Calcular IMC
 */
function calcularIMC(peso, altura) {
    if (!peso || !altura || peso <= 0 || altura <= 0) return null;
    const alturaMetros = altura / 100;
    return (peso / (alturaMetros * alturaMetros)).toFixed(1);
}

/**
 * Mostrar IMC en tiempo real
 */
function actualizarIMC() {
    const pesoInput = document.getElementById('peso');
    const alturaInput = document.getElementById('altura');
    const imcDisplay = document.getElementById('imc-display');

    if (!pesoInput || !alturaInput || !imcDisplay) return;

    const peso = parseFloat(pesoInput.value);
    const altura = parseFloat(alturaInput.value);

    const imc = calcularIMC(peso, altura);

    if (imc) {
        let clasificacion = '';
        if (imc < 18.5) clasificacion = 'Bajo peso';
        else if (imc < 25) clasificacion = 'Normal';
        else if (imc < 30) clasificacion = 'Sobrepeso';
        else clasificacion = 'Obesidad';

        imcDisplay.innerHTML = `<strong>IMC:</strong> ${imc} - ${clasificacion}`;
        imcDisplay.style.display = 'block';
    } else {
        imcDisplay.style.display = 'none';
    }
}

/**
 * Imprimir página
 */
function imprimirPagina() {
    window.print();
}

/**
 * Paleta de Comandos (Ctrl+K / Cmd+K)
 */
let commandPaletteOpen = false;
let previouslyFocusedElement = null;

function initCommandPalette() {
    // Crear estructura HTML de la paleta
    const paletteHTML = `
        <div id="command-palette" class="modal-backdrop" style="display: none;" role="presentation">
            <section class="modal command-palette-dialog" role="dialog" aria-modal="true" aria-labelledby="command-palette-title">
                <h2 id="command-palette-title" class="visually-hidden">Paleta de comandos</h2>
                <div class="modal-header" style="padding: var(--space-4);">
                    <input
                        type="text"
                        id="command-search"
                        aria-label="Buscar comandos o páginas"
                        aria-controls="command-results"
                        aria-autocomplete="list"
                        placeholder="Buscar comando o página..."
                        class="form-input"
                        style="border: none; background: transparent; font-size: var(--text-lg);"
                        autocomplete="off"
                    >
                </div>
                <div class="modal-body" style="padding: 0; max-height: 400px; overflow-y: auto;">
                    <div id="command-results" role="listbox" aria-label="Comandos disponibles"></div>
                </div>
            </section>
        </div>
    `;
    document.body.insertAdjacentHTML('beforeend', paletteHTML);

    // Atajos de teclado
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
            e.preventDefault();
            toggleCommandPalette();
        }
        if (e.key === 'Escape' && commandPaletteOpen) {
            closeCommandPalette();
        }
        if (commandPaletteOpen && e.key === 'Tab') {
            const palette = document.getElementById('command-palette');
            const focusable = Array.from(palette.querySelectorAll('input, button, a[href], [tabindex]:not([tabindex="-1"])'));
            const first = focusable[0];
            const last = focusable[focusable.length - 1];
            if (e.shiftKey && document.activeElement === first) {
                e.preventDefault();
                last.focus();
            } else if (!e.shiftKey && document.activeElement === last) {
                e.preventDefault();
                first.focus();
            }
        }
    });

    // Cerrar al hacer clic fuera
    document.getElementById('command-palette')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeCommandPalette();
        }
    });

    // Búsqueda en tiempo real
    document.getElementById('command-search')?.addEventListener('input', function(e) {
        filterCommands(e.target.value);
    });

    // Navegación con teclado en resultados
    document.getElementById('command-search')?.addEventListener('keydown', function(e) {
        const results = document.querySelectorAll('#command-results .command-item');
        const selected = document.querySelector('.command-item.selected');
        let index = Array.from(results).indexOf(selected);

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            index = Math.min(index + 1, results.length - 1);
            selectCommandItem(index);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            index = Math.max(index - 1, 0);
            selectCommandItem(index);
        } else if (e.key === 'Enter' && selected) {
            e.preventDefault();
            executeCommand(selected.dataset.action);
        }
    });
}

function toggleCommandPalette() {
    if (commandPaletteOpen) {
        closeCommandPalette();
    } else {
        openCommandPalette();
    }
}

function openCommandPalette() {
    const palette = document.getElementById('command-palette');
    const searchInput = document.getElementById('command-search');

    if (palette && searchInput) {
        previouslyFocusedElement = document.activeElement;
        palette.style.display = 'flex';
        searchInput.value = '';
        commandPaletteOpen = true;
        filterCommands('');
        searchInput.focus();
    }
}

function closeCommandPalette() {
    const palette = document.getElementById('command-palette');
    if (palette) {
        palette.style.display = 'none';
        commandPaletteOpen = false;
        previouslyFocusedElement?.focus();
    }
}

function filterCommands(query) {
    const commands = getAvailableCommands();
    const resultsContainer = document.getElementById('command-results');

    if (!resultsContainer) return;

    const normalizedQuery = query.toLowerCase().trim();
    const filtered = commands.filter(cmd =>
        cmd.title.toLowerCase().includes(normalizedQuery) ||
        cmd.description.toLowerCase().includes(normalizedQuery)
    );

    if (filtered.length === 0) {
        resultsContainer.innerHTML = '<div class="empty-state" style="padding: var(--space-8);"><p>No se encontraron comandos</p></div>';
        return;
    }

    resultsContainer.replaceChildren();
    filtered.forEach((cmd, index) => {
        const item = document.createElement('button');
        item.type = 'button';
        item.className = `command-item${index === 0 ? ' selected' : ''}`;
        item.dataset.action = cmd.action;
        item.id = `command-option-${index}`;
        item.setAttribute('role', 'option');
        item.setAttribute('aria-selected', index === 0 ? 'true' : 'false');
        item.innerHTML = `<span aria-hidden="true">${cmd.icon}</span><span class="command-copy"><span class="command-title">${cmd.title}</span><span class="command-description">${cmd.description}</span></span>`;
        item.addEventListener('click', () => executeCommand(cmd.action));
        resultsContainer.appendChild(item);
    });
    document.getElementById('command-search')?.setAttribute('aria-activedescendant', 'command-option-0');
}

function selectCommandItem(index) {
    const items = document.querySelectorAll('#command-results .command-item');
    items.forEach((item, i) => {
        item.classList.toggle('selected', i === index);
        item.setAttribute('aria-selected', i === index ? 'true' : 'false');
    });
    if (items[index]) {
        document.getElementById('command-search')?.setAttribute('aria-activedescendant', items[index].id);
        items[index].scrollIntoView({ block: 'nearest' });
    }
}

function getAvailableCommands() {
    const baseCommands = [
        { title: 'Telemedicina en Vivo', description: 'Abrir sala de videoconsulta WebRTC', icon: '📹', action: 'nav:index.php?page=telemedicina' },
        { title: 'Chat Clínico', description: 'Mensajería directa paciente - médico', icon: '💬', action: 'nav:index.php?page=chat' },
        { title: 'Carné Digital Wallet', description: 'Ver credencial con QR e imprimir', icon: '💳', action: 'nav:index.php?page=carne-digital' },
        { title: 'Signos Vitales y Score', description: 'Puntaje de salud 0-100 y cálculo de IMC', icon: '📈', action: 'nav:index.php?page=signos-vitales' },
        { title: 'Carné de Vacunación', description: 'Esquema de vacunas aplicadas y alertas', icon: '💉', action: 'nav:index.php?page=vacunacion' },
        { title: 'Lista de Espera', description: 'Gestión inteligente de cupos liberados', icon: '⏱️', action: 'nav:index.php?page=lista-espera' },
        { title: 'Auditoría y Seguridad', description: 'Bitácora de eventos del sistema en vivo', icon: '🛡️', action: 'nav:index.php?page=auditoria' },
        { title: 'Exportar Citas (CSV)', description: 'Descargar reporte completo en CSV', icon: '📥', action: 'nav:index.php?page=exportar-citas' },
        { title: 'Exportar Pacientes (Excel)', description: 'Descargar base de pacientes en CSV', icon: '📊', action: 'nav:index.php?page=exportar-pacientes' },
        { title: 'Agendar Cita', description: 'Reservar nueva consulta médica', icon: '📅', action: 'nav:index.php?page=agendar-cita' },
        { title: 'Mis Citas', description: 'Ver tus citas agendadas', icon: '📋', action: 'nav:index.php?page=mis-citas' },
        { title: 'Historial Clínico', description: 'Ver tu historial médico', icon: '🩺', action: 'nav:index.php?page=mi-historial' },
        { title: 'Fórmulas Médicas', description: 'Consultar tus recetas', icon: '💊', action: 'nav:index.php?page=mis-formulas' },
        { title: 'Línea de Tiempo', description: 'Evolución de tu salud', icon: '📊', action: 'nav:index.php?page=linea-tiempo' },
        { title: 'Gestionar Usuarios', description: 'Administrar cuentas de usuario', icon: '👥', action: 'nav:index.php?page=usuarios' },
        { title: 'Gestionar Médicos', description: 'Administrar médicos del sistema', icon: '👨‍⚕️', action: 'nav:index.php?page=medicos' },
        { title: 'Reportes y Analítica', description: 'Ver estadísticas del sistema', icon: '📈', action: 'nav:index.php?page=reportes' },
        { title: 'Cambiar Tema', description: 'Alternar entre modo claro y oscuro', icon: '🌓', action: 'toggle-theme' },
        { title: 'Cerrar Sesión', description: 'Salir de tu cuenta', icon: '🚪', action: 'logout' }
    ];

    return baseCommands;
}

function executeCommand(action) {
    closeCommandPalette();

    if (action === 'toggle-theme') {
        toggleTheme();
    } else if (action === 'logout') {
        if (confirm('¿Estás seguro de cerrar sesión?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'index.php?page=logout';
            const token = document.createElement('input');
            token.type = 'hidden';
            token.name = 'csrf_token';
            token.value = document.querySelector('meta[name="csrf-token"]')?.content || '';
            form.appendChild(token);
            document.body.appendChild(form);
            form.submit();
        }
    } else if (action.startsWith('nav:')) {
        window.location.href = action.substring(4);
    }
}

/**
 * Animaciones de entrada
 */
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.animation = 'fadeInUp 0.5s ease forwards';
        }
    });
}, observerOptions);

// Observar elementos con animación
document.querySelectorAll('.stat-card, .dashboard-section, .action-card').forEach(el => {
    observer.observe(el);
});

// Agregar animaciones CSS
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

`;
document.head.appendChild(style);
