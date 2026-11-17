<?php
/**
 * Controlador de Citas
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../models/Cita.php';
require_once __DIR__ . '/../models/Paciente.php';
require_once __DIR__ . '/../models/Medico.php';

class CitaController {
    private $citaModel;
    private $pacienteModel;
    private $medicoModel;

    public function __construct() {
        $this->citaModel = new Cita();
        $this->pacienteModel = new Paciente();
        $this->medicoModel = new Medico();
    }

    /**
     * Ver citas del paciente
     */
    public function misCitas() {
        if (!tiene_rol('paciente')) {
            redirigir('dashboard');
            return;
        }

        // Manejar cancelación de cita
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cancelar') {
            if (!verificar_csrf($_POST['csrf_token'] ?? '')) {
                $_SESSION['mensaje_error'] = 'Token inválido';
            } else {
                $cita_id = $_POST['cita_id'] ?? 0;
                $motivo = trim($_POST['motivo'] ?? '');
                $paciente = $this->pacienteModel->obtenerPorUsuarioId($_SESSION['usuario_id']);
                $db = Database::getInstance()->getConnection();

                try {
                    $db->beginTransaction();
                    $citaCancelada = $this->citaModel->obtenerPorId($cita_id, $paciente['id']);
                    if (!$citaCancelada || !$this->citaModel->cancelar($cita_id, $motivo, $paciente['id'])) {
                        throw new RuntimeException('No se pudo cancelar la cita solicitada.');
                    }

                    $this->registrarAuditoriaTransaccional($db, 'CITA_CANCELADA', 'Citas', "Cita #$cita_id cancelada. Motivo: $motivo");

                    // Buscar y reasignar el cupo liberado sin borrar la cita cancelada.
                    $stmtEspera = $db->prepare("SELECT le.*, CONCAT(u.nombre, ' ', u.apellido) as paciente_nombre, u.id as usuario_id
                                               FROM lista_espera le
                                               INNER JOIN pacientes p ON le.paciente_id = p.id
                                               INNER JOIN usuarios u ON p.usuario_id = u.id
                                               WHERE le.estado = 'esperando'
                                                 AND (le.medico_id = ? OR le.especialidad_id = (SELECT especialidad_id FROM medicos WHERE id = ?))
                                               ORDER BY CASE le.prioridad WHEN 'alta' THEN 1 WHEN 'media' THEN 2 ELSE 3 END, le.fecha_solicitud ASC LIMIT 1
                                               FOR UPDATE");
                    $stmtEspera->execute([$citaCancelada['medico_id'], $citaCancelada['medico_id']]);
                    $asignado = $stmtEspera->fetch();

                    if ($asignado) {
                        $stmtNewCita = $db->prepare("INSERT INTO citas (paciente_id, medico_id, fecha, hora_inicio, hora_fin, motivo_consulta, estado) VALUES (?, ?, ?, ?, ?, ?, 'confirmada')");
                        $stmtNewCita->execute([
                            $asignado['paciente_id'],
                            $citaCancelada['medico_id'],
                            $citaCancelada['fecha'],
                            $citaCancelada['hora_inicio'],
                            $citaCancelada['hora_fin'],
                            'Cupo asignado automáticamente desde lista de espera: ' . $asignado['motivo']
                        ]);
                        $nuevaCitaId = $db->lastInsertId();

                        $stmtUpd = $db->prepare("UPDATE lista_espera SET estado = 'asignado', cita_asignada_id = ? WHERE id = ? AND estado = 'esperando'");
                        $stmtUpd->execute([$nuevaCitaId, $asignado['id']]);
                        if ($stmtUpd->rowCount() !== 1) {
                            throw new RuntimeException('No se pudo actualizar la lista de espera.');
                        }

                        $stmtNotif = $db->prepare("INSERT INTO notificaciones (usuario_id, tipo, titulo, mensaje) VALUES (?, 'cita', '¡Cupo Liberado Asignado!', ?)");
                        $stmtNotif->execute([
                            $asignado['usuario_id'],
                            "Se ha liberado un cupo con {$citaCancelada['medico_nombre']} para el {$citaCancelada['fecha']} a las {$citaCancelada['hora_inicio']}. Tu cita ha sido confirmada automáticamente."
                        ]);

                        $this->registrarAuditoriaTransaccional($db, 'LISTA_ESPERA_ASIGNADA', 'Lista de Espera', "Cupo #{$nuevaCitaId} asignado automáticamente a paciente {$asignado['paciente_nombre']} desde lista de espera.");
                    }

                    $db->commit();
                    $_SESSION['mensaje_exito'] = $asignado
                        ? "Cita cancelada. El cupo liberado fue asignado a {$asignado['paciente_nombre']}."
                        : 'Cita cancelada exitosamente';
                } catch (Throwable $e) {
                    if ($db->inTransaction()) {
                        $db->rollBack();
                    }
                    error_log('Error al cancelar y reasignar cita: ' . $e->getMessage());
                    $_SESSION['mensaje_error'] = 'No se pudo cancelar la cita. Inténtalo de nuevo.';
                }
            }
            redirigir('mis-citas');
            return;
        }

        $paciente = $this->pacienteModel->obtenerPorUsuarioId($_SESSION['usuario_id']);
        $citas = $this->citaModel->obtenerPorPaciente($paciente['id']);

        $titulo = 'Mis Citas';
        require_once __DIR__ . '/../views/citas/mis-citas.php';
    }

    /**
     * Agendar cita
     */
    public function agendar() {
        if (!tiene_rol('paciente')) {
            redirigir('dashboard');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->procesarAgendarCita();
        } else {
            $especialidades = $this->medicoModel->obtenerEspecialidades();
            $medicos = $this->medicoModel->listar();

            $titulo = 'Agendar Cita';
            require_once __DIR__ . '/../views/citas/agendar.php';
        }
    }

    /**
     * Procesar agendamiento de cita
     */
    private function procesarAgendarCita() {
        if (!verificar_csrf($_POST['csrf_token'] ?? '')) {
            $error = 'Token de seguridad inválido';
            $this->mostrarFormularioAgendar($error);
            return;
        }

        $paciente = $this->pacienteModel->obtenerPorUsuarioId($_SESSION['usuario_id']);

        $datos = [
            'paciente_id' => $paciente['id'],
            'medico_id' => $_POST['medico_id'] ?? 0,
            'fecha' => $_POST['fecha'] ?? '',
            'hora_inicio' => $_POST['hora_inicio'] ?? '',
            'hora_fin' => $_POST['hora_fin'] ?? '',
            'motivo_consulta' => trim($_POST['motivo_consulta'] ?? '')
        ];

        // Validaciones
        $errores = [];

        if (empty($datos['medico_id'])) $errores[] = 'Seleccione un médico';
        if (empty($datos['fecha'])) $errores[] = 'Seleccione una fecha';
        if (empty($datos['hora_inicio'])) $errores[] = 'Seleccione una hora';
        if (empty($datos['motivo_consulta'])) $errores[] = 'Ingrese el motivo de consulta';

        // Validar fecha futura
        if ($datos['fecha'] < date('Y-m-d')) {
            $errores[] = 'No se pueden agendar citas en fechas pasadas';
        }

        // Verificar disponibilidad
        if (!$this->citaModel->verificarDisponibilidad(
            $datos['medico_id'],
            $datos['fecha'],
            $datos['hora_inicio'],
            $datos['hora_fin']
        )) {
            $errores[] = 'El horario seleccionado no está disponible';
        }

        if (!empty($errores)) {
            $error = implode('<br>', $errores);
            $this->mostrarFormularioAgendar($error);
            return;
        }

        try {
            if ($this->citaModel->crear($datos)) {
                // Crear notificación
                $this->crearNotificacionCita($paciente['usuario_id'], $datos);

                $_SESSION['mensaje_exito'] = 'Cita agendada exitosamente';
                redirigir('mis-citas');
            } else {
                $error = 'Error al agendar la cita';
                $this->mostrarFormularioAgendar($error);
            }
        } catch (Exception $e) {
            $error = 'Error: ' . $e->getMessage();
            $this->mostrarFormularioAgendar($error);
        }
    }

    /**
     * Mostrar formulario de agendar con errores
     */
    private function mostrarFormularioAgendar($error = null) {
        $especialidades = $this->medicoModel->obtenerEspecialidades();
        $medicos = $this->medicoModel->listar();
        $titulo = 'Agendar Cita';
        require_once __DIR__ . '/../views/citas/agendar.php';
    }

    /**
     * Registrar auditoría en la misma transacción de citas
     */
    private function registrarAuditoriaTransaccional($db, $accion, $modulo, $descripcion) {
        $stmt = $db->prepare("INSERT INTO auditoria_logs (usuario_id, accion, modulo, descripcion, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_SESSION['usuario_id'] ?? null,
            $accion,
            $modulo,
            $descripcion,
            $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            substr($_SERVER['HTTP_USER_AGENT'] ?? 'CLI/Browser', 0, 255)
        ]);
    }

    /**
     * Crear notificación de cita
     */
    private function crearNotificacionCita($usuario_id, $datos) {
        $db = Database::getInstance()->getConnection();
        $sql = "INSERT INTO notificaciones (usuario_id, tipo, titulo, mensaje, fecha_evento)
                VALUES (:usuario_id, 'cita', 'Cita Agendada', :mensaje, :fecha_evento)";

        $medico = $this->medicoModel->obtenerPorId($datos['medico_id']);
        $mensaje = sprintf(
            'Tienes una cita el %s a las %s con %s %s',
            formatear_fecha($datos['fecha']),
            date('h:i A', strtotime($datos['hora_inicio'])),
            $medico['nombre'],
            $medico['apellido']
        );

        $fecha_evento = $datos['fecha'] . ' ' . $datos['hora_inicio'];

        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':usuario_id' => $usuario_id,
            ':mensaje' => $mensaje,
            ':fecha_evento' => $fecha_evento
        ]);
    }
}
