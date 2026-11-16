<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="page-header" style="margin-bottom: var(--space-4);">
    <h1 class="page-title">Centro de Mensajería Clínica</h1>
    <p class="page-subtitle">Comunicación directa, cifrada y asincrónica entre pacientes y especialistas</p>
</div>

<div class="chat-layout">
    <!-- Lista Lateral de Contactos -->
    <div class="glass-card" style="padding: 0; display: flex; flex-direction: column; overflow: hidden;">
        <div style="padding: var(--space-4); border-bottom: 1px solid var(--glass-border); background: var(--glass-bg-secondary);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--space-2);">
                <h3 style="font-size: var(--text-base); font-weight: var(--weight-bold); color: var(--text-primary); margin: 0;">
                    <?php echo tiene_rol('paciente') ? 'Especialistas' : 'Pacientes Asignados'; ?>
                </h3>
                <span class="badge badge-info"><?php echo count($contactos); ?></span>
            </div>
            <input type="text" id="contact-search" class="form-input" placeholder="Filtrar por nombre..." oninput="filtrarContactos(this.value)" style="font-size: var(--text-xs); padding: var(--space-2) var(--space-3);">
        </div>

        <div id="contacts-list" style="flex: 1; overflow-y: auto; padding: var(--space-2);">
            <?php if (empty($contactos)): ?>
                <div class="empty-state" style="padding: var(--space-6);">
                    <p style="font-size: var(--text-xs);">No hay contactos disponibles.</p>
                </div>
            <?php else: ?>
                <?php foreach ($contactos as $c): ?>
                    <a href="index.php?page=chat&contacto_id=<?php echo $c['usuario_id']; ?>"
                       class="contact-item <?php echo ($contacto_activo && $contacto_activo['usuario_id'] == $c['usuario_id']) ? 'active' : ''; ?>"
                       data-name="<?php echo strtolower($c['nombre_completo']); ?>"
                       style="display: flex; align-items: center; gap: var(--space-3); padding: var(--space-3) var(--space-4); border-radius: var(--radius-md); text-decoration: none; margin-bottom: 2px; transition: all var(--duration-fast) var(--ease-smooth); <?php echo ($contacto_activo && $contacto_activo['usuario_id'] == $c['usuario_id']) ? 'background: var(--glass-bg-tertiary); border: 1px solid var(--glass-glow);' : ''; ?>">
                        <div style="width: 38px; height: 38px; border-radius: var(--radius-full); background: linear-gradient(135deg, var(--accent-primary), var(--accent-hover)); display: flex; align-items: center; justify-content: center; color: white; font-weight: var(--weight-bold); font-size: var(--text-xs); flex-shrink: 0;">
                            <?php echo strtoupper(substr($c['nombre_completo'], 0, 1)); ?>
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <div style="display: flex; justify-content: space-between; align-items: baseline;">
                                <span style="font-size: var(--text-sm); font-weight: var(--weight-semibold); color: var(--text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    <?php echo e($c['nombre_completo']); ?>
                                </span>
                                <?php if ($c['no_leidos'] > 0): ?>
                                    <span class="badge badge-cancelled" style="font-size: 10px; padding: 1px 6px;">
                                        <?php echo $c['no_leidos']; ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            <span style="font-size: var(--text-xs); color: var(--text-tertiary); display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                <?php echo e($c['especialidad'] ?? 'Atención General'); ?>
                            </span>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Área de Chat Activo -->
    <div class="glass-card chat-thread-container" style="padding: 0; overflow: hidden;">
        <?php if ($contacto_activo): ?>
            <!-- Encabezado de la Conversación -->
            <div style="padding: var(--space-4); border-bottom: 1px solid var(--glass-border); display: flex; justify-content: space-between; align-items: center; background: var(--glass-bg-secondary);">
                <div style="display: flex; align-items: center; gap: var(--space-3);">
                    <div style="width: 42px; height: 42px; border-radius: var(--radius-full); background: linear-gradient(135deg, var(--accent-primary), var(--accent-hover)); display: flex; align-items: center; justify-content: center; color: white; font-weight: var(--weight-bold); font-size: var(--text-sm);">
                        <?php echo strtoupper(substr($contacto_activo['nombre_completo'], 0, 1)); ?>
                    </div>
                    <div>
                        <h3 style="font-size: var(--text-base); font-weight: var(--weight-bold); color: var(--text-primary); margin: 0;">
                            <?php echo e($contacto_activo['nombre_completo']); ?>
                        </h3>
                        <div style="display: flex; align-items: center; gap: var(--space-2); margin-top: 2px;">
                            <span style="width: 8px; height: 8px; border-radius: var(--radius-full); background: var(--success); display: inline-block;"></span>
                            <span style="font-size: var(--text-xs); color: var(--text-tertiary);">
                                <?php echo e($contacto_activo['especialidad'] ?? ''); ?> • En Línea
                            </span>
                        </div>
                    </div>
                </div>

                <div style="display: flex; gap: var(--space-2);">
                    <a href="index.php?page=telemedicina&amp;contacto_id=<?php echo (int) $contacto_activo['usuario_id']; ?>" class="btn btn-secondary btn-sm" title="Iniciar Videoconsulta">
                        <svg class="icon icon-sm" aria-hidden="true" style="color: var(--accent-primary);"><use href="<?php echo asset('img/icons.svg#video'); ?>"></use></svg>
                        <span>Videoconsulta</span>
                    </a>
                </div>
            </div>

            <!-- Contenedor de Mensajes con Scroll -->
            <div id="chat-messages-container" class="chat-messages-area">
                <?php if (empty($mensajes)): ?>
                    <div id="chat-empty-hint" class="empty-state" style="padding: var(--space-12);">
                        <svg class="empty-illustration" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#message'); ?>"></use></svg>
                        <h4>Inicia la conversación</h4>
                        <p>Envía tu consulta o indicación médica para recibir atención inmediata.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($mensajes as $m): ?>
                        <?php $esMio = ($m['remitente_id'] == $_SESSION['usuario_id']); ?>
                        <div class="chat-bubble <?php echo $esMio ? 'chat-bubble-outgoing' : 'chat-bubble-incoming'; ?>" data-id="<?php echo $m['id']; ?>">
                            <div><?php echo nl2br(e($m['mensaje'])); ?></div>
                            <span class="chat-bubble-time">
                                <?php echo date('H:i', strtotime($m['created_at'])); ?>
                                <?php if ($esMio): ?>
                                    <span><?php echo $m['leido'] ? '✓✓' : '✓'; ?></span>
                                <?php endif; ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Indicador Simulado de Escritura -->
            <div id="typing-indicator-box" style="display: none; padding: var(--space-2) var(--space-4); background: var(--glass-bg-primary);">
                <div class="typing-indicator">
                    <span class="typing-dot"></span>
                    <span class="typing-dot"></span>
                    <span class="typing-dot"></span>
                    <span style="font-size: var(--text-xs); color: var(--text-tertiary); margin-left: 6px;">
                        <?php echo e($contacto_activo['nombre_completo']); ?> está escribiendo...
                    </span>
                </div>
            </div>

            <!-- Formulario de Envío de Mensaje -->
            <form id="chat-send-form" onsubmit="enviarMensaje(event)" style="padding: var(--space-3) var(--space-4); border-top: 1px solid var(--glass-border); display: flex; gap: var(--space-3); background: var(--glass-bg-secondary);">
                <input type="text" id="chat-input" class="form-input" placeholder="Escribe tu mensaje médico aquí..." autocomplete="off" required style="flex: 1; padding: var(--space-3) var(--space-4);">
                <button type="submit" class="btn btn-primary" id="btn-send-message">
                    <span>Enviar</span>
                    <svg class="icon icon-sm" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#send'); ?>"></use></svg>
                </button>
            </form>
        <?php else: ?>
            <div class="empty-state" style="padding: var(--space-16);">
                <svg class="empty-illustration" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#message'); ?>"></use></svg>
                <h3>Selecciona un contacto</h3>
                <p>Elige un especialista o paciente de la lista para ver el historial y chatear.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
