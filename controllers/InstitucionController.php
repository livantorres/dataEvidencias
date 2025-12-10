<?php
require_once 'models/Institucion.php';

class InstitucionController {
    private $db;
    private $institucion;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->institucion = new Institucion($this->db);
    }

	public function index() {
    $this->checkSession();
    $this->checkAdmin();
    
    $keyword = isset($_GET['search']) ? trim($_GET['search']) : '';
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 10;
    $offset = ($page - 1) * $limit;
    
    $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
              strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    
    if ($keyword) {
        $instituciones = $this->institucion->search($keyword, $limit, $offset);
        $total_instituciones = $this->institucion->countSearch($keyword);
    } else {
        $instituciones = $this->institucion->getAll($limit, $offset);
        $total_instituciones = $this->institucion->countAll();
    }
    
    $current_count = $instituciones->rowCount();
    $total_pages = ceil($total_instituciones / $limit);
    $has_more = $page < $total_pages;
    
    if ($isAjax) {
        ob_start();
        include 'views/instituciones/partials/institution_table.php';
        $html = ob_get_clean();
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'html' => $html,
            'has_more' => $has_more,
            'total' => $total_instituciones,
            'page' => $page,
            'showing_count' => $current_count,
            'total_pages' => $total_pages
        ]);
        exit();
    }
    
    // Variables para la vista completa
    $page_title = "Gestión de Instituciones";
    
    include 'includes/header.php';
    include 'views/instituciones/index.php';
    include 'includes/footer.php';
}
    /*public function index() {
		$this->checkSession();
		$this->checkAdmin();
		
		$keyword = isset($_GET['search']) ? $_GET['search'] : '';
		
		if ($keyword) {
			$instituciones = $this->institucion->search($keyword);
		} else {
			$instituciones = $this->institucion->getAll();
		}
		
		$page_title = "Gestión de Instituciones";
		
		include 'includes/header.php';
		// include 'includes/sidebar.php'; // Comentado si no existe
		include 'views/instituciones/index.php';
		include 'includes/footer.php';
	}*/

    // Crear institución via modal (AJAX)
	public function create() {
    // Configurar headers JSON primero
    header('Content-Type: application/json');
    
    // Inicializar respuesta
    $response = ['success' => false, 'message' => '', 'data' => null];
    
    try {
        // Debug: log de lo que llega
        error_log("=== CREATE INSTITUCION INICIADO ===");
        error_log("REQUEST METHOD: " . $_SERVER['REQUEST_METHOD']);
        error_log("POST DATA: " . print_r($_POST, true));
        error_log("FILES DATA: " . print_r($_FILES, true));
        error_log("SESSION: " . print_r($_SESSION, true));
        
        // Verificar método
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new Exception("Método no permitido. Se esperaba POST.");
        }
        
        // Verificar sesión
        if (!isset($_SESSION['usuario_id'])) {
            throw new Exception("Sesión no iniciada o expirada. Por favor, inicie sesión nuevamente.");
        }
        
        // Verificar permisos
        if (!isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != 1) {
            throw new Exception("No tiene permisos de administrador para realizar esta acción.");
        }
        
        // Obtener y validar datos
        $nombre = trim($_POST['nombre'] ?? '');
        $ciudad = trim($_POST['ciudad'] ?? '');
        $activo = isset($_POST['activo']) ? 1 : 0;
        
        error_log("Datos procesados - Nombre: '$nombre', Ciudad: '$ciudad', Activo: '$activo'");
        
        // Validaciones básicas
        if (empty($nombre)) {
            throw new Exception("El nombre de la institución es requerido.");
        }
        
        if (empty($ciudad)) {
            throw new Exception("La ciudad es requerida.");
        }
        
        if (strlen($nombre) < 3) {
            throw new Exception("El nombre debe tener al menos 3 caracteres.");
        }
        
        if (strlen($ciudad) < 3) {
            throw new Exception("La ciudad debe tener al menos 3 caracteres.");
        }
        
        // Verificar duplicados
        $check_query = "SELECT id FROM instituciones WHERE LOWER(nombre) = LOWER(:nombre) AND LOWER(ciudad) = LOWER(:ciudad)";
        $check_stmt = $this->db->prepare($check_query);
        $check_stmt->bindParam(":nombre", $nombre);
        $check_stmt->bindParam(":ciudad", $ciudad);
        $check_stmt->execute();
        
        if ($check_stmt->rowCount() > 0) {
            throw new Exception("Ya existe una institución con el nombre '$nombre' en la ciudad '$ciudad'.");
        }
        
        // Crear institución
        $this->institucion->nombre = $nombre;
        $this->institucion->ciudad = $ciudad;
        $this->institucion->activo = $activo;
        
        error_log("Intentando crear institución en BD...");
        
        if ($this->institucion->create()) {
            $institucion_id = $this->db->lastInsertId();
            error_log("Institución creada con ID: $institucion_id");
            
            // Procesar escudo si se subió
            if (isset($_FILES['escudo']) && $_FILES['escudo']['error'] === UPLOAD_ERR_OK) {
                error_log("Procesando archivo escudo...");
                try {
                    $this->uploadEscudo($institucion_id);
                    error_log("Escudo subido exitosamente");
                } catch (Exception $e) {
                    error_log("Error al subir escudo: " . $e->getMessage());
                    // No lanzar excepción, solo loggear el error
                }
            } else {
                error_log("No se subió archivo escudo o hubo error: " . ($_FILES['escudo']['error'] ?? 'No definido'));
            }
            
            // Obtener datos completos de la institución creada
            $institucion_data = $this->institucion->getById($institucion_id);
            
            // Buscar escudo para incluirlo en la respuesta
            if ($institucion_data) {
                $escudo_path = "storage/instituciones/escudos/{$institucion_id}.*";
                $escudo_files = glob($escudo_path);
                $institucion_data['escudo'] = count($escudo_files) > 0 ? $escudo_files[0] : null;
                $institucion_data['escudo_url'] = count($escudo_files) > 0 ? 
                    $escudo_files[0] . '?' . time() : 
                    'assets/img/default-institution.png';
            }
            
            $response['success'] = true;
            $response['message'] = "Institución '$nombre' creada exitosamente";
            $response['data'] = $institucion_data;
            $response['id'] = $institucion_id;
            
            error_log("Respuesta exitosa preparada");
            
        } else {
            throw new Exception("Error al crear la institución en la base de datos. Por favor, intente nuevamente.");
        }
        
    } catch (Exception $e) {
        error_log("ERROR en create(): " . $e->getMessage());
        error_log("Trace: " . $e->getTraceAsString());
        
        $response['message'] = $e->getMessage();
        $response['error_code'] = $e->getCode();
        
        // Incluir más detalles en desarrollo
        if (ini_get('display_errors')) {
            $response['debug'] = [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTrace()
            ];
        }
        
        http_response_code(400);
    } catch (PDOException $e) {
        error_log("ERROR PDO en create(): " . $e->getMessage());
        
        $response['message'] = "Error de base de datos: " . $e->getMessage();
        $response['error_type'] = 'pdo';
        
        http_response_code(500);
    }
    
    // Log de respuesta final
    error_log("=== CREATE INSTITUCION FINALIZADO ===");
    error_log("RESPONSE: " . json_encode($response));
    
    echo json_encode($response);
    exit();
}
    /*public function create() {
		 // Configurar respuesta JSON desde el inicio
		header('Content-Type: application/json');
	
        $this->checkSession();
        $this->checkAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $response = ['success' => false, 'message' => '', 'data' => null];
            
            try {
                $nombre = trim($_POST['nombre'] ?? '');
                $ciudad = trim($_POST['ciudad'] ?? '');
                $activo = isset($_POST['activo']) ? 1 : 0;
                
                if (empty($nombre) || empty($ciudad)) {
                    throw new Exception("Nombre y ciudad son requeridos");
                }
                
                // Verificar si ya existe institución con ese nombre en la ciudad
                $check_query = "SELECT id FROM instituciones WHERE nombre = :nombre AND ciudad = :ciudad";
                $check_stmt = $this->db->prepare($check_query);
                $check_stmt->bindParam(":nombre", $nombre);
                $check_stmt->bindParam(":ciudad", $ciudad);
                $check_stmt->execute();
                
                if ($check_stmt->rowCount() > 0) {
                    throw new Exception("Ya existe una institución con ese nombre en esa ciudad");
                }
                
                // Crear institución
                $this->institucion->nombre = $nombre;
                $this->institucion->ciudad = $ciudad;
                $this->institucion->activo = $activo;
                
                if ($this->institucion->create()) {
                    $institucion_id = $this->db->lastInsertId();
                    
                    // Procesar escudo si se subió
                    if (!empty($_FILES['escudo']['name'])) {
                        $this->uploadEscudo($institucion_id);
                    }
                    
                    // Obtener datos completos de la institución creada
                    $institucion_data = $this->institucion->getById($institucion_id);
                    
                    $response['success'] = true;
                    $response['message'] = "Institución creada exitosamente";
                    $response['data'] = $institucion_data;
                } else {
                    throw new Exception("Error al crear la institución en la base de datos");
                }
                
            } catch (Exception $e) {
                $response['message'] = $e->getMessage();
            }
            
            header('Content-Type: application/json');
            echo json_encode($response);
            exit();
        }
    }*/

    // Editar institución via modal (AJAX)
    public function edit() {
        $this->checkSession();
        $this->checkAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $response = ['success' => false, 'message' => '', 'data' => null];
            
            try {
                $id = $_POST['id'] ?? 0;
                $nombre = trim($_POST['nombre'] ?? '');
                $ciudad = trim($_POST['ciudad'] ?? '');
                $activo = isset($_POST['activo']) ? 1 : 0;
                
                if (empty($id) || empty($nombre) || empty($ciudad)) {
                    throw new Exception("Datos incompletos");
                }
                
                // Verificar si ya existe otra institución con ese nombre en la ciudad
                $check_query = "SELECT id FROM instituciones WHERE nombre = :nombre AND ciudad = :ciudad AND id != :id";
                $check_stmt = $this->db->prepare($check_query);
                $check_stmt->bindParam(":nombre", $nombre);
                $check_stmt->bindParam(":ciudad", $ciudad);
                $check_stmt->bindParam(":id", $id);
                $check_stmt->execute();
                
                if ($check_stmt->rowCount() > 0) {
                    throw new Exception("Ya existe otra institución con ese nombre en esa ciudad");
                }
                
                // Actualizar institución
                $query = "UPDATE instituciones 
                         SET nombre = :nombre, 
                             ciudad = :ciudad, 
                             activo = :activo 
                         WHERE id = :id";
                
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(":nombre", $nombre);
                $stmt->bindParam(":ciudad", $ciudad);
                $stmt->bindParam(":activo", $activo);
                $stmt->bindParam(":id", $id);
                
                if ($stmt->execute()) {
                    // Procesar nuevo escudo si se subió
                    if (!empty($_FILES['escudo']['name'])) {
                        $this->uploadEscudo($id);
                    }
                    
                    // Obtener datos actualizados
                    $institucion_data = $this->institucion->getById($id);
                    
                    $response['success'] = true;
                    $response['message'] = "Institución actualizada exitosamente";
                    $response['data'] = $institucion_data;
                } else {
                    throw new Exception("Error al actualizar la institución");
                }
                
            } catch (Exception $e) {
                $response['message'] = $e->getMessage();
            }
            
            header('Content-Type: application/json');
            echo json_encode($response);
            exit();
        }
        
        // GET request: devolver datos de la institución
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $institucion = $this->institucion->getById($id);
            
            if ($institucion) {
                // Buscar escudo
                $escudo_path = "storage/instituciones/escudos/{$id}.*";
                $escudo_files = glob($escudo_path);
                $institucion['escudo'] = count($escudo_files) > 0 ? $escudo_files[0] : null;
                
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'data' => $institucion]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Institución no encontrada']);
            }
            exit();
        }
    }

    // Cambiar estado via AJAX
    public function toggleStatus() {
        $this->checkSession();
        $this->checkAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $response = ['success' => false, 'message' => ''];
            
            try {
                $id = $_POST['institucion_id'] ?? 0;
                $action = $_POST['action'] ?? '';
                
                if (empty($id) || empty($action)) {
                    throw new Exception("Datos incompletos");
                }
                
                $new_status = ($action == 'activate') ? 1 : 0;
                
                $query = "UPDATE instituciones SET activo = :activo WHERE id = :id";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(":activo", $new_status);
                $stmt->bindParam(":id", $id);
                
                if ($stmt->execute()) {
                    $response['success'] = true;
                    $response['message'] = ($action == 'activate') 
                        ? 'Institución activada' 
                        : 'Institución desactivada';
                } else {
                    throw new Exception("Error al cambiar el estado");
                }
                
            } catch (Exception $e) {
                $response['message'] = $e->getMessage();
            }
            
            header('Content-Type: application/json');
            echo json_encode($response);
            exit();
        }
    }

    private function uploadEscudo($institucion_id) {
        $target_dir = "storage/instituciones/escudos/";
        
        // Eliminar archivos anteriores
        $old_files = glob($target_dir . $institucion_id . ".*");
        foreach ($old_files as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
        
        // Validar y subir nuevo archivo
        if ($_FILES['escudo']['error'] === UPLOAD_ERR_OK) {
            $file_type = $_FILES['escudo']['type'];
            $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            
            if (!in_array($file_type, $allowed_types)) {
                throw new Exception("Tipo de archivo no permitido. Solo se aceptan imágenes JPEG, PNG o GIF");
            }
            
            $max_size = 5 * 1024 * 1024; // 5MB
            if ($_FILES['escudo']['size'] > $max_size) {
                throw new Exception("La imagen es demasiado grande. Máximo 5MB");
            }
            
            $file_extension = pathinfo($_FILES['escudo']['name'], PATHINFO_EXTENSION);
            $target_file = $target_dir . $institucion_id . "." . strtolower($file_extension);
            
            if (!move_uploaded_file($_FILES['escudo']['tmp_name'], $target_file)) {
                throw new Exception("Error al subir la imagen del escudo");
            }
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
			// En lugar de redirigir, devuelve JSON
			header('Content-Type: application/json');
			echo json_encode([
				'success' => false, 
				'message' => 'No tiene permisos para esta acción'
			]);
			exit();
		}
	}
    private function checkAdmin_old() {
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