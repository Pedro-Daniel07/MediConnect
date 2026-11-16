<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="dashboard-header">
    <h1>Gestión de Usuarios</h1>
    <p>Administración de usuarios del sistema</p>
</div>

<div class="dashboard-section">
    <form method="GET" action="index.php" style="margin-bottom: 24px;">
        <input type="hidden" name="page" value="usuarios">
        <div style="display: grid; grid-template-columns: 1fr 1fr auto; gap: 12px;">
            <input type="text" name="busqueda" placeholder="Buscar por nombre, email o documento"
                   value="<?php echo e($_GET['busqueda'] ?? ''); ?>">

            <select name="rol_id">
                <option value="">Todos los roles</option>
                <option value="1" <?php echo ($_GET['rol_id'] ?? '') == '1' ? 'selected' : ''; ?>>Administrador</option>
                <option value="2" <?php echo ($_GET['rol_id'] ?? '') == '2' ? 'selected' : ''; ?>>Médico</option>
                <option value="3" <?php echo ($_GET['rol_id'] ?? '') == '3' ? 'selected' : ''; ?>>Paciente</option>
            </select>

            <button type="submit" class="btn btn-primary">Buscar</button>
        </div>
    </form>

    <?php if (empty($usuarios)): ?>
        <div class="empty-state">
            <p>No se encontraron usuarios</p>
        </div>
    <?php else: ?>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: var(--color-bg-secondary); text-align: left;">
                    <th style="padding: 12px; border-bottom: 2px solid var(--color-border);">Nombre</th>
                    <th style="padding: 12px; border-bottom: 2px solid var(--color-border);">Email</th>
                    <th style="padding: 12px; border-bottom: 2px solid var(--color-border);">Documento</th>
                    <th style="padding: 12px; border-bottom: 2px solid var(--color-border);">Rol</th>
                    <th style="padding: 12px; border-bottom: 2px solid var(--color-border);">Teléfono</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $user): ?>
                    <tr style="border-bottom: 1px solid var(--color-border);">
                        <td style="padding: 12px;"><?php echo e($user['nombre'] . ' ' . $user['apellido']); ?></td>
                        <td style="padding: 12px;"><?php echo e($user['email']); ?></td>
                        <td style="padding: 12px;"><?php echo e($user['documento']); ?></td>
                        <td style="padding: 12px;">
                            <span class="badge badge-info"><?php echo ucfirst($user['rol_nombre']); ?></span>
                        </td>
                        <td style="padding: 12px;"><?php echo e($user['telefono'] ?: '-'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
