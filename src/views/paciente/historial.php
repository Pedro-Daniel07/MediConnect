<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="dashboard-header">
    <h1>Mi Historial Clínico</h1>
    <p>Registro de consultas y diagnósticos</p>
</div>

<?php if (empty($historias)): ?>
    <div class="dashboard-section">
        <div class="empty-state">
            <p>No tienes historial clínico registrado</p>
            <a href="index.php?page=agendar-cita" class="btn btn-primary">Agendar Primera Cita</a>
        </div>
    </div>
<?php else: ?>
    <div class="list-container">
        <?php foreach ($historias as $historia): ?>
            <div class="dashboard-section" style="margin-bottom: 16px;">
                <div class="section-header">
                    <div>
                        <h3><?php echo formatear_fecha($historia['fecha_cita']); ?></h3>
                        <p class="text-muted"><?php echo e($historia['medico_nombre']); ?> - <?php echo e($historia['especialidad']); ?></p>
                    </div>
                </div>

                <div style="padding: 16px;">
                    <h4 style="margin-bottom: 8px;">Diagnóstico</h4>
                    <p style="margin-bottom: 16px;"><?php echo nl2br(e($historia['diagnostico'])); ?></p>

                    <?php if (!empty($historia['sintomas'])): ?>
                        <h4 style="margin-bottom: 8px;">Síntomas</h4>
                        <p style="margin-bottom: 16px;"><?php echo nl2br(e($historia['sintomas'])); ?></p>
                    <?php endif; ?>

                    <?php if ($historia['peso'] || $historia['altura'] || $historia['presion_arterial']): ?>
                        <h4 style="margin-bottom: 8px;">Signos Vitales</h4>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 12px; margin-bottom: 16px;">
                            <?php if ($historia['peso']): ?>
                                <div><strong>Peso:</strong> <?php echo e($historia['peso']); ?> kg</div>
                            <?php endif; ?>
                            <?php if ($historia['altura']): ?>
                                <div><strong>Altura:</strong> <?php echo e($historia['altura']); ?> cm</div>
                            <?php endif; ?>
                            <?php if ($historia['peso'] && $historia['altura']): ?>
                                <div><strong>IMC:</strong> <?php echo calcular_imc($historia['peso'], $historia['altura']); ?></div>
                            <?php endif; ?>
                            <?php if ($historia['presion_arterial']): ?>
                                <div><strong>Presión:</strong> <?php echo e($historia['presion_arterial']); ?></div>
                            <?php endif; ?>
                            <?php if ($historia['temperatura']): ?>
                                <div><strong>Temperatura:</strong> <?php echo e($historia['temperatura']); ?>°C</div>
                            <?php endif; ?>
                            <?php if ($historia['frecuencia_cardiaca']): ?>
                                <div><strong>FC:</strong> <?php echo e($historia['frecuencia_cardiaca']); ?> ppm</div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($historia['plan_tratamiento'])): ?>
                        <h4 style="margin-bottom: 8px;">Plan de Tratamiento</h4>
                        <p><?php echo nl2br(e($historia['plan_tratamiento'])); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
