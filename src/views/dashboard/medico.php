<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="dashboard-header">
    <h1>Dr. <?php echo e($_SESSION['apellido']); ?></h1>
    <p>Panel del Médico</p>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">📅</div>
        <div class="stat-info">
            <h3><?php echo $stats['citas_hoy']; ?></h3>
            <p>Citas Hoy</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon">⏳</div>
        <div class="stat-info">
            <h3><?php echo $stats['pendientes_hoy']; ?></h3>
            <p>Pendientes Hoy</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon">✅</div>
        <div class="stat-info">
            <h3><?php echo $stats['completadas_mes']; ?></h3>
            <p>Completadas (Mes)</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon">📆</div>
        <div class="stat-info">
            <h3><?php echo $stats['proximas_semana']; ?></h3>
            <p>Próxima Semana</p>
        </div>
    </div>
</div>

<div class="dashboard-grid">
    <div class="dashboard-section">
        <div class="section-header">
            <h2>Agenda de Hoy</h2>
            <span class="text-muted"><?php echo formatear_fecha(date('Y-m-d')); ?></span>
        </div>

        <?php if (empty($citas_hoy)): ?>
            <div class="empty-state">
                <p>No tienes citas programadas para hoy</p>
            </div>
        <?php else: ?>
            <div class="list-container">
                <?php foreach ($citas_hoy as $cita): ?>
                    <div class="list-item">
                        <div class="list-item-icon">
                            <span class="badge badge-<?php echo $cita['estado']; ?>"><?php echo ucfirst($cita['estado']); ?></span>
                        </div>
                        <div class="list-item-content">
                            <h4><?php echo date('h:i A', strtotime($cita['hora_inicio'])); ?> - <?php echo e($cita['paciente_nombre']); ?></h4>
                            <p><?php echo e($cita['motivo_consulta']); ?></p>
                            <?php if (!empty($cita['alergias'])): ?>
                                <p class="text-danger"><strong>⚠️ Alergias:</strong> <?php echo e($cita['alergias']); ?></p>
                            <?php endif; ?>
                            <?php if ($cita['estado'] === 'confirmada' || $cita['estado'] === 'pendiente'): ?>
                                <a href="index.php?page=atender-cita&id=<?php echo $cita['id']; ?>" class="btn btn-sm btn-primary">Atender</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <a href="index.php?page=agenda-medico" class="btn btn-block">Ver Agenda Completa</a>
    </div>

    <div class="dashboard-section">
        <div class="section-header">
            <h2>Próximas Citas (7 días)</h2>
        </div>

        <?php if (empty($proximas_citas)): ?>
            <div class="empty-state">
                <p>No hay citas programadas</p>
            </div>
        <?php else: ?>
            <div class="list-container">
                <?php
                $mostradas = 0;
                foreach ($proximas_citas as $cita):
                    if ($cita['fecha'] === date('Y-m-d')) continue; // Saltar las de hoy
                    if ($mostradas >= 5) break; // Mostrar máximo 5
                    $mostradas++;
                ?>
                    <div class="list-item">
                        <div class="list-item-icon">
                            <?php echo date('d', strtotime($cita['fecha'])); ?><br>
                            <small><?php echo date('M', strtotime($cita['fecha'])); ?></small>
                        </div>
                        <div class="list-item-content">
                            <h4><?php echo e($cita['paciente_nombre']); ?></h4>
                            <p><?php echo date('h:i A', strtotime($cita['hora_inicio'])); ?> - <?php echo e($cita['motivo_consulta']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="quick-actions">
    <h2>Acceso Rápido</h2>
    <div class="actions-grid">
        <a href="index.php?page=agenda-medico" class="action-card">
            <div class="action-icon">📅</div>
            <h3>Mi Agenda</h3>
            <p>Ver calendario completo</p>
        </a>
        <a href="index.php?page=pacientes" class="action-card">
            <div class="action-icon">👥</div>
            <h3>Mis Pacientes</h3>
            <p>Listado de pacientes</p>
        </a>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
