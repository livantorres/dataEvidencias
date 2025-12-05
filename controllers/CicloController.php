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
        
        $ciclos = $this->ciclo->getAll();
        $cicloActual = $this->ciclo->getActiveCiclo();
        
        $page_title = "Gestión de Ciclos";
        
        include 'includes/header.php';
        // include 'includes/sidebar.php';
        include 'views/ciclos/index.php';
        include 'includes/footer.php';
    }

    // Crear ciclo via modal (AJAX)
    public function create() {
        $this->checkSession();
        $this->checkAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $response = ['success' => false, 'message' => '', 'data' => null];
            
            try {
                $descripcion = trim($_POST['descripcion'] ?? '');
                $activo = isset($_POST['activo']) ? 1 : 0;
                
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
                    $this->db->query("UPDATE ciclos SET activo = 0");
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
            
            header('Content-Type: application/json');
            echo json_encode($response);
            exit();
        }
    }

    // Editar ciclo via modal (AJAX)
    public function edit() {
        $this->checkSession();
        $this->checkAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $response = ['success' => false, 'message' => '', 'data' => null];
            
            try {
                $id = $_POST['id'] ?? 0;
                $descripcion = trim($_POST['descripcion'] ?? '');
                $activo = isset($_POST['activo']) ? 1 : 0;
                
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
                    $this->db->query("UPDATE ciclos SET activo = 0");
                    
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
            
            header('Content-Type: application/json');
            echo json_encode($response);
            exit();
        }
        
        // GET request: devolver datos del ciclo
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $ciclo = $this->ciclo->getById($id);
            
            if ($ciclo) {
                echo json_encode(['success' => true, 'data' => $ciclo]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Ciclo no encontrado']);
            }
            exit();
        }
    }

    // Activar ciclo via AJAX
    public function activate() {
        $this->checkSession();
        $this->checkAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $response = ['success' => false, 'message' => ''];
            
            try {
                $id = $_POST['ciclo_id'] ?? 0;
                
                if (empty($id)) {
                    throw new Exception("Datos incompletos");
                }
                
                // Obtener ciclo
                $ciclo = $this->ciclo->getById($id);
                if (!$ciclo) {
                    throw new Exception("Ciclo no encontrado");
                }
                
                // Desactivar todos los ciclos
                $this->db->query("UPDATE ciclos SET activo = 0");
                
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
            
            header('Content-Type: application/json');
            echo json_encode($response);
            exit();
        }
    }

    // Eliminar ciclo via AJAX
    public function delete() {
        $this->checkSession();
        $this->checkAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $response = ['success' => false, 'message' => ''];
            
            try {
                $id = $_POST['ciclo_id'] ?? 0;
                
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
                if ($ciclo['activo'] == 1) {
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
            
            header('Content-Type: application/json');
            echo json_encode($response);
            exit();
        }
    }

    private function checkSession() {
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: index.php?modulo=auth&accion=login");
            exit();
        }
    }

    private function checkAdmin() {
        if ($_SESSION['rol_id'] != 1) {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                echo json_encode(['success' => false, 'message' => 'No tiene permisos para esta acción']);
                exit();
            } else {
                $_SESSION['error'] = "No tiene permisos para acceder a esta sección";
                header("Location: index.php?modulo=dashboard&accion=index");
                exit();
            }
        }
    }
}
?>