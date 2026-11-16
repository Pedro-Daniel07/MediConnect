<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="page-header" style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: var(--space-4);">
    <div>
        <h1 class="page-title">Carné de Inmunización y Vacunación</h1>
        <p class="page-subtitle">Registro digital oficial de biológicos administrados, lotes y esquema preventivo</p>
    </div>
    <div style="display: flex; gap: var(--space-3);">
        <a href="index.php?page=carne-digital" class="btn btn-secondary">
            <svg class="icon icon-sm" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#qrcode'); ?>"></use></svg>
            <span>Ver Carné Wallet</span>
        </a>
    </div>
</div>

<!-- Resumen de Inmunización (KPIs) -->
<div class="stats-grid" style="margin-bottom: var(--space-6);">
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Dosis Aplicadas</span>
            <div class="stat-icon" style="color: var(--success); background: rgba(16, 185, 129, 0.15);">
                <svg class="icon" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#check'); ?>"></use></svg>
            </div>
        </div>
        <div class="stat-value" style="color: var(--success);"><?php echo $total_aplicadas; ?></div>
        <div class="stat-trend positive">
            <span>Inmunidad activa verificada</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Dosis Pendientes</span>
            <div class="stat-icon" style="color: var(--accent-primary); background: rgba(14, 165, 233, 0.15);">
                <svg class="icon" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#clock'); ?>"></use></svg>
            </div>
        </div>
        <div class="stat-value" style="color: var(--accent-primary);"><?php echo $total_pendientes; ?></div>
        <div class="stat-trend">
            <span style="color: var(--text-tertiary);">Programadas este año</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Refuerzos Vencidos</span>
            <div class="stat-icon" style="color: var(--error); background: rgba(239, 68, 68, 0.15);">
                <svg class="icon" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#alert-triangle'); ?>"></use></svg>
            </div>
        </div>
        <div class="stat-value" style="color: var(--error);"><?php echo $total_vencidas; ?></div>
        <div class="stat-trend negative">
            <span><?php echo $total_vencidas > 0 ? 'Requiere atención' : 'Al día'; ?></span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Esquema General</span>
            <div class="stat-icon" style="color: #a855f7; background: rgba(168, 85, 247, 0.15);">
                <svg class="icon" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#syringe'); ?>"></use></svg>
            </div>
        </div>
        <div class="stat-value" style="color: #a855f7;">
            <?php
                $total = count($vacunas);
                echo $total > 0 ? round(($total_aplicadas / $total) * 100) . '%' : '100%';
            ?>
        </div>
        <div class="stat-trend positive">
            <span>Cobertura poblacional</span>
        </div>
    </div>
</div>

<!-- Alerta Si Hay Vacunas Vencidas o Próximas -->
<?php if ($total_vencidas > 0): ?>
    <div class="alert alert-error" style="margin-bottom: var(--space-6);">
        <svg class="icon" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#alert-triangle'); ?>"></use></svg>
        <div style="flex: 1;">
            <strong>Atención: Tienes dosis o refuerzos vencidos</strong>
            <p style="font-size: var(--text-sm); margin-top: 2px;">
                Para garantizar la protección inmunológica continua, agenda una cita de vacunación en el centro clínico más cercano.
            </p>
        </div>
        <a href="index.php?page=agendar-cita" class="btn btn-sm btn-error">Agendar Cita</a>
    </div>
<?php elseif ($total_pendientes > 0): ?>
    <div class="alert alert-info" style="margin-bottom: var(--space-6);">
        <svg class="icon" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#bell'); ?>"></use></svg>
        <div style="flex: 1;">
            <strong>Próximas Dosis Programadas</strong>
            <p style="font-size: var(--text-sm); margin-top: 2px;">
                Tienes vacunas pendientes recomendadas según tu historial y esquema etario.
            </p>
        </div>
    </div>
<?php endif; ?>

