<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="dashboard-header">
    <h1>Reportes y Estadísticas</h1>
    <p>Análisis del sistema</p>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">👥</div>
        <div class="stat-info">
            <h3><?php echo $stats['total_usuarios']; ?></h3>
            <p>Total Usuarios</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon">🏥</div>
        <div class="stat-info">
            <h3><?php echo $stats['total_pacientes']; ?></h3>
            <p>Pacientes</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon">👨‍⚕️</div>
        <div class="stat-info">
            <h3><?php echo $stats['total_medicos']; ?></h3>
            <p>Médicos</p>
        </div>
    </div>
</div>

<div class="dashboard-grid">
    <div class="dashboard-section">
        <h2>Citas por Mes (Últimos 6 Meses)</h2>
        <div class="chart-box">
            <canvas id="citasMesChart"></canvas>
        </div>
    </div>

    <div class="dashboard-section">
        <h2>Especialidades Más Solicitadas</h2>
        <?php if (empty($especialidades_top)): ?>
            <div class="empty-state">
                <p>No hay datos disponibles</p>
            </div>
        <?php else: ?>
            <div class="chart-box">
                <canvas id="especialidadesChart"></canvas>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gráfico de citas por mes
    const ctxMes = document.getElementById('citasMesChart');
    if (ctxMes) {
        const citasPorMes = <?php echo json_encode($citas_por_mes); ?>;
        new Chart(ctxMes.getContext('2d'), {
            type: 'bar',
            data: {
                labels: citasPorMes.map(c => c.mes),
                datasets: [{
                    label: 'Citas',
                    data: citasPorMes.map(c => c.total),
                    backgroundColor: '#3b82f6'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }

    // Gráfico de especialidades
    const ctxEsp = document.getElementById('especialidadesChart');
    if (ctxEsp) {
        const especialidades = <?php echo json_encode($especialidades_top); ?>;
        new Chart(ctxEsp.getContext('2d'), {
            type: 'pie',
            data: {
                labels: especialidades.map(e => e.nombre),
                datasets: [{
                    data: especialidades.map(e => e.total),
                    backgroundColor: ['#3b82f6', '#10b981', '#fbbf24', '#ef4444', '#8b5cf6']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }
});
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
