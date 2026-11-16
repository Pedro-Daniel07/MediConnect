<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="dashboard-header">
    <h1>Mi Agenda</h1>
    <p>Citas de los próximos 7 días</p>
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

<?php if (empty($citas)): ?>
    <div class="dashboard-section">
        <div class="empty-state">
            <p>No tienes citas programadas para los próximos 7 días</p>
        </div>
    </div>
<?php else: ?>
    <?php
    $citas_por_dia = [];
    foreach ($citas as $cita) {
        $fecha = $cita['fecha'];
        if (!isset($citas_por_dia[$fecha])) {
            $citas_por_dia[$fecha] = [];
        }
        $citas_por_dia[$fecha][] = $cita;
    }
    ?>

    <?php foreach ($citas_por_dia as $fecha => $citas_dia): ?>
        <div class="dashboard-section" style="margin-bottom: 24px;">
            <div class="section-header">
                <h2><?php echo formatear_fecha($fecha); ?></h2>
                <span class="badge badge-info"><?php echo count($citas_dia); ?> citas</span>
            </div>

            <div class="list-container">
                <?php foreach ($citas_dia as $cita): ?>
                    <div class="list-item">
                        <div class="list-item-icon">
                            <?php echo date('h:i A', strtotime($cita['hora_inicio'])); ?>
                        </div>
                        <div class="list-item-content">
                            <h4><?php echo e($cita['paciente_nombre']); ?></h4>
                            <p><strong>Motivo:</strong> <?php echo e($cita['motivo_consulta']); ?></p>
                            <?php if (!empty($cita['alergias'])): ?>
                                <p class="text-danger"><strong>⚠️ Alergias:</strong> <?php echo e($cita['alergias']); ?></p>
                            <?php endif; ?>
                            <?php if (!empty($cita['grupo_sanguineo'])): ?>
                                <p><strong>Grupo sanguíneo:</strong> <?php echo e($cita['grupo_sanguineo']); ?></p>
                            <?php endif; ?>
                            <div style="margin-top: 10px;">
                                <span class="badge badge-<?php echo $cita['estado']; ?>"><?php echo ucfirst($cita['estado']); ?></span>
                                <?php if ($cita['estado'] === 'pendiente' || $cita['estado'] === 'confirmada'): ?>
                                    <a href="index.php?page=atender-cita&id=<?php echo $cita['id']; ?>" class="btn btn-sm btn-primary" style="margin-left: 10px;">Atender</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