const activeContactId = <?php echo intval($contacto_id); ?>;
const currentUserId = <?php echo intval($_SESSION['usuario_id']); ?>;
let pollingInterval = null;

document.addEventListener('DOMContentLoaded', function() {
    scrollChatToBottom();
    // Iniciar Polling de mensajes cada 3.5 segundos
    if (activeContactId > 0) {
        pollingInterval = setInterval(fetchNewMessages, 3500);
    }
});

function scrollChatToBottom() {
    const container = document.getElementById('chat-messages-container');
    if (container) {
        container.scrollTop = container.scrollHeight;
    }
}

function filtrarContactos(query) {
    const q = query.toLowerCase().trim();
    document.querySelectorAll('.contact-item').forEach(item => {
        const name = item.dataset.name || '';
        item.style.display = name.includes(q) ? 'flex' : 'none';
    });
}

function enviarMensaje(e) {
    e.preventDefault();
    const input = document.getElementById('chat-input');
    const msg = input.value.trim();
    if (!msg || activeContactId <= 0) return;

    // Inmediatamente mostrar en la UI de forma reactiva
    const container = document.getElementById('chat-messages-container');
    const emptyHint = document.getElementById('chat-empty-hint');
    if (emptyHint) emptyHint.remove();

    const now = new Date();
    const timeStr = String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0');

    const tempBubble = document.createElement('div');
    tempBubble.className = 'chat-bubble chat-bubble-outgoing';
    tempBubble.innerHTML = `<div>${escapeHtml(msg)}</div><span class="chat-bubble-time">${timeStr} <span>✓</span></span>`;
    container.appendChild(tempBubble);
    input.value = '';
    scrollChatToBottom();

    // Simular "médico escribiendo..."
    setTimeout(() => {
        const typingBox = document.getElementById('typing-indicator-box');
        if (typingBox) {
            typingBox.style.display = 'block';
            scrollChatToBottom();
            setTimeout(() => {
                typingBox.style.display = 'none';
            }, 3000);
        }
    }, 1000);

    // Enviar a backend
    const formData = new FormData();
    formData.append('destinatario_id', activeContactId);
    formData.append('mensaje', msg);
    formData.append('csrf_token', document.querySelector('meta[name="csrf-token"]')?.content || '');

    fetch('index.php?page=api-chat-enviar', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success) {
            showToast('Error al enviar mensaje: ' + (data.error || ''));
        }
    })
    .catch(err => console.error('Error enviando mensaje:', err));
}

