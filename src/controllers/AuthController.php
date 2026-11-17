<?php
/**
 * Controlador de Autenticación
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../models/IntentoLogin.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Paciente.php';

class AuthController {
    private $usuarioModel;
    private $pacienteModel;

    public function __construct() {
        $this->usuarioModel = new Usuario();
        $this->pacienteModel = new Paciente();
    }

    /**
     * Mostrar formulario de login / Procesar login
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->procesarLogin();
        } else {
            require_once __DIR__ . '/../views/auth/login.php';
        }
    }

    /**
     * Procesar inicio de sesión
     */
    private function procesarLogin() {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $error = 'Por favor complete todos los campos';
            require_once __DIR__ . '/../views/auth/login.php';
            return;
        }

        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $intentosLogin = new IntentoLogin();
        $espera = $intentosLogin->tiempoEspera($email, $ip);
        if ($espera > 0) {
            if ($espera >= 3600) {
                http_response_code(503);
                $error = "Servicio no disponible, intenta más tarde.";
            } else {
                http_response_code(429);
                header('Retry-After: ' . $espera);
                $error = "Demasiados intentos. Intenta de nuevo en {$espera} segundos.";
            }
            require_once __DIR__ . '/../views/auth/login.php';
            return;
        }

        $usuario = $this->usuarioModel->buscarPorEmail($email);

        if (!$usuario || !password_verify($password, $usuario['password'])) {
            $espera = $intentosLogin->registrarFallo($email, $ip);
            if ($espera > 0) {
                if ($espera >= 3600) {
                    http_response_code(503);
                    $error = "Servicio no disponible, intenta más tarde.";
                } else {
                    http_response_code(429);
                    header('Retry-After: ' . $espera);
                    $error = "Demasiados intentos. Intenta de nuevo en {$espera} segundos.";
                }
            } else {
                $error = 'Credenciales incorrectas';
            }
            require_once __DIR__ . '/../views/auth/login.php';
            return;
        }

        if (!session_regenerate_id(true)) {
            $error = 'No se pudo iniciar sesión. Intenta de nuevo.';
            require_once __DIR__ . '/../views/auth/login.php';
            return;
        }
        $_SESSION['last_regeneration'] = time();
        $intentosLogin->limpiar($email, $ip);

        // Iniciar sesión
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['nombre'] = $usuario['nombre'];
        $_SESSION['apellido'] = $usuario['apellido'];
        $_SESSION['email'] = $usuario['email'];
        $_SESSION['rol'] = $usuario['rol_nombre'];
        $_SESSION['rol_id'] = $usuario['rol_id'];

        // Actualizar último acceso
        $this->usuarioModel->actualizarUltimoAcceso($usuario['id']);

        redirigir('dashboard');
    }

    /**
     * Mostrar formulario de registro / Procesar registro
     */
    public function registro() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->procesarRegistro();
        } else {
            require_once __DIR__ . '/../views/auth/registro.php';
        }
    }

    /**
     * Procesar registro de paciente
     */
    private function procesarRegistro() {
        // Validar CSRF
        if (!verificar_csrf($_POST['csrf_token'] ?? '')) {
            $error = 'Token de seguridad inválido';
            require_once __DIR__ . '/../views/auth/registro.php';
            return;
        }

        $errores = [];
        $datos = [
            'nombre' => trim($_POST['nombre'] ?? ''),
            'apellido' => trim($_POST['apellido'] ?? ''),
            'documento' => trim($_POST['documento'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'telefono' => trim($_POST['telefono'] ?? ''),
            'fecha_nacimiento' => $_POST['fecha_nacimiento'] ?? '',
            'genero' => $_POST['genero'] ?? 'Otro',
            'direccion' => trim($_POST['direccion'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'password_confirm' => $_POST['password_confirm'] ?? ''
        ];

        // Validaciones
        if (empty($datos['nombre'])) $errores[] = 'El nombre es requerido';
        if (empty($datos['apellido'])) $errores[] = 'El apellido es requerido';
        if (empty($datos['documento'])) $errores[] = 'El documento es requerido';
        if (empty($datos['email']) || !filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'Email inválido';
        }
        if (empty($datos['password']) || strlen($datos['password']) < 8) {
            $errores[] = 'La contraseña debe tener al menos 8 caracteres';
        }
        if ($datos['password'] !== $datos['password_confirm']) {
            $errores[] = 'Las contraseñas no coinciden';
        }

        // Verificar duplicados
        if ($this->usuarioModel->emailExiste($datos['email'])) {
            $errores[] = 'El email ya está registrado';
        }
        if ($this->usuarioModel->documentoExiste($datos['documento'])) {
            $errores[] = 'El documento ya está registrado';
        }

        if (!empty($errores)) {
            require_once __DIR__ . '/../views/auth/registro.php';
            return;
        }

        // Obtener ID del rol paciente
        $datos['rol_id'] = $this->usuarioModel->obtenerRolId('paciente');

        try {
            // Crear usuario
            if ($this->usuarioModel->crear($datos)) {
                $usuario = $this->usuarioModel->buscarPorEmail($datos['email']);

                // Crear registro de paciente
                $this->pacienteModel->crear($usuario['id']);

                $exito = 'Registro exitoso. Ahora puedes iniciar sesión.';
                require_once __DIR__ . '/../views/auth/login.php';
            } else {
                $error = 'Error al registrar usuario';
                require_once __DIR__ . '/../views/auth/registro.php';
            }
        } catch (Exception $e) {
            error_log("Error en registro de usuario: " . $e->getMessage());
            $error = 'Error al registrar usuario. Por favor intente más tarde.';
            require_once __DIR__ . '/../views/auth/registro.php';
        }
    }

    /**
     * Cerrar sesión
     */
    public function logout() {
        session_destroy();
        redirigir('login');
    }

    /**
     * Recuperar contraseña (simulado)
     */
    public function recuperar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');

            if (empty($email)) {
                $error = 'Por favor ingrese su email';
                require_once __DIR__ . '/../views/auth/recuperar.php';
                return;
            }

            // En producción, aquí se enviaría un email real
            $mensaje = 'Si el email existe en nuestro sistema, recibirás un enlace de recuperación.';
            require_once __DIR__ . '/../views/auth/recuperar.php';
        } else {
            require_once __DIR__ . '/../views/auth/recuperar.php';
        }
    }
}
