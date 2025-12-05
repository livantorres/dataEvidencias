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
        // include 'views/includes/sidebar.php';
        include 'views/usuarios/index.php';
        include 'includes/footer.php';
    }

    public function create() {
        $this->checkSession();
        $this->checkAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = trim($_POST['username']);
            $email = trim($_POST['email']);
            $password = $_POST['password'];
            $nombre_completo = trim($_POST['nombre_completo']);
            $rol_id = $_POST['rol_id'];
            
            // Validaciones
            if (empty($username) || empty($email) || empty($password) || empty($nombre_completo)) {
                $_SESSION['error'] = "Todos los campos son requeridos";
            } elseif ($this->usuario->checkUsernameExists($username)) {
                $_SESSION['error'] = "El nombre de usuario ya existe";
            } elseif ($this->usuario->checkEmailExists($email)) {
                $_SESSION['error'] = "El correo electrónico ya está registrado";
            } else {
                $this->usuario->username = $username;
                $this->usuario->email = $email;
                $this->usuario->password = password_hash($password, PASSWORD_DEFAULT);
                $this->usuario->nombre_completo = $nombre_completo;
                $this->usuario->rol_id = $rol_id;
                $this->usuario->activo = 1;
                
                if ($this->usuario->create()) {
                    $_SESSION['success'] = "Usuario creado exitosamente";
                    header("Location: index.php?modulo=usuarios&accion=index");
                    exit();
                } else {
                    $_SESSION['error'] = "Error al crear el usuario";
                }
            }
        }
        
        $roles = $this->rol->getAll();
        $page_title = "Crear Usuario";
        
        include 'includes/header.php';
        // include 'includes/sidebar.php';
        include 'views/usuarios/create.php';
        include 'includes/footer.php';
    }

    public function resetPassword() {
        $this->checkSession();
        $this->checkAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $usuario_id = $_POST['usuario_id'];
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];
            
            if ($new_password !== $confirm_password) {
                echo json_encode(['success' => false, 'message' => 'Las contraseñas no coinciden']);
                exit();
            }
            
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            
            $query = "UPDATE usuarios SET password = :password WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":password", $hashed_password);
            $stmt->bindParam(":id", $usuario_id);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Contraseña restablecida exitosamente']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al restablecer la contraseña']);
            }
        }
        exit();
    }

    public function toggleStatus() {
        $this->checkSession();
        $this->checkAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $usuario_id = $_POST['usuario_id'];
            $action = $_POST['action'];
            
            $new_status = ($action == 'activate') ? 1 : 0;
            
            $query = "UPDATE usuarios SET activo = :activo WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":activo", $new_status);
            $stmt->bindParam(":id", $usuario_id);
            
            if ($stmt->execute()) {
                $message = ($action == 'activate') ? 'Usuario activado' : 'Usuario desactivado';
                echo json_encode(['success' => true, 'message' => $message]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al cambiar el estado']);
            }
        }
        exit();
    }

    public function delete() {
        $this->checkSession();
        $this->checkAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $usuario_id = $_POST['usuario_id'];
            
            // No permitir eliminar al propio usuario
            if ($usuario_id == $_SESSION['usuario_id']) {
                echo json_encode(['success' => false, 'message' => 'No puede eliminar su propio usuario']);
                exit();
            }
            
            $query = "DELETE FROM usuarios WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":id", $usuario_id);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Usuario eliminado']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al eliminar el usuario']);
            }
        }
        exit();
    }

    private function checkSession() {
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: index.php?modulo=auth&accion=login");
            exit();
        }
    }

    private function checkAdmin() {
        if ($_SESSION['rol_id'] != 1) {
            $_SESSION['error'] = "No tiene permisos para acceder a esta sección";
            header("Location: index.php?modulo=dashboard&accion=index");
            exit();
        }
    }
}
?>