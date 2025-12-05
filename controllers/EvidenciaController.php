<?php
require_once 'models/Evidencia.php';
require_once 'models/Institucion.php';
require_once 'models/Ciclo.php';

class EvidenciaController {
    private $db;
    private $evidencia;
    private $institucion;
    private $ciclo;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->evidencia = new Evidencia($this->db);
        $this->institucion = new Institucion($this->db);
        $this->ciclo = new Ciclo($this->db);
    }

    public function index() {
        $this->checkSession();
        
        $keyword = isset($_GET['search']) ? $_GET['search'] : '';
        $institucion_id = isset($_GET['institucion']) ? $_GET['institucion'] : null;
        
        if ($keyword) {
            $instituciones = $this->institucion->search($keyword);
        } else {
            $instituciones = $this->institucion->getAll();
        }
        
        // Obtener ciclo actual para mostrar en la vista
        $ciclo_actual = null;
        if (isset($_SESSION['ciclo_actual'])) {
            $ciclo_actual = $this->ciclo->getById($_SESSION['ciclo_actual']);
        }
        
        // Obtener ciclos para el select
        $ciclos = $this->ciclo->getAll();
        
        // Pasar variables a la vista
        $page_title = "Evidencias";
        
        include 'includes/header.php';
        include 'views/evidencias/index.php';
        include 'includes/footer.php';
    }

    public function create() {
        $this->checkSession();
          // DEBUG INICIAL
		error_log("=== DEBUG CREATE START ===");
		error_log("SERVER REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD']);
		error_log("POST data: " . print_r($_POST, true));
		error_log("FILES data: " . print_r($_FILES, true));
		error_log("FILES['imagenes'] exists: " . (isset($_FILES['imagenes']) ? 'YES' : 'NO'));
		
		if (isset($_FILES['imagenes'])) {
			error_log("FILES['imagenes'] count: " . count($_FILES['imagenes']['name']));
			error_log("FILES['imagenes']['name']: " . print_r($_FILES['imagenes']['name'], true));
			error_log("FILES['imagenes']['tmp_name']: " . print_r($_FILES['imagenes']['tmp_name'], true));
			error_log("FILES['imagenes']['error']: " . print_r($_FILES['imagenes']['error'], true));
		}
	
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                // Debug
                error_log("=== INICIO CREATE EVIDENCIA ===");
                error_log("POST: " . print_r($_POST, true));
                error_log("FILES: " . print_r($_FILES, true));
                
                // Validaciones básicas
                if (empty($_POST['institucion_id'])) {
                    throw new Exception("Debe seleccionar una institución");
                }
                
                // Usar ciclo de POST si está definido, sino de sesión
                $ciclo_id = $_POST['ciclo_id'] ?? ($_SESSION['ciclo_actual'] ?? null);
                if (empty($ciclo_id)) {
                    throw new Exception("Debe seleccionar un ciclo primero");
                }
                
                // Verificar que hay imágenes
                if (!isset($_FILES['imagenes']) || empty($_FILES['imagenes']['name'][0])) {
                    throw new Exception("Debe seleccionar al menos una imagen");
                }
                
                // Asignar datos
                $this->evidencia->institucion_id = $_POST['institucion_id'];
                $this->evidencia->ciclo_id = $ciclo_id;
                $this->evidencia->descripcion = $_POST['descripcion'] ?? '';
                $this->evidencia->fecha = $_POST['fecha'] ?? date('Y-m-d');
                $this->evidencia->usuario_id = $_SESSION['usuario_id'];
                
                // Crear evidencia
                $evidencia_id = $this->evidencia->create();
                
                if (!$evidencia_id) {
                    throw new Exception("Error al crear el registro de evidencia");
                }
                
                error_log("Evidencia creada con ID: $evidencia_id");
                
                // Obtener datos para rutas
                $institucion = $this->institucion->getById($_POST['institucion_id']);
                $ciclo = $this->ciclo->getById($ciclo_id);
                
                if (!$institucion || !$ciclo) {
                    throw new Exception("Error al obtener información de institución o ciclo");
                }
                
                // Crear estructura de carpetas
                $ciclo_folder = 'ciclo_' . preg_replace('/[^a-zA-Z0-9]/', '_', $ciclo['descripcion']);
                $institucion_folder = preg_replace('/[^a-zA-Z0-9]/', '_', $institucion['nombre']);
                $evidencia_folder = $evidencia_id;
                
                $base_path = "storage/instituciones/evidencias/{$ciclo_folder}/{$institucion_folder}/{$evidencia_folder}/";
                
                // Crear directorios recursivamente
                if (!file_exists($base_path)) {
                    mkdir($base_path, 0777, true);
                }
                
                error_log("Base path creado: $base_path");
                
                // Procesar imágenes
                $uploaded_count = 0;
                $image_errors = [];
                
                // Manejar múltiples imágenes
                $total_files = count($_FILES['imagenes']['name']);
                
                for ($i = 0; $i < $total_files; $i++) {
                    // Verificar si hay error en este archivo específico
                    if ($_FILES['imagenes']['error'][$i] === UPLOAD_ERR_OK) {
                        // Validar que sea imagen
                        $tmp_name = $_FILES['imagenes']['tmp_name'][$i];
                        $mime_type = mime_content_type($tmp_name);
                        
                        if (strpos($mime_type, 'image/') === 0) {
                            $original_name = $_FILES['imagenes']['name'][$i];
                            $extension = pathinfo($original_name, PATHINFO_EXTENSION);
                            $new_filename = uniqid('img_', true) . '.' . strtolower($extension);
                            $destination = $base_path . $new_filename;
                            
                            if (move_uploaded_file($tmp_name, $destination)) {
                                // Guardar en base de datos
                                $success = $this->evidencia->saveImage(
                                    $evidencia_id,
                                    $new_filename,
                                    $destination,
                                    $mime_type,
                                    $_FILES['imagenes']['size'][$i]
                                );
                                
                                if ($success) {
                                    $uploaded_count++;
                                    error_log("Imagen guardada: $destination");
                                } else {
                                    $image_errors[] = "Error al guardar $original_name en la base de datos";
                                }
                            } else {
                                $image_errors[] = "Error al mover el archivo $original_name";
                            }
                        } else {
                            $image_errors[] = "Archivo $original_name no es una imagen válida";
                        }
                    } else {
                        $error_msg = $this->getUploadError($_FILES['imagenes']['error'][$i]);
                        $image_errors[] = "Error en archivo: $error_msg";
                    }
                }
                
                if ($uploaded_count == 0) {
                    // Si no se subió ninguna imagen, revertir
                    $this->deleteEvidence($evidencia_id);
                    
                    $error_message = "No se pudieron subir las imágenes.";
                    if (!empty($image_errors)) {
                        $error_message .= " Errores: " . implode(', ', $image_errors);
                    }
                    throw new Exception($error_message);
                }
                
                // Si algunas imágenes fallaron pero otras no, mostrar advertencia
                if (!empty($image_errors)) {
                    error_log("Algunas imágenes fallaron: " . implode(', ', $image_errors));
                    $_SESSION['warning'] = "Evidencia creada con $uploaded_count imagen(es), pero algunos errores: " . 
                        implode(', ', array_slice($image_errors, 0, 3));
                } else {
                    $_SESSION['success'] = "Evidencia registrada exitosamente con $uploaded_count imagen(es)";
                }
                
                // Redirigir
                header("Location: index.php?modulo=evidencia&accion=index&success=1");
                exit();
                
            } catch (Exception $e) {
                error_log("ERROR en create(): " . $e->getMessage());
                $_SESSION['error'] = $e->getMessage();
                header("Location: " . ($_SERVER['HTTP_REFERER'] ?? 'index.php?modulo=evidencia&accion=index'));
                exit();
            }
        }
    }

    // Método auxiliar para obtener mensajes de error de upload
    private function getUploadError($error_code) {
        switch ($error_code) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return 'El archivo es demasiado grande';
            case UPLOAD_ERR_PARTIAL:
                return 'El archivo se subió parcialmente';
            case UPLOAD_ERR_NO_FILE:
                return 'No se seleccionó archivo';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'No hay directorio temporal';
            case UPLOAD_ERR_CANT_WRITE:
                return 'No se pudo escribir en el disco';
            case UPLOAD_ERR_EXTENSION:
                return 'Extensión no permitida';
            default:
                return 'Error desconocido (' . $error_code . ')';
        }
    }

    // Método para eliminar evidencia si falla la subida de imágenes
    private function deleteEvidence($evidencia_id) {
        try {
            // Eliminar imágenes de la base de datos
            $delete_images = $this->db->prepare("DELETE FROM evidencia_imagenes WHERE evidencia_id = ?");
            $delete_images->execute([$evidencia_id]);
            
            // Eliminar evidencia
            $delete_evidence = $this->db->prepare("DELETE FROM evidencias WHERE id = ?");
            $delete_evidence->execute([$evidencia_id]);
            
            error_log("Evidencia $evidencia_id eliminada por fallo en subida de imágenes");
        } catch (Exception $e) {
            error_log("Error al eliminar evidencia fallida: " . $e->getMessage());
        }
    }

    // Ver evidencia individual
    public function view() {
        $this->checkSession();
        
        if (!isset($_GET['id'])) {
            $_SESSION['error'] = "ID de evidencia no especificado";
            header("Location: index.php?modulo=evidencia&accion=index");
            exit();
        }
        
        $id = $_GET['id'];
        $evidencia = $this->evidencia->getById($id);
        
        if (!$evidencia) {
            $_SESSION['error'] = "Evidencia no encontrada";
            header("Location: index.php?modulo=evidencia&accion=index");
            exit();
        }
        
        // Verificar permisos (solo admin o el usuario que creó la evidencia)
        if ($_SESSION['rol_id'] != 1 && $evidencia['usuario_id'] != $_SESSION['usuario_id']) {
            $_SESSION['error'] = "No tiene permisos para ver esta evidencia";
            header("Location: index.php?modulo=evidencia&accion=index");
            exit();
        }
        
        // Obtener imágenes
        $imagenes = $this->evidencia->getImages($id);
        
        $page_title = "Ver Evidencia - " . $evidencia['institucion_nombre'];
        
        include 'includes/header.php';
        include 'views/evidencias/view.php';
        include 'includes/footer.php';
    }

    // Editar evidencia
    public function edit() {
        $this->checkSession();
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $response = ['success' => false, 'message' => '', 'data' => null];
            
            try {
                $id = $_POST['id'] ?? 0;
                $descripcion = trim($_POST['descripcion'] ?? '');
                $fecha = $_POST['fecha'] ?? '';
                
                if (empty($id) || empty($fecha)) {
                    throw new Exception("Datos incompletos");
                }
                
                // Obtener evidencia actual
                $evidencia_actual = $this->evidencia->getById($id);
                if (!$evidencia_actual) {
                    throw new Exception("Evidencia no encontrada");
                }
                
                // Verificar permisos (solo admin o el usuario que creó la evidencia)
                if ($_SESSION['rol_id'] != 1 && $evidencia_actual['usuario_id'] != $_SESSION['usuario_id']) {
                    throw new Exception("No tiene permisos para editar esta evidencia");
                }
                
                // Actualizar evidencia
                $query = "UPDATE evidencias 
                         SET descripcion = :descripcion, 
                             fecha = :fecha 
                         WHERE id = :id";
                
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(":descripcion", $descripcion);
                $stmt->bindParam(":fecha", $fecha);
                $stmt->bindParam(":id", $id);
                
                if ($stmt->execute()) {
                    $response['success'] = true;
                    $response['message'] = "Evidencia actualizada exitosamente";
                    $response['data'] = $this->evidencia->getById($id);
                } else {
                    throw new Exception("Error al actualizar la evidencia");
                }
                
            } catch (Exception $e) {
                $response['message'] = $e->getMessage();
            }
            
            header('Content-Type: application/json');
            echo json_encode($response);
            exit();
        }
        
        // GET request: mostrar formulario de edición
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $evidencia = $this->evidencia->getById($id);
            
            if (!$evidencia) {
                $_SESSION['error'] = "Evidencia no encontrada";
                header("Location: index.php?modulo=evidencia&accion=index");
                exit();
            }
            
            // Verificar permisos
            if ($_SESSION['rol_id'] != 1 && $evidencia['usuario_id'] != $_SESSION['usuario_id']) {
                $_SESSION['error'] = "No tiene permisos para editar esta evidencia";
                header("Location: index.php?modulo=evidencia&accion=index");
                exit();
            }
            
            // Obtener imágenes
            $imagenes = $this->evidencia->getImages($id);
            
            // Obtener ciclo actual
            $ciclo_actual = null;
            if (isset($_SESSION['ciclo_actual'])) {
                $ciclo_actual = $this->ciclo->getById($_SESSION['ciclo_actual']);
            }
            
            $page_title = "Editar Evidencia";
            
            include 'includes/header.php';
            include 'views/evidencias/edit.php';
            include 'includes/footer.php';
        }
    }

    // Eliminar evidencia
	public function delete() {
    $this->checkSession();
    
    // DEBUG: Verificar si es AJAX
    error_log("=== DELETE EVIDENCIA ===");
    error_log("Método: " . $_SERVER['REQUEST_METHOD']);
    error_log("POST: " . print_r($_POST, true));
    //error_log("Headers: " . print_r(getallheaders(), true));
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $response = ['success' => false, 'message' => ''];
        
        try {
            $id = $_POST['id'] ?? 0;
            error_log("ID recibido: " . $id);
            
            if (empty($id)) {
                throw new Exception("ID de evidencia no especificado");
            }
            
            // Obtener evidencia
            $evidencia = $this->evidencia->getById($id);
            if (!$evidencia) {
                throw new Exception("Evidencia no encontrada");
            }
            
            error_log("Evidencia encontrada: " . print_r($evidencia, true));
            
            // Verificar permisos (solo admin o el usuario que creó la evidencia)
            if ($_SESSION['rol_id'] != 1 && $evidencia['usuario_id'] != $_SESSION['usuario_id']) {
                throw new Exception("No tiene permisos para eliminar esta evidencia");
            }
            
            // Obtener imágenes para eliminar archivos
            $imagenes = $this->evidencia->getImages($id);
            $imagenesCount = 0;
            
            while ($imagen = $imagenes->fetch(PDO::FETCH_ASSOC)) {
                $imagenesCount++;
                error_log("Imagen encontrada: " . $imagen['ruta']);
                
                // Eliminar archivo físico
                if (file_exists($imagen['ruta'])) {
                    if (unlink($imagen['ruta'])) {
                        error_log("Archivo eliminado: " . $imagen['ruta']);
                    } else {
                        error_log("ERROR: No se pudo eliminar archivo: " . $imagen['ruta']);
                    }
                } else {
                    error_log("Advertencia: Archivo no existe: " . $imagen['ruta']);
                }
            }
            
            error_log("Total imágenes: " . $imagenesCount);
            
            // Eliminar registros de la base de datos
            $this->db->beginTransaction();
            
            // Eliminar imágenes primero
            $delete_images = $this->db->prepare("DELETE FROM evidencia_imagenes WHERE evidencia_id = ?");
            $delete_images->execute([$id]);
            error_log("Imágenes eliminadas de BD: " . $delete_images->rowCount());
            
            // Eliminar evidencia
            $delete_evidence = $this->db->prepare("DELETE FROM evidencias WHERE id = ?");
            $delete_evidence->execute([$id]);
            error_log("Evidencia eliminada de BD: " . $delete_evidence->rowCount());
            
            $this->db->commit();
            
            $response['success'] = true;
            $response['message'] = "Evidencia eliminada exitosamente";
            error_log("DELETE exitoso para ID: " . $id);
            
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("ERROR en delete: " . $e->getMessage());
            $response['message'] = $e->getMessage();
        }
        
        error_log("Respuesta final: " . print_r($response, true));
        
        // Limpiar buffer de salida
        if (ob_get_length()) {
            ob_end_clean();
        }
        
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($response);
        exit();
    }
}
/*
    public function delete_old() {
        $this->checkSession();
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $response = ['success' => false, 'message' => ''];
            
            try {
                $id = $_POST['id'] ?? 0;
                
                if (empty($id)) {
                    throw new Exception("ID de evidencia no especificado");
                }
                
                // Obtener evidencia
                $evidencia = $this->evidencia->getById($id);
                if (!$evidencia) {
                    throw new Exception("Evidencia no encontrada");
                }
                
                // Verificar permisos (solo admin o el usuario que creó la evidencia)
                if ($_SESSION['rol_id'] != 1 && $evidencia['usuario_id'] != $_SESSION['usuario_id']) {
                    throw new Exception("No tiene permisos para eliminar esta evidencia");
                }
                
                // Obtener imágenes para eliminar archivos
                $imagenes = $this->evidencia->getImages($id);
                while ($imagen = $imagenes->fetch(PDO::FETCH_ASSOC)) {
                    // Eliminar archivo físico
                    if (file_exists($imagen['ruta'])) {
                        unlink($imagen['ruta']);
                    }
                }
                
                // Eliminar registros de la base de datos
                $this->db->beginTransaction();
                
                // Eliminar imágenes primero
                $delete_images = $this->db->prepare("DELETE FROM evidencia_imagenes WHERE evidencia_id = ?");
                $delete_images->execute([$id]);
                
                // Eliminar evidencia
                $delete_evidence = $this->db->prepare("DELETE FROM evidencias WHERE id = ?");
                $delete_evidence->execute([$id]);
                
                $this->db->commit();
                
                $response['success'] = true;
                $response['message'] = "Evidencia eliminada exitosamente";
                
            } catch (Exception $e) {
                $this->db->rollBack();
                $response['message'] = $e->getMessage();
            }
            
            header('Content-Type: application/json');
            echo json_encode($response);
            exit();
        }
    }*/

   public function deleteImage() {
    $this->checkSession();
    
    // Asegurar que solo procesamos POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405); // Method Not Allowed
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
        exit();
    }
    
    // Verificar que es una petición AJAX
    $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
              strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    
    $response = ['success' => false, 'message' => ''];
    
    try {
        // Verificar que tenemos el ID de la imagen
        if (!isset($_POST['imagen_id']) || empty($_POST['imagen_id'])) {
            throw new Exception("ID de imagen no especificado");
        }
        
        $imagen_id = (int)$_POST['imagen_id'];
        
        // Obtener información de la imagen con verificación de permisos
        $query = "SELECT ei.*, e.usuario_id, e.id as evidencia_id 
                 FROM evidencia_imagenes ei
                 INNER JOIN evidencias e ON ei.evidencia_id = e.id
                 WHERE ei.id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $imagen_id, PDO::PARAM_INT);
        $stmt->execute();
        $imagen = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$imagen) {
            throw new Exception("Imagen no encontrada");
        }
        
        // Verificar permisos (solo admin o el usuario que creó la evidencia)
        if ($_SESSION['rol_id'] != 1 && $imagen['usuario_id'] != $_SESSION['usuario_id']) {
            throw new Exception("No tiene permisos para eliminar esta imagen");
        }
        
        // Eliminar archivo físico
        if (file_exists($imagen['ruta'])) {
            if (!unlink($imagen['ruta'])) {
                throw new Exception("Error al eliminar el archivo físico");
            }
        }
        
        // Eliminar registro de la base de datos
        $delete_query = "DELETE FROM evidencia_imagenes WHERE id = :id";
        $delete_stmt = $this->db->prepare($delete_query);
        $delete_stmt->bindParam(":id", $imagen_id, PDO::PARAM_INT);
        
        if ($delete_stmt->execute()) {
            $response['success'] = true;
            $response['message'] = "Imagen eliminada exitosamente";
            $response['evidencia_id'] = $imagen['evidencia_id'];
        } else {
            throw new Exception("Error al eliminar la imagen de la base de datos");
        }
        
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }
    
    // Limpiar cualquier output previo
    if (ob_get_length()) {
        ob_clean();
    }
    
    // Forzar headers JSON
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    echo json_encode($response);
    exit();
}
    // Agregar imágenes a evidencia existente
    public function addImages() {
        $this->checkSession();
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $response = ['success' => false, 'message' => '', 'count' => 0];
            
            try {
                $evidencia_id = $_POST['evidencia_id'] ?? 0;
                
                if (empty($evidencia_id)) {
                    throw new Exception("ID de evidencia no especificado");
                }
                
                // Obtener evidencia
                $evidencia = $this->evidencia->getById($evidencia_id);
                if (!$evidencia) {
                    throw new Exception("Evidencia no encontrada");
                }
                
                // Verificar permisos
                if ($_SESSION['rol_id'] != 1 && $evidencia['usuario_id'] != $_SESSION['usuario_id']) {
                    throw new Exception("No tiene permisos para agregar imágenes a esta evidencia");
                }
                
                // Verificar que hay nuevas imágenes
                if (!isset($_FILES['nuevas_imagenes']) || empty($_FILES['nuevas_imagenes']['name'][0])) {
                    throw new Exception("No se seleccionaron imágenes para subir");
                }
                
                // Obtener datos para la ruta
                $institucion = $this->institucion->getById($evidencia['institucion_id']);
                $ciclo = $this->ciclo->getById($evidencia['ciclo_id']);
                
                // Crear estructura de carpetas
                $ciclo_folder = 'ciclo_' . $ciclo['descripcion'];
                $institucion_folder = preg_replace('/[^a-zA-Z0-9_-]/', '_', $institucion['nombre']);
                $evidencia_folder = $evidencia_id;
                
                $base_path = "storage/instituciones/evidencias/{$ciclo_folder}/{$institucion_folder}/{$evidencia_folder}/";
                
                // Crear directorio si no existe
                if (!file_exists($base_path)) {
                    mkdir($base_path, 0777, true);
                }
                
                // Subir nuevas imágenes
                $uploaded_count = 0;
                $total_files = count($_FILES['nuevas_imagenes']['name']);
                
                for ($i = 0; $i < $total_files; $i++) {
                    if ($_FILES['nuevas_imagenes']['error'][$i] === UPLOAD_ERR_OK) {
                        // Validar que sea imagen
                        $tmp_name = $_FILES['nuevas_imagenes']['tmp_name'][$i];
                        $mime_type = mime_content_type($tmp_name);
                        
                        if (strpos($mime_type, 'image/') === 0) {
                            $original_name = $_FILES['nuevas_imagenes']['name'][$i];
                            $extension = pathinfo($original_name, PATHINFO_EXTENSION);
                            $new_filename = uniqid('img_', true) . '.' . strtolower($extension);
                            $destination = $base_path . $new_filename;
                            
                            if (move_uploaded_file($tmp_name, $destination)) {
                                // Guardar en base de datos
                                $success = $this->evidencia->saveImage(
                                    $evidencia_id,
                                    $new_filename,
                                    $destination,
                                    $mime_type,
                                    $_FILES['nuevas_imagenes']['size'][$i]
                                );
                                
                                if ($success) {
                                    $uploaded_count++;
                                }
                            }
                        }
                    }
                }
                
                if ($uploaded_count > 0) {
                    $response['success'] = true;
                    $response['message'] = "{$uploaded_count} imagen(es) agregada(s) exitosamente";
                    $response['count'] = $uploaded_count;
                } else {
                    throw new Exception("No se pudo subir ninguna imagen");
                }
                
            } catch (Exception $e) {
                $response['message'] = $e->getMessage();
            }
            
            header('Content-Type: application/json');
            echo json_encode($response);
            exit();
        }
    }

    // Descargar evidencia como ZIP
    public function download() {
        $this->checkSession();
        
        if (!isset($_GET['id'])) {
            $_SESSION['error'] = "ID de evidencia no especificado";
            header("Location: index.php?modulo=evidencia&accion=index");
            exit();
        }
        
        $id = $_GET['id'];
        $evidencia = $this->evidencia->getById($id);
        
        if (!$evidencia) {
            $_SESSION['error'] = "Evidencia no encontrada";
            header("Location: index.php?modulo=evidencia&accion=index");
            exit();
        }
        
        // Verificar permisos
        if ($_SESSION['rol_id'] != 1 && $evidencia['usuario_id'] != $_SESSION['usuario_id']) {
            $_SESSION['error'] = "No tiene permisos para descargar esta evidencia";
            header("Location: index.php?modulo=evidencia&accion=index");
            exit();
        }
        
        // Obtener imágenes
        $imagenes = $this->evidencia->getImages($id);
        
        // Crear archivo ZIP
        $zip = new ZipArchive();
        $zip_filename = "evidencia_{$id}_{$evidencia['institucion_nombre']}_{$evidencia['fecha']}.zip";
        $temp_zip_path = sys_get_temp_dir() . '/' . $zip_filename;
        
        if ($zip->open($temp_zip_path, ZipArchive::CREATE) === TRUE) {
            // Agregar archivo TXT con información
            $info_content = "EVIDENCIA DIGITAL\n";
            $info_content .= "=================\n\n";
            $info_content .= "Institución: {$evidencia['institucion_nombre']}\n";
            $info_content .= "Ciudad: {$evidencia['ciudad']}\n";
            $info_content .= "Ciclo: {$evidencia['ciclo_descripcion']}\n";
            $info_content .= "Fecha: {$evidencia['fecha']}\n";
            $info_content .= "Descripción: {$evidencia['descripcion']}\n";
            $info_content .= "\nGenerado el: " . date('Y-m-d H:i:s') . "\n";
            
            $zip->addFromString("informacion.txt", $info_content);
            
            // Agregar imágenes
            while ($imagen = $imagenes->fetch(PDO::FETCH_ASSOC)) {
                if (file_exists($imagen['ruta'])) {
                    $zip->addFile($imagen['ruta'], "imagenes/" . $imagen['nombre_archivo']);
                }
            }
            
            $zip->close();
            
            // Descargar archivo
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="' . $zip_filename . '"');
            header('Content-Length: ' . filesize($temp_zip_path));
            header('Pragma: no-cache');
            header('Expires: 0');
            
            readfile($temp_zip_path);
            
            // Eliminar archivo temporal
            unlink($temp_zip_path);
            exit();
        } else {
            $_SESSION['error'] = "Error al crear el archivo ZIP";
            header("Location: index.php?modulo=evidencia&accion=view&id=" . $id);
            exit();
        }
    }

    private function checkSession() {
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: index.php?modulo=auth&accion=login");
            exit();
        }
    }
	
	
	// En EvidenciaController.php, agrega estos métodos:

