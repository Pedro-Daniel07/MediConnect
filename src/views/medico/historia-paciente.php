<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="dashboard-header">
    <h1><?php echo $titulo; ?></h1>
    <button onclick="window.history.back()" class="btn btn-secondary">← Volver</button>
</div>

<div class="dashboard-section" style="margin-bottom: 24px;">
    <h2>Información del Paciente</h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 16px; padding: 16px; background: var(--color-bg-secondary); border-radius: 8px;">
        <div>
            <strong>Nombre:</strong> <?php echo e($paciente['nombre'] . ' ' . $paciente['apellido']); ?>
        </div>
        <div>
            <strong>Documento:</strong> <?php echo e($paciente['documento']); ?>
        </div>
        <div>
            <strong>Fecha de nacimiento:</strong> <?php echo formatear_fecha($paciente['fecha_nacimiento']); ?>
            (<?php echo calcular_edad($paciente['fecha_nacimiento']); ?> años)
        </div>
        <?php if (!empty($paciente['grupo_sanguineo'])): ?>
            <div>
                <strong>Grupo sanguíneo:</strong> <?php echo e($paciente['grupo_sanguineo']); ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($paciente['alergias'])): ?>
            <div class="text-danger">
                <strong>⚠️ Alergias:</strong> <?php echo e($paciente['alergias']); ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($paciente['enfermedades_cronicas'])): ?>
            <div>
                <strong>Enfermedades crónicas:</strong> <?php echo e($paciente['enfermedades_cronicas']); ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<h2>Historial Clínico</h2>

<?php if (empty($historias)): ?>
    <div class="dashboard-section">
        <div class="empty-state">
            <p>No hay historial clínico registrado</p>
        </div>
    </div>
<?php else: ?>
    <?php foreach ($historias as $historia): ?>
        <div class="dashboard-section" style="margin-bottom: 16px;">
            <div class="section-header">
                <div>
                    <h3><?php echo formatear_fecha($historia['created_at'], true); ?></h3>
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
<?php endif; ?>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
