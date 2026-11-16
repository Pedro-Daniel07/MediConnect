<?php
/**
 * Controlador de Notificaciones
 */

require_once __DIR__ . '/../../config/config.php';

class NotificacionController {
    /**
     * Listar notificaciones del usuario
     */
    public function index() {
        if (!esta_autenticado()) {
            redirigir('login');
            return;
        }

        $db = Database::getInstance()->getConnection();

        // Marcar como leídas si se recibe el parámetro
        if (isset($_GET['marcar_leidas'])) {
            $sql = "UPDATE notificaciones SET leida = 1
                    WHERE usuario_id = :usuario_id AND leida = 0";
            $stmt = $db->prepare($sql);
            $stmt->execute([':usuario_id' => $_SESSION['usuario_id']]);
            redirigir('notificaciones');
            return;
        }

        // Obtener notificaciones
        $sql = "SELECT * FROM notificaciones
                WHERE usuario_id = :usuario_id
                ORDER BY created_at DESC
                LIMIT 50";

        $stmt = $db->prepare($sql);
        $stmt->execute([':usuario_id' => $_SESSION['usuario_id']]);
        $notificaciones = $stmt->fetchAll();

        $titulo = 'Notificaciones';
        require_once __DIR__ . '/../views/notificaciones/index.php';
    }
}
