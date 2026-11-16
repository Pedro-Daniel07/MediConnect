<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="dashboard-header">
    <h1>Atender Cita</h1>
    <p><?php echo e($cita['paciente_nombre']); ?> - <?php echo formatear_fecha($cita['fecha']); ?></p>
</div>

<div class="dashboard-section" style="margin-bottom: 24px;">
    <h2>Información del Paciente</h2>
    <div style="padding: 16px; background: var(--color-bg-secondary); border-radius: 8px;">
        <p><strong>Motivo de consulta:</strong> <?php echo e($cita['motivo_consulta']); ?></p>
        <?php if (!empty($cita['alergias'])): ?>
            <p class="text-danger"><strong>⚠️ Alergias:</strong> <?php echo e($cita['alergias']); ?></p>
        <?php endif; ?>
        <?php if (!empty($cita['enfermedades_cronicas'])): ?>
            <p><strong>Enfermedades crónicas:</strong> <?php echo e($cita['enfermedades_cronicas']); ?></p>
        <?php endif; ?>
        <?php if (!empty($cita['grupo_sanguineo'])): ?>
            <p><strong>Grupo sanguíneo:</strong> <?php echo e($cita['grupo_sanguineo']); ?></p>
        <?php endif; ?>
    </div>

    <?php if (!empty($historial_previo)): ?>
        <div style="margin-top: 16px;">
            <h3>Historial Previo (últimas 5 consultas)</h3>
            <div class="list-container">
                <?php foreach ($historial_previo as $hist): ?>
                    <div style="padding: 12px; background: var(--color-bg-tertiary); border-radius: 8px; margin-top: 8px;">
                        <p><strong><?php echo formatear_fecha($hist['created_at']); ?></strong></p>
                        <p><?php echo e(substr($hist['diagnostico'], 0, 150)); ?>...</p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<form method="POST" action="index.php?page=atender-cita&id=<?php echo $cita['id']; ?>">
    <input type="hidden" name="csrf_token" value="<?php echo obtener_csrf_token(); ?>">

    <div class="dashboard-section" style="margin-bottom: 24px;">
        <h2>Signos Vitales</h2>
        <div class="form-row">
            <div class="form-group">
                <label for="peso">Peso (kg)</label>
                <input type="number" step="0.1" id="peso" name="peso" onchange="actualizarIMC()">
            </div>
            <div class="form-group">
                <label for="altura">Altura (cm)</label>
                <input type="number" step="0.1" id="altura" name="altura" onchange="actualizarIMC()">
            </div>
            <div class="form-group">
                <label for="presion_arterial">Presión Arterial</label>
                <input type="text" id="presion_arterial" name="presion_arterial" placeholder="120/80">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="temperatura">Temperatura (°C)</label>
                <input type="number" step="0.1" id="temperatura" name="temperatura" placeholder="36.5">
            </div>
            <div class="form-group">
                <label for="frecuencia_cardiaca">Frecuencia Cardíaca (ppm)</label>
                <input type="number" id="frecuencia_cardiaca" name="frecuencia_cardiaca" placeholder="72">
            </div>
        </div>
        <div id="imc-display" style="display: none; padding: 12px; background: var(--color-info); color: white; border-radius: 8px; margin-top: 8px;"></div>
    </div>

    <div class="dashboard-section" style="margin-bottom: 24px;">
        <h2>Historia Clínica</h2>
        <div class="form-group">
            <label for="sintomas">Síntomas</label>
            <textarea id="sintomas" name="sintomas" rows="3"></textarea>
        </div>

        <div class="form-group">
            <label for="examen_fisico">Examen Físico</label>
            <textarea id="examen_fisico" name="examen_fisico" rows="3"></textarea>
        </div>

        <div class="form-group">
            <label for="diagnostico">Diagnóstico *</label>
            <textarea id="diagnostico" name="diagnostico" rows="4" required></textarea>
        </div>

        <div class="form-group">
            <label for="plan_tratamiento">Plan de Tratamiento</label>
            <textarea id="plan_tratamiento" name="plan_tratamiento" rows="4"></textarea>
        </div>

        <div class="form-group">
            <label for="observaciones">Observaciones</label>
            <textarea id="observaciones" name="observaciones" rows="2"></textarea>
        </div>
    </div>

    <div class="dashboard-section" style="margin-bottom: 24px;">
        <h2>Fórmula Médica (Opcional)</h2>
        <div id="medicamentos-container">
            <div class="medicamento-item" style="padding: 16px; background: var(--color-bg-secondary); border-radius: 8px; margin-bottom: 12px;">
                <div class="form-row">
                    <div class="form-group">
                        <label>Medicamento</label>
                        <select name="medicamentos[]">
                            <option value="">Seleccionar...</option>
                            <?php foreach ($medicamentos as $med): ?>
                                <option value="<?php echo $med['id']; ?>"><?php echo e($med['nombre']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Dosis</label>
                        <input type="text" name="dosis[]" placeholder="500mg">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Frecuencia</label>
                        <input type="text" name="frecuencia[]" placeholder="Cada 8 horas">
                    </div>
                    <div class="form-group">
                        <label>Duración</label>
                        <input type="text" name="duracion[]" placeholder="7 días">
                    </div>
                </div>
                <div class="form-group">
                    <label>Instrucciones</label>
                    <input type="text" name="instrucciones[]" placeholder="Tomar con alimentos">
                </div>
            </div>
        </div>
        <button type="button" onclick="agregarMedicamento()" class="btn btn-secondary btn-sm">+ Agregar Medicamento</button>

        <div class="form-group" style="margin-top: 16px;">
            <label for="observaciones_formula">Observaciones de la Fórmula</label>
            <textarea id="observaciones_formula" name="observaciones_formula" rows="2"></textarea>
        </div>
    </div>

    <button type="submit" class="btn btn-primary btn-block">Guardar y Completar Cita</button>
    <a href="index.php?page=agenda-medico" class="btn btn-secondary btn-block" style="margin-top: 12px;">Cancelar</a>
</form>

<script>
function agregarMedicamento() {
    const container = document.getElementById('medicamentos-container');
    const template = container.querySelector('.medicamento-item').cloneNode(true);
    template.querySelectorAll('input, select').forEach(input => input.value = '');
    container.appendChild(template);
}
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
