<?php
require_once 'models/Usuario.php';
require_once 'models/Rol.php';

class UsuariosController {
    private $db;
    private $usuario;
    private $rol;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->usuario = new Usuario($this->db);
        $this->rol = new Rol($this->db);
    }

    public function index() {
        $this->checkSession();
        $this->checkAdmin();
        
        $usuarios = $this->usuario->getAll();
        $roles = $this->rol->getAll();
        
        $page_title = "Gestión de Usuarios";
        
        include 'includes/header.php';
        include 'views/usuarios/index.php';
        include 'includes/footer.php';
    }

    // Crear usuario via modal (AJAX)
    public function create() {
        // Configurar headers JSON primero
        header('Content-Type: application/json; charset=utf-8');
        
        // Verificar si es AJAX request
        $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
        
        if (!$isAjax) {
            echo json_encode(['success' => false, 'message' => 'Esta acción solo está disponible via AJAX']);
            exit();
        }
        
        // Verificar sesión y permisos sin redirigir
        if (!isset($_SESSION['usuario_id'])) {
            echo json_encode([
                'success' => false, 
                'message' => 'Sesión expirada. Por favor, inicie sesión nuevamente.',
                'redirect' => 'index.php?modulo=auth&accion=login'
            ]);
            exit();
        }
        
        if (!isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != 1) {
            echo json_encode([
                'success' => false, 
                'message' => 'No tiene permisos para realizar esta acción'
            ]);
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $response = ['success' => false, 'message' => '', 'data' => null];
            
            try {
                $username = trim($_POST['username'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $password = $_POST['password'] ?? '';
                $nombre_completo = trim($_POST['nombre_completo'] ?? '');
                $rol_id = $_POST['rol_id'] ?? 0;
                $activo = isset($_POST['activo']) ? 1 : 0;
                
                // Validaciones
                if (empty($username) || empty($email) || empty($password) || empty($nombre_completo) || empty($rol_id)) {
                    throw new Exception("Todos los campos son requeridos");
                }
                
                if ($this->usuario->checkUsernameExists($username)) {
                    throw new Exception("El nombre de usuario ya existe");
                }
                
                if ($this->usuario->checkEmailExists($email)) {
                    throw new Exception("El correo electrónico ya está registrado");
                }
                
                if (strlen($password) < 6) {
                    throw new Exception("La contraseña debe tener al menos 6 caracteres");
                }
                
                $this->usuario->username = $username;
                $this->usuario->email = $email;
                $this->usuario->password = password_hash($password, PASSWORD_DEFAULT);
                $this->usuario->nombre_completo = $nombre_completo;
                $this->usuario->rol_id = $rol_id;
                $this->usuario->activo = $activo;
                
                if ($this->usuario->create()) {
                    $usuario_id = $this->db->lastInsertId();
                    
                    // Obtener datos del usuario creado
                    $usuario_data = $this->usuario->getById($usuario_id);
                    
                    $response['success'] = true;
                    $response['message'] = "Usuario creado exitosamente";
                    $response['data'] = $usuario_data;
                } else {
                    throw new Exception("Error al crear el usuario en la base de datos");
                }
                
            } catch (Exception $e) {
                $response['message'] = $e->getMessage();
            }
            
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit();
        }
    }

    public function resetPassword() {
        // Configurar headers JSON primero
        header('Content-Type: application/json; charset=utf-8');
        
        // Verificar si es AJAX request
        $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
        
        if (!$isAjax) {
            echo json_encode(['success' => false, 'message' => 'Esta acción solo está disponible via AJAX']);
            exit();
        }
        
        $this->checkAjaxSession();
        $this->checkAjaxAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $response = ['success' => false, 'message' => ''];
            
            try {
                $usuario_id = $_POST['usuario_id'] ?? 0;
                $new_password = $_POST['new_password'] ?? '';
                $confirm_password = $_POST['confirm_password'] ?? '';
                
                if (empty($usuario_id) || empty($new_password) || empty($confirm_password)) {
                    throw new Exception("Todos los campos son requeridos");
                }
                
                if ($new_password !== $confirm_password) {
                    throw new Exception("Las contraseñas no coinciden");
                }
                
                if (strlen($new_password) < 6) {
                    throw new Exception("La contraseña debe tener al menos 6 caracteres");
                }
                
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                
                $query = "UPDATE usuarios SET password = :password WHERE id = :id";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(":password", $hashed_password);
                $stmt->bindParam(":id", $usuario_id);
                
                if ($stmt->execute()) {
                    $response['success'] = true;
                    $response['message'] = 'Contraseña restablecida exitosamente';
                } else {
                    throw new Exception("Error al restablecer la contraseña");
                }
                
            } catch (Exception $e) {
                $response['message'] = $e->getMessage();
            }
            
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit();
        }
    }

    public function toggleStatus() {
        // Configurar headers JSON primero
        header('Content-Type: application/json; charset=utf-8');
        
        // Verificar si es AJAX request
        $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
        
        if (!$isAjax) {
            echo json_encode(['success' => false, 'message' => 'Esta acción solo está disponible via AJAX']);
            exit();
        }
        
        $this->checkAjaxSession();
        $this->checkAjaxAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $response = ['success' => false, 'message' => ''];
            
            try {
                $usuario_id = $_POST['usuario_id'] ?? 0;
                $action = $_POST['action'] ?? '';
                
                if (empty($usuario_id) || empty($action)) {
                    throw new Exception("Datos incompletos");
                }
                
                // Verificar que no sea el propio usuario
                if ($usuario_id == $_SESSION['usuario_id']) {
                    throw new Exception("No puede cambiar su propio estado");
                }
                
                $new_status = ($action == 'activate') ? 1 : 0;
                
                $query = "UPDATE usuarios SET activo = :activo WHERE id = :id";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(":activo", $new_status);
                $stmt->bindParam(":id", $usuario_id);
                
                if ($stmt->execute()) {
                    $message = ($action == 'activate') ? 'Usuario activado' : 'Usuario desactivado';
                    $response['success'] = true;
                    $response['message'] = $message;
                } else {
                    throw new Exception("Error al cambiar el estado");
                }
                
            } catch (Exception $e) {
                $response['message'] = $e->getMessage();
            }
            
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit();
        }
    }

    public function delete() {
        // Configurar headers JSON primero
        header('Content-Type: application/json; charset=utf-8');
        
        // Verificar si es AJAX request
        $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
        
        if (!$isAjax) {
            echo json_encode(['success' => false, 'message' => 'Esta acción solo está disponible via AJAX']);
            exit();
        }
        
        $this->checkAjaxSession();
        $this->checkAjaxAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $response = ['success' => false, 'message' => ''];
            
            try {
                $usuario_id = $_POST['usuario_id'] ?? 0;
                
                if (empty($usuario_id)) {
                    throw new Exception("Datos incompletos");
                }
                
                // No permitir eliminar al propio usuario
                if ($usuario_id == $_SESSION['usuario_id']) {
                    throw new Exception('No puede eliminar su propio usuario');
                }
                
                $query = "DELETE FROM usuarios WHERE id = :id";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(":id", $usuario_id);
                
                if ($stmt->execute()) {
                    $response['success'] = true;
                    $response['message'] = 'Usuario eliminado';
                } else {
                    throw new Exception("Error al eliminar el usuario");
                }
                
            } catch (Exception $e) {
                $response['message'] = $e->getMessage();
            }
            
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit();
        }
    }

    // Métodos de verificación para AJAX (no redirigen)
    private function checkAjaxSession() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false, 
                'message' => 'Sesión expirada. Por favor, inicie sesión nuevamente.',
                'redirect' => 'index.php?modulo=auth&accion=login'
            ]);
            exit();
        }
    }

    private function checkAjaxAdmin() {
        if (!isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != 1) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false, 
                'message' => 'No tiene permisos para realizar esta acción'
            ]);
            exit();
        }
    }

    // Métodos de verificación para vistas normales (redirigen)
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
	
	// Agrega este método a la clase UsuariosController
