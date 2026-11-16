<?php
/**
 * Modelo Cita
 */

require_once __DIR__ . '/../../config/database.php';

class Cita {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Crear una nueva cita
     */
    public function crear($datos) {
        $sql = "INSERT INTO citas (paciente_id, medico_id, fecha, hora_inicio, hora_fin, motivo_consulta, estado)
                VALUES (:paciente_id, :medico_id, :fecha, :hora_inicio, :hora_fin, :motivo_consulta, 'pendiente')";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':paciente_id' => $datos['paciente_id'],
            ':medico_id' => $datos['medico_id'],
            ':fecha' => $datos['fecha'],
            ':hora_inicio' => $datos['hora_inicio'],
            ':hora_fin' => $datos['hora_fin'],
            ':motivo_consulta' => $datos['motivo_consulta']
        ]);
    }

    /**
     * Verificar disponibilidad de horario
     */
    public function verificarDisponibilidad($medico_id, $fecha, $hora_inicio, $hora_fin, $excluir_cita_id = null) {
        $sql = "SELECT COUNT(*) FROM citas
                WHERE medico_id = :medico_id
                AND fecha = :fecha
                AND estado NOT IN ('cancelada')
                AND (
                    (hora_inicio < :hora_fin AND hora_fin > :hora_inicio)
                )";

        if ($excluir_cita_id) {
            $sql .= " AND id != :excluir_id";
        }

        $stmt = $this->db->prepare($sql);
        $params = [
            ':medico_id' => $medico_id,
            ':fecha' => $fecha,
            ':hora_inicio' => $hora_inicio,
            ':hora_fin' => $hora_fin
        ];

        if ($excluir_cita_id) {
            $params[':excluir_id'] = $excluir_cita_id;
        }

        $stmt->execute($params);
        return $stmt->fetchColumn() == 0;
    }

    /**
     * Obtener citas de un paciente
     */
    public function obtenerPorPaciente($paciente_id, $filtro_estado = null) {
        $sql = "SELECT c.*,
                CONCAT(u.nombre, ' ', u.apellido) as medico_nombre,
                e.nombre as especialidad
                FROM citas c
                INNER JOIN medicos m ON c.medico_id = m.id
                INNER JOIN usuarios u ON m.usuario_id = u.id
                INNER JOIN especialidades e ON m.especialidad_id = e.id
                WHERE c.paciente_id = :paciente_id";

        if ($filtro_estado) {
            $sql .= " AND c.estado = :estado";
        }

        $sql .= " ORDER BY c.fecha DESC, c.hora_inicio DESC";

        $stmt = $this->db->prepare($sql);
        $params = [':paciente_id' => $paciente_id];

        if ($filtro_estado) {
            $params[':estado'] = $filtro_estado;
        }

        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Obtener agenda de un médico
     */
    public function obtenerPorMedico($medico_id, $fecha_inicio = null, $fecha_fin = null) {
        $sql = "SELECT c.*,
                CONCAT(u.nombre, ' ', u.apellido) as paciente_nombre,
                u.telefono as paciente_telefono,
                p.grupo_sanguineo, p.alergias
                FROM citas c
                INNER JOIN pacientes p ON c.paciente_id = p.id
                INNER JOIN usuarios u ON p.usuario_id = u.id
                WHERE c.medico_id = :medico_id";

        if ($fecha_inicio) {
            $sql .= " AND c.fecha >= :fecha_inicio";
        }
        if ($fecha_fin) {
            $sql .= " AND c.fecha <= :fecha_fin";
        }

        $sql .= " ORDER BY c.fecha, c.hora_inicio";

        $stmt = $this->db->prepare($sql);
        $params = [':medico_id' => $medico_id];

        if ($fecha_inicio) {
            $params[':fecha_inicio'] = $fecha_inicio;
        }
        if ($fecha_fin) {
            $params[':fecha_fin'] = $fecha_fin;
        }

        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Obtener cita por ID
     */
    public function obtenerPorId($id) {
        $sql = "SELECT c.*,
                CONCAT(um.nombre, ' ', um.apellido) as medico_nombre,
                CONCAT(up.nombre, ' ', up.apellido) as paciente_nombre,
                e.nombre as especialidad,
                p.grupo_sanguineo, p.alergias, p.enfermedades_cronicas
                FROM citas c
                INNER JOIN medicos m ON c.medico_id = m.id
                INNER JOIN usuarios um ON m.usuario_id = um.id
                INNER JOIN especialidades e ON m.especialidad_id = e.id
                INNER JOIN pacientes p ON c.paciente_id = p.id
                INNER JOIN usuarios up ON p.usuario_id = up.id
                WHERE c.id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Actualizar estado de cita
     */
    public function actualizarEstado($id, $estado, $motivo = null) {
        $sql = "UPDATE citas SET estado = :estado";
        if ($motivo && $estado === 'cancelada') {
            $sql .= ", motivo_cancelacion = :motivo";
        }
        $sql .= " WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        $params = [':id' => $id, ':estado' => $estado];
        if ($motivo && $estado === 'cancelada') {
            $params[':motivo'] = $motivo;
        }

        return $stmt->execute($params);
    }

    /**
     * Cancelar cita
     */
    public function cancelar($id, $motivo) {
        return $this->actualizarEstado($id, 'cancelada', $motivo);
    }

    /**
     * Confirmar cita
     */
    public function confirmar($id) {
        return $this->actualizarEstado($id, 'confirmada');
    }

    /**
     * Completar cita
     */
    public function completar($id) {
        return $this->actualizarEstado($id, 'completada');
    }

    /**
     * Contar citas por estado
     */
    public function contarPorEstado($filtros = []) {
        $where = ["1=1"];
        $params = [];

        if (!empty($filtros['medico_id'])) {
            $where[] = "medico_id = :medico_id";
            $params[':medico_id'] = $filtros['medico_id'];
        }

        if (!empty($filtros['paciente_id'])) {
            $where[] = "paciente_id = :paciente_id";
            $params[':paciente_id'] = $filtros['paciente_id'];
        }

        if (!empty($filtros['fecha_inicio'])) {
            $where[] = "fecha >= :fecha_inicio";
            $params[':fecha_inicio'] = $filtros['fecha_inicio'];
        }

        if (!empty($filtros['fecha_fin'])) {
            $where[] = "fecha <= :fecha_fin";
            $params[':fecha_fin'] = $filtros['fecha_fin'];
        }

        $where_sql = implode(' AND ', $where);

        $sql = "SELECT estado, COUNT(*) as total
                FROM citas
                WHERE $where_sql
                GROUP BY estado";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        $resultado = [];
        foreach ($stmt->fetchAll() as $row) {
            $resultado[$row['estado']] = $row['total'];
        }

        return $resultado;
    }

    /**
     * Obtener horarios disponibles de un médico
     */
    public function obtenerHorariosDisponibles($medico_id, $fecha) {
        // Obtener día de la semana (1=Lunes, 7=Domingo)
        $dia_semana = date('N', strtotime($fecha));

        // Obtener horarios configurados para ese día
        $sql = "SELECT hora_inicio, hora_fin, duracion_cita
                FROM horarios_medicos
                WHERE medico_id = :medico_id
                AND dia_semana = :dia_semana
                AND activo = 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':medico_id' => $medico_id,
            ':dia_semana' => $dia_semana
        ]);

        $horarios_configurados = $stmt->fetchAll();

        if (empty($horarios_configurados)) {
            return [];
        }

        // Obtener citas ya agendadas para ese día
        $sql = "SELECT hora_inicio, hora_fin
                FROM citas
                WHERE medico_id = :medico_id
                AND fecha = :fecha
                AND estado NOT IN ('cancelada')";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':medico_id' => $medico_id,
            ':fecha' => $fecha
        ]);

        $citas_ocupadas = $stmt->fetchAll();

        // Generar slots disponibles
        $disponibles = [];

        foreach ($horarios_configurados as $horario) {
            $inicio = strtotime($horario['hora_inicio']);
            $fin = strtotime($horario['hora_fin']);
            $duracion = $horario['duracion_cita'] * 60; // Convertir a segundos

            for ($slot = $inicio; $slot < $fin; $slot += $duracion) {
                $hora_inicio = date('H:i:s', $slot);
                $hora_fin = date('H:i:s', $slot + $duracion);

                // Verificar si el slot está ocupado
                $ocupado = false;
                foreach ($citas_ocupadas as $ocupada) {
                    if ($hora_inicio < $ocupada['hora_fin'] && $hora_fin > $ocupada['hora_inicio']) {
                        $ocupado = true;
                        break;
                    }
                }

                if (!$ocupado) {
                    $disponibles[] = [
                        'hora_inicio' => $hora_inicio,
                        'hora_fin' => $hora_fin,
                        'hora_display' => date('h:i A', $slot)
                    ];
                }
            }
        }

        return $disponibles;
    }
}
