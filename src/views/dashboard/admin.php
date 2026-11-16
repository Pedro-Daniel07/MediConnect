<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="page-header">
    <h1 class="page-title">Centro de Control Administrativo</h1>
    <p class="page-subtitle">Monitoreo en tiempo real de operaciones, especialistas y métricas clínicas</p>
</div>

<!-- Métricas Principales (KPIs) -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Pacientes Registrados</span>
            <div class="stat-icon">
                <svg class="icon" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#users'); ?>"></use></svg>
            </div>
        </div>
        <div class="stat-value"><?php echo $stats['total_pacientes']; ?></div>
        <div class="stat-trend positive">
            <span>▲ +12% vs mes anterior</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Cuerpo Médico</span>
            <div class="stat-icon">
                <svg class="icon" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#user'); ?>"></use></svg>
            </div>
        </div>
        <div class="stat-value"><?php echo $stats['total_medicos']; ?></div>
        <div class="stat-trend positive">
            <span>▲ 8 Especialidades</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Citas Programadas Hoy</span>
            <div class="stat-icon">
                <svg class="icon" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#calendar'); ?>"></use></svg>
            </div>
        </div>
        <div class="stat-value" style="color: var(--accent-primary);"><?php echo $stats['citas_hoy']; ?></div>
        <div class="stat-trend">
            <span style="color: var(--text-tertiary);">Agenda activa</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Cuentas Activas</span>
            <div class="stat-icon">
                <svg class="icon" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#shield'); ?>"></use></svg>
            </div>
        </div>
        <div class="stat-value"><?php echo $stats['total_usuarios']; ?></div>
        <div class="stat-trend positive">
            <span>100% verificadas</span>
        </div>
    </div>
</div>

<!-- Grid de Gráficos y Listas -->
<div class="grid grid-2" style="margin-bottom: var(--space-8);">
    <!-- Gráfico de Citas del Mes -->
    <div class="glass-card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h3 class="card-title">Distribución de Citas</h3>
                <p class="card-subtitle"><?php echo date('F Y'); ?> - Total mensual</p>
            </div>
            <span class="badge badge-info">En Vivo</span>
        </div>

        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: var(--space-2); margin-bottom: var(--space-4);">
            <div style="text-align: center; padding: var(--space-2); background: var(--glass-bg-secondary); border-radius: var(--radius-md); border: 1px solid var(--glass-border);">
                <span class="badge badge-pending" style="margin-bottom: 4px;">Pendientes</span>
                <div style="font-weight: var(--weight-bold); font-size: var(--text-lg);"><?php echo $citas_mes['pendiente'] ?? 0; ?></div>
            </div>
            <div style="text-align: center; padding: var(--space-2); background: var(--glass-bg-secondary); border-radius: var(--radius-md); border: 1px solid var(--glass-border);">
                <span class="badge badge-confirmed" style="margin-bottom: 4px;">Confirmadas</span>
                <div style="font-weight: var(--weight-bold); font-size: var(--text-lg);"><?php echo $citas_mes['confirmada'] ?? 0; ?></div>
            </div>
            <div style="text-align: center; padding: var(--space-2); background: var(--glass-bg-secondary); border-radius: var(--radius-md); border: 1px solid var(--glass-border);">
                <span class="badge badge-completed" style="margin-bottom: 4px;">Completadas</span>
                <div style="font-weight: var(--weight-bold); font-size: var(--text-lg);"><?php echo $citas_mes['completada'] ?? 0; ?></div>
            </div>
            <div style="text-align: center; padding: var(--space-2); background: var(--glass-bg-secondary); border-radius: var(--radius-md); border: 1px solid var(--glass-border);">
                <span class="badge badge-cancelled" style="margin-bottom: 4px;">Canceladas</span>
                <div style="font-weight: var(--weight-bold); font-size: var(--text-lg);"><?php echo $citas_mes['cancelada'] ?? 0; ?></div>
            </div>
        </div>

        <div class="chart-box">
            <canvas id="citasChart"></canvas>
        </div>
    </div>

    <!-- Especialidades Más Solicitadas -->
    <div class="glass-card">
        <div class="card-header">
            <h3 class="card-title">Especialidades con Mayor Demanda</h3>
            <p class="card-subtitle">Volumen de consultas acumuladas</p>
        </div>

        <?php if (empty($especialidades_populares)): ?>
            <div class="empty-state" style="padding: var(--space-8);">
                <svg class="empty-illustration" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#chart-line'); ?>"></use></svg>
                <p>No hay datos disponibles aún</p>
            </div>
        <?php else: ?>
            <div style="display: flex; flex-direction: column; gap: var(--space-3);">
                <?php
                    $max_citas = max(array_column($especialidades_populares, 'total'));
                    foreach ($especialidades_populares as $esp):
                        $porcentaje = $max_citas > 0 ? round(($esp['total'] / $max_citas) * 100) : 0;
                ?>
                    <div style="padding: var(--space-3) var(--space-4); background: var(--glass-bg-secondary); border-radius: var(--radius-md); border: 1px solid var(--glass-border);">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--space-2);">
                            <span style="font-weight: var(--weight-semibold); color: var(--text-primary); font-size: var(--text-sm);">
                                <?php echo e($esp['nombre']); ?>
                            </span>
                            <span class="badge badge-info" style="font-family: var(--font-mono);">
                                <?php echo $esp['total']; ?> citas
                            </span>
                        </div>
                        <div style="width: 100%; height: 6px; background: rgba(148, 163, 184, 0.15); border-radius: var(--radius-full); overflow: hidden;">
                            <div style="width: <?php echo $porcentaje; ?>%; height: 100%; background: linear-gradient(90deg, var(--accent-primary), var(--accent-hover)); border-radius: var(--radius-full); transition: width 0.5s ease;"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Accesos Rápidos de Administración -->
