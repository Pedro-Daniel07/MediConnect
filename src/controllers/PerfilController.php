<?php
/**
 * Controlador de Perfil
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Paciente.php';

class PerfilController {
    private $usuarioModel;
    private $pacienteModel;

    public function __construct() {
        $this->usuarioModel = new Usuario();
        $this->pacienteModel = new Paciente();
    }

    /**
     * Ver y editar perfil
     */
    public function index() {
        if (!esta_autenticado()) {
            redirigir('login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->procesarActualizacion();
        } else {
            $usuario = $this->usuarioModel->buscarPorId($_SESSION['usuario_id']);

            $paciente = null;
            if (tiene_rol('paciente')) {
                $paciente = $this->pacienteModel->obtenerPorUsuarioId($_SESSION['usuario_id']);
            }

            $titulo = 'Mi Perfil';
            require_once __DIR__ . '/../views/perfil/index.php';
        }
    }

    /**
     * Procesar actualización de perfil
     */
    private function procesarActualizacion() {
        if (!verificar_csrf($_POST['csrf_token'] ?? '')) {
            $_SESSION['mensaje_error'] = 'Token inválido';
            redirigir('perfil');
            return;
        }

        $action = $_POST['action'] ?? '';

        if ($action === 'datos_personales') {
            $datos = [
                'nombre' => trim($_POST['nombre']),
                'apellido' => trim($_POST['apellido']),
                'telefono' => trim($_POST['telefono']),
                'direccion' => trim($_POST['direccion'])
            ];

            if ($this->usuarioModel->actualizar($_SESSION['usuario_id'], $datos)) {
                $_SESSION['nombre'] = $datos['nombre'];
                $_SESSION['apellido'] = $datos['apellido'];
                $_SESSION['mensaje_exito'] = 'Perfil actualizado';
            } else {
                $_SESSION['mensaje_error'] = 'Error al actualizar';
            }

        } elseif ($action === 'datos_medicos' && tiene_rol('paciente')) {
            $datos = [
                'grupo_sanguineo' => trim($_POST['grupo_sanguineo'] ?? ''),
                'alergias' => trim($_POST['alergias'] ?? ''),
                'enfermedades_cronicas' => trim($_POST['enfermedades_cronicas'] ?? ''),
                'contacto_emergencia_nombre' => trim($_POST['contacto_emergencia_nombre'] ?? ''),
                'contacto_emergencia_telefono' => trim($_POST['contacto_emergencia_telefono'] ?? '')
            ];

            if ($this->pacienteModel->actualizar($_SESSION['usuario_id'], $datos)) {
                $_SESSION['mensaje_exito'] = 'Información médica actualizada';
            } else {
                $_SESSION['mensaje_error'] = 'Error al actualizar';
            }

        } elseif ($action === 'cambiar_password') {
            $password_actual = $_POST['password_actual'] ?? '';
            $password_nueva = $_POST['password_nueva'] ?? '';
            $password_confirm = $_POST['password_confirm'] ?? '';

            if ($password_nueva !== $password_confirm) {
                $_SESSION['mensaje_error'] = 'Las contraseñas no coinciden';
            } elseif (strlen($password_nueva) < 8) {
                $_SESSION['mensaje_error'] = 'La contraseña debe tener al menos 8 caracteres';
            } else {
                $usuario = $this->usuarioModel->buscarPorId($_SESSION['usuario_id']);

                if (!password_verify($password_actual, $usuario['password'])) {
                    $_SESSION['mensaje_error'] = 'Contraseña actual incorrecta';
                } else {
                    if ($this->usuarioModel->cambiarPassword($_SESSION['usuario_id'], $password_nueva)) {
                        $_SESSION['mensaje_exito'] = 'Contraseña cambiada exitosamente';
                    } else {
                        $_SESSION['mensaje_error'] = 'Error al cambiar contraseña';
                    }
                }
            }
        }

        redirigir('perfil');
    }
}
