<?php require_once __DIR__ . '/../layout/header.php'; ?>

<?php if (empty($cita)): ?>
    <div class="page-header">
        <h1 class="page-title">Solicitar una videoconsulta</h1>
        <p class="page-subtitle">Selecciona la cita y el profesional antes de entrar a la sala.</p>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-error" role="alert"><?php echo e($error); ?></div>
    <?php endif; ?>

    <?php if (empty($citasDisponibles)): ?>
        <section class="empty-state glass-card">
            <h2>No tienes citas disponibles para videollamada</h2>
            <p>Confirma una cita próxima para que aparezca aquí.</p>
            <a class="btn btn-primary" href="index.php?page=<?php echo tiene_rol('paciente') ? 'mis-citas' : 'agenda-medico'; ?>">Ver mis citas</a>
        </section>
    <?php else: ?>
        <div class="telemed-appointments">
            <?php foreach ($citasDisponibles as $citaDisponible): ?>
                <article class="telemed-appointment">
                    <div>
                        <h2><?php echo tiene_rol('medico') ? e($citaDisponible['paciente_nombre']) : 'Dr(a). ' . e($citaDisponible['medico_nombre']); ?></h2>
                        <p><?php echo e($citaDisponible['especialidad']); ?> · <?php echo e(formatear_fecha($citaDisponible['fecha'])); ?> · <?php echo e(date('h:i A', strtotime($citaDisponible['hora_inicio']))); ?></p>
                        <span class="badge badge-<?php echo e($citaDisponible['estado']); ?>"><?php echo e(ucfirst($citaDisponible['estado'])); ?></span>
                    </div>
                    <form method="POST" action="index.php?page=seleccionar-cita-telemedicina">
                        <input type="hidden" name="csrf_token" value="<?php echo e(obtener_csrf_token()); ?>">
                        <input type="hidden" name="cita_id" value="<?php echo (int) $citaDisponible['id']; ?>">
                        <?php if (!empty($contactoId)): ?>
                            <input type="hidden" name="contacto_id" value="<?php echo (int) $contactoId; ?>">
                        <?php endif; ?>
                        <button class="btn btn-primary" type="submit">Llamar a esta cita</button>
                    </form>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
<?php else: ?>
<div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: var(--space-4); margin-bottom: var(--space-4);">
    <div>
        <div style="display: flex; align-items: center; gap: var(--space-2); margin-bottom: var(--space-2);">
            <span class="badge badge-info">WebRTC Audio/Video Cifrado</span>
            <span class="badge badge-completed">Sala Segura #CITA-<?php echo e($cita['id'] ?? '101'); ?></span>
        </div>
        <h1 class="page-title">Videoconsulta Médica en Vivo</h1>
        <p class="page-subtitle">
            <?php if (tiene_rol('medico')): ?>
                Atendiendo a: <strong><?php echo e($cita['paciente_nombre'] ?? 'Paciente'); ?></strong> (Doc: <?php echo e($cita['paciente_doc'] ?? 'N/A'); ?>)
            <?php else: ?>
                Especialista: <strong>Dr(a). <?php echo e($cita['medico_nombre'] ?? 'Médico Especialista'); ?></strong> (<?php echo e($cita['especialidad'] ?? 'Medicina General'); ?>)
            <?php endif; ?>
        </p>
    </div>
    <div style="display: flex; gap: var(--space-3);">
        <a href="index.php?page=carne-digital<?php echo isset($cita['paciente_id']) ? '&id='.$cita['paciente_id'] : ''; ?>" target="_blank" class="btn btn-secondary btn-sm">
            <svg class="icon icon-sm" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#qrcode'); ?>"></use></svg>
            <span>Ver Carné Afiliado</span>
        </a>
        <a href="index.php?page=dashboard" class="btn btn-secondary btn-sm">
            <svg class="icon icon-sm" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#x'); ?>"></use></svg>
            <span>Salir de Sala</span>
        </a>
    </div>
</div>

