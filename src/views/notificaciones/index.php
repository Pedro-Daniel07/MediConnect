<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="dashboard-header">
    <h1>Notificaciones</h1>
    <p>Mensajes y recordatorios del sistema</p>
</div>

<?php
$no_leidas = array_filter($notificaciones, fn($n) => !$n['leida']);
$leidas = array_filter($notificaciones, fn($n) => $n['leida']);
?>

<?php if (count($no_leidas) > 0): ?>
    <div style="margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center;">
        <p><strong><?php echo count($no_leidas); ?> notificaciones sin leer</strong></p>
        <form method="POST" action="index.php?page=notificaciones" style="display: inline;">
            <input type="hidden" name="csrf_token" value="<?php echo obtener_csrf_token(); ?>">
            <input type="hidden" name="marcar_leidas" value="1">
            <button type="submit" class="btn btn-sm btn-secondary">
                Marcar todas como leídas
            </button>
        </form>
    </div>
<?php endif; ?>

<?php if (empty($notificaciones)): ?>
    <div class="dashboard-section">
        <div class="empty-state">
            <p>No tienes notificaciones</p>
        </div>
    </div>
<?php else: ?>
    <?php if (count($no_leidas) > 0): ?>
        <div class="dashboard-section" style="margin-bottom: 24px;">
            <h2>Sin Leer</h2>
            <div class="list-container">
                <?php foreach ($no_leidas as $notif): ?>
                    <div class="list-item" style="background: rgba(59, 130, 246, 0.05); border-left: 3px solid var(--color-primary);">
                        <div class="list-item-icon">
                            <?php
                            $iconos = [
                                'cita' => '📅',
                                'medicamento' => '💊',
                                'resultado' => '🔬',
                                'general' => '📢'
                            ];
                            echo $iconos[$notif['tipo']] ?? '📢';
                            ?>
                        </div>
                        <div class="list-item-content">
                            <h4><?php echo e($notif['titulo']); ?></h4>
                            <p><?php echo e($notif['mensaje']); ?></p>
                            <p class="text-muted"><small><?php echo formatear_fecha($notif['created_at'], true); ?></small></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if (count($leidas) > 0): ?>
        <div class="dashboard-section">
            <h2>Leídas</h2>
            <div class="list-container">
                <?php foreach ($leidas as $notif): ?>
                    <div class="list-item">
                        <div class="list-item-icon" style="opacity: 0.5;">
                            <?php
                            $iconos = [
                                'cita' => '📅',
                                'medicamento' => '💊',
                                'resultado' => '🔬',
                                'general' => '📢'
                            ];
                            echo $iconos[$notif['tipo']] ?? '📢';
                            ?>
                        </div>
                        <div class="list-item-content">
                            <h4><?php echo e($notif['titulo']); ?></h4>
                            <p><?php echo e($notif['mensaje']); ?></p>
                            <p class="text-muted"><small><?php echo formatear_fecha($notif['created_at'], true); ?></small></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
