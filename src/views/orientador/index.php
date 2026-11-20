<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="dashboard-header">
    <h1>🩺 Orientador de Síntomas</h1>
    <p>Evaluación preliminar de síntomas</p>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-error"><?php echo e($error); ?></div>
<?php endif; ?>

<div class="alert alert-warning" style="border-left: 4px solid #fbbf24;">
    <strong>⚠️ AVISO IMPORTANTE:</strong> Este orientador proporciona información general únicamente.
    <strong>NO reemplaza una consulta médica profesional.</strong> Ante cualquier síntoma,
    siempre consulta con un médico calificado.
</div>

<div class="dashboard-section">
    <form method="POST" action="index.php?page=orientador">
        <input type="hidden" name="csrf_token" value="<?php echo obtener_csrf_token(); ?>">
        <h3 style="margin-bottom: 16px;">Selecciona tus síntomas:</h3>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 12px;">
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                <input type="checkbox" name="sintomas[]" value="fiebre" <?php echo in_array('fiebre', $sintomas_seleccionados) ? 'checked' : ''; ?>>
                <span>🌡️ Fiebre</span>
            </label>

            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                <input type="checkbox" name="sintomas[]" value="tos" <?php echo in_array('tos', $sintomas_seleccionados) ? 'checked' : ''; ?>>
                <span>😷 Tos</span>
            </label>

            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                <input type="checkbox" name="sintomas[]" value="dolor_cabeza" <?php echo in_array('dolor_cabeza', $sintomas_seleccionados) ? 'checked' : ''; ?>>
                <span>🤕 Dolor de cabeza</span>
            </label>

            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                <input type="checkbox" name="sintomas[]" value="dolor_pecho_leve" <?php echo in_array('dolor_pecho_leve', $sintomas_seleccionados) ? 'checked' : ''; ?>>
                <span>💓 Dolor de pecho leve</span>
            </label>

            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                <input type="checkbox" name="sintomas[]" value="dolor_pecho" <?php echo in_array('dolor_pecho', $sintomas_seleccionados) ? 'checked' : ''; ?>>
                <span>🚨 Dolor de pecho intenso</span>
            </label>

            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                <input type="checkbox" name="sintomas[]" value="dificultad_respirar_severa" <?php echo in_array('dificultad_respirar_severa', $sintomas_seleccionados) ? 'checked' : ''; ?>>
                <span>🚨 Dificultad para respirar severa</span>
            </label>

            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                <input type="checkbox" name="sintomas[]" value="palpitaciones" <?php echo in_array('palpitaciones', $sintomas_seleccionados) ? 'checked' : ''; ?>>
                <span>💗 Palpitaciones</span>
            </label>

            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                <input type="checkbox" name="sintomas[]" value="erupcion_piel" <?php echo in_array('erupcion_piel', $sintomas_seleccionados) ? 'checked' : ''; ?>>
                <span>🔴 Erupción en la piel</span>
            </label>

            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                <input type="checkbox" name="sintomas[]" value="picazon" <?php echo in_array('picazon', $sintomas_seleccionados) ? 'checked' : ''; ?>>
                <span>😣 Picazón</span>
            </label>

            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                <input type="checkbox" name="sintomas[]" value="nauseas" <?php echo in_array('nauseas', $sintomas_seleccionados) ? 'checked' : ''; ?>>
                <span>🤢 Náuseas</span>
            </label>

            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                <input type="checkbox" name="sintomas[]" value="mareo" <?php echo in_array('mareo', $sintomas_seleccionados) ? 'checked' : ''; ?>>
                <span>😵 Mareo</span>
            </label>

            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                <input type="checkbox" name="sintomas[]" value="fatiga" <?php echo in_array('fatiga', $sintomas_seleccionados) ? 'checked' : ''; ?>>
                <span>😴 Fatiga</span>
            </label>
        </div>

        <button type="submit" class="btn btn-primary btn-block" style="margin-top: 24px;">
            Evaluar Síntomas
        </button>
    </form>
</div>

<?php if ($resultado): ?>
    <div class="dashboard-section" style="margin-top: 24px; border-left: 4px solid <?php
        echo $resultado['urgencia'] === 'alta' ? 'var(--color-error)' :
            ($resultado['urgencia'] === 'media' ? 'var(--color-warning)' : 'var(--color-success)');
    ?>;">
        <h2 style="margin-bottom: 16px;">Resultado de la Evaluación</h2>

        <div style="background: var(--color-bg-secondary); padding: 20px; border-radius: 8px;">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                <span style="font-size: 2rem;">
                    <?php
                    echo $resultado['urgencia'] === 'alta' ? '🚨' :
                        ($resultado['urgencia'] === 'media' ? '⚠️' : '✅');
                    ?>
                </span>
                <div>
                    <h3 style="margin: 0;">Especialidad Recomendada: <?php echo e($resultado['especialidad']); ?></h3>
                    <p style="margin: 4px 0 0 0; color: <?php
                        echo $resultado['urgencia'] === 'alta' ? 'var(--color-error)' :
                            ($resultado['urgencia'] === 'media' ? 'var(--color-warning)' : 'var(--color-success)');
                    ?>;">
                        Nivel de urgencia: <strong><?php echo ucfirst($resultado['urgencia']); ?></strong>
                    </p>
                </div>
            </div>

            <p style="font-size: 1.1rem; margin-bottom: 20px;"><?php echo $resultado['recomendacion']; ?></p>

            <?php if ($resultado['urgencia'] !== 'alta'): ?>
                <a href="index.php?page=agendar-cita" class="btn btn-primary">
                    Agendar Cita con <?php echo e($resultado['especialidad']); ?>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="alert alert-info" style="margin-top: 16px;">
        <strong>Recuerda:</strong> Esta es solo una orientación preliminar. Un médico debe realizar
        un examen completo para un diagnóstico preciso.
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
