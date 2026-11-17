<?php
/**
 * Modelo Paciente
 */

require_once __DIR__ . '/../../config/database.php';

class Paciente {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Crear registro de paciente
     */
    public function crear($usuario_id, $datos = []) {
        $sql = "INSERT INTO pacientes (usuario_id, grupo_sanguineo, alergias, enfermedades_cronicas, contacto_emergencia_nombre, contacto_emergencia_telefono)
                VALUES (:usuario_id, :grupo_sanguineo, :alergias, :enfermedades_cronicas, :contacto_emergencia_nombre, :contacto_emergencia_telefono)";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':usuario_id' => $usuario_id,
            ':grupo_sanguineo' => $datos['grupo_sanguineo'] ?? null,
            ':alergias' => $datos['alergias'] ?? null,
            ':enfermedades_cronicas' => $datos['enfermedades_cronicas'] ?? null,
            ':contacto_emergencia_nombre' => $datos['contacto_emergencia_nombre'] ?? null,
            ':contacto_emergencia_telefono' => $datos['contacto_emergencia_telefono'] ?? null
        ]);
    }

    /**
     * Obtener datos completos del paciente
     */
    public function obtenerPorUsuarioId($usuario_id) {
        $sql = "SELECT p.*, u.nombre, u.apellido, u.email, u.documento, u.telefono, u.fecha_nacimiento, u.genero
                FROM pacientes p
                INNER JOIN usuarios u ON p.usuario_id = u.id
                WHERE p.usuario_id = :usuario_id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':usuario_id' => $usuario_id]);
        return $stmt->fetch();
    }

    /**
     * Obtener paciente por ID
     */
    public function obtenerPorId($id) {
        $sql = "SELECT p.*, u.nombre, u.apellido, u.email, u.documento, u.telefono, u.fecha_nacimiento, u.genero
                FROM pacientes p
                INNER JOIN usuarios u ON p.usuario_id = u.id
                WHERE p.id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Actualizar información médica
     */
    public function actualizar($usuario_id, $datos) {
        $sql = "UPDATE pacientes SET
                grupo_sanguineo = :grupo_sanguineo,
                alergias = :alergias,
                enfermedades_cronicas = :enfermedades_cronicas,
                contacto_emergencia_nombre = :contacto_emergencia_nombre,
                contacto_emergencia_telefono = :contacto_emergencia_telefono
                WHERE usuario_id = :usuario_id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':usuario_id' => $usuario_id,
            ':grupo_sanguineo' => $datos['grupo_sanguineo'],
            ':alergias' => $datos['alergias'],
            ':enfermedades_cronicas' => $datos['enfermedades_cronicas'],
            ':contacto_emergencia_nombre' => $datos['contacto_emergencia_nombre'],
            ':contacto_emergencia_telefono' => $datos['contacto_emergencia_telefono']
        ]);
    }

    /**
     * Verificar si el médico ha tenido alguna cita con el paciente
     */
    public function tieneCitaConMedico($paciente_id, $medico_usuario_id) {
        $sql = "SELECT 1 FROM citas c
                INNER JOIN medicos m ON c.medico_id = m.id
                WHERE c.paciente_id = :paciente_id AND m.usuario_id = :medico_usuario_id
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':paciente_id' => $paciente_id,
            ':medico_usuario_id' => $medico_usuario_id
        ]);
        return (bool) $stmt->fetchColumn();
    }

    /**
     * Obtener historia clínica del paciente
     */
    public function obtenerHistoriaClinica($paciente_id, $limit = 10) {
        $sql = "SELECT hc.*, c.fecha as fecha_cita, c.hora_inicio,
                CONCAT(u.nombre, ' ', u.apellido) as medico_nombre,
                e.nombre as especialidad
                FROM historias_clinicas hc
                INNER JOIN citas c ON hc.cita_id = c.id
                INNER JOIN medicos m ON hc.medico_id = m.id
                INNER JOIN usuarios u ON m.usuario_id = u.id
                INNER JOIN especialidades e ON m.especialidad_id = e.id
                WHERE hc.paciente_id = :paciente_id
                ORDER BY hc.created_at DESC
                LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':paciente_id', $paciente_id, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Obtener línea de tiempo de salud (peso, altura, presión)
     */
    public function obtenerLineaTiempo($paciente_id, $limit = 12) {
        $sql = "SELECT created_at as fecha, peso, altura, presion_arterial, temperatura, frecuencia_cardiaca
                FROM historias_clinicas
                WHERE paciente_id = :paciente_id
                AND (peso IS NOT NULL OR presion_arterial IS NOT NULL)
                ORDER BY created_at DESC
                LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':paciente_id', $paciente_id, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Listar todos los pacientes
     */
    public function listar($filtros = [], $page = 1, $per_page = 10) {
        $offset = ($page - 1) * $per_page;
        $where = ["u.activo = 1"];
        $params = [];

        if (!empty($filtros['busqueda'])) {
            $where[] = "(u.nombre LIKE :busqueda OR u.apellido LIKE :busqueda OR u.documento LIKE :busqueda)";
            $params[':busqueda'] = '%' . $filtros['busqueda'] . '%';
        }

        $where_sql = implode(' AND ', $where);

        $sql = "SELECT p.id, p.usuario_id, u.nombre, u.apellido, u.documento, u.telefono, u.fecha_nacimiento, u.email,
                p.grupo_sanguineo, p.alergias
                FROM pacientes p
                INNER JOIN usuarios u ON p.usuario_id = u.id
                WHERE $where_sql
                ORDER BY u.apellido, u.nombre
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
