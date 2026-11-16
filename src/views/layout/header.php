<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(obtener_csrf_token()); ?>">
    <title><?php echo e($titulo ?? SITE_TITLE); ?></title>
    <link rel="stylesheet" href="<?php echo asset('css/style.css'); ?>">
    <script>
        // Aplicar tema guardado inmediatamente para evitar parpadeos
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
<body>
    <?php if (esta_autenticado()): ?>
        <nav class="navbar">
            <div class="nav-container">
                <div class="nav-brand">
                    <svg class="icon icon-lg" aria-hidden="true" style="color: var(--accent-primary);">
                        <use href="<?php echo asset('img/icons.svg#activity'); ?>"></use>
                    </svg>
                    <span><?php echo SITE_NAME; ?></span>
                </div>

                <!-- Buscador con atajo Ctrl+K -->
                <div class="nav-search" onclick="openCommandPalette()" style="cursor: pointer;">
                    <input type="text" placeholder="Buscar pacientes, citas..." readonly style="cursor: pointer;">
                    <span class="search-hint">Ctrl K</span>
                </div>

                <button class="nav-toggle" type="button" aria-label="Abrir menú de navegación" aria-controls="primary-navigation" aria-expanded="false">
                    <svg class="icon" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#menu'); ?>"></use></svg>
                </button>

                <ul class="nav-menu" id="primary-navigation">
                    <?php if (tiene_rol('paciente')): ?>
                        <li>
                            <a href="index.php?page=dashboard">
                                <svg class="icon icon-sm" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#home'); ?>"></use></svg>
                                <span>Inicio</span>
                            </a>
                        </li>
                        <li>
                            <a href="index.php?page=mis-citas">
                                <svg class="icon icon-sm" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#calendar'); ?>"></use></svg>
                                <span>Citas</span>
                            </a>
                        </li>
                        <li>
                            <a href="index.php?page=telemedicina">
                                <svg class="icon icon-sm" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#video'); ?>"></use></svg>
                                <span>Telemedicina</span>
                            </a>
                        </li>
                        <li>
                            <a href="index.php?page=chat">
                                <svg class="icon icon-sm" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#message'); ?>"></use></svg>
                                <span>Chat</span>
                            </a>
                        </li>
                        <li>
                            <a href="index.php?page=carne-digital">
                                <svg class="icon icon-sm" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#qrcode'); ?>"></use></svg>
                                <span>Carné</span>
                            </a>
                        </li>
                        <li>
                            <a href="index.php?page=signos-vitales">
                                <svg class="icon icon-sm" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#activity'); ?>"></use></svg>
                                <span>Score Salud</span>
                            </a>
                        </li>
                        <li>
                            <a href="index.php?page=vacunacion">
                                <svg class="icon icon-sm" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#syringe'); ?>"></use></svg>
                                <span>Vacunas</span>
                            </a>
                        </li>
                    <?php elseif (tiene_rol('medico')): ?>
                        <li>
                            <a href="index.php?page=dashboard">
                                <svg class="icon icon-sm" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#home'); ?>"></use></svg>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="index.php?page=agenda-medico">
                                <svg class="icon icon-sm" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#calendar'); ?>"></use></svg>
                                <span>Mi Agenda</span>
                            </a>
                        </li>
                        <li>
                            <a href="index.php?page=telemedicina">
                                <svg class="icon icon-sm" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#video'); ?>"></use></svg>
                                <span>Telemedicina</span>
                            </a>
                        </li>
                        <li>
                            <a href="index.php?page=chat">
                                <svg class="icon icon-sm" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#message'); ?>"></use></svg>
                                <span>Chat</span>
                            </a>
                        </li>
                        <li>
                            <a href="index.php?page=pacientes">
                                <svg class="icon icon-sm" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#users'); ?>"></use></svg>
                                <span>Pacientes</span>
                            </a>
                        </li>
                        <li>
                            <a href="index.php?page=lista-espera">
                                <svg class="icon icon-sm" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#clock'); ?>"></use></svg>
                                <span>Lista Espera</span>
                            </a>
                        </li>
                    <?php elseif (tiene_rol('administrador')): ?>
                        <li>
                            <a href="index.php?page=dashboard">
                                <svg class="icon icon-sm" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#home'); ?>"></use></svg>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="index.php?page=auditoria">
                                <svg class="icon icon-sm" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#shield'); ?>"></use></svg>
                                <span>Auditoría</span>
                            </a>
                        </li>
                        <li>
                            <a href="index.php?page=lista-espera">
                                <svg class="icon icon-sm" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#clock'); ?>"></use></svg>
                                <span>Lista Espera</span>
                            </a>
                        </li>
                        <li>
                            <a href="index.php?page=usuarios">
                                <svg class="icon icon-sm" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#users'); ?>"></use></svg>
                                <span>Usuarios</span>
                            </a>
                        </li>
                        <li>
                            <a href="index.php?page=medicos">
                                <svg class="icon icon-sm" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#user'); ?>"></use></svg>
                                <span>Médicos</span>
                            </a>
                        </li>
                        <li>
                            <a href="index.php?page=reportes">
                                <svg class="icon icon-sm" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#chart-line'); ?>"></use></svg>
                                <span>Reportes</span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>

                <div class="nav-actions">
                    <!-- Botón Notificaciones con badge -->
                    <a href="index.php?page=notificaciones" class="nav-icon-btn" title="Notificaciones">
                        <svg class="icon" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#bell'); ?>"></use></svg>
                        <span class="badge" id="notif-count" style="display: none;">0</span>
                    </a>

                    <!-- Toggle Modo Claro / Oscuro -->
                    <button class="nav-icon-btn" onclick="toggleTheme()" title="Cambiar Tema" type="button">
                        <svg class="icon" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#moon'); ?>"></use></svg>
                    </button>

                    <!-- Dropdown Usuario -->
                    <div class="dropdown">
                        <div class="nav-user">
                            <div style="width: 28px; height: 28px; border-radius: var(--radius-full); background: linear-gradient(135deg, var(--accent-primary), var(--accent-hover)); display: flex; align-items: center; justify-content: center; color: white; font-weight: var(--weight-bold); font-size: var(--text-xs);">
                                <?php echo strtoupper(substr($_SESSION['nombre'] ?? 'U', 0, 1)); ?>
                            </div>
                            <div class="nav-user-info">
                                <span class="nav-user-name"><?php echo e($_SESSION['nombre'] ?? 'Usuario'); ?></span>
                                <span class="nav-user-role"><?php echo ucfirst(e($_SESSION['rol'] ?? '')); ?></span>
                            </div>
                        </div>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="index.php?page=perfil">
                                    <svg class="icon icon-sm" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#user'); ?>"></use></svg>
                                    <span>Mi Perfil</span>
                                </a>
                            </li>
                            <?php if (tiene_rol('paciente')): ?>
                                <li>
                                    <a href="index.php?page=orientador">
                                        <svg class="icon icon-sm" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#heart'); ?>"></use></svg>
                                        <span>Orientador de Síntomas</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <li>
                                <a href="#" onclick="openCommandPalette(); return false;">
                                    <svg class="icon icon-sm" aria-hidden="true"><use href="<?php echo asset('img/icons.svg#search'); ?>"></use></svg>
                                    <span>Comandos (Ctrl+K)</span>
                                </a>
                            </li>
                            <hr>
                            <li>
                                <a href="index.php?page=logout" style="color: var(--error);">
                                    <svg class="icon icon-sm" aria-hidden="true" style="stroke: var(--error);"><use href="<?php echo asset('img/icons.svg#x'); ?>"></use></svg>
                                    <span>Cerrar Sesión</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
    <?php endif; ?>

    <main class="container">
