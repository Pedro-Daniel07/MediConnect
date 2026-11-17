<?php
/**
 * Controlador de Paciente
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../models/Paciente.php';

class PacienteController {
    private $pacienteModel;

    public function __construct() {
        $this->pacienteModel = new Paciente();
    }

    /**
     * Obtener un paciente solo si el usuario actual tiene permiso para verlo
     */
    private function obtenerPacienteAutorizado($paciente_id = null) {
        if ($paciente_id && (tiene_rol('medico') || tiene_rol('administrador'))) {
            $paciente = $this->pacienteModel->obtenerPorId($paciente_id);
            if (!$paciente || (tiene_rol('medico') && !$this->pacienteModel->tieneCitaConMedico($paciente['id'], $_SESSION['usuario_id']))) {
                return null;
            }
            return $paciente;
        }

        return $this->pacienteModel->obtenerPorUsuarioId($_SESSION['usuario_id']);
    }

    /**
     * Ver historial clínico
     */
    public function historial() {
        if (!tiene_rol('paciente')) {
            redirigir('dashboard');
            return;
        }

        $paciente = $this->pacienteModel->obtenerPorUsuarioId($_SESSION['usuario_id']);
        $historias = $this->pacienteModel->obtenerHistoriaClinica($paciente['id'], 50);

        $titulo = 'Mi Historial Clínico';
        require_once __DIR__ . '/../views/paciente/historial.php';
    }

    /**
     * Ver fórmulas médicas
     */
    public function formulas() {
        if (!tiene_rol('paciente')) {
            redirigir('dashboard');
            return;
        }

        $paciente = $this->pacienteModel->obtenerPorUsuarioId($_SESSION['usuario_id']);

        $db = Database::getInstance()->getConnection();
        $sql = "SELECT fm.*, hc.diagnostico, hc.created_at as fecha_consulta,
                CONCAT(u.nombre, ' ', u.apellido) as medico_nombre,
                e.nombre as especialidad
                FROM formulas_medicas fm
                INNER JOIN historias_clinicas hc ON fm.historia_clinica_id = hc.id
                INNER JOIN medicos m ON fm.medico_id = m.id
                INNER JOIN usuarios u ON m.usuario_id = u.id
                INNER JOIN especialidades e ON m.especialidad_id = e.id
                WHERE fm.paciente_id = :paciente_id
                ORDER BY fm.created_at DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute([':paciente_id' => $paciente['id']]);
        $formulas = $stmt->fetchAll();

        // Obtener medicamentos de cada fórmula
        foreach ($formulas as &$formula) {
            $sql = "SELECT fm.*, med.nombre as medicamento_nombre
                    FROM formula_medicamentos fm
                    INNER JOIN medicamentos med ON fm.medicamento_id = med.id
                    WHERE fm.formula_id = :formula_id";

            $stmt = $db->prepare($sql);
            $stmt->execute([':formula_id' => $formula['id']]);
            $formula['medicamentos'] = $stmt->fetchAll();
        }

        $titulo = 'Mis Fórmulas Médicas';
        require_once __DIR__ . '/../views/paciente/formulas.php';
    }

    /**
     * Ver resultados de laboratorio
     */
    public function resultados() {
        if (!tiene_rol('paciente')) {
            redirigir('dashboard');
            return;
        }

        $paciente = $this->pacienteModel->obtenerPorUsuarioId($_SESSION['usuario_id']);

        $db = Database::getInstance()->getConnection();
        $sql = "SELECT rl.*,
                CONCAT(u.nombre, ' ', u.apellido) as medico_nombre
                FROM resultados_laboratorio rl
                INNER JOIN medicos m ON rl.medico_id = m.id
                INNER JOIN usuarios u ON m.usuario_id = u.id
                WHERE rl.paciente_id = :paciente_id
                ORDER BY rl.fecha_examen DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute([':paciente_id' => $paciente['id']]);
        $resultados = $stmt->fetchAll();

        $titulo = 'Resultados de Laboratorio';
        require_once __DIR__ . '/../views/paciente/resultados.php';
    }

    /**
     * Línea de tiempo de salud
     */
    public function lineaTiempo() {
        if (!tiene_rol('paciente')) {
            redirigir('dashboard');
            return;
        }

        $paciente = $this->pacienteModel->obtenerPorUsuarioId($_SESSION['usuario_id']);
        $datos = $this->pacienteModel->obtenerLineaTiempo($paciente['id'], 24);

        // Calcular IMC para cada registro
        foreach ($datos as &$dato) {
            if ($dato['peso'] && $dato['altura']) {
                $dato['imc'] = calcular_imc($dato['peso'], $dato['altura']);
            }
        }

        $titulo = 'Línea de Tiempo de Salud';
        require_once __DIR__ . '/../views/paciente/linea-tiempo.php';
    }

    /**
     * Carné Digital tipo Wallet con QR dinámico e impresión
     */
    public function carneDigital() {
        if (!esta_autenticado()) {
            redirigir('login');
            return;
        }

        $paciente = $this->obtenerPacienteAutorizado($_GET['id'] ?? null);

        if (!$paciente) {
            redirigir('dashboard');
            return;
        }

        registrar_auditoria('CARNE_DIGITAL_CONSULTADO', 'Carné Digital', 'Consulta de carné digital para afiliado ' . ($paciente['codigo_afiliado'] ?? 'MC-'.$paciente['id']));

        $titulo = 'Carné Digital de Salud';
        require_once __DIR__ . '/../views/paciente/carne-digital.php';
    }

    /**
     * Carné de Vacunación con Esquema y Alertas
     */
    public function vacunacion() {
        if (!esta_autenticado()) {
            redirigir('login');
            return;
        }

        $paciente = $this->obtenerPacienteAutorizado($_GET['id'] ?? null);

        if (!$paciente) {
            redirigir('dashboard');
            return;
        }

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM vacunaciones WHERE paciente_id = ? ORDER BY fecha_aplicacion DESC");
        $stmt->execute([$paciente['id']]);
        $vacunas = $stmt->fetchAll();

        // Contadores
        $total_aplicadas = 0;
        $total_pendientes = 0;
        $total_vencidas = 0;

        foreach ($vacunas as $v) {
            if ($v['estado'] === 'aplicada') $total_aplicadas++;
            elseif ($v['estado'] === 'pendiente') $total_pendientes++;
            elseif ($v['estado'] === 'vencida') $total_vencidas++;
        }

        $titulo = 'Carné de Vacunación Digital';
        require_once __DIR__ . '/../views/paciente/vacunacion.php';
    }

    /**
     * Signos Vitales y Puntaje de Salud (0 - 100)
     */
    public function signosVitales() {
        if (!esta_autenticado()) {
            redirigir('login');
            return;
        }

        $db = Database::getInstance()->getConnection();

        $paciente = $this->obtenerPacienteAutorizado($_GET['id'] ?? null);

        if (!$paciente) {
            redirigir('dashboard');
            return;
        }

        $mensaje_exito = null;
        $error = null;

        // Registrar nuevos signos vitales vía POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'guardar_signos') {
            if (!verificar_csrf($_POST['csrf_token'] ?? '')) {
                $error = 'Error de validación de seguridad (CSRF).';
            } else {
                $peso = floatval($_POST['peso'] ?? 0);
                $altura = floatval($_POST['altura'] ?? 0);
                $sistolica = intval($_POST['presion_sistolica'] ?? 120);
                $diastolica = intval($_POST['presion_diastolica'] ?? 80);
                $fc = intval($_POST['frecuencia_cardiaca'] ?? 72);
                $fr = intval($_POST['frecuencia_respiratoria'] ?? 18);
                $temp = floatval($_POST['temperatura'] ?? 36.6);
                $spo2 = intval($_POST['saturacion_oxigeno'] ?? 98);
                $glucosa = intval($_POST['glucosa'] ?? 95);
                $notas = trim($_POST['notas'] ?? '');

                if ($peso <= 0 || $altura <= 0) {
                    $error = 'El peso y la estatura deben ser mayores a cero.';
                } else {
                    $imc = calcular_imc($peso, $altura);
                    $clasif_imc = clasificar_imc($imc);
                    $score = calcular_puntaje_salud($peso, $altura, $sistolica, $diastolica, $fc, $spo2, $glucosa);

                    $medico_id = null;
                    if (tiene_rol('medico')) {
                        $stmtMed = $db->prepare("SELECT id FROM medicos WHERE usuario_id = ?");
                        $stmtMed->execute([$_SESSION['usuario_id']]);
                        $medico_id = $stmtMed->fetchColumn() ?: null;
                    }

                    $insertStmt = $db->prepare("INSERT INTO signos_vitales (paciente_id, medico_id, peso, altura, presion_sistolica, presion_diastolica, frecuencia_cardiaca, frecuencia_respiratoria, temperatura, saturacion_oxigeno, glucosa, puntaje_salud, imc, clasificacion_imc, notas) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $insertStmt->execute([$paciente['id'], $medico_id, $peso, $altura, $sistolica, $diastolica, $fc, $fr, $temp, $spo2, $glucosa, $score, $imc, $clasif_imc, $notas]);

                    registrar_auditoria('SIGNOS_REGISTRADOS', 'Signos Vitales', "Registro biométrico para {$paciente['nombre']} {$paciente['apellido']} (Score Salud: {$score}/100)");

                    $mensaje_exito = '¡Signos vitales registrados y Puntaje de Salud actualizado con éxito!';
                }
            }
        }

        // Obtener historial de signos vitales
        $stmtSignos = $db->prepare("SELECT sv.*, CONCAT(u.nombre, ' ', u.apellido) as medico_nombre FROM signos_vitales sv LEFT JOIN medicos m ON sv.medico_id = m.id LEFT JOIN usuarios u ON m.usuario_id = u.id WHERE sv.paciente_id = ? ORDER BY sv.fecha_registro DESC LIMIT 20");
        $stmtSignos->execute([$paciente['id']]);
        $historial_signos = $stmtSignos->fetchAll();

        // Signos más recientes
        $ultimo_signo = $historial_signos[0] ?? null;

        $score_actual = $ultimo_signo ? $ultimo_signo['puntaje_salud'] : 92;

        $titulo = 'Signos Vitales y Puntaje de Salud';
        require_once __DIR__ . '/../views/paciente/signos-vitales.php';
    }
}
