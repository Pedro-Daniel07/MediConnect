<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: var(--space-4);">
        <div>
            <h1 class="page-title">Cuerpo Médico Especializado</h1>
            <p class="page-subtitle">Profesionales de la salud de vanguardia registrados en MediConnect</p>
        </div>
        <div style="display: flex; gap: var(--space-3); align-items: center;">
            <span class="badge badge-info" style="font-size: var(--text-sm); padding: var(--space-2) var(--space-4);">
                <svg class="icon icon-sm" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#user'); ?>"></use></svg>
                <?php echo count($medicos); ?> Especialistas Activos
            </span>
        </div>
    </div>
</div>

<?php if (empty($medicos)): ?>
    <div class="empty-state glass-card">
        <svg class="empty-illustration" aria-hidden="true">
            <use href="<?php echo asset('img/icons.svg#user'); ?>"></use>
        </svg>
        <h3>No hay médicos registrados</h3>
        <p>Los profesionales médicos aparecerán aquí una vez que sean dados de alta en el sistema.</p>
    </div>
<?php else: ?>
    <div class="grid grid-3" style="gap: var(--space-6);">
        <?php foreach ($medicos as $index => $medico): ?>
            <?php
                $iniciales = strtoupper(substr($medico['nombre'], 0, 1) . substr($medico['apellido'], 0, 1));
                // Gradientes variados para avatares
                $gradientes = [
                    'linear-gradient(135deg, #0ea5e9, #38bdf8)',
                    'linear-gradient(135deg, #10b981, #34d399)',
                    'linear-gradient(135deg, #8b5cf6, #a78bfa)',
                    'linear-gradient(135deg, #f59e0b, #fbbf24)',
                    'linear-gradient(135deg, #06b6d4, #22d3ee)',
                    'linear-gradient(135deg, #ec4899, #f472b6)',
                    'linear-gradient(135deg, #f97316, #fb923c)',
                    'linear-gradient(135deg, #6366f1, #818cf8)'
                ];
                $gradiente = $gradientes[$index % count($gradientes)];
            ?>
            <div class="glass-card stagger-item" style="display: flex; flex-direction: column; justify-content: space-between; position: relative; overflow: hidden;">
                <!-- Acento superior luminoso -->
                <div style="position: absolute; top: 0; left: 0; right: 0; height: 3px; background: <?php echo $gradiente; ?>;"></div>

                <div>
                    <!-- Header de la tarjeta con avatar y nombre -->
                    <div style="display: flex; gap: var(--space-4); align-items: center; margin-bottom: var(--space-4); padding-top: var(--space-2);">
                        <div style="width: 56px; height: 56px; border-radius: var(--radius-full); background: <?php echo $gradiente; ?>; display: flex; align-items: center; justify-content: center; color: white; font-weight: var(--weight-bold); font-size: var(--text-lg); box-shadow: 0 4px 12px rgba(0,0,0,0.15); flex-shrink: 0; border: 2px solid var(--glass-border);">
                            <?php echo $iniciales; ?>
                        </div>
                        <div style="overflow: hidden;">
                            <h3 style="font-size: var(--text-lg); font-weight: var(--weight-semibold); color: var(--text-primary); margin-bottom: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                Dr(a). <?php echo e($medico['nombre'] . ' ' . $medico['apellido']); ?>
                            </h3>
                            <span class="badge badge-confirmed" style="font-size: var(--text-xs);">
                                <?php echo e($medico['especialidad_nombre']); ?>
                            </span>
                        </div>
                    </div>

                    <!-- Datos clave en pills -->
                    <div style="display: flex; flex-direction: column; gap: var(--space-2); margin-bottom: var(--space-4); font-size: var(--text-sm);">
                        <div style="display: flex; justify-content: space-between; padding: var(--space-2) var(--space-3); background: var(--glass-bg-secondary); border-radius: var(--radius-md); border: 1px solid var(--glass-border);">
                            <span style="color: var(--text-tertiary);">Licencia Médica:</span>
                            <span style="font-family: var(--font-mono); font-weight: var(--weight-medium); color: var(--text-primary);"><?php echo e($medico['licencia_medica']); ?></span>
                        </div>

                        <div style="display: flex; justify-content: space-between; padding: var(--space-2) var(--space-3); background: var(--glass-bg-secondary); border-radius: var(--radius-md); border: 1px solid var(--glass-border);">
                            <span style="color: var(--text-tertiary);">Consultorio:</span>
                            <span style="color: var(--text-primary); font-weight: var(--weight-medium);"><?php echo e($medico['consultorio'] ?? 'Principal'); ?></span>
                        </div>

                        <?php if ($medico['anos_experiencia'] > 0): ?>
                            <div style="display: flex; justify-content: space-between; padding: var(--space-2) var(--space-3); background: var(--glass-bg-secondary); border-radius: var(--radius-md); border: 1px solid var(--glass-border);">
                                <span style="color: var(--text-tertiary);">Experiencia:</span>
                                <span style="color: var(--success); font-weight: var(--weight-semibold);"><?php echo $medico['anos_experiencia']; ?> años de práctica</span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Biografía profesional si existe -->
                    <?php if (!empty($medico['biografia'])): ?>
                        <p style="font-size: var(--text-xs); color: var(--text-secondary); line-height: 1.5; margin-bottom: var(--space-4); display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            <?php echo e($medico['biografia']); ?>
                        </p>
                    <?php endif; ?>
                </div>

                <!-- Footer con tarifa y contacto -->
                <div style="padding-top: var(--space-4); border-top: 1px solid var(--glass-border); display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <div style="font-size: var(--text-xs); color: var(--text-tertiary);">Tarifa Consulta</div>
                        <div style="font-size: var(--text-xl); font-weight: var(--weight-bold); color: var(--accent-primary);">
                            <?php echo formatear_moneda($medico['tarifa_consulta']); ?>
                        </div>
                    </div>
                    <div style="display: flex; gap: var(--space-2);">
                        <a href="mailto:<?php echo e($medico['email']); ?>" class="nav-icon-btn" title="Enviar Email" style="width: 36px; height: 36px;">
                            <svg class="icon icon-sm" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#message'); ?>"></use></svg>
                        </a>
                        <a href="tel:<?php echo e($medico['telefono']); ?>" class="nav-icon-btn" title="Llamar" style="width: 36px; height: 36px;">
                            <svg class="icon icon-sm" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#user'); ?>"></use></svg>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