<div class="page-header" style="margin-bottom: var(--space-4);">
    <h2 style="font-size: var(--text-xl); font-weight: var(--weight-bold); color: var(--text-primary);">
        Módulos del Sistema
    </h2>
</div>

<div class="grid grid-4" style="margin-bottom: var(--space-8);">
    <a href="index.php?page=usuarios" class="glass-card" style="display: flex; flex-direction: column; gap: var(--space-3); transition: all var(--duration-fast) var(--ease-smooth);">
        <div style="width: 44px; height: 44px; border-radius: var(--radius-md); background: rgba(14, 165, 233, 0.15); display: flex; align-items: center; justify-content: center; color: var(--accent-primary); border: 1px solid rgba(14, 165, 233, 0.3);">
            <svg class="icon" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#users'); ?>"></use></svg>
        </div>
        <div>
            <h3 style="font-size: var(--text-base); font-weight: var(--weight-semibold); color: var(--text-primary); margin-bottom: 2px;">Usuarios</h3>
            <p style="font-size: var(--text-xs); color: var(--text-tertiary);">Gestionar cuentas y roles</p>
        </div>
    </a>

    <a href="index.php?page=medicos" class="glass-card" style="display: flex; flex-direction: column; gap: var(--space-3); transition: all var(--duration-fast) var(--ease-smooth);">
        <div style="width: 44px; height: 44px; border-radius: var(--radius-md); background: rgba(16, 185, 129, 0.15); display: flex; align-items: center; justify-content: center; color: var(--success); border: 1px solid rgba(16, 185, 129, 0.3);">
            <svg class="icon" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#user'); ?>"></use></svg>
        </div>
        <div>
            <h3 style="font-size: var(--text-base); font-weight: var(--weight-semibold); color: var(--text-primary); margin-bottom: 2px;">Médicos</h3>
            <p style="font-size: var(--text-xs); color: var(--text-tertiary);">Especialistas y agendas</p>
        </div>
    </a>

    <a href="index.php?page=especialidades" class="glass-card" style="display: flex; flex-direction: column; gap: var(--space-3); transition: all var(--duration-fast) var(--ease-smooth);">
        <div style="width: 44px; height: 44px; border-radius: var(--radius-md); background: rgba(139, 92, 246, 0.15); display: flex; align-items: center; justify-content: center; color: #a78bfa; border: 1px solid rgba(139, 92, 246, 0.3);">
            <svg class="icon" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#heart'); ?>"></use></svg>
        </div>
        <div>
            <h3 style="font-size: var(--text-base); font-weight: var(--weight-semibold); color: var(--text-primary); margin-bottom: 2px;">Especialidades</h3>
            <p style="font-size: var(--text-xs); color: var(--text-tertiary);">Catálogo de áreas médicas</p>
        </div>
    </a>

    <a href="index.php?page=reportes" class="glass-card" style="display: flex; flex-direction: column; gap: var(--space-3); transition: all var(--duration-fast) var(--ease-smooth);">
        <div style="width: 44px; height: 44px; border-radius: var(--radius-md); background: rgba(245, 158, 11, 0.15); display: flex; align-items: center; justify-content: center; color: var(--warning); border: 1px solid rgba(245, 158, 11, 0.3);">
            <svg class="icon" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#chart-line'); ?>"></use></svg>
        </div>
        <div>
            <h3 style="font-size: var(--text-base); font-weight: var(--weight-semibold); color: var(--text-primary); margin-bottom: 2px;">Reportes</h3>
            <p style="font-size: var(--text-xs); color: var(--text-tertiary);">Analítica y exportación</p>
        </div>
    </a>
</div>

<script>
// Gráfico de citas del mes con colores de diseño
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('citasChart');
    if (ctx) {
        new Chart(ctx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Pendientes', 'Confirmadas', 'Completadas', 'Canceladas'],
                datasets: [{
                    data: [
                        <?php echo $citas_mes['pendiente'] ?? 0; ?>,
                        <?php echo $citas_mes['confirmada'] ?? 0; ?>,
                        <?php echo $citas_mes['completada'] ?? 0; ?>,
                        <?php echo $citas_mes['cancelada'] ?? 0; ?>
                    ],
                    backgroundColor: ['#f59e0b', '#0ea5e9', '#10b981', '#ef4444'],
                    borderColor: 'rgba(15, 23, 42, 0.8)',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: 'rgba(203, 213, 225, 0.8)',
                            font: { family: 'system-ui, sans-serif' },
                            padding: 16
                        }
                    }
                }
            }
        });
    }
});
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
