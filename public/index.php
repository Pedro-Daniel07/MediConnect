<?php
/**
 * Punto de entrada principal
 * Sistema MediConnect
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

// Obtener la página solicitada
$page = $_GET['page'] ?? (esta_autenticado() ? 'dashboard' : 'login');
$publicBasePath = parse_url(BASE_URL, PHP_URL_PATH) ?: '';

// Rutas públicas (sin autenticación)
$public_pages = ['login', 'registro', 'recuperar'];

// Si no está autenticado y la página no es pública, redirigir a login
if (!esta_autenticado() && !in_array($page, $public_pages)) {
    $page = 'login';
}

// Si está autenticado y trata de acceder a páginas públicas, redirigir a dashboard
if (esta_autenticado() && in_array($page, $public_pages)) {
    $page = 'dashboard';
}

// Enrutamiento
switch ($page) {
    // Autenticación
    case 'login':
        require_once __DIR__ . '/../src/controllers/AuthController.php';
        $controller = new AuthController();
        $controller->login();
        break;

    case 'registro':
        require_once __DIR__ . '/../src/controllers/AuthController.php';
        $controller = new AuthController();
        $controller->registro();
        break;

    case 'logout':
        // Logout solo acepta POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Método no permitido');
        }
        require_once __DIR__ . '/../src/controllers/AuthController.php';
        $controller = new AuthController();
        $controller->logout();
        break;

    case 'recuperar':
        require_once __DIR__ . '/../src/controllers/AuthController.php';
        $controller = new AuthController();
        $controller->recuperar();
        break;

    // Dashboard
    case 'dashboard':
        require_once __DIR__ . '/../src/controllers/DashboardController.php';
        $controller = new DashboardController();
        $controller->index();
        break;

    // Paciente
    case 'mis-citas':
        require_once __DIR__ . '/../src/controllers/CitaController.php';
        $controller = new CitaController();
        $controller->misCitas();
        break;

    case 'agendar-cita':
        require_once __DIR__ . '/../src/controllers/CitaController.php';
        $controller = new CitaController();
        $controller->agendar();
        break;

    case 'mi-historial':
        require_once __DIR__ . '/../src/controllers/PacienteController.php';
        $controller = new PacienteController();
        $controller->historial();
        break;

    case 'mis-formulas':
        require_once __DIR__ . '/../src/controllers/PacienteController.php';
        $controller = new PacienteController();
        $controller->formulas();
        break;

    case 'resultados':
        require_once __DIR__ . '/../src/controllers/PacienteController.php';
        $controller = new PacienteController();
        $controller->resultados();
        break;

    case 'linea-tiempo':
        require_once __DIR__ . '/../src/controllers/PacienteController.php';
        $controller = new PacienteController();
        $controller->lineaTiempo();
        break;

    case 'carne-digital':
        require_once __DIR__ . '/../src/controllers/PacienteController.php';
        $controller = new PacienteController();
        $controller->carneDigital();
        break;

    case 'vacunacion':
        require_once __DIR__ . '/../src/controllers/PacienteController.php';
        $controller = new PacienteController();
        $controller->vacunacion();
        break;

    case 'signos-vitales':
        require_once __DIR__ . '/../src/controllers/PacienteController.php';
        $controller = new PacienteController();
        $controller->signosVitales();
        break;

    // Telemedicina
    case 'telemedicina':
        require_once __DIR__ . '/../src/controllers/TelemedicinaController.php';
        $controller = new TelemedicinaController();
        $controller->index();
        break;

    case 'seleccionar-cita-telemedicina':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verificar_csrf($_POST['csrf_token'] ?? '')) {
            http_response_code(403);
            exit('Solicitud no autorizada');
        }
        $citaId = filter_input(INPUT_POST, 'cita_id', FILTER_VALIDATE_INT);
        $contactoId = filter_input(INPUT_POST, 'contacto_id', FILTER_VALIDATE_INT);
        if (!$citaId || $citaId < 1 || $contactoId === false || ($contactoId !== null && $contactoId < 1)) {
            http_response_code(400);
            exit('Selecciona una cita válida');
        }
        $query = 'index.php?page=telemedicina&cita_id=' . $citaId;
        if ($contactoId !== null) {
            $query .= '&contacto_id=' . $contactoId;
        }
        header('Location: ' . $publicBasePath . '/' . $query);
        exit;

    case 'api-telemedicina-notas':
        require_once __DIR__ . '/../src/controllers/TelemedicinaController.php';
        $controller = new TelemedicinaController();
        $controller->guardarNotas();
        break;

    // Chat en Tiempo Real
    case 'chat':
        require_once __DIR__ . '/../src/controllers/ChatController.php';
        $controller = new ChatController();
        $controller->index();
        break;

    case 'api-chat-mensajes':
        require_once __DIR__ . '/../src/controllers/ChatController.php';
        $controller = new ChatController();
        $controller->apiMensajes();
        break;

    case 'api-chat-enviar':
        require_once __DIR__ . '/../src/controllers/ChatController.php';
        $controller = new ChatController();
        $controller->apiEnviar();
        break;

    // Médico
    case 'agenda-medico':
        require_once __DIR__ . '/../src/controllers/MedicoController.php';
        $controller = new MedicoController();
        $controller->agenda();
        break;

    case 'atender-cita':
        require_once __DIR__ . '/../src/controllers/MedicoController.php';
        $controller = new MedicoController();
        $controller->atenderCita();
        break;

    case 'pacientes':
        require_once __DIR__ . '/../src/controllers/MedicoController.php';
        $controller = new MedicoController();
        $controller->pacientes();
        break;

    case 'ver-historia':
        require_once __DIR__ . '/../src/controllers/MedicoController.php';
        $controller = new MedicoController();
        $controller->verHistoria();
        break;

    // Administrador
    case 'usuarios':
        require_once __DIR__ . '/../src/controllers/AdminController.php';
        $controller = new AdminController();
        $controller->usuarios();
        break;

    case 'medicos':
        require_once __DIR__ . '/../src/controllers/AdminController.php';
        $controller = new AdminController();
        $controller->medicos();
        break;

    case 'especialidades':
        require_once __DIR__ . '/../src/controllers/AdminController.php';
        $controller = new AdminController();
        $controller->especialidades();
        break;

    case 'reportes':
        require_once __DIR__ . '/../src/controllers/AdminController.php';
        $controller = new AdminController();
        $controller->reportes();
        break;

    case 'auditoria':
        require_once __DIR__ . '/../src/controllers/AdminController.php';
        $controller = new AdminController();
        $controller->auditoria();
        break;

    case 'lista-espera':
        require_once __DIR__ . '/../src/controllers/AdminController.php';
        $controller = new AdminController();
        $controller->listaEspera();
        break;

    case 'exportar-citas':
        require_once __DIR__ . '/../src/controllers/AdminController.php';
        $controller = new AdminController();
        $controller->exportarCitas();
        break;

    case 'exportar-pacientes':
        require_once __DIR__ . '/../src/controllers/AdminController.php';
        $controller = new AdminController();
        $controller->exportarPacientes();
        break;

    // Común
    case 'perfil':
        require_once __DIR__ . '/../src/controllers/PerfilController.php';
        $controller = new PerfilController();
        $controller->index();
        break;

    case 'notificaciones':
        require_once __DIR__ . '/../src/controllers/NotificacionController.php';
        $controller = new NotificacionController();
        $controller->index();
        break;

    case 'orientador':
        require_once __DIR__ . '/../src/controllers/OrientadorController.php';
        $controller = new OrientadorController();
        $controller->index();
        break;

    // API endpoints para AJAX
    case 'api-horarios':
        require_once __DIR__ . '/../src/controllers/ApiController.php';
        $controller = new ApiController();
        $controller->obtenerHorarios();
        break;

    case 'api-notificaciones':
        require_once __DIR__ . '/../src/controllers/ApiController.php';
        $controller = new ApiController();
        $controller->contarNotificaciones();
        break;

    default:
        http_response_code(404);
        echo '<h1>Página no encontrada</h1>';
        break;
}