<!-- Listado del Esquema de Vacunas -->
<div class="glass-card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: var(--space-4);">
        <div>
            <h3 class="card-title">Historial y Esquema de Vacunación</h3>
            <p class="card-subtitle">Paciente: <?php echo e($paciente['nombre'] . ' ' . $paciente['apellido']); ?> (Afiliado: <?php echo e($paciente['codigo_afiliado'] ?? 'MC-'.$paciente['id']); ?>)</p>
        </div>
        <div style="display: flex; gap: var(--space-2);">
            <button type="button" class="btn btn-secondary btn-sm" onclick="filtrarVacunas('todas')">Todas</button>
            <button type="button" class="btn btn-secondary btn-sm" onclick="filtrarVacunas('aplicada')">Aplicadas</button>
            <button type="button" class="btn btn-secondary btn-sm" onclick="filtrarVacunas('pendiente')">Pendientes</button>
        </div>
    </div>

    <?php if (empty($vacunas)): ?>
        <div class="empty-state">
            <svg class="empty-illustration" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#syringe'); ?>"></use></svg>
            <h3>No hay vacunas registradas</h3>
            <p>Aún no se han asentado registros biológicos para este paciente en el sistema.</p>
        </div>
    <?php else: ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: var(--space-4);">
            <?php foreach ($vacunas as $v): ?>
                <div class="vaccine-card vac-item" data-estado="<?php echo $v['estado']; ?>">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: var(--space-3);">
                        <div style="display: flex; align-items: center; gap: var(--space-3);">
                            <div style="width: 38px; height: 38px; border-radius: var(--radius-md); background: rgba(14, 165, 233, 0.15); display: flex; align-items: center; justify-content: center; color: var(--accent-primary); border: 1px solid rgba(14, 165, 233, 0.3);">
                                <svg class="icon icon-sm" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#syringe'); ?>"></use></svg>
                            </div>
                            <div>
                                <h4 style="font-size: var(--text-base); font-weight: var(--weight-semibold); color: var(--text-primary); margin-bottom: 2px;">
                                    <?php echo e($v['nombre_vacuna']); ?>
                                </h4>
                                <span style="font-size: var(--text-xs); color: var(--text-tertiary);"><?php echo e($v['dosis']); ?></span>
                            </div>
                        </div>

                        <?php if ($v['estado'] === 'aplicada'): ?>
                            <span class="badge badge-completed">Aplicada</span>
                        <?php elseif ($v['estado'] === 'pendiente'): ?>
                            <span class="badge badge-pending">Pendiente</span>
                        <?php else: ?>
                            <span class="badge badge-cancelled">Vencida</span>
                        <?php endif; ?>
                    </div>

                    <div style="background: var(--glass-bg-primary); border-radius: var(--radius-md); padding: var(--space-3); border: 1px solid var(--glass-border); font-size: var(--text-xs); display: flex; flex-direction: column; gap: var(--space-2); margin-bottom: var(--space-3);">
                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: var(--text-tertiary);">Fecha Aplicación:</span>
                            <span style="font-family: var(--font-mono); color: var(--text-primary); font-weight: var(--weight-medium);"><?php echo formatear_fecha($v['fecha_aplicacion']); ?></span>
                        </div>
                        <?php if ($v['fecha_proxima_dosis']): ?>
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: var(--text-tertiary);">Próximo Refuerzo:</span>
                                <span style="font-family: var(--font-mono); color: <?php echo $v['estado'] === 'vencida' ? 'var(--error)' : 'var(--accent-primary)'; ?>; font-weight: var(--weight-bold);"><?php echo formatear_fecha($v['fecha_proxima_dosis']); ?></span>
                            </div>
                        <?php endif; ?>
                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: var(--text-tertiary);">Lote del Biológico:</span>
                            <span style="font-family: var(--font-mono); color: var(--text-secondary);"><?php echo e($v['lote']); ?></span>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: var(--text-tertiary);">Profesional:</span>
                            <span style="color: var(--text-secondary);"><?php echo e($v['profesional']); ?></span>
                        </div>
                    </div>

                    <?php if (!empty($v['observaciones'])): ?>
                        <p style="font-size: var(--text-xs); color: var(--text-tertiary); margin: 0; font-style: italic;">
                            "<?php echo e($v['observaciones']); ?>"
                        </p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
function filtrarVacunas(estado) {
    const items = document.querySelectorAll('.vac-item');
    items.forEach(item => {
        if (estado === 'todas' || item.dataset.estado === estado) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
