<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="dashboard-header">
    <h1>Agendar Cita</h1>
    <p>Programa tu consulta médica</p>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-error"><?php echo $error; ?></div>
<?php endif; ?>

<div class="dashboard-section">
    <form method="POST" action="index.php?page=agendar-cita" id="form-agendar">
        <input type="hidden" name="csrf_token" value="<?php echo obtener_csrf_token(); ?>">
        <input type="hidden" name="hora_fin" id="hora_fin">

        <div class="form-group">
            <label for="especialidad_id">Especialidad</label>
            <select id="especialidad_id" name="especialidad_id" onchange="filtrarMedicosPorEspecialidad()">
                <option value="">Todas las especialidades</option>
                <?php foreach ($especialidades as $esp): ?>
                    <option value="<?php echo $esp['id']; ?>"><?php echo e($esp['nombre']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="medico_id">Médico *</label>
            <select id="medico_id" name="medico_id" required onchange="cargarHorariosDisponibles()">
                <option value="">Seleccione un médico</option>
                <?php foreach ($medicos as $medico): ?>
                    <option value="<?php echo $medico['id']; ?>" data-especialidad="<?php echo $medico['especialidad_id']; ?>">
                        Dr(a). <?php echo e($medico['nombre'] . ' ' . $medico['apellido']); ?> - <?php echo e($medico['especialidad_nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="fecha">Fecha *</label>
            <input type="date" id="fecha" name="fecha" required onchange="cargarHorariosDisponibles()">
            <small>Selecciona la fecha de tu cita</small>
        </div>

        <div class="form-group">
            <label>Horario Disponible *</label>
            <div id="horarios-disponibles">
                <p class="text-muted">Selecciona un médico y una fecha para ver los horarios disponibles</p>
            </div>
        </div>

        <div class="form-group">
            <label for="motivo_consulta">Motivo de Consulta *</label>
            <textarea id="motivo_consulta" name="motivo_consulta" rows="4" required
                      placeholder="Describe brevemente el motivo de tu consulta"></textarea>
        </div>

        <button type="submit" class="btn btn-primary btn-block">Agendar Cita</button>
        <a href="index.php?page=mis-citas" class="btn btn-secondary btn-block" style="margin-top: 10px;">Cancelar</a>
    </form>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
