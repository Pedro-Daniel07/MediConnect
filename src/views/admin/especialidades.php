<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="dashboard-header">
    <h1>Gestión de Especialidades</h1>
    <p>Especialidades médicas disponibles</p>
</div>

<?php if (isset($_SESSION['mensaje_exito'])): ?>
    <div class="alert alert-success">
        <?php echo e($_SESSION['mensaje_exito']); ?>
        <?php unset($_SESSION['mensaje_exito']); ?>
    </div>
<?php endif; ?>

<div class="dashboard-grid">
    <div class="dashboard-section">
        <h2>Nueva Especialidad</h2>
        <form method="POST" action="index.php?page=especialidades">
            <input type="hidden" name="csrf_token" value="<?php echo obtener_csrf_token(); ?>">
            <input type="hidden" name="action" value="crear">

            <div class="form-group">
                <label for="nombre">Nombre de la Especialidad</label>
                <input type="text" id="nombre" name="nombre" required>
            </div>

            <div class="form-group">
                <label for="descripcion">Descripción</label>
                <textarea id="descripcion" name="descripcion" rows="3"></textarea>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Crear Especialidad</button>
        </form>
    </div>

    <div class="dashboard-section">
        <h2>Especialidades Existentes</h2>
        <?php if (empty($especialidades)): ?>
            <div class="empty-state">
                <p>No hay especialidades registradas</p>
            </div>
        <?php else: ?>
            <div class="list-container">
                <?php foreach ($especialidades as $esp): ?>
                    <div class="list-item">
                        <div class="list-item-icon">🏥</div>
                        <div class="list-item-content">
                            <h4><?php echo e($esp['nombre']); ?></h4>
                            <?php if (!empty($esp['descripcion'])): ?>
                                <p><?php echo e($esp['descripcion']); ?></p>
                            <?php endif; ?>
                            <span class="badge badge-<?php echo $esp['activo'] ? 'success' : 'error'; ?>">
                                <?php echo $esp['activo'] ? 'Activa' : 'Inactiva'; ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