function fetchNewMessages() {
    if (activeContactId <= 0) return;

    fetch(`index.php?page=api-chat-mensajes&contacto_id=${activeContactId}`)
        .then(res => res.json())
        .then(data => {
            if (data.mensajes) {
                renderMensajes(data.mensajes, data.usuario_id);
            }
        })
        .catch(err => console.error('Error en polling:', err));
}

function renderMensajes(mensajes, myId) {
    const container = document.getElementById('chat-messages-container');
    if (!container) return;

    // Evitar parpadeos: contar mensajes actuales
    const currentBubbles = container.querySelectorAll('.chat-bubble').length;
    if (mensajes.length === currentBubbles) return; // Sin cambios

    let html = '';
    mensajes.forEach(m => {
        const esMio = (m.remitente_id == myId);
        const time = m.created_at.substring(11, 16);
        const checks = esMio ? (m.leido == 1 ? '✓✓' : '✓') : '';
        html += `
            <div class="chat-bubble ${esMio ? 'chat-bubble-outgoing' : 'chat-bubble-incoming'}" data-id="${m.id}">
                <div>${escapeHtml(m.mensaje)}</div>
                <span class="chat-bubble-time">${time} <span>${checks}</span></span>
            </div>
        `;
    });

    container.innerHTML = html;
    scrollChatToBottom();
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
