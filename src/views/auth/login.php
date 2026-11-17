<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso al Sistema - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo asset('css/style.css'); ?>">
    <script>
        (function() {
            try {
                const savedTheme = localStorage.getItem('theme') || 'dark';
                document.documentElement.setAttribute('data-theme', savedTheme);
            } catch (error) {
                document.documentElement.setAttribute('data-theme', 'dark');
            }
        })();
    </script>
</head>
<body style="min-height: 100vh; display: flex; align-items: stretch; background: var(--backdrop); overflow-x: hidden;">
    <!-- Panel Izquierdo: Marca e Ilustración Futurista (Desktop) -->
    <div style="flex: 1.1; background: linear-gradient(135deg, rgba(15, 23, 42, 0.95), rgba(30, 41, 59, 0.90)), radial-gradient(circle at 20% 30%, rgba(14, 165, 233, 0.15), transparent 70%); display: flex; flex-direction: column; justify-content: space-between; padding: var(--space-12); position: relative; border-right: 1px solid var(--glass-border);" class="auth-brand-panel">
        <div>
            <!-- Logo y Nombre -->
            <div style="display: flex; align-items: center; gap: var(--space-3); margin-bottom: var(--space-8);">
                <div style="width: 44px; height: 44px; border-radius: var(--radius-md); background: linear-gradient(135deg, var(--accent-primary), var(--accent-hover)); display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 16px rgba(14, 165, 233, 0.3);">
                    <svg class="icon icon-lg" aria-hidden="true" style="color: white;"><use href="<?php echo asset('img/icons.svg#activity'); ?>"></use></svg>
                </div>
                <span style="font-size: var(--text-2xl); font-weight: var(--weight-bold); color: var(--text-primary); letter-spacing: -0.02em;">
                    <?php echo SITE_NAME; ?>
                </span>
            </div>

            <!-- Titular de impacto -->
            <h1 style="font-size: var(--text-4xl); font-weight: var(--weight-bold); line-height: 1.15; color: var(--text-primary); margin-bottom: var(--space-4); max-width: 480px;">
                Gestión de Salud de <span style="color: var(--accent-primary);">Próxima Generación</span>
            </h1>
            <p style="font-size: var(--text-lg); color: var(--text-secondary); line-height: 1.6; max-width: 440px;">
                Plataforma clínica integral con arquitectura pura, seguridad de nivel hospitalario y experiencia visual de 2030.
            </p>
        </div>

        <!-- Ilustración SVG Médica Futurista -->
        <div style="margin: var(--space-8) 0; display: flex; justify-content: center;">
            <svg width="320" height="240" viewBox="0 0 320 240" fill="none" xmlns="http://www.w3.org/2000/svg" style="max-width: 100%;">
                <!-- Ondas de pulso médico con brillo -->
                <path d="M20 120 H80 L100 60 L120 180 L140 90 L160 140 L180 120 H300" stroke="var(--accent-primary)" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="filter: drop-shadow(0 0 8px var(--glass-glow));"/>
                <!-- Puntos de monitoreo -->
                <circle cx="100" cy="60" r="4" fill="var(--accent-hover)"/>
                <circle cx="120" cy="180" r="4" fill="var(--accent-hover)"/>
                <circle cx="140" cy="90" r="4" fill="var(--accent-hover)"/>
                <!-- Tarjeta holográfica flotante -->
                <rect x="50" y="30" width="220" height="180" rx="16" fill="rgba(30, 41, 59, 0.4)" stroke="var(--glass-border)" stroke-width="1.5" style="backdrop-filter: blur(8px);"/>
                <!-- Líneas de datos simulados -->
                <rect x="80" y="55" width="80" height="8" rx="4" fill="rgba(248, 250, 252, 0.6)"/>
                <rect x="80" y="75" width="120" height="6" rx="3" fill="rgba(148, 163, 184, 0.4)"/>
                <rect x="80" y="90" width="100" height="6" rx="3" fill="rgba(148, 163, 184, 0.3)"/>
                <circle cx="230" cy="70" r="16" fill="rgba(16, 185, 129, 0.15)" stroke="var(--success)" stroke-width="1.5"/>
                <path d="M224 70 L228 74 L236 66" stroke="var(--success)" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </div>

        <!-- Badges de tecnología en footer -->
        <div style="display: flex; gap: var(--space-4); flex-wrap: wrap;">
            <span class="badge badge-info" style="text-transform: none;">PHP 8.0+ Puro</span>
            <span class="badge badge-info" style="text-transform: none;">MySQL PDO</span>
            <span class="badge badge-info" style="text-transform: none;">CSRF Seguro</span>
            <span class="badge badge-info" style="text-transform: none;">Sin Frameworks</span>
        </div>
    </div>

    <!-- Panel Derecho: Formulario de Acceso -->
    <div class="auth-login-panel" style="flex: 1; display: flex; align-items: center; justify-content: center; padding: var(--space-8); background: var(--backdrop);">
        <div class="glass-card" style="width: 100%; max-width: 440px; padding: var(--space-8); box-shadow: var(--shadow-glass-lg);">
            <div style="text-align: center; margin-bottom: var(--space-6);">
                <h2 style="font-size: var(--text-2xl); font-weight: var(--weight-bold); color: var(--text-primary); margin-bottom: var(--space-2);">
                    Acceso Clínico
                </h2>
                <p style="font-size: var(--text-sm); color: var(--text-secondary);">
                    Ingresa tus credenciales para acceder al sistema
                </p>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-error" style="margin-bottom: var(--space-4);">
                    <svg class="icon icon-sm" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#alert-triangle'); ?>"></use></svg>
                    <span><?php echo e($error); ?></span>
                </div>
            <?php endif; ?>

            <?php if (isset($exito)): ?>
                <div class="alert alert-success" style="margin-bottom: var(--space-4);">
                    <svg class="icon icon-sm" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#check'); ?>"></use></svg>
                    <span><?php echo e($exito); ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" action="index.php?page=login" style="display: flex; flex-direction: column; gap: var(--space-4);">
                <input type="hidden" name="csrf_token" value="<?php echo obtener_csrf_token(); ?>">
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="email" class="form-label required">Correo Electrónico</label>
                    <input type="email" id="email" name="email" class="form-input" value="<?php echo e($_POST['email'] ?? ''); ?>" required autofocus placeholder="usuario@mediconnect.com">
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--space-2);">
                        <label for="password" class="form-label required" style="margin-bottom: 0;">Contraseña</label>
                        <a href="index.php?page=recuperar" style="font-size: var(--text-xs); color: var(--accent-primary);">¿Olvidaste tu contraseña?</a>
                    </div>
                    <input type="password" id="password" name="password" class="form-input" required placeholder="••••••••">
                </div>

                <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; margin-top: var(--space-2);">
                    <span>Iniciar Sesión</span>
                    <svg class="icon icon-sm" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#arrow-right'); ?>"></use></svg>
                </button>
            </form>

            <div style="text-align: center; margin-top: var(--space-6); font-size: var(--text-sm); color: var(--text-secondary);">
                ¿No tienes cuenta? <a href="index.php?page=registro" style="font-weight: var(--weight-semibold); color: var(--accent-primary);">Crear una cuenta</a>
            </div>

            <!-- Credenciales de Demostración con Autorelleno al Clic -->
            <div style="margin-top: var(--space-6); padding-top: var(--space-6); border-top: 1px solid var(--glass-border);">
                <div style="font-size: var(--text-xs); color: var(--text-tertiary); text-align: center; margin-bottom: var(--space-3); text-transform: uppercase; letter-spacing: 0.05em; font-weight: var(--weight-semibold);">
                    Usuarios de Demostración (Clic para rellenar)
                </div>
                <div style="display: flex; flex-direction: column; gap: var(--space-2);">
                    <button type="button" class="btn btn-secondary btn-sm" onclick="setDemo('admin@mediconnect.com', 'Admin123!')" style="justify-content: space-between; width: 100%;">
                        <span><strong>Admin:</strong> admin@mediconnect.com</span>
                        <span class="badge badge-info">Admin</span>
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="setDemo('dr.rodriguez@mediconnect.com', 'Admin123!')" style="justify-content: space-between; width: 100%;">
                        <span><strong>Médico:</strong> dr.rodriguez@mediconnect.com</span>
                        <span class="badge badge-confirmed">Médico</span>
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="setDemo('paciente1@mediconnect.com', 'Admin123!')" style="justify-content: space-between; width: 100%;">
                        <span><strong>Paciente:</strong> paciente1@mediconnect.com</span>
                        <span class="badge badge-completed">Paciente</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Script de Autorelleno -->
    <script>
        function setDemo(email, pass) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = pass;
            // Destello visual de confirmación
            document.getElementById('email').style.borderColor = 'var(--accent-primary)';
            document.getElementById('password').style.borderColor = 'var(--accent-primary)';
        }
    </script>

    <style>
        @media (max-width: 900px) {
            .auth-brand-panel {
                display: none !important;
            }

            .auth-login-panel > div {
                min-width: 0;
            }

            .auth-login-panel .btn > span:first-child {
                min-width: 0;
                overflow-wrap: anywhere;
            }

            .auth-login-panel {
                flex: 1 1 100% !important;
                min-width: 0;
                padding: var(--space-4) !important;
            }
        }
    </style>
</body>
</html>
