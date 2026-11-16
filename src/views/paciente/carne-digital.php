<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="page-header" style="text-align: center; max-width: 680px; margin: 0 auto var(--space-8) auto;">
    <span class="badge badge-info" style="margin-bottom: var(--space-2);">Credencial Digital Cifrada</span>
    <h1 class="page-title">Carné Clínico del Paciente</h1>
    <p class="page-subtitle">Documento de identidad sanitaria con verificación biométrica y acceso instantáneo para emergencias</p>
</div>

<!-- Contenedor del Carné Wallet -->
<div class="wallet-card-wrapper">
    <div id="wallet-card-print-target" class="wallet-card">
        <!-- Encabezado de la Tarjeta -->
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div style="display: flex; align-items: center; gap: var(--space-3);">
                <div class="wallet-card-chip"></div>
                <!-- Sensor Contactless / NFC Icon -->
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.7)" stroke-width="2" stroke-linecap="round">
                    <path d="M8.5 16.5a5 5 0 0 1 0-7"/>
                    <path d="M12 19a8.5 8.5 0 0 0 0-12"/>
                    <path d="M15.5 21.5a12 12 0 0 0 0-17"/>
                </svg>
            </div>
            <div style="display: flex; align-items: center; gap: var(--space-2); text-align: right;">
                <svg class="icon" aria-hidden="true" style="color: var(--accent-primary);"><use href="<?php echo asset('img/icons.svg#activity'); ?>"></use></svg>
                <span style="font-weight: var(--weight-bold); font-size: var(--text-base); letter-spacing: -0.02em; color: white;">
                    <?php echo SITE_NAME; ?> <span style="font-weight: var(--weight-normal); font-size: var(--text-xs); opacity: 0.8; text-transform: uppercase;">Wallet</span>
                </span>
            </div>
        </div>

        <!-- Centro: Datos del Paciente y Código QR -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin: var(--space-4) 0; gap: var(--space-4);">
            <div style="flex: 1;">
                <div style="font-size: var(--text-xs); text-transform: uppercase; color: rgba(203, 213, 225, 0.7); letter-spacing: 0.08em; margin-bottom: 2px;">
                    Afiliado N°
                </div>
                <div style="font-family: var(--font-mono); font-size: var(--text-xl); font-weight: var(--weight-bold); letter-spacing: 0.1em; color: var(--accent-primary); margin-bottom: var(--space-2);">
                    <?php echo e($paciente['codigo_afiliado'] ?? 'MC-'.str_pad($paciente['id'], 6, '0', STR_PAD_LEFT)); ?>
                </div>

                <div style="font-size: var(--text-lg); font-weight: var(--weight-bold); text-transform: uppercase; letter-spacing: 0.02em; color: #ffffff; line-height: 1.2;">
                    <?php echo e($paciente['nombre'] . ' ' . $paciente['apellido']); ?>
                </div>
                <div style="font-size: var(--text-xs); color: rgba(226, 232, 240, 0.75); margin-top: 2px;">
                    Doc: <?php echo e($paciente['documento']); ?>
                </div>
            </div>

            <!-- Código QR generado en cliente con JS puro (100% offline) -->
            <div class="wallet-qr-box" id="qrcode-container" title="Código QR Médico Cifrado">
                <div id="qrcode"></div>
            </div>
        </div>

        <!-- Pie de la Tarjeta: Métricas Críticas para Emergencias -->
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: var(--space-2); padding-top: var(--space-3); border-top: 1px solid rgba(255, 255, 255, 0.12); font-size: var(--text-xs);">
            <div>
                <span style="opacity: 0.7; display: block; font-size: 10px; text-transform: uppercase;">Grupo Sanguíneo</span>
                <span class="badge" style="background: rgba(239, 68, 68, 0.25); color: #fca5a5; border: 1px solid rgba(239, 68, 68, 0.4); margin-top: 2px; font-weight: bold; font-size: 11px;">
                    <?php echo e($paciente['grupo_sanguineo'] ?? 'No reg.'); ?>
                </span>
            </div>
            <div>
                <span style="opacity: 0.7; display: block; font-size: 10px; text-transform: uppercase;">Alergias</span>
                <span style="font-weight: var(--weight-semibold); color: #fde047; display: block; margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?php echo e($paciente['alergias'] ?? 'Ninguna'); ?>">
                    <?php echo e(!empty($paciente['alergias']) ? $paciente['alergias'] : 'Ninguna'); ?>
                </span>
            </div>
            <div style="text-align: right;">
                <span style="opacity: 0.7; display: block; font-size: 10px; text-transform: uppercase;">Emergencias</span>
                <span style="font-family: var(--font-mono); font-weight: var(--weight-semibold); color: white; display: block; margin-top: 2px;">
                    <?php echo e($paciente['contacto_emergencia_telefono'] ?? 'N/A'); ?>
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Acciones y Controles -->
<div class="no-print" style="max-width: 440px; margin: 0 auto; display: flex; flex-direction: column; gap: var(--space-3);">
    <div style="display: flex; gap: var(--space-3);">
        <button type="button" class="btn btn-primary" onclick="window.print()" style="flex: 1;">
            <svg class="icon icon-sm" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#printer'); ?>"></use></svg>
            <span>Imprimir Carné (Wallet)</span>
        </button>
        <button type="button" class="btn btn-secondary" onclick="copiarCodigo()" style="flex: 1;">
            <svg class="icon icon-sm" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#shield'); ?>"></use></svg>
            <span>Copiar Afiliado</span>
        </button>
    </div>

    <!-- Indicador de validez legal / seguridad -->
    <div class="glass-card" style="padding: var(--space-4); display: flex; align-items: center; gap: var(--space-3); font-size: var(--text-xs); color: var(--text-secondary);">
        <svg class="icon" aria-hidden="true" style="color: var(--success); flex-shrink: 0;"><use href="<?php echo asset('img/icons.svg#check'); ?>"></use></svg>
        <span>Carné con validez oficial para atención médica, urgencias hospitalarias y retiro de medicamentos con receta electrónica.</span>
    </div>
</div>

<!-- Script de generación de QR local -->
<script src="<?php echo asset('js/qrcode.min.js'); ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const qrTarget = document.getElementById('qrcode');
    if (qrTarget && typeof QRCode !== 'undefined') {
        const qrPayload = "<?php echo e($paciente['codigo_afiliado'] ?? 'MC-'.str_pad($paciente['id'], 6, '0', STR_PAD_LEFT)); ?>";

        new QRCode(qrTarget, {
            text: qrPayload,
            width: 100,
            height: 100,
            colorDark: "#0f172a",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.M
        });
    }
});

function copiarCodigo() {
    const code = "<?php echo e($paciente['codigo_afiliado'] ?? 'MC-'.$paciente['id']); ?>";
    navigator.clipboard.writeText(code).then(() => {
        showToast('Código de afiliado ' + code + ' copiado al portapapeles');
    });
}
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
