<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="dashboard-header">
    <h1>Resultados de Laboratorio</h1>
    <p>Exámenes y estudios médicos</p>
</div>

<?php if (empty($resultados)): ?>
    <div class="dashboard-section">
        <div class="empty-state">
            <p>No tienes resultados de laboratorio registrados</p>
        </div>
    </div>
<?php else: ?>
    <div class="list-container">
        <?php foreach ($resultados as $resultado): ?>
            <div class="dashboard-section" style="margin-bottom: 16px;">
                <div class="section-header">
                    <div>
                        <h3><?php echo e($resultado['tipo_examen']); ?></h3>
                        <p class="text-muted"><?php echo formatear_fecha($resultado['fecha_examen']); ?> - Dr(a). <?php echo e($resultado['medico_nombre']); ?></p>
                    </div>
                </div>

                <div style="padding: 16px;">
                    <h4 style="margin-bottom: 8px;">Resultado</h4>
                    <pre style="background: var(--color-bg-secondary); padding: 12px; border-radius: 8px; white-space: pre-wrap;"><?php echo e($resultado['resultado']); ?></pre>

                    <?php if (!empty($resultado['valores_referencia'])): ?>
                        <h4 style="margin-top: 16px; margin-bottom: 8px;">Valores de Referencia</h4>
                        <pre style="background: var(--color-bg-tertiary); padding: 12px; border-radius: 8px; white-space: pre-wrap;"><?php echo e($resultado['valores_referencia']); ?></pre>
                    <?php endif; ?>

                    <?php if (!empty($resultado['observaciones'])): ?>
                        <h4 style="margin-top: 16px; margin-bottom: 8px;">Observaciones</h4>
                        <p><?php echo nl2br(e($resultado['observaciones'])); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
