<?php
/**
 * Configuración de la base de datos
 * Sistema MediConnect
 */

$serverName = $_SERVER['SERVER_NAME'] ?? '';
$serverName = strtolower(trim($serverName));
$isLocal = in_array($serverName, ['localhost', '127.0.0.1'], true);

$configFileExists = file_exists(__DIR__ . '/database.local.php');
$dbConfig = null;

if ($configFileExists) {
    $dbConfig = include __DIR__ . '/database.local.php';
}

if (!is_array($dbConfig)) {
    // Tratar como si faltara
    if ($isLocal) {
        $dbConfig = [
            'DB_HOST' => 'localhost',
            'DB_NAME' => 'mediconnect',
            'DB_USER' => 'root',
            'DB_PASS' => '',
            'DB_CHARSET' => 'utf8mb4',
        ];
    } else {
        error_log("Configuración de base de datos faltante o inválida: database.local.php no encontrado o no devuelve array en host {$serverName}");
        http_response_code(500);
        die("Error de configuración. Contacte al administrador.");
    }
}

define('DB_HOST', $dbConfig['DB_HOST'] ?? 'localhost');
define('DB_NAME', $dbConfig['DB_NAME'] ?? 'mediconnect');
define('DB_USER', $dbConfig['DB_USER'] ?? 'root');
define('DB_PASS', $dbConfig['DB_PASS'] ?? '');
define('DB_CHARSET', $dbConfig['DB_CHARSET'] ?? 'utf8mb4');

/**
 * Conexión PDO a la base de datos
 */
class Database {
    private static $instance = null;
    private $connection;

    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            error_log("Error de conexión a BD: " . $e->getMessage());
            http_response_code(500);
            die("Error interno del sistema. Por favor intente más tarde.");
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }

    // Prevenir clonación
    private function __clone() {}
}
