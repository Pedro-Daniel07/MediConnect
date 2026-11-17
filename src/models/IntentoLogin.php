<?php
/**
 * Limita temporalmente los intentos fallidos de inicio de sesión.
 */

require_once __DIR__ . '/../../config/database.php';

class IntentoLogin {
    private $db;

    private const MAX_FALLOS = 5;
    private const VENTANA_SEGUNDOS = 300;
    private const BLOQUEO_SEGUNDOS = 30;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function tiempoEspera($email, $ip) {
        $this->db->exec(
            'DELETE FROM intentos_login
             WHERE inicio_ventana < DATE_SUB(NOW(6), INTERVAL 600 SECOND)
               AND (bloqueado_hasta IS NULL OR bloqueado_hasta <= NOW(6))'
        );
        $stmt = $this->db->prepare(
            'SELECT MAX(CEIL(TIMESTAMPDIFF(MICROSECOND, NOW(6), bloqueado_hasta) / 1000000))
             FROM intentos_login
             WHERE clave IN (?, ?) AND bloqueado_hasta > NOW(6)'
        );
        $stmt->execute($this->obtenerClaves($email, $ip));
        return max(0, (int) $stmt->fetchColumn());
    }

    public function registrarFallo($email, $ip) {
        $claves = $this->obtenerClaves($email, $ip);
        $sql = sprintf(
            'INSERT INTO intentos_login (clave, fallos, inicio_ventana, bloqueado_hasta)
             VALUES (?, 1, NOW(6), NULL)
             ON DUPLICATE KEY UPDATE
                 fallos = IF(bloqueado_hasta > NOW(6), fallos,
                     IF(inicio_ventana < DATE_SUB(NOW(6), INTERVAL %d SECOND)
                         OR bloqueado_hasta <= NOW(6), 1, fallos + 1)),
                 inicio_ventana = IF(inicio_ventana < DATE_SUB(NOW(6), INTERVAL %d SECOND)
                     OR bloqueado_hasta <= NOW(6), NOW(6), inicio_ventana),
                 bloqueado_hasta = IF(bloqueado_hasta > NOW(6), bloqueado_hasta,
                     IF(fallos >= %d, DATE_ADD(NOW(6), INTERVAL %d SECOND), NULL))',
            self::VENTANA_SEGUNDOS,
            self::VENTANA_SEGUNDOS,
            self::MAX_FALLOS,
            self::BLOQUEO_SEGUNDOS
        );

        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare($sql);
            foreach ($claves as $clave) {
                $stmt->execute([$clave]);
            }
            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }

        return $this->tiempoEspera($email, $ip);
    }

    public function limpiar($email, $ip) {
        // shortcut: limpiar solo la clave del email para no regalar intentos de IP compartida
        $stmt = $this->db->prepare('DELETE FROM intentos_login WHERE clave = ?');
        $stmt->execute([hash('sha256', 'email:' . strtolower(trim($email)))]);
    }

    private function obtenerClaves($email, $ip) {
        // shortcut: SHA-256 oculta los identificadores pero permite correlacionarlos si la tabla se expone; para evitarlo, derivar con hash_hmac y un secreto fuera del repositorio.
        $ipEmpaquetada = inet_pton($ip);
        $claves = [
            hash('sha256', 'email:' . strtolower(trim($email))),
            hash('sha256', 'ip:' . ($ipEmpaquetada === false ? strtolower(trim($ip)) : bin2hex($ipEmpaquetada)))
        ];
        sort($claves, SORT_STRING);
        return $claves;
    }
}