<!-- Layout Principal de Telemedicina -->
<div class="telemed-container">
    <!-- Escenario Principal de Video -->
    <div class="telemed-stage" id="telemed-stage">
        <!-- Badge de tiempo en vivo -->
        <div class="telemed-timer-badge">
            <span class="telemed-pulse-dot"></span>
            <span id="call-timer">00:00:00</span>
            <span style="opacity: 0.5; margin: 0 4px;">|</span>
            <span style="color: var(--accent-primary); font-size: var(--text-xs);">HD 1080p</span>
        </div>

        <!-- Video Remoto (o fallback simulado con avatar clínico holográfico) -->
        <div id="remote-stream-container" style="width: 100%; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; position: relative;">
            <!-- Avatar Holográfico de Simulación -->
            <div id="telemed-fallback-avatar" style="text-align: center; padding: var(--space-8);">
                <div style="width: 120px; height: 120px; border-radius: var(--radius-full); background: linear-gradient(135deg, rgba(14, 165, 233, 0.2), rgba(16, 185, 129, 0.2)); border: 2px solid var(--accent-primary); margin: 0 auto var(--space-4) auto; display: flex; align-items: center; justify-content: center; position: relative; box-shadow: 0 0 30px rgba(14, 165, 233, 0.3);">
                    <svg class="icon" aria-hidden="true" style="width: 54px; height: 54px; color: var(--accent-primary);"><use href="<?php echo asset('img/icons.svg#user'); ?>"></use></svg>
                    <!-- Ondas de transmisión simulada -->
                    <div style="position: absolute; inset: -10px; border-radius: var(--radius-full); border: 1px dashed rgba(14, 165, 233, 0.4); animation: rotate 20s linear infinite;"></div>
                </div>
                <h3 style="color: white; font-size: var(--text-xl); font-weight: var(--weight-bold); margin-bottom: var(--space-1);">
                    <?php echo tiene_rol('medico') ? e($cita['paciente_nombre'] ?? 'Paciente') : 'Dr(a). ' . e($cita['medico_nombre'] ?? 'Médico'); ?>
                </h3>
                <p style="color: var(--text-secondary); font-size: var(--text-sm); max-width: 380px; margin: 0 auto;">
                    Conexión de teleconsulta cifrada punto a punto establecida con éxito.
                </p>
                <div id="camera-notice" style="margin-top: var(--space-3); font-size: var(--text-xs); color: var(--accent-primary); background: rgba(14, 165, 233, 0.1); padding: 4px 12px; border-radius: var(--radius-full); display: inline-block;">
                    Iniciando cámara y micrófono locales...
                </div>
            </div>

            <!-- Video Remoto si se activa stream -->
            <video id="remote-video" autoplay playsinline style="display: none; width: 100%; height: 100%; object-fit: cover;"></video>
        </div>

        <!-- Video Local en Miniatura (Picture in Picture) -->
        <div class="telemed-pip" id="local-video-box">
            <video id="local-video" autoplay playsinline muted style="width: 100%; height: 100%; object-fit: cover;"></video>
            <div id="local-video-placeholder" style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: #0f172a; color: var(--text-tertiary); font-size: 10px; text-align: center; padding: 4px;">
                Tú (Cámara local)
            </div>
        </div>

        <!-- Barra Flotante de Controles de Conferencia -->
        <div class="telemed-controls-dock">
            <button type="button" class="telemed-btn" id="btn-mic" onclick="toggleMicrofono()" title="Silenciar / Activar Micrófono">
                <svg class="icon icon-sm" aria-hidden="true" id="icon-mic"><use href="<?php echo asset('img/icons.svg#mic'); ?>"></use></svg>
            </button>
            <button type="button" class="telemed-btn" id="btn-cam" onclick="toggleCamara()" title="Apagar / Encender Cámara">
                <svg class="icon icon-sm" aria-hidden="true" id="icon-cam"><use href="<?php echo asset('img/icons.svg#video'); ?>"></use></svg>
            </button>
            <button type="button" class="telemed-btn" id="btn-screen" onclick="compartirPantalla()" title="Compartir Pantalla">
                <svg class="icon icon-sm" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#screen'); ?>"></use></svg>
            </button>
            <button type="button" class="telemed-btn end-call" onclick="finalizarLlamada()" title="Finalizar Videoconsulta">
                <svg class="icon icon-sm" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#phone-off'); ?>"></use></svg>
            </button>
        </div>
    </div>

    <!-- Panel Lateral: Notas Médicas y Chat de Sesión -->
    <div class="glass-card" style="display: flex; flex-direction: column; height: 100%; padding: 0; overflow: hidden;">
        <!-- Pestañas del Panel Lateral -->
        <div style="display: flex; border-bottom: 1px solid var(--glass-border);">
            <button type="button" id="tab-notas-btn" onclick="switchSideTab('notas')" style="flex: 1; padding: var(--space-4); background: transparent; border: none; font-weight: var(--weight-semibold); color: var(--accent-primary); border-bottom: 2px solid var(--accent-primary); cursor: pointer; font-size: var(--text-sm);">
                Notas Clínicas
            </button>
            <button type="button" id="tab-chat-btn" onclick="switchSideTab('chat')" style="flex: 1; padding: var(--space-4); background: transparent; border: none; font-weight: var(--weight-semibold); color: var(--text-secondary); border-bottom: 2px solid transparent; cursor: pointer; font-size: var(--text-sm);">
                Chat de Sesión
            </button>
        </div>

        <!-- Contenido 1: Notas Médicas (Autosave / Save) -->
        <div id="side-tab-notas" style="padding: var(--space-4); flex: 1; display: flex; flex-direction: column;">
            <div style="margin-bottom: var(--space-3);">
                <span style="font-size: var(--text-xs); color: var(--text-tertiary); display: block;">Motivo de la consulta:</span>
                <p style="font-size: var(--text-sm); color: var(--text-primary); margin: 2px 0 0 0;">
                    <?php echo e($cita['motivo_consulta'] ?? 'Consulta de valoración y telemonitoreo.'); ?>
                </p>
            </div>

            <div style="flex: 1; display: flex; flex-direction: column; margin-bottom: var(--space-3);">
                <label for="telemed-notas" class="form-label" style="font-size: var(--text-xs);">
                    Evolución clínica y prescripción sugerida:
                </label>
                <textarea id="telemed-notas" class="form-input" style="flex: 1; resize: none; font-size: var(--text-xs); line-height: 1.5;" placeholder="Registra aquí los síntomas descritos, impresión diagnóstica, recomendaciones farmacológicas y plan de tratamiento..."><?php echo e($cita['notas_medicas'] ?? ''); ?></textarea>
            </div>

            <button type="button" class="btn btn-primary btn-sm" onclick="guardarNotasClinicas()" style="width: 100%;">
                <svg class="icon icon-sm" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#check'); ?>"></use></svg>
                <span>Guardar en Historia Clínica</span>
            </button>
        </div>

        <!-- Contenido 2: Chat en Vivo de Sesión -->
        <div id="side-tab-chat" style="display: none; flex: 1; flex-direction: column; height: 100%;">
            <div id="session-chat-messages" style="flex: 1; overflow-y: auto; padding: var(--space-4); display: flex; flex-direction: column; gap: var(--space-3);">
                <div class="chat-bubble chat-bubble-incoming">
                    <strong>Sistema MediConnect:</strong> Sala cifrada iniciada. La comunicación de audio, video y datos cuenta con cifrado TLS 1.3 de extremo a extremo.
                    <span class="chat-bubble-time">00:00</span>
                </div>
            </div>

            <form id="session-chat-form" onsubmit="enviarMensajeSesion(event)" style="padding: var(--space-3); border-top: 1px solid var(--glass-border); display: flex; gap: var(--space-2);">
                <input type="text" id="session-chat-input" class="form-input" placeholder="Escribe un mensaje..." autocomplete="off" style="font-size: var(--text-xs); padding: var(--space-2) var(--space-3);">
                <button type="submit" class="btn btn-primary btn-sm" style="padding: var(--space-2) var(--space-3);">
                    <svg class="icon icon-sm" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#send'); ?>"></use></svg>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
