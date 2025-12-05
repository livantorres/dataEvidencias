<?php
require_once 'models/Usuario.php';
require_once 'models/Rol.php';

class AuthController {
    private $db;
    private $usuario;
    private $rol;

    public function __construct() {
        try {
            $database = new Database();
            $this->db = $database->getConnection();
            $this->usuario = new Usuario($this->db);
            $this->rol = new Rol($this->db);
        } catch (Exception $e) {
            die("Error al inicializar AuthController: " . $e->getMessage());
        }
    }
public function login() {
    // Si ya está logueado, redirigir al dashboard
    if (isset($_SESSION['usuario_id'])) {
        header("Location: index.php?modulo=dashboard&accion=index");
        exit();
    }

    $error = '';

    // ✔ POST correcto
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        try {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            // Verificar campos vacíos
            if (empty($username) || empty($password)) {
                $error = "Usuario y contraseña son requeridos";

            } else {

                /*
                |--------------------------------------------------------------------------
                | ✔ AGREGAR VALIDACIÓN reCAPTCHA AQUÍ
                |--------------------------------------------------------------------------
                */
                if (!isset($_POST['g-recaptcha-response']) || empty($_POST['g-recaptcha-response'])) {
                    $error = "Por favor confirma que no eres un robot.";
                    include 'views/auth/login.php';
                    return;
                }

                $recaptcha = $_POST['g-recaptcha-response'];

                // Clave secreta de Google
                $secretKey = "6LcFXSIsAAAAAGh27CRwMAwn3ScptbU6x-F9qThc";

                // Enviar solicitud a Google
                $url = "https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$recaptcha";
                $verifyResponse = file_get_contents($url);
                $responseData = json_decode($verifyResponse);

                if (!$responseData->success) {
                    $error = "Error de verificación reCAPTCHA. Inténtalo nuevamente.";
                    include 'views/auth/login.php';
                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | ✔ SI reCAPTCHA ES CORRECTO → CONTINÚA LOGIN
                |--------------------------------------------------------------------------
                */

                $user = $this->usuario->login($username, $password);

                if ($user) {
                    // Crear sesión
                    $_SESSION['usuario_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['nombre_completo'] = $user['nombre_completo'];
                    $_SESSION['rol_id'] = $user['rol_id'];
                    $_SESSION['rol_nombre'] = $user['rol_nombre'];
                    $_SESSION['email'] = $user['email'];

                    $_SESSION['success'] = "Bienvenido, " . $user['nombre_completo'];

                    header("Location: index.php?modulo=dashboard&accion=index");
                    exit();

                } else {
                    $error = "Usuario o contraseña incorrectos";
                }
            }

        } catch (Exception $e) {
            $error = "Error en el proceso de autenticación";
            error_log("Login error: " . $e->getMessage());
        }
    }

    include 'views/auth/login.php';
}

   /* public function login() {
        // Si ya está logueado, redirigir al dashboard
        if (isset($_SESSION['usuario_id'])) {
            header("Location: index.php?modulo=dashboard&accion=index");
            exit();
        }
        
        $error = '';
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $username = trim($_POST['username'] ?? '');
                $password = $_POST['password'] ?? '';
                
                if (empty($username) || empty($password)) {
                    $error = "Usuario y contraseña son requeridos";
                } else {
                    $user = $this->usuario->login($username, $password);
                    
                    if ($user) {
                        // Crear sesión
                        $_SESSION['usuario_id'] = $user['id'];
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['nombre_completo'] = $user['nombre_completo'];
                        $_SESSION['rol_id'] = $user['rol_id'];
                        $_SESSION['rol_nombre'] = $user['rol_nombre'];
                        $_SESSION['email'] = $user['email'];
                        
                        $_SESSION['success'] = "Bienvenido, " . $user['nombre_completo'];
                        header("Location: index.php?modulo=dashboard&accion=index");
                        exit();
                    } else {
                        $error = "Usuario o contraseña incorrectos";
                    }
                }
            } catch (Exception $e) {
                $error = "Error en el proceso de autenticación";
                error_log("Login error: " . $e->getMessage());
            }
        }
        
        include 'views/auth/login.php';
    }*/

    public function logout() {
        session_destroy();
        header("Location: index.php?modulo=auth&accion=login");
        exit();
    }

    public function profile() {
        $this->checkSession();
        
        try {
            $usuario = $this->usuario->getById($_SESSION['usuario_id']);
            
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                // Validar datos
                $nombre_completo = trim($_POST['nombre_completo'] ?? '');
                $email = trim($_POST['email'] ?? '');
                
                if (empty($nombre_completo) || empty($email)) {
                    $_SESSION['error'] = "Todos los campos son requeridos";
                } else {
                    // Actualizar perfil
                    $query = "UPDATE usuarios 
                             SET nombre_completo = :nombre_completo, 
                                 email = :email 
                             WHERE id = :id";
                    
                    $stmt = $this->db->prepare($query);
                    $stmt->bindParam(":nombre_completo", $nombre_completo);
                    $stmt->bindParam(":email", $email);
                    $stmt->bindParam(":id", $_SESSION['usuario_id']);
                    
                    if ($stmt->execute()) {
                        // Actualizar sesión
                        $_SESSION['nombre_completo'] = $nombre_completo;
                        $_SESSION['email'] = $email;
                        
                        $_SESSION['success'] = "Perfil actualizado exitosamente";
                        header("Location: index.php?modulo=auth&accion=profile");
                        exit();
                    } else {
                        $_SESSION['error'] = "Error al actualizar el perfil";
                    }
                }
            }
            
            // Incluir vistas
            $page_title = "Mi Perfil";
            include 'includes/header.php';
            // include 'includes/sidebar.php';
            include 'views/auth/profile.php';
            include 'includes/footer.php';
            
        } catch (Exception $e) {
            $_SESSION['error'] = "Error al cargar el perfil";
            header("Location: index.php?modulo=dashboard&accion=index");
            exit();
        }
    }

