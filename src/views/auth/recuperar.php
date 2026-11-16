<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo asset('css/style.css'); ?>">
</head>
<body class="auth-page">
    <div class="auth-container">
        <div class="auth-box">
            <div class="auth-header">
                <img src="<?php echo asset('img/logo.png'); ?>" alt="<?php echo SITE_NAME; ?>" class="auth-logo" onerror="this.style.display='none'">
                <h1>Recuperar Contraseña</h1>
                <p>Ingresa tu correo electrónico</p>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-error"><?php echo e($error); ?></div>
            <?php endif; ?>

            <?php if (isset($mensaje)): ?>
                <div class="alert alert-success"><?php echo e($mensaje); ?></div>
            <?php endif; ?>

            <form method="POST" action="index.php?page=recuperar" class="auth-form">
                <div class="form-group">
                    <label for="email">Correo Electrónico</label>
                    <input type="email" id="email" name="email" value="<?php echo e($_POST['email'] ?? ''); ?>" required autofocus>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Enviar Enlace de Recuperación</button>
            </form>

            <div class="auth-links">
                <a href="index.php?page=login">Volver al inicio de sesión</a>
            </div>

            <div class="alert alert-info" style="margin-top: 20px;">
                <strong>Nota:</strong> Esta es una función simulada para el proyecto universitario. En producción, se enviaría un correo electrónico real.
            </div>
        </div>
    </div>

    <script src="<?php echo asset('js/app.js'); ?>"></script>
</body>
</html>
