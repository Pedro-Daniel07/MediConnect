<?php
/**
 * Modelo Usuario
 */

require_once __DIR__ . '/../../config/database.php';

class Usuario {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Crear nuevo usuario
     */
    public function crear($datos) {
        $sql = "INSERT INTO usuarios (email, password, rol_id, nombre, apellido, documento, telefono, fecha_nacimiento, direccion, genero)
                VALUES (:email, :password, :rol_id, :nombre, :apellido, :documento, :telefono, :fecha_nacimiento, :direccion, :genero)";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':email' => $datos['email'],
            ':password' => password_hash($datos['password'], PASSWORD_DEFAULT),
            ':rol_id' => $datos['rol_id'],
            ':nombre' => $datos['nombre'],
            ':apellido' => $datos['apellido'],
            ':documento' => $datos['documento'],
            ':telefono' => $datos['telefono'] ?? null,
            // '' no es NULL y MySQL rechaza la cadena vacía en una columna DATE
            ':fecha_nacimiento' => !empty($datos['fecha_nacimiento']) ? $datos['fecha_nacimiento'] : null,
            ':direccion' => $datos['direccion'] ?? null,
            ':genero' => $datos['genero'] ?? 'Otro'
        ]);
    }

    /**
     * Buscar usuario por email
     */
    public function buscarPorEmail($email) {
        $sql = "SELECT u.*, r.nombre as rol_nombre
                FROM usuarios u
                INNER JOIN roles r ON u.rol_id = r.id
                WHERE u.email = :email AND u.activo = 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetch();
    }

    /**
     * Buscar usuario por ID
     */
    public function buscarPorId($id) {
        $sql = "SELECT u.*, r.nombre as rol_nombre
                FROM usuarios u
                INNER JOIN roles r ON u.rol_id = r.id
                WHERE u.id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Verificar si email ya existe
     */
    public function emailExiste($email, $excluir_id = null) {
        $sql = "SELECT COUNT(*) FROM usuarios WHERE email = :email";
        if ($excluir_id) {
            $sql .= " AND id != :id";
        }

        $stmt = $this->db->prepare($sql);
        $params = [':email' => $email];
        if ($excluir_id) {
            $params[':id'] = $excluir_id;
        }
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Verificar si documento ya existe
     */
    public function documentoExiste($documento, $excluir_id = null) {
        $sql = "SELECT COUNT(*) FROM usuarios WHERE documento = :documento";
        if ($excluir_id) {
            $sql .= " AND id != :id";
        }

        $stmt = $this->db->prepare($sql);
        $params = [':documento' => $documento];
        if ($excluir_id) {
            $params[':id'] = $excluir_id;
        }
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Actualizar último acceso
     */
    public function actualizarUltimoAcceso($id) {
        $sql = "UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Actualizar perfil
     */
    public function actualizar($id, $datos) {
        $sql = "UPDATE usuarios SET
                nombre = :nombre,
                apellido = :apellido,
                telefono = :telefono,
                direccion = :direccion,
                updated_at = NOW()
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':nombre' => $datos['nombre'],
            ':apellido' => $datos['apellido'],
            ':telefono' => $datos['telefono'],
            ':direccion' => $datos['direccion']
        ]);
    }

    /**
     * Cambiar contraseña
     */
    public function cambiarPassword($id, $nueva_password) {
        $sql = "UPDATE usuarios SET password = :password WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':password' => password_hash($nueva_password, PASSWORD_DEFAULT)
        ]);
    }

    /**
     * Listar usuarios con paginación
     */
    public function listar($filtros = [], $page = 1, $per_page = 10) {
        $offset = ($page - 1) * $per_page;
        $where = ["u.activo = 1"];
        $params = [];

        if (!empty($filtros['rol_id'])) {
            $where[] = "u.rol_id = :rol_id";
            $params[':rol_id'] = $filtros['rol_id'];
        }

        if (!empty($filtros['busqueda'])) {
            $where[] = "(u.nombre LIKE :busqueda OR u.apellido LIKE :busqueda OR u.email LIKE :busqueda OR u.documento LIKE :busqueda)";
            $params[':busqueda'] = '%' . $filtros['busqueda'] . '%';
        }

        $where_sql = implode(' AND ', $where);

        $sql = "SELECT u.*, r.nombre as rol_nombre
                FROM usuarios u
                INNER JOIN roles r ON u.rol_id = r.id
                WHERE $where_sql
                ORDER BY u.created_at DESC
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

    /**
     * Contar usuarios
     */
    public function contar($filtros = []) {
        $where = ["activo = 1"];
        $params = [];

        if (!empty($filtros['rol_id'])) {
            $where[] = "rol_id = :rol_id";
            $params[':rol_id'] = $filtros['rol_id'];
        }

        if (!empty($filtros['busqueda'])) {
            $where[] = "(nombre LIKE :busqueda OR apellido LIKE :busqueda OR email LIKE :busqueda OR documento LIKE :busqueda)";
            $params[':busqueda'] = '%' . $filtros['busqueda'] . '%';
        }

        $where_sql = implode(' AND ', $where);

        $sql = "SELECT COUNT(*) FROM usuarios WHERE $where_sql";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    /**
     * Obtener ID de rol por nombre
     */
    public function obtenerRolId($nombre_rol) {
        $sql = "SELECT id FROM roles WHERE nombre = :nombre";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':nombre' => $nombre_rol]);
        return $stmt->fetchColumn();
    }
}