    public function changePassword() {
        $this->checkSession();
        
        $error = '';
        
        try {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $current_password = $_POST['current_password'] ?? '';
                $new_password = $_POST['new_password'] ?? '';
                $confirm_password = $_POST['confirm_password'] ?? '';
                
                // Validar que todos los campos estén completos
                if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
                    $error = "Todos los campos son requeridos";
                } else {
                    // Verificar contraseña actual
                    $user = $this->usuario->getById($_SESSION['usuario_id']);
                    
                    if ($user && password_verify($current_password, $user['password'])) {
                        if ($new_password === $confirm_password) {
                            // Actualizar contraseña
                            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                            
                            $query = "UPDATE usuarios SET password = :password WHERE id = :id";
                            $stmt = $this->db->prepare($query);
                            $stmt->bindParam(":password", $hashed_password);
                            $stmt->bindParam(":id", $_SESSION['usuario_id']);
                            
                            if ($stmt->execute()) {
                                $_SESSION['success'] = "Contraseña cambiada exitosamente";
                                header("Location: index.php?modulo=auth&accion=profile");
                                exit();
                            } else {
                                $error = "Error al actualizar la contraseña";
                            }
                        } else {
                            $error = "Las contraseñas nuevas no coinciden";
                        }
                    } else {
                        $error = "Contraseña actual incorrecta";
                    }
                }
            }
        } catch (Exception $e) {
            $error = "Error en el proceso de cambio de contraseña";
            error_log("Change password error: " . $e->getMessage());
        }
        
        // Incluir vistas
        $page_title = "Cambiar Contraseña";
        include 'includes/header.php';
        include 'includes/sidebar.php';
        include 'views/auth/change_password.php';
        include 'includes/footer.php';
    }

    private function checkSession() {
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: index.php?modulo=auth&accion=login");
            exit();
        }
    }
}
?>