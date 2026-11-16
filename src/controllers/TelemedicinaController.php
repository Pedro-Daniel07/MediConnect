<?php
/**
 * Controlador de Telemedicina y Consulta Virtual
 * MediConnect
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

class TelemedicinaController {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function index() {
        if (!esta_autenticado()) {
            redirigir('login');
            return;
        }

        $rol = $_SESSION['rol'];
        if (!in_array($rol, ['paciente', 'medico'], true)) {
            redirigir('dashboard');
            return;
        }

        $citaId = intval($_GET['cita_id'] ?? 0);
        $contactoId = intval($_GET['contacto_id'] ?? 0);
        $esMedico = $rol === 'medico';
        $usuarioActual = intval($_SESSION['usuario_id']);
        $baseSql = "SELECT c.*,
                    CONCAT(up.nombre, ' ', up.apellido) as paciente_nombre, up.documento as paciente_doc, p.grupo_sanguineo, p.alergias,
                    CONCAT(um.nombre, ' ', um.apellido) as medico_nombre, e.nombre as especialidad, m.consultorio,
                    p.usuario_id as paciente_usuario_id, um.id as medico_usuario_id
                    FROM citas c
                    INNER JOIN pacientes p ON c.paciente_id = p.id
                    INNER JOIN usuarios up ON p.usuario_id = up.id
                    INNER JOIN medicos m ON c.medico_id = m.id
                    INNER JOIN usuarios um ON m.usuario_id = um.id
                    INNER JOIN especialidades e ON m.especialidad_id = e.id ";

        if ($citaId > 0) {
            $ownerColumn = $esMedico ? 'um.id' : 'up.id';
            $otherColumn = $esMedico ? 'up.id' : 'um.id';
            $sql = $baseSql . "WHERE c.id = ? AND {$ownerColumn} = ? AND c.estado IN ('pendiente', 'confirmada') AND c.fecha >= CURRENT_DATE";
            $params = [$citaId, $usuarioActual];
            if ($contactoId > 0) {
                $sql .= " AND {$otherColumn} = ?";
                $params[] = $contactoId;
            }
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $cita = $stmt->fetch();
            if (!$cita) {
                $error = 'No se encontró una cita válida para tu cuenta. Selecciona una cita disponible.';
            }
        }

        if (empty($cita)) {
            $ownerColumn = $esMedico ? 'um.id' : 'up.id';
            $otherColumn = $esMedico ? 'up.id' : 'um.id';
            $sql = $baseSql . "WHERE {$ownerColumn} = ? AND c.estado IN ('pendiente', 'confirmada') AND c.fecha >= CURRENT_DATE";
            $params = [$usuarioActual];
            if ($contactoId > 0) {
                $sql .= " AND {$otherColumn} = ?";
                $params[] = $contactoId;
            }
            $sql .= ' ORDER BY c.fecha ASC, c.hora_inicio ASC';
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $citasDisponibles = $stmt->fetchAll();
        }

        if (!empty($cita)) {
            registrar_auditoria('TELEMEDICINA_INICIADA', 'Telemedicina', 'Acceso a sala virtual cifrada para cita #' . $cita['id']);
        }

        $titulo = empty($cita) ? 'Seleccionar cita de telemedicina' : 'Sala de Telemedicina Cifrada';
        require_once __DIR__ . '/../views/telemedicina/sala.php';
    }

    /**
     * Guardar notas médicas de la videoconsulta vía AJAX
     */
    public function guardarNotas() {
        header('Content-Type: application/json; charset=utf-8');
        if (!esta_autenticado() || $_SESSION['rol'] !== 'medico' || !verificar_csrf($_POST['csrf_token'] ?? '')) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'No autorizado']);
            exit;
        }

        $cita_id = intval($_POST['cita_id'] ?? 0);
        $notas = trim($_POST['notas'] ?? '');
        $stmt = $this->db->prepare("SELECT 1 FROM citas c INNER JOIN medicos m ON c.medico_id = m.id WHERE c.id = ? AND m.usuario_id = ? AND c.estado IN ('pendiente', 'confirmada') AND c.fecha >= CURRENT_DATE");
        $stmt->execute([$cita_id, $_SESSION['usuario_id']]);

        if ($cita_id > 0 && $stmt->fetchColumn()) {
            $stmt = $this->db->prepare("UPDATE citas SET notas_medicas = ? WHERE id = ?");
            $stmt->execute([$notas, $cita_id]);

            registrar_auditoria('NOTAS_TELEMEDICINA_GUARDADAS', 'Telemedicina', "Notas clínicas guardadas para cita #{$cita_id}");

            echo json_encode(['success' => true, 'mensaje' => 'Notas clínicas sincronizadas en la historia clínica.']);
        } else {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Cita no autorizada']);
        }
        exit;
    }
}
