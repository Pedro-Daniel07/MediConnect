<?php
/**
 * Controlador de Chat Clínico Paciente - Médico
 * MediConnect
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

class ChatController {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function index() {
        if (!esta_autenticado()) {
            redirigir('login');
            return;
        }

        $usuario_id = $_SESSION['usuario_id'];
        $rol = $_SESSION['rol'];

        // Obtener lista de contactos disponibles según el rol
        $contactos = [];

        if ($rol === 'paciente') {
            // Doctores disponibles (con los que ha tenido cita o directorio médico)
            $stmt = $this->db->prepare("SELECT u.id as usuario_id, CONCAT(u.nombre, ' ', u.apellido) as nombre_completo,
                    e.nombre as especialidad, m.consultorio,
                    (SELECT COUNT(*) FROM mensajes WHERE remitente_id = u.id AND destinatario_id = ? AND leido = 0) as no_leidos
                    FROM medicos m
                    INNER JOIN usuarios u ON m.usuario_id = u.id
                    INNER JOIN especialidades e ON m.especialidad_id = e.id
                    ORDER BY no_leidos DESC, u.nombre ASC");
            $stmt->execute([$usuario_id]);
            $contactos = $stmt->fetchAll();
        } else if ($rol === 'medico') {
            // Limitar el directorio del médico a pacientes con cita y conversaciones previas.
            $stmt = $this->db->prepare("SELECT DISTINCT u.id as usuario_id, CONCAT(u.nombre, ' ', u.apellido) as nombre_completo,
                    'Paciente' as especialidad, NULL as consultorio,
                    (SELECT COUNT(*) FROM mensajes WHERE remitente_id = u.id AND destinatario_id = ? AND leido = 0) as no_leidos
                    FROM pacientes p
                    INNER JOIN usuarios u ON p.usuario_id = u.id
                    WHERE EXISTS (
                        SELECT 1 FROM citas c
                        INNER JOIN medicos m ON c.medico_id = m.id
                        WHERE c.paciente_id = p.id AND m.usuario_id = ?
                    ) OR EXISTS (
                        SELECT 1 FROM mensajes msg
                        WHERE (msg.remitente_id = u.id AND msg.destinatario_id = ?)
                           OR (msg.remitente_id = ? AND msg.destinatario_id = u.id)
                    )
                    ORDER BY no_leidos DESC, u.nombre ASC");
            $stmt->execute([$usuario_id, $usuario_id, $usuario_id, $usuario_id]);
            $contactos = $stmt->fetchAll();
        } else {
            // Administrador ve lista de especialistas
            $stmt = $this->db->prepare("SELECT u.id as usuario_id, CONCAT(u.nombre, ' ', u.apellido) as nombre_completo,
                    e.nombre as especialidad, m.consultorio,
                    0 as no_leidos
                    FROM medicos m
                    INNER JOIN usuarios u ON m.usuario_id = u.id
                    INNER JOIN especialidades e ON m.especialidad_id = e.id");
            $stmt->execute();
            $contactos = $stmt->fetchAll();
        }

        // Contacto activo seleccionado
        $contacto_id = intval($_GET['contacto_id'] ?? ($contactos[0]['usuario_id'] ?? 0));
        $contacto_activo = null;

        foreach ($contactos as $c) {
            if ($c['usuario_id'] == $contacto_id) {
                $contacto_activo = $c;
                break;
            }
        }

        if (!$contacto_activo && !empty($contactos)) {
            $contacto_activo = $contactos[0];
            $contacto_id = $contacto_activo['usuario_id'];
        }

        // Marcar como leídos los mensajes recibidos del contacto activo
        if ($contacto_id > 0) {
            $updateStmt = $this->db->prepare("UPDATE mensajes SET leido = 1 WHERE remitente_id = ? AND destinatario_id = ?");
            $updateStmt->execute([$contacto_id, $usuario_id]);
        }

        // Cargar historial inicial de mensajes
        $mensajes = [];
        if ($contacto_id > 0) {
            $stmtMsg = $this->db->prepare("SELECT m.*, u.nombre as remitente_nombre
                    FROM mensajes m
                    INNER JOIN usuarios u ON m.remitente_id = u.id
                    WHERE (m.remitente_id = ? AND m.destinatario_id = ?)
                       OR (m.remitente_id = ? AND m.destinatario_id = ?)
                    ORDER BY m.created_at ASC");
            $stmtMsg->execute([$usuario_id, $contacto_id, $contacto_id, $usuario_id]);
            $mensajes = $stmtMsg->fetchAll();
        }

        $titulo = 'Mensajería Clínica y Soporte';
        require_once __DIR__ . '/../views/chat/index.php';
    }

    /**
     * Endpoint API para Polling de Mensajes (GET)
     */
    public function apiMensajes() {
        header('Content-Type: application/json; charset=utf-8');

        if (!esta_autenticado()) {
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        $usuario_id = $_SESSION['usuario_id'];
        $contacto_id = intval($_GET['contacto_id'] ?? 0);

        if ($contacto_id <= 0 || !$this->usuarioPuedeContactar($usuario_id, $contacto_id)) {
            http_response_code(403);
            echo json_encode(['error' => 'Contacto no autorizado', 'mensajes' => []]);
            exit;
        }

        // Marcar como leídos
        $updateStmt = $this->db->prepare("UPDATE mensajes SET leido = 1 WHERE remitente_id = ? AND destinatario_id = ?");
        $updateStmt->execute([$contacto_id, $usuario_id]);

        $stmtMsg = $this->db->prepare("SELECT m.*, u.nombre as remitente_nombre
                FROM mensajes m
                INNER JOIN usuarios u ON m.remitente_id = u.id
                WHERE (m.remitente_id = ? AND m.destinatario_id = ?)
                   OR (m.remitente_id = ? AND m.destinatario_id = ?)
                ORDER BY m.created_at ASC");
        $stmtMsg->execute([$usuario_id, $contacto_id, $contacto_id, $usuario_id]);
        $mensajes = $stmtMsg->fetchAll();

        echo json_encode(['mensajes' => $mensajes, 'usuario_id' => $usuario_id]);
        exit;
    }

    /**
     * Endpoint API para Enviar Mensaje (POST)
     */
    public function apiEnviar() {
        header('Content-Type: application/json; charset=utf-8');

        if (!esta_autenticado() || !verificar_csrf($_POST['csrf_token'] ?? '')) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'No autorizado']);
            exit;
        }

        $usuario_id = $_SESSION['usuario_id'];
        $destinatario_id = intval($_POST['destinatario_id'] ?? 0);
        $mensaje = trim($_POST['mensaje'] ?? '');

        if ($destinatario_id <= 0 || empty($mensaje) || !$this->usuarioPuedeContactar($usuario_id, $destinatario_id)) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Destinatario no autorizado o mensaje inválido']);
            exit;
        }

        $stmt = $this->db->prepare("INSERT INTO mensajes (remitente_id, destinatario_id, mensaje, leido) VALUES (?, ?, ?, 0)");
        $stmt->execute([$usuario_id, $destinatario_id, $mensaje]);
        $msgId = $this->db->lastInsertId();

        registrar_auditoria('MENSAJE_ENVIADO', 'Chat Clínico', "Mensaje enviado a usuario #{$destinatario_id}");

        echo json_encode([
            'success' => true,
            'id' => $msgId,
            'mensaje' => $mensaje,
            'created_at' => date('Y-m-d H:i:s'),
            'remitente_id' => $usuario_id
        ]);
        exit;
    }

    private function usuarioPuedeContactar($usuarioId, $contactoId) {
        $stmt = $this->db->prepare("SELECT 1 FROM usuarios actual
                INNER JOIN roles rol ON actual.rol_id = rol.id
                INNER JOIN usuarios contacto ON contacto.id = ?
                INNER JOIN roles rol_contacto ON contacto.rol_id = rol_contacto.id
                WHERE actual.id = ? AND actual.activo = 1 AND contacto.activo = 1
                  AND ((rol.nombre = 'medico' AND rol_contacto.nombre = 'paciente')
                    OR (rol.nombre = 'paciente' AND rol_contacto.nombre = 'medico'))");
        $stmt->execute([$contactoId, $usuarioId]);
        if (!$stmt->fetchColumn()) return false;

        if ($_SESSION['rol'] === 'medico') {
            $stmt = $this->db->prepare("SELECT 1 FROM pacientes p
                    WHERE p.usuario_id = ? AND (
                        EXISTS (SELECT 1 FROM citas c INNER JOIN medicos m ON c.medico_id = m.id
                                WHERE c.paciente_id = p.id AND m.usuario_id = ?)
                        OR EXISTS (SELECT 1 FROM mensajes msg
                                   WHERE (msg.remitente_id = p.usuario_id AND msg.destinatario_id = ?)
                                      OR (msg.remitente_id = ? AND msg.destinatario_id = p.usuario_id))
                    )");
            $stmt->execute([$contactoId, $usuarioId, $usuarioId, $usuarioId]);
            return (bool) $stmt->fetchColumn();
        }

        return true;
    }
}
