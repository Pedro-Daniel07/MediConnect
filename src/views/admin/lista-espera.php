<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="page-header" style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: var(--space-4);">
    <div>
        <div style="display: flex; align-items: center; gap: var(--space-2); margin-bottom: var(--space-2);">
            <span class="badge badge-info">Algoritmo de Reasignación Inmediata</span>
            <span class="badge badge-completed">Capacidad Dinámica</span>
        </div>
        <h1 class="page-title">Lista de Espera Inteligente</h1>
        <p class="page-subtitle">Gestión algorítmica de cupos liberados: asignación automática ante cancelaciones médicas</p>
    </div>
</div>

<?php if (isset($_SESSION['mensaje_exito'])): ?>
    <div class="alert alert-success">
        <svg class="icon" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#check'); ?>"></use></svg>
        <span><?php echo e($_SESSION['mensaje_exito']); unset($_SESSION['mensaje_exito']); ?></span>
    </div>
<?php endif; ?>

<!-- Panel Informativo del Mecanismo Autónomo -->
<div class="glass-card" style="margin-bottom: var(--space-6); background: linear-gradient(135deg, rgba(14, 165, 233, 0.12), rgba(16, 185, 129, 0.08)); border-color: rgba(14, 165, 233, 0.3);">
    <div style="display: flex; align-items: center; gap: var(--space-4);">
        <div style="width: 48px; height: 48px; border-radius: var(--radius-md); background: rgba(14, 165, 233, 0.2); display: flex; align-items: center; justify-content: center; color: var(--accent-primary); flex-shrink: 0;">
            <svg class="icon icon-lg" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#clock'); ?>"></use></svg>
        </div>
        <div style="flex: 1;">
            <h3 style="font-size: var(--text-base); font-weight: var(--weight-bold); color: var(--text-primary); margin-bottom: 2px;">
                Mecanismo de Reasignación en Tiempo Real
            </h3>
            <p style="font-size: var(--text-xs); color: var(--text-secondary); margin: 0; line-height: 1.5;">
                Cuando cualquier cita es cancelada por un paciente o especialista en <a href="index.php?page=mis-citas" style="color: var(--accent-primary);">Mis Citas</a>, el motor autónomo busca al primer paciente en cola de espera que coincida en especialidad o médico, le asigna el cupo, genera la cita confirmada y le envía una notificación instantánea.
            </p>
        </div>
    </div>
</div>

<!-- Tabla de Registros en Lista de Espera -->
<div class="glass-card">
    <div class="card-header">
        <h3 class="card-title">Pacientes en Cola de Espera</h3>
        <p class="card-subtitle">Ordenados por prioridad clínica y antigüedad de solicitud</p>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Paciente</th>
                    <th>Afiliado / Teléfono</th>
                    <th>Especialidad Requerida</th>
                    <th>Médico Preferente</th>
                    <th>Prioridad</th>
                    <th>Estado</th>
                    <th>Fecha Solicitud</th>
                    <th style="text-align: right;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($solicitudes)): ?>
                    <tr>
                        <td colspan="8" style="text-align: center; padding: var(--space-8); color: var(--text-tertiary);">
                            No hay solicitudes en la lista de espera actualmente.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($solicitudes as $s): ?>
                        <tr>
                            <td>
                                <strong style="color: var(--text-primary); display: block;"><?php echo e($s['paciente_nombre']); ?></strong>
                                <span style="font-size: var(--text-xs); color: var(--text-tertiary);">Doc: <?php echo e($s['documento']); ?></span>
                            </td>
                            <td>
                                <span class="badge badge-info" style="font-family: var(--font-mono); font-size: 11px;"><?php echo e($s['codigo_afiliado'] ?? 'MC-'.$s['paciente_id']); ?></span>
                                <span style="display: block; font-size: var(--text-xs); color: var(--text-secondary); margin-top: 2px;"><?php echo e($s['telefono'] ?? 'N/A'); ?></span>
                            </td>
                            <td>
                                <span style="font-weight: var(--weight-medium);"><?php echo e($s['especialidad_nombre']); ?></span>
                            </td>
                            <td>
                                <?php echo e($s['medico_nombre'] ?? 'Cualquier especialista'); ?>
                            </td>
                            <td>
                                <?php if ($s['prioridad'] === 'alta'): ?>
                                    <span class="badge badge-cancelled">Alta</span>
                                <?php elseif ($s['prioridad'] === 'media'): ?>
                                    <span class="badge badge-pending">Media</span>
                                <?php else: ?>
                                    <span class="badge badge-info">Baja</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($s['estado'] === 'esperando'): ?>
                                    <span class="badge badge-pending">En Espera</span>
                                <?php elseif ($s['estado'] === 'asignado'): ?>
                                    <span class="badge badge-completed">Cupo Asignado</span>
                                <?php else: ?>
                                    <span class="badge" style="background: rgba(148, 163, 184, 0.2);">Descartado</span>
                                <?php endif; ?>
                            </td>
                            <td style="font-family: var(--font-mono); font-size: var(--text-xs);">
                                <?php echo formatear_fecha($s['fecha_solicitud']); ?>
                            </td>
                            <td style="text-align: right;">
                                <?php if ($s['estado'] === 'esperando'): ?>
                                    <form method="POST" action="index.php?page=lista-espera" style="display: inline-flex; gap: 4px;">
                                        <input type="hidden" name="csrf_token" value="<?php echo obtener_csrf_token(); ?>">
                                        <input type="hidden" name="espera_id" value="<?php echo $s['id']; ?>">
                                        <button type="submit" name="action" value="asignar_cupo" class="btn btn-secondary btn-sm" title="Forzar Asignación Manual">
                                            <span>Asignar</span>
                                        </button>
                                        <button type="submit" name="action" value="descartar" class="btn btn-ghost btn-sm" style="color: var(--error);" title="Descartar de lista">
                                            <svg class="icon icon-sm" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#x'); ?>"></use></svg>
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span style="font-size: var(--text-xs); color: var(--text-tertiary);">Procesado</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
