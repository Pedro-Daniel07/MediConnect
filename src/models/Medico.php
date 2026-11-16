<?php
/**
 * Modelo Medico
 */

require_once __DIR__ . '/../../config/database.php';

class Medico {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Obtener médico por usuario_id
     */
    public function obtenerPorUsuarioId($usuario_id) {
        $sql = "SELECT m.*, u.nombre, u.apellido, u.email, u.telefono,
                e.nombre as especialidad_nombre
                FROM medicos m
                INNER JOIN usuarios u ON m.usuario_id = u.id
                INNER JOIN especialidades e ON m.especialidad_id = e.id
                WHERE m.usuario_id = :usuario_id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':usuario_id' => $usuario_id]);
        return $stmt->fetch();
    }

    /**
     * Obtener médico por ID
     */
    public function obtenerPorId($id) {
        $sql = "SELECT m.*, u.nombre, u.apellido, u.email, u.telefono,
                e.nombre as especialidad_nombre
                FROM medicos m
                INNER JOIN usuarios u ON m.usuario_id = u.id
                INNER JOIN especialidades e ON m.especialidad_id = e.id
                WHERE m.id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Listar médicos
     */
    public function listar($filtros = []) {
        $where = ["u.activo = 1"];
        $params = [];

        if (!empty($filtros['especialidad_id'])) {
            $where[] = "m.especialidad_id = :especialidad_id";
            $params[':especialidad_id'] = $filtros['especialidad_id'];
        }

        if (!empty($filtros['busqueda'])) {
            $where[] = "(u.nombre LIKE :busqueda OR u.apellido LIKE :busqueda)";
            $params[':busqueda'] = '%' . $filtros['busqueda'] . '%';
        }

        $where_sql = implode(' AND ', $where);

        $sql = "SELECT m.*, u.nombre, u.apellido, u.telefono, u.email,
                e.nombre as especialidad_nombre
                FROM medicos m
                INNER JOIN usuarios u ON m.usuario_id = u.id
                INNER JOIN especialidades e ON m.especialidad_id = e.id
                WHERE $where_sql
                ORDER BY u.apellido, u.nombre";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Obtener especialidades
     */
    public function obtenerEspecialidades() {
        $sql = "SELECT * FROM especialidades WHERE activo = 1 ORDER BY nombre";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Obtener pacientes atendidos por el médico
     */
    public function obtenerPacientes($medico_id) {
        $sql = "SELECT DISTINCT p.id, p.usuario_id,
                CONCAT(u.nombre, ' ', u.apellido) as nombre_completo,
                u.documento, u.telefono, u.fecha_nacimiento,
                p.grupo_sanguineo, p.alergias,
                MAX(c.fecha) as ultima_cita
                FROM pacientes p
                INNER JOIN usuarios u ON p.usuario_id = u.id
                INNER JOIN citas c ON p.id = c.paciente_id
                WHERE c.medico_id = :medico_id
                AND c.estado = 'completada'
                GROUP BY p.id
                ORDER BY ultima_cita DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':medico_id' => $medico_id]);
        return $stmt->fetchAll();
    }
}
