<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="dashboard-header">
    <h1>Mis Pacientes</h1>
    <p>Listado de pacientes atendidos</p>
</div>

<div class="dashboard-section">
    <?php if (empty($pacientes)): ?>
        <div class="empty-state">
            <p>No has atendido pacientes aún</p>
        </div>
    <?php else: ?>
        <div class="list-container">
            <?php foreach ($pacientes as $pac): ?>
                <div class="list-item">
                    <div class="list-item-icon">👤</div>
                    <div class="list-item-content">
                        <h4><?php echo e($pac['nombre_completo']); ?></h4>
                        <p><strong>Documento:</strong> <?php echo e($pac['documento']); ?></p>
                        <p><strong>Teléfono:</strong> <?php echo e($pac['telefono']); ?></p>
                        <?php if (!empty($pac['grupo_sanguineo'])): ?>
                            <p><strong>Grupo sanguíneo:</strong> <?php echo e($pac['grupo_sanguineo']); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($pac['alergias'])): ?>
                            <p class="text-danger"><strong>⚠️ Alergias:</strong> <?php echo e($pac['alergias']); ?></p>
                        <?php endif; ?>
                        <p class="text-muted"><small>Última cita: <?php echo formatear_fecha($pac['ultima_cita']); ?></small></p>
                        <a href="index.php?page=ver-historia&id=<?php echo $pac['id']; ?>" class="btn btn-sm btn-primary" style="margin-top: 8px;">
                            Ver Historia Clínica
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