public function changeRole() {
    // Configurar headers JSON primero
    header('Content-Type: application/json; charset=utf-8');
    
    // Verificar si es AJAX request
    $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
              strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    
    if (!$isAjax) {
        echo json_encode(['success' => false, 'message' => 'Esta acción solo está disponible via AJAX']);
        exit();
    }
    
    $this->checkAjaxSession();
    $this->checkAjaxAdmin();
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $response = ['success' => false, 'message' => ''];
        
        try {
            $usuario_id = $_POST['usuario_id'] ?? 0;
            $new_role_id = $_POST['new_role_id'] ?? 0;
            
            if (empty($usuario_id) || empty($new_role_id)) {
                throw new Exception("Datos incompletos");
            }
            
            // No permitir cambiar el rol del propio usuario a no administrador
            if ($usuario_id == $_SESSION['usuario_id'] && $new_role_id != 1) {
                throw new Exception("No puede quitarse a sí mismo el rol de administrador");
            }
            
            // Verificar si el usuario existe
            $usuario = $this->usuario->getById($usuario_id);
            if (!$usuario) {
                throw new Exception("Usuario no encontrado");
            }
            
            // Verificar si el rol existe
            $check_role = "SELECT id FROM roles WHERE id = :role_id";
            $stmt_role = $this->db->prepare($check_role);
            $stmt_role->bindParam(":role_id", $new_role_id);
            $stmt_role->execute();
            
            if ($stmt_role->rowCount() == 0) {
                throw new Exception("Rol no válido");
            }
            
            // Actualizar el rol
            $query = "UPDATE usuarios SET rol_id = :rol_id WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":rol_id", $new_role_id);
            $stmt->bindParam(":id", $usuario_id);
            
            if ($stmt->execute()) {
                // Obtener nombre del nuevo rol
                $get_role_name = "SELECT nombre FROM roles WHERE id = :role_id";
                $stmt_name = $this->db->prepare($get_role_name);
                $stmt_name->bindParam(":role_id", $new_role_id);
                $stmt_name->execute();
                $role_data = $stmt_name->fetch(PDO::FETCH_ASSOC);
                
                $response['success'] = true;
                $response['message'] = "Rol cambiado exitosamente";
                $response['new_role_name'] = $role_data['nombre'] ?? '';
                $response['new_role_id'] = $new_role_id;
            } else {
                throw new Exception("Error al cambiar el rol");
            }
            
        } catch (Exception $e) {
            $response['message'] = $e->getMessage();
        }
        
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit();
    }
}
}
?>