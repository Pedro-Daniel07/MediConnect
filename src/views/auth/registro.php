<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo asset('css/style.css'); ?>">
</head>
<body class="auth-page">
    <div class="auth-container">
        <div class="auth-box auth-box-wide">
            <div class="auth-header">
                <img src="<?php echo asset('img/logo.png'); ?>" alt="<?php echo SITE_NAME; ?>" class="auth-logo" onerror="this.style.display='none'">
                <h1>Crear Cuenta</h1>
                <p>Regístrate como paciente en <?php echo SITE_NAME; ?></p>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-error" role="alert"><?php echo e($error); ?></div>
            <?php endif; ?>

            <?php if (isset($errores) && !empty($errores)): ?>
                <div class="alert alert-error" role="alert">
                    <ul>
                        <?php foreach ($errores as $err): ?>
                            <li><?php echo e($err); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="index.php?page=registro" class="auth-form">
                <input type="hidden" name="csrf_token" value="<?php echo obtener_csrf_token(); ?>">

                <div class="form-row">
                    <div class="form-group">
                        <label for="nombre">Nombre <span aria-hidden="true">*</span></label>
                        <input type="text" id="nombre" name="nombre" value="<?php echo e($datos['nombre'] ?? ''); ?>" autocomplete="given-name" required>
                    </div>

                    <div class="form-group">
                        <label for="apellido">Apellido <span aria-hidden="true">*</span></label>
                        <input type="text" id="apellido" name="apellido" value="<?php echo e($datos['apellido'] ?? ''); ?>" autocomplete="family-name" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="documento">Documento de Identidad <span aria-hidden="true">*</span></label>
                        <input type="text" id="documento" name="documento" value="<?php echo e($datos['documento'] ?? ''); ?>" autocomplete="off" required>
                    </div>

                    <div class="form-group">
                        <label for="fecha_nacimiento">Fecha de Nacimiento</label>
                        <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo e($datos['fecha_nacimiento'] ?? ''); ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="genero">Género</label>
                        <select id="genero" name="genero">
                            <option value="Otro" <?php echo ($datos['genero'] ?? 'Otro') === 'Otro' ? 'selected' : ''; ?>>Prefiero no decirlo</option>
                            <option value="M" <?php echo ($datos['genero'] ?? '') === 'M' ? 'selected' : ''; ?>>Masculino</option>
                            <option value="F" <?php echo ($datos['genero'] ?? '') === 'F' ? 'selected' : ''; ?>>Femenino</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="telefono">Teléfono</label>
                        <input type="tel" id="telefono" name="telefono" value="<?php echo e($datos['telefono'] ?? ''); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Correo Electrónico *</label>
                    <input type="email" id="email" name="email" value="<?php echo e($datos['email'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="direccion">Dirección</label>
                    <input type="text" id="direccion" name="direccion" value="<?php echo e($datos['direccion'] ?? ''); ?>">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="password">Contraseña *</label>
                        <input type="password" id="password" name="password" required minlength="8">
                        <small>Mínimo 8 caracteres</small>
                    </div>

                    <div class="form-group">
                        <label for="password_confirm">Confirmar Contraseña *</label>
                        <input type="password" id="password_confirm" name="password_confirm" required minlength="8">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Registrarse</button>
            </form>

            <div class="auth-links">
                <a href="index.php?page=login">¿Ya tienes cuenta? Inicia sesión</a>
            </div>
        </div>
    </div>

    <script src="<?php echo asset('js/app.js'); ?>"></script>
</body>
</html>
