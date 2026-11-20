<?php
/**
 * Configuración general del sistema
 * Sistema MediConnect
 */

// Nombre del sistema
define('SITE_NAME', 'MediConnect');
define('SITE_TITLE', 'MediConnect - Sistema de Gestión de Salud');

// Base URL dinámica para recursos estáticos
$scriptDir = dirname($_SERVER['SCRIPT_NAME']);
$baseUrl = rtrim($scriptDir, '/');
define('BASE_URL', $baseUrl);

/**
 * Helper para rutas de recursos estáticos
 * Uso: asset('css/style.css') -> /ruthxd/public/assets/css/style.css
 */
function asset($path) {
    return BASE_URL . '/assets/' . ltrim($path, '/');
}

// Zona horaria
date_default_timezone_set('America/Bogota');

// Configuración de sesiones seguras
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
$esHttps = filter_var($_SERVER['HTTPS'] ?? false, FILTER_VALIDATE_BOOLEAN);
ini_set('session.cookie_secure', $esHttps ? '1' : '0');
ini_set('session.cookie_samesite', 'Strict');

// Iniciar sesión si no está activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Regenerar ID de sesión periódicamente
if (!isset($_SESSION['last_regeneration'])) {
    $_SESSION['last_regeneration'] = time();
} elseif (time() - $_SESSION['last_regeneration'] > 300) {
    session_regenerate_id(true);
    $_SESSION['last_regeneration'] = time();
}

// Generar token CSRF si no existe
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/**
 * Verificar token CSRF
 */
function verificar_csrf($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Obtener token CSRF
 */
function obtener_csrf_token() {
    return $_SESSION['csrf_token'] ?? '';
}

/**
 * Verificar si el usuario está autenticado
 */
function esta_autenticado() {
    return isset($_SESSION['usuario_id']) && isset($_SESSION['rol']);
}

/**
 * Verificar rol del usuario
 */
function tiene_rol($rol) {
    return esta_autenticado() && $_SESSION['rol'] === $rol;
}

/**
 * Redirigir a una página
 */
function redirigir($pagina, $params = []) {
    // shortcut: $pagina a veces trae "&id=N" incrustado; separar cuando se toque esa ruta
    $query = 'page=' . $pagina;
    if ($params) {
        $query .= '&' . http_build_query($params);
    }
    header("Location: index.php?$query");
    exit;
}

/**
 * Sanitizar salida HTML
 */
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Formatear fecha
 */
function formatear_fecha($fecha, $incluir_hora = false) {
    if (empty($fecha)) return '';
    $timestamp = strtotime($fecha);
    if ($incluir_hora) {
        return date('d/m/Y H:i', $timestamp);
    }
    return date('d/m/Y', $timestamp);
}

/**
 * Formatear moneda
 */
function formatear_moneda($valor) {
    return '$' . number_format($valor, 0, ',', '.');
}

/**
 * Calcular edad desde fecha de nacimiento
 */
function calcular_edad($fecha_nacimiento) {
    $nacimiento = new DateTime($fecha_nacimiento);
    $hoy = new DateTime();
    return $nacimiento->diff($hoy)->y;
}

/**
 * Calcular IMC
 */
function calcular_imc($peso, $altura) {
    if ($peso <= 0 || $altura <= 0) return null;
    $altura_metros = $altura / 100;
    return round($peso / ($altura_metros * $altura_metros), 1);
}

/**
 * Clasificación IMC
 */
function clasificar_imc($imc) {
    if ($imc === null) return 'No disponible';
    if ($imc < 18.5) return 'Bajo peso';
    if ($imc < 25) return 'Normal';
    if ($imc < 30) return 'Sobrepeso';
    return 'Obesidad';
}

/**
 * Registrar evento en la bitácora de auditoría en vivo
 */
function registrar_auditoria($accion, $modulo, $descripcion, $usuario_id = null) {
    try {
        if ($usuario_id === null && isset($_SESSION['usuario_id'])) {
            $usuario_id = $_SESSION['usuario_id'];
        }
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'CLI/Browser';

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("INSERT INTO auditoria_logs (usuario_id, accion, modulo, descripcion, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$usuario_id, $accion, $modulo, $descripcion, $ip, substr($ua, 0, 255)]);
    } catch (Exception $e) {
        error_log("Error al registrar auditoría: " . $e->getMessage());
    }
}

/**
 * Calcular Puntaje de Salud (0 - 100)
 */
function calcular_puntaje_salud($peso, $altura, $sistolica, $diastolica, $fc, $spo2 = 98, $glucosa = 95) {
    $score = 0;

    // 1. Presión Arterial (máx 25 pts)
    if ($sistolica >= 110 && $sistolica <= 125 && $diastolica >= 70 && $diastolica <= 85) {
        $score += 25;
    } elseif ($sistolica <= 135 && $diastolica <= 89) {
        $score += 18;
    } else {
        $score += 10;
    }

    // 2. Frecuencia Cardiaca (máx 20 pts)
    if ($fc >= 60 && $fc <= 85) {
        $score += 20;
    } elseif ($fc <= 100) {
        $score += 14;
    } else {
        $score += 8;
    }

    // 3. Saturación de Oxígeno (máx 20 pts)
    if ($spo2 >= 96) {
        $score += 20;
    } elseif ($spo2 >= 92) {
        $score += 12;
    } else {
        $score += 5;
    }

    // 4. Glucosa en ayunas (máx 20 pts)
    if ($glucosa >= 70 && $glucosa <= 100) {
        $score += 20;
    } elseif ($glucosa <= 125) {
        $score += 12;
    } else {
        $score += 6;
    }

    // 5. IMC (máx 15 pts)
    $imc = calcular_imc($peso, $altura);
    if ($imc >= 18.5 && $imc <= 24.9) {
        $score += 15;
    } elseif ($imc <= 29.9) {
        $score += 9;
    } else {
        $score += 4;
    }

    return min(100, max(0, $score));
}
