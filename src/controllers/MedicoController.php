<?php
/**
 * Controlador de Médico
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../models/Medico.php';
require_once __DIR__ . '/../models/Cita.php';

class MedicoController {
    private $medicoModel;
    private $citaModel;

    public function __construct() {
        $this->medicoModel = new Medico();
        $this->citaModel = new Cita();
    }

    /**
     * Ver agenda del médico
     */
    public function agenda() {
        if (!tiene_rol('medico')) {
            redirigir('dashboard');
            return;
        }

        $medico = $this->medicoModel->obtenerPorUsuarioId($_SESSION['usuario_id']);

        // Obtener citas de la semana actual
        $fecha_inicio = date('Y-m-d');
        $fecha_fin = date('Y-m-d', strtotime('+7 days'));

        $citas = $this->citaModel->obtenerPorMedico($medico['id'], $fecha_inicio, $fecha_fin);

        $titulo = 'Mi Agenda';
        require_once __DIR__ . '/../views/medico/agenda.php';
    }

    /**
     * Atender cita
     */
    public function atenderCita() {
        if (!tiene_rol('medico')) {
            redirigir('dashboard');
            return;
        }

        $cita_id = $_GET['id'] ?? 0;
        $medico = $this->medicoModel->obtenerPorUsuarioId($_SESSION['usuario_id']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->procesarAtencion($cita_id, $medico['id']);
        } else {
            $cita = $this->citaModel->obtenerPorId($cita_id);

            if (!$cita || $cita['medico_id'] != $medico['id']) {
                $_SESSION['mensaje_error'] = 'Cita no encontrada';
                redirigir('agenda-medico');
                return;
            }

            // Obtener historial del paciente
            require_once __DIR__ . '/../models/Paciente.php';
            $pacienteModel = new Paciente();
            $historial_previo = $pacienteModel->obtenerHistoriaClinica($cita['paciente_id'], 5);

            // Obtener medicamentos disponibles
            $db = Database::getInstance()->getConnection();
            $stmt = $db->query("SELECT * FROM medicamentos WHERE activo = 1 ORDER BY nombre");
            $medicamentos = $stmt->fetchAll();

            $titulo = 'Atender Cita';
            require_once __DIR__ . '/../views/medico/atender.php';
        }
    }

    /**
     * Procesar atención de cita
     */
    private function procesarAtencion($cita_id, $medico_id) {
        if (!verificar_csrf($_POST['csrf_token'] ?? '')) {
            $_SESSION['mensaje_error'] = 'Token de seguridad inválido';
            redirigir('agenda-medico');
            return;
        }

        $cita = $this->citaModel->obtenerPorId($cita_id);

        if (!$cita || $cita['medico_id'] != $medico_id) {
            $_SESSION['mensaje_error'] = 'Cita no encontrada';
            redirigir('agenda-medico');
            return;
        }

        $db = Database::getInstance()->getConnection();

        try {
            $db->beginTransaction();

            // Crear historia clínica
            $sql = "INSERT INTO historias_clinicas
                    (cita_id, paciente_id, medico_id, diagnostico, sintomas, examen_fisico,
                     observaciones, plan_tratamiento, peso, altura, presion_arterial,
                     temperatura, frecuencia_cardiaca)
                    VALUES (:cita_id, :paciente_id, :medico_id, :diagnostico, :sintomas,
                            :examen_fisico, :observaciones, :plan_tratamiento, :peso, :altura,
                            :presion_arterial, :temperatura, :frecuencia_cardiaca)";

            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':cita_id' => $cita_id,
                ':paciente_id' => $cita['paciente_id'],
                ':medico_id' => $medico_id,
                ':diagnostico' => trim($_POST['diagnostico']),
                ':sintomas' => trim($_POST['sintomas'] ?? ''),
                ':examen_fisico' => trim($_POST['examen_fisico'] ?? ''),
                ':observaciones' => trim($_POST['observaciones'] ?? ''),
                ':plan_tratamiento' => trim($_POST['plan_tratamiento'] ?? ''),
                ':peso' => !empty($_POST['peso']) ? $_POST['peso'] : null,
                ':altura' => !empty($_POST['altura']) ? $_POST['altura'] : null,
                ':presion_arterial' => trim($_POST['presion_arterial'] ?? '') ?: null,
                ':temperatura' => !empty($_POST['temperatura']) ? $_POST['temperatura'] : null,
                ':frecuencia_cardiaca' => !empty($_POST['frecuencia_cardiaca']) ? $_POST['frecuencia_cardiaca'] : null
            ]);

            $historia_id = $db->lastInsertId();

            // Si hay medicamentos, crear fórmula
            if (!empty($_POST['medicamentos'])) {
                $sql = "INSERT INTO formulas_medicas (historia_clinica_id, paciente_id, medico_id, codigo_qr, observaciones)
                        VALUES (:historia_id, :paciente_id, :medico_id, :codigo_qr, :observaciones)";

                $codigo_qr = 'FM-' . date('Y') . '-' . str_pad($historia_id, 6, '0', STR_PAD_LEFT);

                $stmt = $db->prepare($sql);
                $stmt->execute([
                    ':historia_id' => $historia_id,
                    ':paciente_id' => $cita['paciente_id'],
                    ':medico_id' => $medico_id,
                    ':codigo_qr' => $codigo_qr,
                    ':observaciones' => trim($_POST['observaciones_formula'] ?? '')
                ]);

                $formula_id = $db->lastInsertId();

                // Insertar medicamentos
                foreach ($_POST['medicamentos'] as $idx => $medicamento_id) {
                    if (empty($medicamento_id)) continue;

                    $sql = "INSERT INTO formula_medicamentos
                            (formula_id, medicamento_id, dosis, frecuencia, duracion, instrucciones)
                            VALUES (:formula_id, :medicamento_id, :dosis, :frecuencia, :duracion, :instrucciones)";

                    $stmt = $db->prepare($sql);
                    $stmt->execute([
                        ':formula_id' => $formula_id,
                        ':medicamento_id' => $medicamento_id,
                        ':dosis' => $_POST['dosis'][$idx] ?? '',
                        ':frecuencia' => $_POST['frecuencia'][$idx] ?? '',
                        ':duracion' => $_POST['duracion'][$idx] ?? '',
                        ':instrucciones' => $_POST['instrucciones'][$idx] ?? ''
                    ]);
                }
            }

            // Marcar cita como completada
            $this->citaModel->completar($cita_id);

            $db->commit();

            $_SESSION['mensaje_exito'] = 'Cita atendida exitosamente';
            redirigir('agenda-medico');

        } catch (Exception $e) {
            $db->rollBack();
            error_log("Error al registrar atención médica: " . $e->getMessage());
            $_SESSION['mensaje_error'] = 'Error al registrar atención. Por favor intente más tarde.';
            redirigir('atender-cita&id=' . $cita_id);
        }
    }

    /**
     * Listar pacientes
     */
    public function pacientes() {
        if (!tiene_rol('medico')) {
            redirigir('dashboard');
            return;
        }

        $medico = $this->medicoModel->obtenerPorUsuarioId($_SESSION['usuario_id']);
        $pacientes = $this->medicoModel->obtenerPacientes($medico['id']);

        $titulo = 'Mis Pacientes';
        require_once __DIR__ . '/../views/medico/pacientes.php';
    }

    /**
     * Ver historia de un paciente
     */
    public function verHistoria() {
        if (!tiene_rol('medico') && !tiene_rol('administrador')) {
            redirigir('dashboard');
            return;
        }

        $paciente_id = $_GET['id'] ?? 0;

        require_once __DIR__ . '/../models/Paciente.php';
        $pacienteModel = new Paciente();

        $paciente = $pacienteModel->obtenerPorId($paciente_id);
        if (!$paciente || (tiene_rol('medico') && !$pacienteModel->tieneCitaConMedico($paciente['id'], $_SESSION['usuario_id']))) {
            $_SESSION['mensaje_error'] = 'Paciente no encontrado';
            redirigir('pacientes');
            return;
        }

        $historias = $pacienteModel->obtenerHistoriaClinica($paciente['id'], 50);

        $titulo = 'Historia Clínica - ' . $paciente['nombre'] . ' ' . $paciente['apellido'];
        require_once __DIR__ . '/../views/medico/historia-paciente.php';
    }
}
