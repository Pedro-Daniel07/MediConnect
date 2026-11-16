<?php
/**
 * Controlador de API (endpoints AJAX)
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../models/Cita.php';

class ApiController {
    private $citaModel;

    public function __construct() {
        $this->citaModel = new Cita();
    }

    /**
     * Obtener horarios disponibles de un médico
     */
    public function obtenerHorarios() {
        header('Content-Type: application/json');

        if (!esta_autenticado()) {
            echo json_encode(['error' => 'No autenticado']);
            return;
        }

        $medico_id = $_GET['medico_id'] ?? 0;
        $fecha = $_GET['fecha'] ?? '';

        if (!$medico_id || !$fecha) {
            echo json_encode(['error' => 'Parámetros incompletos']);
            return;
        }

        // Validar fecha futura
        if ($fecha < date('Y-m-d')) {
            echo json_encode(['error' => 'Fecha inválida', 'horarios' => []]);
            return;
        }

        $horarios = $this->citaModel->obtenerHorariosDisponibles($medico_id, $fecha);

        echo json_encode([
            'success' => true,
            'horarios' => $horarios
        ]);
    }

    /**
     * Contar notificaciones no leídas
     */
    public function contarNotificaciones() {
        header('Content-Type: application/json');

        if (!esta_autenticado()) {
            echo json_encode(['count' => 0]);
            return;
        }

        $db = Database::getInstance()->getConnection();
        $sql = "SELECT COUNT(*) FROM notificaciones
                WHERE usuario_id = :usuario_id AND leida = 0";

        $stmt = $db->prepare($sql);
        $stmt->execute([':usuario_id' => $_SESSION['usuario_id']]);
        $count = $stmt->fetchColumn();

        echo json_encode(['count' => (int)$count]);
    }
}
