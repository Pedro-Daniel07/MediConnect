<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="dashboard-header">
    <h1>Bienvenido, <?php echo e($_SESSION['nombre']); ?></h1>
    <p>Panel de Paciente</p>
</div>

<?php if (isset($_SESSION['mensaje_exito'])): ?>
    <div class="alert alert-success">
        <?php echo e($_SESSION['mensaje_exito']); ?>
        <?php unset($_SESSION['mensaje_exito']); ?>
    </div>
<?php endif; ?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">📅</div>
        <div class="stat-info">
            <h3><?php echo $stats['citas_pendientes']; ?></h3>
            <p>Citas Pendientes</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon">✅</div>
        <div class="stat-info">
            <h3><?php echo $stats['citas_completadas']; ?></h3>
            <p>Citas Completadas</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon">📋</div>
        <div class="stat-info">
            <h3><?php echo $stats['total_consultas']; ?></h3>
            <p>Consultas Registradas</p>
        </div>
    </div>

    <div class="stat-card stat-card-action">
        <a href="index.php?page=agendar-cita" class="stat-link">
            <div class="stat-icon">➕</div>
            <div class="stat-info">
                <h3>Agendar</h3>
                <p>Nueva Cita</p>
            </div>
        </a>
    </div>
</div>

<div class="dashboard-grid">
    <div class="dashboard-section">
        <div class="section-header">
            <h2>Próximas Citas</h2>
            <a href="index.php?page=mis-citas" class="btn btn-sm">Ver Todas</a>
        </div>

        <?php if (empty($proximas_citas)): ?>
            <div class="empty-state">
                <p>No tienes citas programadas</p>
                <a href="index.php?page=agendar-cita" class="btn btn-primary">Agendar Cita</a>
            </div>
        <?php else: ?>
            <div class="list-container">
                <?php foreach ($proximas_citas as $cita): ?>
                    <div class="list-item">
                        <div class="list-item-icon">
                            <span class="badge badge-<?php echo $cita['estado']; ?>"><?php echo ucfirst($cita['estado']); ?></span>
                        </div>
                        <div class="list-item-content">
                            <h4><?php echo e($cita['medico_nombre']); ?></h4>
                            <p><?php echo e($cita['especialidad']); ?></p>
                            <p class="text-muted">
                                <?php echo formatear_fecha($cita['fecha']); ?> -
                                <?php echo date('h:i A', strtotime($cita['hora_inicio'])); ?>
                            </p>
                            <?php if (!empty($cita['motivo_consulta'])): ?>
                                <p><em><?php echo e($cita['motivo_consulta']); ?></em></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="dashboard-section">
        <div class="section-header">
            <h2>Historial Reciente</h2>
            <a href="index.php?page=mi-historial" class="btn btn-sm">Ver Completo</a>
        </div>

        <?php if (empty($historial)): ?>
            <div class="empty-state">
                <p>No tienes historial clínico registrado</p>
            </div>
        <?php else: ?>
            <div class="list-container">
                <?php foreach ($historial as $historia): ?>
                    <div class="list-item">
                        <div class="list-item-icon">📋</div>
                        <div class="list-item-content">
                            <h4><?php echo formatear_fecha($historia['fecha_cita']); ?></h4>
                            <p><strong><?php echo e($historia['medico_nombre']); ?></strong> - <?php echo e($historia['especialidad']); ?></p>
                            <p><?php echo e(substr($historia['diagnostico'], 0, 100)); ?><?php echo strlen($historia['diagnostico']) > 100 ? '...' : ''; ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="quick-actions">
    <h2>Acciones Rápidas</h2>
    <div class="actions-grid">
        <a href="index.php?page=agendar-cita" class="action-card">
            <div class="action-icon">📅</div>
            <h3>Agendar Cita</h3>
            <p>Programa una consulta médica</p>
        </a>
        <a href="index.php?page=mis-formulas" class="action-card">
            <div class="action-icon">💊</div>
            <h3>Mis Fórmulas</h3>
            <p>Ver prescripciones médicas</p>
        </a>
        <a href="index.php?page=resultados" class="action-card">
            <div class="action-icon">🔬</div>
            <h3>Resultados</h3>
            <p>Consultar exámenes de laboratorio</p>
        </a>
        <a href="index.php?page=orientador" class="action-card">
            <div class="action-icon">🩺</div>
            <h3>Orientador</h3>
            <p>Evaluación de síntomas</p>
        </a>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
