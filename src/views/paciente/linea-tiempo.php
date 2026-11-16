<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="dashboard-header">
    <h1>Línea de Tiempo de Salud</h1>
    <p>Seguimiento de peso, presión arterial e IMC</p>
</div>

<?php if (empty($datos)): ?>
    <div class="dashboard-section">
        <div class="empty-state">
            <p>No hay datos de seguimiento disponibles</p>
            <p class="text-muted">Los datos aparecerán después de tus consultas médicas</p>
        </div>
    </div>
<?php else: ?>
    <div class="dashboard-section">
        <h2>Evolución del Peso</h2>
        <div class="chart-box">
            <canvas id="pesoChart"></canvas>
        </div>
    </div>

    <div class="dashboard-section" style="margin-top: 24px;">
        <h2>Evolución del IMC</h2>
        <div class="chart-box">
            <canvas id="imcChart"></canvas>
        </div>
    </div>

    <div class="dashboard-section" style="margin-top: 24px;">
        <h2>Registros Detallados</h2>
        <div class="list-container">
            <?php foreach ($datos as $dato): ?>
                <div class="list-item">
                    <div class="list-item-icon">📊</div>
                    <div class="list-item-content">
                        <h4><?php echo formatear_fecha($dato['fecha'], true); ?></h4>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 8px; margin-top: 8px;">
                            <?php if ($dato['peso']): ?>
                                <div><strong>Peso:</strong> <?php echo e($dato['peso']); ?> kg</div>
                            <?php endif; ?>
                            <?php if ($dato['altura']): ?>
                                <div><strong>Altura:</strong> <?php echo e($dato['altura']); ?> cm</div>
                            <?php endif; ?>
                            <?php if (isset($dato['imc'])): ?>
                                <div><strong>IMC:</strong> <?php echo e($dato['imc']); ?> (<?php echo clasificar_imc($dato['imc']); ?>)</div>
                            <?php endif; ?>
                            <?php if ($dato['presion_arterial']): ?>
                                <div><strong>Presión:</strong> <?php echo e($dato['presion_arterial']); ?></div>
                            <?php endif; ?>
                            <?php if ($dato['temperatura']): ?>
                                <div><strong>Temp:</strong> <?php echo e($dato['temperatura']); ?>°C</div>
                            <?php endif; ?>
                            <?php if ($dato['frecuencia_cardiaca']): ?>
                                <div><strong>FC:</strong> <?php echo e($dato['frecuencia_cardiaca']); ?> ppm</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const datos = <?php echo json_encode(array_reverse($datos)); ?>;

        // Gráfico de peso
        const ctxPeso = document.getElementById('pesoChart');
        if (ctxPeso && datos.some(d => d.peso)) {
            new Chart(ctxPeso.getContext('2d'), {
                type: 'line',
                data: {
                    labels: datos.filter(d => d.peso).map(d => new Date(d.fecha).toLocaleDateString('es-ES')),
                    datasets: [{
                        label: 'Peso (kg)',
                        data: datos.filter(d => d.peso).map(d => d.peso),
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }

        // Gráfico de IMC
        const ctxImc = document.getElementById('imcChart');
        if (ctxImc && datos.some(d => d.imc)) {
            new Chart(ctxImc.getContext('2d'), {
                type: 'line',
                data: {
                    labels: datos.filter(d => d.imc).map(d => new Date(d.fecha).toLocaleDateString('es-ES')),
                    datasets: [{
                        label: 'IMC',
                        data: datos.filter(d => d.imc).map(d => d.imc),
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.4
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
<?php endif; ?>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
