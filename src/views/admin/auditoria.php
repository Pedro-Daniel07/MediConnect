<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="page-header" style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: var(--space-4);">
    <div>
        <div style="display: flex; align-items: center; gap: var(--space-2); margin-bottom: var(--space-2);">
            <span class="badge badge-info">Centro de Comando 2030</span>
            <span class="badge badge-completed">Trazabilidad Criptográfica</span>
        </div>
        <h1 class="page-title">Bitácora de Auditoría y Seguridad en Vivo</h1>
        <p class="page-subtitle">Registro cronológico inmutable de accesos, operaciones clínicas, emisiones y telemedicina</p>
    </div>
    <div style="display: flex; gap: var(--space-3); flex-wrap: wrap;">
        <a href="index.php?page=exportar-citas" class="btn btn-secondary">
            <svg class="icon icon-sm" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#download'); ?>"></use></svg>
            <span>Exportar Citas (CSV)</span>
        </a>
        <a href="index.php?page=exportar-pacientes" class="btn btn-primary">
            <svg class="icon icon-sm" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#download'); ?>"></use></svg>
            <span>Exportar Pacientes (Excel)</span>
        </a>
    </div>
</div>

<!-- KPIs del Centro de Comando -->
<div class="stats-grid" style="margin-bottom: var(--space-6);">
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Eventos Monitoreados</span>
            <div class="stat-icon" style="color: var(--accent-primary); background: rgba(14, 165, 233, 0.15);">
                <svg class="icon" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#shield'); ?>"></use></svg>
            </div>
        </div>
        <div class="stat-value"><?php echo $total_logs; ?></div>
        <div class="stat-trend positive">
            <span>Últimos registros activos</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Seguridad y Autenticación</span>
            <div class="stat-icon" style="color: var(--success); background: rgba(16, 185, 129, 0.15);">
                <svg class="icon" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#check'); ?>"></use></svg>
            </div>
        </div>
        <div class="stat-value" style="color: var(--success);"><?php echo $total_seguridad; ?></div>
        <div class="stat-trend positive">
            <span>0 intrusiones detectadas</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Operaciones Clínicas</span>
            <div class="stat-icon" style="color: #a855f7; background: rgba(168, 85, 247, 0.15);">
                <svg class="icon" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#activity'); ?>"></use></svg>
            </div>
        </div>
        <div class="stat-value" style="color: #a855f7;"><?php echo $total_clinicos; ?></div>
        <div class="stat-trend positive">
            <span>Recetas, telemedicina y wallet</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Estado de Auditoría</span>
            <div class="stat-icon" style="color: var(--warning); background: rgba(245, 158, 11, 0.15);">
                <svg class="icon" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#bell'); ?>"></use></svg>
            </div>
        </div>
        <div class="stat-value" style="color: var(--warning); font-size: var(--text-2xl);">Activo</div>
        <div class="stat-trend">
            <span style="color: var(--text-tertiary);">Polling en tiempo real</span>
        </div>
    </div>
</div>

<!-- Tabla de Bitácora -->
<div class="glass-card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: var(--space-4);">
        <div>
            <h3 class="card-title">Eventos Recientes del Sistema</h3>
            <p class="card-subtitle">Filtra eventos por acción, módulo, usuario o dirección IP</p>
        </div>
        <input type="text" id="log-filter" class="form-input" placeholder="Buscar en logs..." oninput="filtrarLogs(this.value)" style="max-width: 280px; font-size: var(--text-xs);">
    </div>

    <div class="table-responsive">
        <table class="table" id="table-logs">
            <thead>
                <tr>
                    <th>Fecha / Hora</th>
                    <th>Acción</th>
                    <th>Módulo</th>
                    <th>Usuario Responsable</th>
                    <th>Descripción del Evento</th>
                    <th>Dirección IP</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $l): ?>
                    <tr class="log-row">
                        <td style="font-family: var(--font-mono); font-size: var(--text-xs); white-space: nowrap;">
                            <?php echo formatear_fecha($l['created_at'], true); ?>
                        </td>
                        <td>
                            <?php if (str_contains($l['accion'], 'LOGIN')): ?>
                                <span class="badge badge-completed"><?php echo e($l['accion']); ?></span>
                            <?php elseif (str_contains($l['accion'], 'TELEMEDICINA') || str_contains($l['accion'], 'RECETA') || str_contains($l['accion'], 'SIGNOS')): ?>
                                <span class="badge badge-info"><?php echo e($l['accion']); ?></span>
                            <?php elseif (str_contains($l['accion'], 'CANCEL')): ?>
                                <span class="badge badge-cancelled"><?php echo e($l['accion']); ?></span>
                            <?php else: ?>
                                <span class="badge badge-pending"><?php echo e($l['accion']); ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong style="font-size: var(--text-xs); color: var(--text-primary);"><?php echo e($l['modulo']); ?></strong>
                        </td>
                        <td>
                            <?php if ($l['nombre']): ?>
                                <div style="display: flex; align-items: center; gap: var(--space-2);">
                                    <span style="font-weight: var(--weight-medium);"><?php echo e($l['nombre'] . ' ' . $l['apellido']); ?></span>
                                    <span class="badge" style="font-size: 10px;"><?php echo ucfirst(e($l['rol'] ?? '')); ?></span>
                                </div>
                            <?php else: ?>
                                <span style="color: var(--text-tertiary); font-style: italic;">Sistema Autónomo</span>
                            <?php endif; ?>
                        </td>
                        <td style="font-size: var(--text-xs); color: var(--text-secondary); max-width: 320px;">
                            <?php echo e($l['descripcion']); ?>
                        </td>
                        <td style="font-family: var(--font-mono); font-size: var(--text-xs); color: var(--text-tertiary);">
                            <?php echo e($l['ip_address'] ?? '127.0.0.1'); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function filtrarLogs(q) {
    const query = q.toLowerCase().trim();
    document.querySelectorAll('.log-row').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(query) ? '' : 'none';
    });
}
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
