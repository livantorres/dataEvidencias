<?php
// Habilitar errores
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

session_start();

// Debug de AJAX requests
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    error_log("=== AJAX REQUEST DETECTED ===");
    error_log("GET: " . print_r($_GET, true));
    error_log("POST: " . print_r($_POST, true));
    error_log("MODULO: " . ($_GET['modulo'] ?? $_POST['modulo'] ?? 'none'));
    error_log("ACCION: " . ($_GET['accion'] ?? $_POST['accion'] ?? 'none'));
}

// Determinar qué módulo cargar
$modulo = isset($_GET['modulo']) ? $_GET['modulo'] : (isset($_POST['modulo']) ? $_POST['modulo'] : 'auth');
$accion = isset($_GET['accion']) ? $_GET['accion'] : (isset($_POST['accion']) ? $_POST['accion'] : 'login');

error_log("Módulo determinado: $modulo");
error_log("Acción determinada: $accion");

// Si es AJAX y no tiene sesión, devolver JSON
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' &&
    !isset($_SESSION['usuario_id'])) {
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Sesión expirada',
        'redirect' => 'index.php?modulo=auth&accion=login'
    ]);
    exit();
}

// Continuar con el resto del código...

// Configurar zona horaria
date_default_timezone_set('America/Bogota');

try {
    // Incluir configuración de base de datos
    if (!file_exists('config/database.php')) {
        throw new Exception('Archivo de configuración de base de datos no encontrado');
    }
    
    require_once 'config/database.php';

    // Determinar qué módulo cargar
    /*$modulo = isset($_GET['modulo']) ? $_GET['modulo'] : 'auth';
    $accion = isset($_GET['accion']) ? $_GET['accion'] : 'login';*/
	
	$modulo = $_REQUEST['modulo'] ?? 'auth';
	$accion = $_REQUEST['accion'] ?? 'login';


    // Si ya está logueado y no se especifica módulo, ir al dashboard
    if (!isset($_GET['modulo']) && isset($_SESSION['usuario_id'])) {
        $modulo = 'dashboard';
        $accion = 'index';
    }

    // Validar módulo
    // $modulos_permitidos = ['auth', 'dashboard', 'evidencia', 'institucion', 'ciclo', 'usuarios', 'reportes'];
    // En tu index.php principal
$modulos_permitidos = ['auth', 'dashboard', 'evidencia', 'institucion', 'ciclo', 'usuarios', 'reportes'];
    if (!in_array($modulo, $modulos_permitidos)) {
        $modulo = 'auth';
        $accion = 'login';
    }

    // Verificar acceso si no es login/logout
    if (!in_array($modulo, ['auth']) && !isset($_SESSION['usuario_id'])) {
        header("Location: index.php?modulo=auth&accion=login");
        exit();
    }

    // Cargar controlador
    $controlador_file = 'controllers/' . ucfirst($modulo) . 'Controller.php';
    
    if (!file_exists($controlador_file)) {
        throw new Exception("Controlador no encontrado: $controlador_file");
    }
    
    require_once $controlador_file;
    $nombre_controlador = ucfirst($modulo) . 'Controller';
    
    // Verificar que la clase existe
    if (!class_exists($nombre_controlador)) {
        throw new Exception("Clase $nombre_controlador no encontrada en $controlador_file");
    }
    
    // Instanciar controlador
    $controlador = new $nombre_controlador();
    
    // Verificar si la acción existe
    if (!method_exists($controlador, $accion)) {
        // Si la acción no existe, redirigir según el contexto
        if (isset($_SESSION['usuario_id'])) {
            header("Location: index.php?modulo=dashboard&accion=index");
        } else {
            header("Location: index.php?modulo=auth&accion=login");
        }
        exit();
    }
    
    // Ejecutar acción
    $controlador->$accion();
    
} catch (Exception $e) {
    // Mostrar error amigable
    http_response_code(500);
    
    $error_message = htmlspecialchars($e->getMessage());
    $error_trace = htmlspecialchars($e->getTraceAsString());
    
    echo '<!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Error - dataEvidencias</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            body {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }
            .error-card {
                background: white;
                border-radius: 10px;
                box-shadow: 0 10px 40px rgba(0,0,0,0.1);
                max-width: 600px;
                width: 100%;
            }
            .error-header {
                background: linear-gradient(135deg, #dc3545 0%, #a71d2a 100%);
                color: white;
                padding: 20px;
                border-radius: 10px 10px 0 0;
            }
            .error-body {
                padding: 20px;
            }
            pre {
                background: #f8f9fa;
                padding: 15px;
                border-radius: 5px;
                font-size: 12px;
                max-height: 200px;
                overflow: auto;
            }
        </style>
    </head>
    <body>
        <div class="error-card">
            <div class="error-header">
                <h3><i class="fas fa-exclamation-triangle"></i> Error en dataEvidencias</h3>
                <p class="mb-0">Ha ocurrido un error en el sistema</p>
            </div>
            
            <div class="error-body">
                <div class="alert alert-danger">
                    <strong>Error:</strong> ' . $error_message . '
                </div>
                
                ' . (isset($_SESSION['usuario_id']) ? '' : '
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> 
                    Si este error persiste, contacte al administrador del sistema.
                </div>') . '
                
                <div class="mt-3">
                    ' . (isset($_SESSION['usuario_id']) ? '
                    <a href="index.php?modulo=dashboard&accion=index" class="btn btn-primary">
                        <i class="fas fa-home"></i> Volver al Dashboard
                    </a>' : '
                    <a href="index.php?modulo=auth&accion=login" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt"></i> Ir al Login
                    </a>') . '
                    
                    <a href="javascript:history.back()" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                    
                    <button class="btn btn-outline-info" onclick="toggleDetails()">
                        <i class="fas fa-bug"></i> Ver detalles
                    </button>
                </div>
                
                <div id="errorDetails" style="display: none; margin-top: 20px;">
                    <h6>Detalles técnicos:</h6>
                    <pre>' . $error_trace . '</pre>
                </div>
            </div>
        </div>
        
        <script>
        function toggleDetails() {
            var details = document.getElementById("errorDetails");
            details.style.display = details.style.display === "none" ? "block" : "none";
        }
        </script>
    </body>
    </html>';
    
    // Log del error (opcional)
    error_log("dataEvidencias Error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
}
?>