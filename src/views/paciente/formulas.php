<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="dashboard-header">
    <h1>Mis Fórmulas Médicas</h1>
    <p>Prescripciones y medicamentos</p>
</div>

<?php if (empty($formulas)): ?>
    <div class="dashboard-section">
        <div class="empty-state">
            <p>No tienes fórmulas médicas registradas</p>
        </div>
    </div>
<?php else: ?>
    <?php foreach ($formulas as $formula): ?>
        <div class="dashboard-section" style="margin-bottom: 24px;" id="formula-<?php echo $formula['id']; ?>">
            <div class="section-header">
                <div>
                    <h3>Fórmula #<?php echo $formula['codigo_qr']; ?></h3>
                    <p class="text-muted"><?php echo formatear_fecha($formula['created_at']); ?> - <?php echo e($formula['medico_nombre']); ?></p>
                </div>
                <button onclick="imprimirFormula(<?php echo $formula['id']; ?>)" class="btn btn-sm btn-primary">🖨️ Imprimir</button>
            </div>

            <div style="padding: 16px;">
                <p><strong>Diagnóstico:</strong> <?php echo e($formula['diagnostico']); ?></p>
                <p><strong>Especialidad:</strong> <?php echo e($formula['especialidad']); ?></p>

                <?php if (!empty($formula['observaciones'])): ?>
                    <p style="margin-top: 12px;"><strong>Observaciones:</strong> <?php echo nl2br(e($formula['observaciones'])); ?></p>
                <?php endif; ?>

                <h4 style="margin-top: 20px; margin-bottom: 12px;">Medicamentos</h4>
                <div class="list-container">
                    <?php foreach ($formula['medicamentos'] as $med): ?>
                        <div class="list-item">
                            <div class="list-item-icon">💊</div>
                            <div class="list-item-content">
                                <h4><?php echo e($med['medicamento_nombre']); ?></h4>
                                <p><strong>Dosis:</strong> <?php echo e($med['dosis']); ?></p>
                                <p><strong>Frecuencia:</strong> <?php echo e($med['frecuencia']); ?></p>
                                <p><strong>Duración:</strong> <?php echo e($med['duracion']); ?></p>
                                <?php if (!empty($med['instrucciones'])): ?>
                                    <p><em><?php echo e($med['instrucciones']); ?></em></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div style="margin-top: 16px; padding: 12px; background: var(--color-bg-secondary); border-radius: 8px;">
                    <p style="text-align: center; margin: 0;">
                        <strong>Código:</strong> <?php echo e($formula['codigo_qr']); ?><br>
                        <small class="text-muted">Este código permite verificar la autenticidad de la fórmula</small>
                    </p>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<script>
function imprimirFormula(id) {
    const formula = document.getElementById('formula-' + id);
    const ventana = window.open('', '', 'width=800,height=600');
    ventana.document.write('<html><head><title>Fórmula Médica</title>');
    ventana.document.write('<link rel="stylesheet" href="<?php echo asset('css/style.css'); ?>">');
    ventana.document.write('<style>body { padding: 20px; } .btn { display: none; }</style>');
    ventana.document.write('</head><body>');
    ventana.document.write('<h1 style="text-align: center;"><?php echo SITE_NAME; ?></h1>');
    ventana.document.write(formula.innerHTML);
    ventana.document.write('</body></html>');
    ventana.document.close();
    ventana.print();
}
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