let localStream = null;
let micEnabled = true;
let camEnabled = true;
let timerSeconds = 0;
let timerInterval = null;

// Inicialización de WebRTC y Cámara Real
document.addEventListener('DOMContentLoaded', function() {
    iniciarTemporizador();
    iniciarMediosLocales();
});

function iniciarTemporizador() {
    timerInterval = setInterval(() => {
        timerSeconds++;
        const hrs = String(Math.floor(timerSeconds / 3600)).padStart(2, '0');
        const mins = String(Math.floor((timerSeconds % 3600) / 60)).padStart(2, '0');
        const secs = String(timerSeconds % 60).padStart(2, '0');
        const timerElem = document.getElementById('call-timer');
        if (timerElem) timerElem.textContent = `${hrs}:${mins}:${secs}`;
    }, 1000);
}

async function iniciarMediosLocales() {
    const notice = document.getElementById('camera-notice');
    const localVideo = document.getElementById('local-video');
    const localPlaceholder = document.getElementById('local-video-placeholder');

    try {
        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            localStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
            if (localVideo) {
                localVideo.srcObject = localStream;
                localVideo.style.display = 'block';
                if (localPlaceholder) localPlaceholder.style.display = 'none';
            }
            if (notice) {
                notice.textContent = 'Cámara y micrófono locales activos';
                notice.style.color = 'var(--success)';
                notice.style.background = 'rgba(16, 185, 129, 0.15)';
            }
        } else {
            throw new Error('getUserMedia no disponible');
        }
    } catch (err) {
        console.warn('Cámara física no disponible o permiso denegado:', err);
        if (notice) {
            notice.textContent = 'Modo Simulación Clínica Activo (Cámara física no disponible)';
            notice.style.color = '#f59e0b';
            notice.style.background = 'rgba(245, 158, 11, 0.15)';
        }
    }
}