public function getByInstitucionAjax() {
    $this->checkSession();
    
    if (!isset($_GET['institucion_id'])) {
        echo json_encode(['success' => false, 'message' => 'ID de institución no especificado']);
        exit();
    }
    
    $institucion_id = $_GET['institucion_id'];
    $evidencias_result = $this->evidencia->getByInstitucion($institucion_id);
    
    $evidencias = [];
    if ($evidencias_result) {
        while ($ev = $evidencias_result->fetch(PDO::FETCH_ASSOC)) {
            // Obtener imágenes de esta evidencia
            $imagenes_result = $this->evidencia->getImages($ev['id']);
            $imagenes = [];
            while ($img = $imagenes_result->fetch(PDO::FETCH_ASSOC)) {
                $imagenes[] = $img;
            }
            
            // Contar imágenes
            $imagenes_count = $this->evidencia->countImages($ev['id']);
            
            $ev['imagenes'] = $imagenes;
            $ev['imagenes_count'] = $imagenes_count;
            $evidencias[] = $ev;
        }
    }
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'evidencias' => $evidencias,
        'count' => count($evidencias)
    ]);
    exit();
}

public function getQuickView() {
    $this->checkSession();
    
    if (!isset($_GET['id'])) {
        echo json_encode(['success' => false, 'message' => 'ID de evidencia no especificado']);
        exit();
    }
    
    $id = $_GET['id'];
    $evidencia = $this->evidencia->getById($id);
    
    if (!$evidencia) {
        echo json_encode(['success' => false, 'message' => 'Evidencia no encontrada']);
        exit();
    }
    
    // Verificar permisos
    if ($_SESSION['rol_id'] != 1 && $evidencia['usuario_id'] != $_SESSION['usuario_id']) {
        echo json_encode(['success' => false, 'message' => 'No tiene permisos para ver esta evidencia']);
        exit();
    }
    
    // Obtener imágenes
    $imagenes_result = $this->evidencia->getImages($id);
    $imagenes = [];
    while ($img = $imagenes_result->fetch(PDO::FETCH_ASSOC)) {
        $imagenes[] = $img;
    }
    
    $evidencia['imagenes'] = $imagenes;
    $evidencia['imagenes_count'] = count($imagenes);
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'evidencia' => $evidencia
    ]);
    exit();
}

// En el modelo Evidencia.php, agrega:
public function countImages($evidencia_id) {
    $query = "SELECT COUNT(*) as total FROM evidencia_imagenes WHERE evidencia_id = :evidencia_id";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':evidencia_id', $evidencia_id);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total'];
}

}