<?php
/**
 * Controlador del Dashboard
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../models/Cita.php';
require_once __DIR__ . '/../models/Paciente.php';
require_once __DIR__ . '/../models/Medico.php';

class DashboardController {
    private $citaModel;
    private $pacienteModel;
    private $medicoModel;

    public function __construct() {
        $this->citaModel = new Cita();
        $this->pacienteModel = new Paciente();
        $this->medicoModel = new Medico();
    }

    /**
     * Dashboard según rol del usuario
     */
    public function index() {
        $rol = $_SESSION['rol'];

        switch ($rol) {
            case 'paciente':
                $this->dashboardPaciente();
                break;
            case 'medico':
                $this->dashboardMedico();
                break;
            case 'administrador':
                $this->dashboardAdmin();
                break;
            default:
                echo 'Rol no reconocido';
        }
    }

    /**
     * Dashboard para pacientes
     */
    private function dashboardPaciente() {
        $paciente = $this->pacienteModel->obtenerPorUsuarioId($_SESSION['usuario_id']);

        // Obtener próximas citas
        $citas = $this->citaModel->obtenerPorPaciente($paciente['id']);
        $proximas_citas = array_filter($citas, function($cita) {
            return $cita['estado'] !== 'cancelada' && $cita['fecha'] >= date('Y-m-d');
        });
        $proximas_citas = array_slice($proximas_citas, 0, 5);

        // Obtener historial reciente
        $historial = $this->pacienteModel->obtenerHistoriaClinica($paciente['id'], 5);

        // Estadísticas
        $stats = [
            'citas_pendientes' => count(array_filter($citas, fn($c) => $c['estado'] === 'pendiente' && $c['fecha'] >= date('Y-m-d'))),
            'citas_completadas' => count(array_filter($citas, fn($c) => $c['estado'] === 'completada')),
            'total_consultas' => count($historial)
        ];

        $titulo = 'Panel de Paciente';
        require_once __DIR__ . '/../views/dashboard/paciente.php';
    }

    /**
     * Dashboard para médicos
     */
    private function dashboardMedico() {
        $medico = $this->medicoModel->obtenerPorUsuarioId($_SESSION['usuario_id']);

        // Citas de hoy
        $citas_hoy = $this->citaModel->obtenerPorMedico($medico['id'], date('Y-m-d'), date('Y-m-d'));

        // Próximas citas (próximos 7 días)
        $fecha_inicio = date('Y-m-d');
        $fecha_fin = date('Y-m-d', strtotime('+7 days'));
        $proximas_citas = $this->citaModel->obtenerPorMedico($medico['id'], $fecha_inicio, $fecha_fin);

        // Estadísticas del mes
        $mes_inicio = date('Y-m-01');
        $mes_fin = date('Y-m-t');
        $citas_mes = $this->citaModel->obtenerPorMedico($medico['id'], $mes_inicio, $mes_fin);

        $stats = [
            'citas_hoy' => count($citas_hoy),
            'pendientes_hoy' => count(array_filter($citas_hoy, fn($c) => $c['estado'] === 'pendiente')),
            'completadas_mes' => count(array_filter($citas_mes, fn($c) => $c['estado'] === 'completada')),
            'proximas_semana' => count($proximas_citas)
        ];

        $titulo = 'Panel del Médico';
        require_once __DIR__ . '/../views/dashboard/medico.php';
    }

    /**
     * Dashboard para administradores
     */
    private function dashboardAdmin() {
        require_once __DIR__ . '/../models/Usuario.php';
        $usuarioModel = new Usuario();

        // Estadísticas generales
        $stats = [
            'total_pacientes' => $usuarioModel->contar(['rol_id' => 3]),
            'total_medicos' => $usuarioModel->contar(['rol_id' => 2]),
            'total_usuarios' => $usuarioModel->contar([])
        ];

        // Citas del día
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) FROM citas WHERE fecha = CURDATE()");
        $stmt->execute();
        $stats['citas_hoy'] = $stmt->fetchColumn();

        // Citas del mes por estado
        $filtros = [
            'fecha_inicio' => date('Y-m-01'),
            'fecha_fin' => date('Y-m-t')
        ];
        $citas_mes = $this->citaModel->contarPorEstado($filtros);

        // Especialidades más solicitadas (últimos 30 días)
        $sql = "SELECT e.nombre, COUNT(*) as total
                FROM citas c
                INNER JOIN medicos m ON c.medico_id = m.id
                INNER JOIN especialidades e ON m.especialidad_id = e.id
                WHERE c.fecha >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                GROUP BY e.id
                ORDER BY total DESC
                LIMIT 5";
        $stmt = $db->query($sql);
        $especialidades_populares = $stmt->fetchAll();

        $titulo = 'Panel de Administración';
        require_once __DIR__ . '/../views/dashboard/admin.php';
    }
}
