<?php
require_once 'models/Ciclo.php';

class CicloController {
    private $db;
    private $ciclo;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->ciclo = new Ciclo($this->db);
    }

    public function index() {
        $this->checkSession();
        $this->checkAdmin();
        
        try {
            $ciclos = $this->ciclo->getAll();
            $cicloActual = $this->ciclo->getActiveCiclo();
            
            $db = $this->db;
            $page_title = "Gestión de Ciclos";
            
            include 'includes/header.php';
            include 'views/ciclos/index.php';
            include 'includes/footer.php';
            
        } catch (Exception $e) {
            error_log("Error en CicloController::index(): " . $e->getMessage());
            
            $page_title = "Error";
            include 'includes/header.php';
            echo '<div class="container mt-4">';
            echo '<div class="alert alert-danger">Error al cargar los ciclos: ' . htmlspecialchars($e->getMessage()) . '</div>';
            echo '</div>';
            include 'includes/footer.php';
        }
    }

    // Crear ciclo via modal (AJAX)
    public function create() {
        $this->checkAjaxSession();
        $this->checkAjaxAdmin();
        
        header('Content-Type: application/json; charset=utf-8');
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $response = ['success' => false, 'message' => '', 'data' => null];
            
            try {
                // Obtener datos de POST o de parámetros GET
                $descripcion = trim($_POST['descripcion'] ?? $_GET['descripcion'] ?? '');
                $activo = isset($_POST['activo']) ? 1 : (isset($_GET['activo']) ? 1 : 0);
                
                if (empty($descripcion)) {
                    throw new Exception("La descripción es requerida");
                }
                
                // Verificar si ya existe ciclo con esa descripción
                $check_query = "SELECT id FROM ciclos WHERE descripcion = :descripcion";
                $check_stmt = $this->db->prepare($check_query);
                $check_stmt->bindParam(":descripcion", $descripcion);
                $check_stmt->execute();
                
                if ($check_stmt->rowCount() > 0) {
                    throw new Exception("Ya existe un ciclo con esa descripción");
                }
                
                // Si se activa este ciclo, desactivar los demás
                if ($activo == 1) {
                    $updateStmt = $this->db->prepare("UPDATE ciclos SET activo = 0");
                    $updateStmt->execute();
                }
                
                // Crear ciclo
                $this->ciclo->descripcion = $descripcion;
                $this->ciclo->activo = $activo;
                
                if ($this->ciclo->create()) {
                    $ciclo_id = $this->db->lastInsertId();
                    
                    // Actualizar sesión si este es el ciclo activo
                    if ($activo == 1) {
                        $_SESSION['ciclo_actual'] = $ciclo_id;
                        $_SESSION['ciclo_descripcion'] = $descripcion;
                    }
                    
                    // Obtener datos del ciclo creado
                    $ciclo_data = $this->ciclo->getById($ciclo_id);
                    
                    $response['success'] = true;
                    $response['message'] = "Ciclo creado exitosamente";
                    $response['data'] = $ciclo_data;
                } else {
                    throw new Exception("Error al crear el ciclo en la base de datos");
                }
                
            } catch (Exception $e) {
                $response['message'] = $e->getMessage();
            }
            
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit();
        }
    }

    // Editar ciclo via modal (AJAX)
    public function edit() {
        $this->checkAjaxSession();
        $this->checkAjaxAdmin();
        
        header('Content-Type: application/json; charset=utf-8');
        
        // GET para obtener datos
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            try {
                $id = $_GET['id'] ?? 0;
                
                if (empty($id)) {
                    throw new Exception("ID no proporcionado");
                }
                
                $ciclo = $this->ciclo->getById($id);
                
                if ($ciclo) {
                    echo json_encode([
                        'success' => true, 
                        'data' => $ciclo
                    ], JSON_UNESCAPED_UNICODE);
                } else {
                    echo json_encode([
                        'success' => false, 
                        'message' => 'Ciclo no encontrado'
                    ], JSON_UNESCAPED_UNICODE);
                }
            } catch (Exception $e) {
                echo json_encode([
                    'success' => false, 
                    'message' => $e->getMessage()
                ], JSON_UNESCAPED_UNICODE);
            }
            exit();
        }
        
        // POST para actualizar
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $response = ['success' => false, 'message' => '', 'data' => null];
            
            try {
                // Obtener datos de POST o de parámetros GET
                $id = $_POST['id'] ?? $_GET['id'] ?? 0;
                $descripcion = trim($_POST['descripcion'] ?? $_GET['descripcion'] ?? '');
                $activo = isset($_POST['activo']) ? 1 : (isset($_GET['activo']) ? 1 : 0);
                
                if (empty($id) || empty($descripcion)) {
                    throw new Exception("Datos incompletos");
                }
                
                // Verificar si ya existe otro ciclo con esa descripción
                $check_query = "SELECT id FROM ciclos WHERE descripcion = :descripcion AND id != :id";
                $check_stmt = $this->db->prepare($check_query);
                $check_stmt->bindParam(":descripcion", $descripcion);
                $check_stmt->bindParam(":id", $id);
                $check_stmt->execute();
                
                if ($check_stmt->rowCount() > 0) {
                    throw new Exception("Ya existe otro ciclo con esa descripción");
                }
                
                // Si se activa este ciclo, desactivar los demás
                if ($activo == 1) {
                    $updateStmt = $this->db->prepare("UPDATE ciclos SET activo = 0");
                    $updateStmt->execute();
                    
                    // Actualizar sesión
                    $_SESSION['ciclo_actual'] = $id;
                    $_SESSION['ciclo_descripcion'] = $descripcion;
                }
                
                // Actualizar ciclo
                $query = "UPDATE ciclos 
                         SET descripcion = :descripcion, 
                             activo = :activo 
                         WHERE id = :id";
                
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(":descripcion", $descripcion);
                $stmt->bindParam(":activo", $activo);
                $stmt->bindParam(":id", $id);
                
                if ($stmt->execute()) {
                    // Obtener datos actualizados
                    $ciclo_data = $this->ciclo->getById($id);
                    
                    $response['success'] = true;
                    $response['message'] = "Ciclo actualizado exitosamente";
                    $response['data'] = $ciclo_data;
                } else {
                    throw new Exception("Error al actualizar el ciclo");
                }
                
            } catch (Exception $e) {
                $response['message'] = $e->getMessage();
            }
            
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit();
        }
    }

    // Activar ciclo via AJAX
    public function activate() {
        $this->checkAjaxSession();
        $this->checkAjaxAdmin();
        
        header('Content-Type: application/json; charset=utf-8');
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $response = ['success' => false, 'message' => ''];
            
            try {
                // Obtener ID de POST o GET
                $id = $_POST['ciclo_id'] ?? $_GET['ciclo_id'] ?? 0;
                
                if (empty($id)) {
                    throw new Exception("Datos incompletos");
                }
                
                // Obtener ciclo
                $ciclo = $this->ciclo->getById($id);
                if (!$ciclo) {
                    throw new Exception("Ciclo no encontrado");
                }
                
                // Desactivar todos los ciclos
                $updateAllStmt = $this->db->prepare("UPDATE ciclos SET activo = 0");
                $updateAllStmt->execute();
                
                // Activar el ciclo seleccionado
                $query = "UPDATE ciclos SET activo = 1 WHERE id = :id";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(":id", $id);
                
                if ($stmt->execute()) {
                    // Actualizar sesión
                    $_SESSION['ciclo_actual'] = $id;
                    $_SESSION['ciclo_descripcion'] = $ciclo['descripcion'];
                    
                    $response['success'] = true;
                    $response['message'] = "Ciclo activado exitosamente";
                } else {
                    throw new Exception("Error al activar el ciclo");
                }
                
            } catch (Exception $e) {
                $response['message'] = $e->getMessage();
            }
            
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit();
        }
    }

    // Eliminar ciclo via AJAX
    public function delete() {
        $this->checkAjaxSession();
        $this->checkAjaxAdmin();
        
        header('Content-Type: application/json; charset=utf-8');
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $response = ['success' => false, 'message' => ''];
            
            try {
                // Obtener ID de POST o GET
                $id = $_POST['ciclo_id'] ?? $_GET['ciclo_id'] ?? 0;
                
                if (empty($id)) {
                    throw new Exception("Datos incompletos");
                }
                
                // Verificar si hay evidencias asociadas
                $check_query = "SELECT COUNT(*) as total FROM evidencias WHERE ciclo_id = :id";
                $check_stmt = $this->db->prepare($check_query);
                $check_stmt->bindParam(":id", $id);
                $check_stmt->execute();
                $result = $check_stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($result['total'] > 0) {
                    throw new Exception("No se puede eliminar el ciclo porque tiene evidencias asociadas");
                }
                
                // Verificar si es el ciclo activo
                $ciclo = $this->ciclo->getById($id);
                if ($ciclo && $ciclo['activo'] == 1) {
                    throw new Exception("No se puede eliminar el ciclo activo. Active otro ciclo primero.");
                }
                
                // Eliminar ciclo
                $query = "DELETE FROM ciclos WHERE id = :id";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(":id", $id);
                
                if ($stmt->execute()) {
                    $response['success'] = true;
                    $response['message'] = "Ciclo eliminado exitosamente";
                } else {
                    throw new Exception("Error al eliminar el ciclo");
                }
                
            } catch (Exception $e) {
                $response['message'] = $e->getMessage();
            }
            
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit();
        }
    }

    // Métodos de verificación para AJAX
    private function checkAjaxSession() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => false, 
                'message' => 'Sesión expirada. Por favor, inicie sesión nuevamente.',
                'redirect' => 'index.php?modulo=auth&accion=login'
            ], JSON_UNESCAPED_UNICODE);
            exit();
        }
    }

    private function checkAjaxAdmin() {
        if (!isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != 1) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => false, 
                'message' => 'No tiene permisos para realizar esta acción'
            ], JSON_UNESCAPED_UNICODE);
            exit();
        }
    }

    // Métodos de verificación para vistas normales
    private function checkSession() {
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: index.php?modulo=auth&accion=login");
            exit();
        }
    }

    private function checkAdmin() {
        if (!isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != 1) {
            $_SESSION['error'] = "No tiene permisos para acceder a esta sección";
            header("Location: index.php?modulo=dashboard&accion=index");
            exit();
        }
    }
}
?>