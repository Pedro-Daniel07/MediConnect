<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="dashboard-header">
    <h1>Mis Citas</h1>
    <p>Gestiona tus citas médicas</p>
</div>

<?php if (isset($_SESSION['mensaje_exito'])): ?>
    <div class="alert alert-success">
        <?php echo e($_SESSION['mensaje_exito']); ?>
        <?php unset($_SESSION['mensaje_exito']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['mensaje_error'])): ?>
    <div class="alert alert-error">
        <?php echo e($_SESSION['mensaje_error']); ?>
        <?php unset($_SESSION['mensaje_error']); ?>
    </div>
<?php endif; ?>

<div class="dashboard-section">
    <div class="section-header">
        <h2>Próximas Citas</h2>
        <a href="index.php?page=agendar-cita" class="btn btn-primary btn-sm">+ Agendar Nueva</a>
    </div>

    <?php
    $proximas = array_filter($citas, function($c) {
        return $c['fecha'] >= date('Y-m-d') && $c['estado'] !== 'cancelada';
    });
    ?>

    <?php if (empty($proximas)): ?>
        <div class="empty-state">
            <p>No tienes citas programadas</p>
            <a href="index.php?page=agendar-cita" class="btn btn-primary">Agendar Cita</a>
        </div>
    <?php else: ?>
        <div class="list-container">
            <?php foreach ($proximas as $cita): ?>
                <div class="list-item">
                    <div class="list-item-icon">
                        <strong><?php echo date('d', strtotime($cita['fecha'])); ?></strong><br>
                        <small><?php echo date('M', strtotime($cita['fecha'])); ?></small>
                    </div>
                    <div class="list-item-content">
                        <h4><?php echo e($cita['medico_nombre']); ?></h4>
                        <p><strong><?php echo e($cita['especialidad']); ?></strong></p>
                        <p><?php echo formatear_fecha($cita['fecha']); ?> - <?php echo date('h:i A', strtotime($cita['hora_inicio'])); ?></p>
                        <p><em><?php echo e($cita['motivo_consulta']); ?></em></p>
                        <div style="margin-top: 10px;">
                            <span class="badge badge-<?php echo $cita['estado']; ?>"><?php echo ucfirst($cita['estado']); ?></span>
                            <?php if ($cita['estado'] === 'pendiente' || $cita['estado'] === 'confirmada'): ?>
                                <button onclick="cancelarCita(<?php echo $cita['id']; ?>)" class="btn btn-sm btn-error" style="margin-left: 10px;">Cancelar</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<div class="dashboard-section" style="margin-top: 24px;">
    <div class="section-header">
        <h2>Historial de Citas</h2>
    </div>

    <?php
    $pasadas = array_filter($citas, function($c) {
        return $c['fecha'] < date('Y-m-d') || $c['estado'] === 'cancelada';
    });
    ?>

    <?php if (empty($pasadas)): ?>
        <div class="empty-state">
            <p>No tienes citas en el historial</p>
        </div>
    <?php else: ?>
        <div class="list-container">
            <?php foreach (array_slice($pasadas, 0, 10) as $cita): ?>
                <div class="list-item">
                    <div class="list-item-icon">
                        <strong><?php echo date('d', strtotime($cita['fecha'])); ?></strong><br>
                        <small><?php echo date('M', strtotime($cita['fecha'])); ?></small>
                    </div>
                    <div class="list-item-content">
                        <h4><?php echo e($cita['medico_nombre']); ?></h4>
                        <p><strong><?php echo e($cita['especialidad']); ?></strong></p>
                        <p><?php echo formatear_fecha($cita['fecha']); ?> - <?php echo date('h:i A', strtotime($cita['hora_inicio'])); ?></p>
                        <p><em><?php echo e($cita['motivo_consulta']); ?></em></p>
                        <span class="badge badge-<?php echo $cita['estado']; ?>"><?php echo ucfirst($cita['estado']); ?></span>
                        <?php if ($cita['estado'] === 'cancelada' && !empty($cita['motivo_cancelacion'])): ?>
                            <p class="text-muted"><small>Motivo: <?php echo e($cita['motivo_cancelacion']); ?></small></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<meta name="csrf-token" content="<?php echo obtener_csrf_token(); ?>">

<script>
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
        inputCsrf.value = '<?php echo obtener_csrf_token(); ?>';

        form.appendChild(inputAction);
        form.appendChild(inputId);
        form.appendChild(inputMotivo);
        form.appendChild(inputCsrf);

        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
