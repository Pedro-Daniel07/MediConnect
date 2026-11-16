<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="dashboard-header">
    <h1>Mi Perfil</h1>
    <p>Gestiona tu información personal</p>
</div>

<?php if (isset($_SESSION['mensaje_exito'])): ?>
    <div class="alert alert-success">
        <?php echo e($_SESSION['mensaje_exito']); ?>
        <?php unset($_SESSION['mensaje_exito']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['mensaje_error'])): ?>
    <div class="alert alert-error">
        <?php echo e($_SESSION['mensaje_error']); ?>
        <?php unset($_SESSION['mensaje_error']); ?>
    </div>
<?php endif; ?>

<div class="dashboard-grid">
    <!-- Datos Personales -->
    <div class="dashboard-section">
        <h2>Datos Personales</h2>
        <form method="POST" action="index.php?page=perfil">
            <input type="hidden" name="csrf_token" value="<?php echo obtener_csrf_token(); ?>">
            <input type="hidden" name="action" value="datos_personales">

            <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo e($usuario['nombre']); ?>" required>
            </div>

            <div class="form-group">
                <label for="apellido">Apellido</label>
                <input type="text" id="apellido" name="apellido" value="<?php echo e($usuario['apellido']); ?>" required>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" value="<?php echo e($usuario['email']); ?>" disabled>
                <small>El email no se puede cambiar</small>
            </div>

            <div class="form-group">
                <label for="telefono">Teléfono</label>
                <input type="tel" id="telefono" name="telefono" value="<?php echo e($usuario['telefono']); ?>">
            </div>

            <div class="form-group">
                <label for="direccion">Dirección</label>
                <input type="text" id="direccion" name="direccion" value="<?php echo e($usuario['direccion']); ?>">
            </div>

            <button type="submit" class="btn btn-primary btn-block">Actualizar Datos</button>
        </form>
    </div>

    <!-- Cambiar Contraseña -->
    <div class="dashboard-section">
        <h2>Cambiar Contraseña</h2>
        <form method="POST" action="index.php?page=perfil">
            <input type="hidden" name="csrf_token" value="<?php echo obtener_csrf_token(); ?>">
            <input type="hidden" name="action" value="cambiar_password">

            <div class="form-group">
                <label for="password_actual">Contraseña Actual</label>
                <input type="password" id="password_actual" name="password_actual" required>
            </div>

            <div class="form-group">
                <label for="password_nueva">Nueva Contraseña</label>
                <input type="password" id="password_nueva" name="password_nueva" required minlength="8">
                <small>Mínimo 8 caracteres</small>
            </div>

            <div class="form-group">
                <label for="password_confirm">Confirmar Nueva Contraseña</label>
                <input type="password" id="password_confirm" name="password_confirm" required minlength="8">
            </div>

            <button type="submit" class="btn btn-primary btn-block">Cambiar Contraseña</button>
        </form>
    </div>
</div>

<?php if (tiene_rol('paciente') && $paciente): ?>
    <div class="dashboard-section dashboard-medical-section" style="margin-top: 24px;">
        <h2>Información Médica</h2>
        <form method="POST" action="index.php?page=perfil">
            <input type="hidden" name="csrf_token" value="<?php echo obtener_csrf_token(); ?>">
            <input type="hidden" name="action" value="datos_medicos">

            <div class="form-row">
                <div class="form-group">
                    <label for="grupo_sanguineo">Grupo Sanguíneo</label>
                    <select id="grupo_sanguineo" name="grupo_sanguineo">
                        <option value="">Seleccionar</option>
                        <?php foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $tipo): ?>
                            <option value="<?php echo $tipo; ?>" <?php echo $paciente['grupo_sanguineo'] === $tipo ? 'selected' : ''; ?>>
                                <?php echo $tipo; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="alergias">Alergias</label>
                <textarea id="alergias" name="alergias" rows="3"><?php echo e($paciente['alergias'] ?? ''); ?></textarea>
                <small>Medicamentos, alimentos u otras alergias</small>
            </div>

            <div class="form-group">
                <label for="enfermedades_cronicas">Enfermedades Crónicas</label>
                <textarea id="enfermedades_cronicas" name="enfermedades_cronicas" rows="3"><?php echo e($paciente['enfermedades_cronicas'] ?? ''); ?></textarea>
            </div>

            <h3 style="margin: 24px 0 16px 0;">Contacto de Emergencia</h3>

            <div class="form-group">
                <label for="contacto_emergencia_nombre">Nombre Completo</label>
                <input type="text" id="contacto_emergencia_nombre" name="contacto_emergencia_nombre"
                       value="<?php echo e($paciente['contacto_emergencia_nombre'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="contacto_emergencia_telefono">Teléfono</label>
                <input type="tel" id="contacto_emergencia_telefono" name="contacto_emergencia_telefono"
                       value="<?php echo e($paciente['contacto_emergencia_telefono'] ?? ''); ?>">
            </div>

            <button type="submit" class="btn btn-primary btn-block">Actualizar Información Médica</button>
        </form>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
