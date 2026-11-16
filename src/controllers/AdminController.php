<?php
/**
 * Controlador de Administración
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Medico.php';

class AdminController {
    private $usuarioModel;
    private $medicoModel;

    public function __construct() {
        $this->usuarioModel = new Usuario();
        $this->medicoModel = new Medico();
    }

    /**
     * Listar usuarios
     */
    public function usuarios() {
        if (!tiene_rol('administrador')) {
            redirigir('dashboard');
            return;
        }

        $filtros = [];
        if (!empty($_GET['busqueda'])) {
            $filtros['busqueda'] = $_GET['busqueda'];
        }
        if (!empty($_GET['rol_id'])) {
            $filtros['rol_id'] = $_GET['rol_id'];
        }

        $usuarios = $this->usuarioModel->listar($filtros);

        $titulo = 'Gestión de Usuarios';
        require_once __DIR__ . '/../views/admin/usuarios.php';
    }

    /**
     * Listar médicos
     */
    public function medicos() {
        if (!tiene_rol('administrador')) {
            redirigir('dashboard');
            return;
        }

        $medicos = $this->medicoModel->listar();
        $especialidades = $this->medicoModel->obtenerEspecialidades();

        $titulo = 'Gestión de Médicos';
        require_once __DIR__ . '/../views/admin/medicos.php';
    }

    /**
     * Gestionar especialidades
     */
    public function especialidades() {
        if (!tiene_rol('administrador')) {
            redirigir('dashboard');
            return;
        }

        $db = Database::getInstance()->getConnection();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            if (!verificar_csrf($_POST['csrf_token'] ?? '')) {
                $_SESSION['mensaje_error'] = 'Token inválido';
            } else {
                if ($_POST['action'] === 'crear') {
                    $sql = "INSERT INTO especialidades (nombre, descripcion) VALUES (:nombre, :descripcion)";
                    $stmt = $db->prepare($sql);
                    if ($stmt->execute([
                        ':nombre' => trim($_POST['nombre']),
                        ':descripcion' => trim($_POST['descripcion'] ?? '')
                    ])) {
                        $_SESSION['mensaje_exito'] = 'Especialidad creada';
                    }
                }
            }
            redirigir('especialidades');
            return;
        }

        $stmt = $db->query("SELECT * FROM especialidades ORDER BY nombre");
        $especialidades = $stmt->fetchAll();

        $titulo = 'Gestión de Especialidades';
        require_once __DIR__ . '/../views/admin/especialidades.php';
    }

    /**
     * Reportes y estadísticas
     */
    public function reportes() {
        if (!tiene_rol('administrador')) {
            redirigir('dashboard');
            return;
        }

        $db = Database::getInstance()->getConnection();

        // Estadísticas generales
        $stats = [
            'total_usuarios' => $this->usuarioModel->contar([]),
            'total_pacientes' => $this->usuarioModel->contar(['rol_id' => 3]),
            'total_medicos' => $this->usuarioModel->contar(['rol_id' => 2])
        ];

        // Citas por mes (últimos 6 meses)
        $sql = "SELECT DATE_FORMAT(fecha, '%Y-%m') as mes, COUNT(*) as total
                FROM citas
                WHERE fecha >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                GROUP BY mes
                ORDER BY mes";
        $stmt = $db->query($sql);
        $citas_por_mes = $stmt->fetchAll();

        // Especialidades más solicitadas
        $sql = "SELECT e.nombre, COUNT(*) as total
                FROM citas c
                INNER JOIN medicos m ON c.medico_id = m.id
                INNER JOIN especialidades e ON m.especialidad_id = e.id
                WHERE c.fecha >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)
                GROUP BY e.id
                ORDER BY total DESC
                LIMIT 10";
        $stmt = $db->query($sql);
        $especialidades_top = $stmt->fetchAll();

        $titulo = 'Reportes y Estadísticas';
        require_once __DIR__ . '/../views/admin/reportes.php';
    }

    /**
     * Centro de Comando y Bitácora de Auditoría en Vivo
     */
    public function auditoria() {
        if (!tiene_rol('administrador')) {
            redirigir('dashboard');
            return;
        }

        $db = Database::getInstance()->getConnection();

        $stmt = $db->query("SELECT al.*, u.nombre, u.apellido, u.email, r.nombre as rol
                            FROM auditoria_logs al
                            LEFT JOIN usuarios u ON al.usuario_id = u.id
                            LEFT JOIN roles r ON u.rol_id = r.id
                            ORDER BY al.created_at DESC
                            LIMIT 100");
        $logs = $stmt->fetchAll();

        // Contadores rápidos de auditoría
        $total_logs = count($logs);
        $total_seguridad = 0;
        $total_clinicos = 0;

        foreach ($logs as $l) {
            if (in_array($l['modulo'], ['Autenticación', 'Seguridad'])) $total_seguridad++;
            if (in_array($l['modulo'], ['Telemedicina', 'Fórmulas Médicas', 'Signos Vitales', 'Carné Digital'])) $total_clinicos++;
        }

        $titulo = 'Centro de Comando y Auditoría en Vivo';
        require_once __DIR__ . '/../views/admin/auditoria.php';
    }

    /**
     * Gestión de Lista de Espera Inteligente
     */
    public function listaEspera() {
        if (!tiene_rol('administrador') && !tiene_rol('medico')) {
            redirigir('dashboard');
            return;
        }

        $db = Database::getInstance()->getConnection();

        // Acción manual para cambiar estado o asignar
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            if (verificar_csrf($_POST['csrf_token'] ?? '')) {
                $espera_id = intval($_POST['espera_id'] ?? 0);
                if ($_POST['action'] === 'asignar_cupo' && $espera_id > 0) {
                    $stmt = $db->prepare("UPDATE lista_espera SET estado = 'asignado' WHERE id = ?");
                    $stmt->execute([$espera_id]);
                    registrar_auditoria('LISTA_ESPERA_MANUAL', 'Lista de Espera', "Cupo asignado manualmente a registro #$espera_id");
                    $_SESSION['mensaje_exito'] = 'Cupo asignado manualmente con éxito.';
                } elseif ($_POST['action'] === 'descartar' && $espera_id > 0) {
                    $stmt = $db->prepare("UPDATE lista_espera SET estado = 'descartado' WHERE id = ?");
                    $stmt->execute([$espera_id]);
                    $_SESSION['mensaje_exito'] = 'Registro descartado de la lista.';
                }
            }
            redirigir('lista-espera');
            return;
        }

        $stmt = $db->query("SELECT le.*,
                            CONCAT(up.nombre, ' ', up.apellido) as paciente_nombre, up.telefono, up.documento, p.codigo_afiliado,
                            e.nombre as especialidad_nombre,
                            CONCAT(um.nombre, ' ', um.apellido) as medico_nombre
                            FROM lista_espera le
                            INNER JOIN pacientes p ON le.paciente_id = p.id
                            INNER JOIN usuarios up ON p.usuario_id = up.id
                            INNER JOIN especialidades e ON le.especialidad_id = e.id
                            LEFT JOIN medicos m ON le.medico_id = m.id
                            LEFT JOIN usuarios um ON m.usuario_id = um.id
                            ORDER BY le.estado ASC,
                                     CASE le.prioridad WHEN 'alta' THEN 1 WHEN 'media' THEN 2 ELSE 3 END,
                                     le.fecha_solicitud ASC");
        $solicitudes = $stmt->fetchAll();

        $titulo = 'Lista de Espera Inteligente';
        require_once __DIR__ . '/../views/admin/lista-espera.php';
    }

    /**
     * Exportación de Citas a CSV con formato compatible con Excel
     */
    public function exportarCitas() {
        if (!tiene_rol('administrador')) {
            redirigir('dashboard');
            return;
        }

        registrar_auditoria('EXPORTACION_CSV', 'Centro de Comando', 'Exportación de historial de citas a CSV/Excel');

        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT c.id, c.fecha, c.hora_inicio, c.hora_fin, c.estado,
                            CONCAT(up.nombre, ' ', up.apellido) as paciente, up.documento as paciente_documento,
                            CONCAT(um.nombre, ' ', um.apellido) as medico, e.nombre as especialidad,
                            c.motivo_consulta
                            FROM citas c
                            INNER JOIN pacientes p ON c.paciente_id = p.id
                            INNER JOIN usuarios up ON p.usuario_id = up.id
                            INNER JOIN medicos m ON c.medico_id = m.id
                            INNER JOIN usuarios um ON m.usuario_id = um.id
                            INNER JOIN especialidades e ON m.especialidad_id = e.id
                            ORDER BY c.fecha DESC, c.hora_inicio DESC");
        $citas = $stmt->fetchAll();

        // Enviar cabeceras de descarga directa
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="citas_mediconnect_' . date('Y-m-d_His') . '.csv"');

        $output = fopen('php://output', 'w');
        // BOM UTF-8 para apertura correcta en Microsoft Excel
        fputs($output, "\xEF\xBB\xBF");

        // Fila de encabezados
        fputcsv($output, ['ID Cita', 'Fecha', 'Hora Inicio', 'Hora Fin', 'Estado', 'Paciente', 'Documento', 'Médico Especialista', 'Especialidad', 'Motivo Consulta'], ';');

        foreach ($citas as $c) {
            fputcsv($output, [
                $c['id'],
                $c['fecha'],
                $c['hora_inicio'],
                $c['hora_fin'],
                ucfirst($c['estado']),
                $c['paciente'],
                $c['paciente_documento'],
                $c['medico'],
                $c['especialidad'],
                $c['motivo_consulta']
            ], ';');
        }

        fclose($output);
        exit;
    }

    /**
     * Exportación de Base de Datos de Pacientes a CSV
     */
    public function exportarPacientes() {
        if (!tiene_rol('administrador')) {
            redirigir('dashboard');
            return;
        }

        registrar_auditoria('EXPORTACION_CSV', 'Centro de Comando', 'Exportación de directorio de pacientes a CSV/Excel');

        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT p.codigo_afiliado, u.documento, u.nombre, u.apellido, u.email, u.telefono,
                            u.fecha_nacimiento, u.genero, p.grupo_sanguineo, p.alergias, p.enfermedades_cronicas,
                            p.contacto_emergencia_nombre, p.contacto_emergencia_telefono, p.fecha_afiliacion
                            FROM pacientes p
                            INNER JOIN usuarios u ON p.usuario_id = u.id
                            ORDER BY p.id ASC");
        $pacientes = $stmt->fetchAll();

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="pacientes_mediconnect_' . date('Y-m-d_His') . '.csv"');

        $output = fopen('php://output', 'w');
        fputs($output, "\xEF\xBB\xBF");

        fputcsv($output, ['Código Afiliado', 'Documento', 'Nombre', 'Apellido', 'Correo Electrónico', 'Teléfono', 'Fecha Nacimiento', 'Género', 'Grupo Sanguíneo', 'Alergias', 'Enfermedades Crónicas', 'Contacto Emergencia', 'Teléfono Emergencia', 'Fecha Afiliación'], ';');

        foreach ($pacientes as $p) {
            fputcsv($output, [
                $p['codigo_afiliado'] ?? 'MC-'.str_pad($p['documento'], 6, '0', STR_PAD_LEFT),
                $p['documento'],
                $p['nombre'],
                $p['apellido'],
                $p['email'],
                $p['telefono'],
                $p['fecha_nacimiento'],
                $p['genero'],
                $p['grupo_sanguineo'],
                $p['alergias'] ?? 'Ninguna',
                $p['enfermedades_cronicas'] ?? 'Ninguna',
                $p['contacto_emergencia_nombre'],
                $p['contacto_emergencia_telefono'],
                $p['fecha_afiliacion'] ?? '2026-01-15'
            ], ';');
        }

        fclose($output);
        exit;
    }
}