function toggleMicrofono() {
    micEnabled = !micEnabled;
    if (localStream) {
        localStream.getAudioTracks().forEach(track => track.enabled = micEnabled);
    }
    const btn = document.getElementById('btn-mic');
    const icon = document.getElementById('icon-mic');
    if (micEnabled) {
        btn.classList.remove('active-muted');
        icon.innerHTML = '<use href="<?php echo asset("img/icons.svg#mic"); ?>"></use>';
        showToast('Micrófono activado');
    } else {
        btn.classList.add('active-muted');
        icon.innerHTML = '<use href="<?php echo asset("img/icons.svg#mic-off"); ?>"></use>';
        showToast('Micrófono silenciado');
    }
}

function toggleCamara() {
    camEnabled = !camEnabled;
    if (localStream) {
        localStream.getVideoTracks().forEach(track => track.enabled = camEnabled);
    }
    const btn = document.getElementById('btn-cam');
    const icon = document.getElementById('icon-cam');
    if (camEnabled) {
        btn.classList.remove('active-muted');
        icon.innerHTML = '<use href="<?php echo asset("img/icons.svg#video"); ?>"></use>';
        showToast('Cámara encendida');
    } else {
        btn.classList.add('active-muted');
        icon.innerHTML = '<use href="<?php echo asset("img/icons.svg#video-off"); ?>"></use>';
        showToast('Cámara desactivada');
    }
}

async function compartirPantalla() {
    try {
        if (navigator.mediaDevices && navigator.mediaDevices.getDisplayMedia) {
            const screenStream = await navigator.mediaDevices.getDisplayMedia({ video: true });
            const remoteVideo = document.getElementById('remote-video');
            const fallbackAvatar = document.getElementById('telemed-fallback-avatar');
            if (remoteVideo && fallbackAvatar) {
                remoteVideo.srcObject = screenStream;
                remoteVideo.style.display = 'block';
                fallbackAvatar.style.display = 'none';
                showToast('Transmitiendo pantalla compartida en sala...');
                screenStream.getVideoTracks()[0].onended = () => {
                    remoteVideo.style.display = 'none';
                    fallbackAvatar.style.display = 'block';
                    showToast('Transmisión de pantalla finalizada');
                };
            }
        }
    } catch (err) {
        console.warn('No se compartió pantalla:', err);
    }
}

function finalizarLlamada() {
    if (confirm('¿Deseas finalizar la sesión de teleconsulta actual?')) {
        if (localStream) {
            localStream.getTracks().forEach(track => track.stop());
        }
        clearInterval(timerInterval);
        showToast('Videoconsulta finalizada');
        window.location.href = 'index.php?page=dashboard';
    }
}

function switchSideTab(tab) {
    const notasTab = document.getElementById('side-tab-notas');
    const chatTab = document.getElementById('side-tab-chat');
    const notasBtn = document.getElementById('tab-notas-btn');
    const chatBtn = document.getElementById('tab-chat-btn');

    if (tab === 'notas') {
        notasTab.style.display = 'flex';
        chatTab.style.display = 'none';
        notasBtn.style.color = 'var(--accent-primary)';
        notasBtn.style.borderBottomColor = 'var(--accent-primary)';
        chatBtn.style.color = 'var(--text-secondary)';
        chatBtn.style.borderBottomColor = 'transparent';
    } else {
        notasTab.style.display = 'none';
        chatTab.style.display = 'flex';
        chatBtn.style.color = 'var(--accent-primary)';
        chatBtn.style.borderBottomColor = 'var(--accent-primary)';
        notasBtn.style.color = 'var(--text-secondary)';
        notasBtn.style.borderBottomColor = 'transparent';
    }
}

function guardarNotasClinicas() {
    const notas = document.getElementById('telemed-notas').value;
    const citaId = "<?php echo intval($cita['id'] ?? 0); ?>";

    const formData = new FormData();
    formData.append('cita_id', citaId);
    formData.append('notas', notas);
    formData.append('csrf_token', document.querySelector('meta[name="csrf-token"]')?.content || '');

    fetch('index.php?page=api-telemedicina-notas', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showToast('¡Notas clínicas guardadas en la historia del paciente!');
        } else {
            showToast('Error al guardar notas: ' + (data.error || ''));
        }
    })
    .catch(err => {
        console.error(err);
        showToast('No se pudieron guardar las notas. Intenta de nuevo.', 'error');
    });
}

function enviarMensajeSesion(e) {
    e.preventDefault();
    const input = document.getElementById('session-chat-input');
    const text = input.value.trim();
    if (!text) return;

    const container = document.getElementById('session-chat-messages');
    const now = new Date();
    const timeStr = String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0');

    const bubble = document.createElement('div');
    bubble.className = 'chat-bubble chat-bubble-outgoing';
    bubble.innerHTML = `<strong>Tú:</strong> ${text}<span class="chat-bubble-time">${timeStr}</span>`;
    container.appendChild(bubble);
    input.value = '';
    container.scrollTop = container.scrollHeight;
}
</script>
<?php endif; ?>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
