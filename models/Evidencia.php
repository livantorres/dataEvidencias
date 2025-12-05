<?php
class Evidencia {
    private $conn;
    private $table = "evidencias";
    private $table_imagenes = "evidencia_imagenes";

    public $id;
    public $institucion_id;
    public $ciclo_id;
    public $descripcion;
    public $fecha;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  SET institucion_id = :institucion_id, 
                      ciclo_id = :ciclo_id, 
                      descripcion = :descripcion, 
                      fecha = :fecha";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":institucion_id", $this->institucion_id);
        $stmt->bindParam(":ciclo_id", $this->ciclo_id);
        $stmt->bindParam(":descripcion", $this->descripcion);
        $stmt->bindParam(":fecha", $this->fecha);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function saveImage($evidencia_id, $nombre_archivo, $ruta, $tipo, $tamano) {
        $query = "INSERT INTO " . $this->table_imagenes . " 
                  SET evidencia_id = :evidencia_id, 
                      nombre_archivo = :nombre_archivo, 
                      ruta = :ruta, 
                      tipo = :tipo, 
                      tamaño = :tamano";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":evidencia_id", $evidencia_id);
        $stmt->bindParam(":nombre_archivo", $nombre_archivo);
        $stmt->bindParam(":ruta", $ruta);
        $stmt->bindParam(":tipo", $tipo);
        $stmt->bindParam(":tamano", $tamano);
        
        return $stmt->execute();
    }

    public function getByInstitucion($institucion_id) {
        $query = "SELECT e.*, c.descripcion as ciclo_descripcion, 
                         i.nombre as institucion_nombre
                  FROM " . $this->table . " e
                  LEFT JOIN ciclos c ON e.ciclo_id = c.id
                  LEFT JOIN instituciones i ON e.institucion_id = i.id
                  WHERE e.institucion_id = :institucion_id
                  ORDER BY e.fecha DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":institucion_id", $institucion_id);
        $stmt->execute();
        return $stmt;
    }

    public function getById($id) {
        $query = "SELECT e.*, c.descripcion as ciclo_descripcion, 
                         i.nombre as institucion_nombre, i.ciudad
                  FROM " . $this->table . " e
                  LEFT JOIN ciclos c ON e.ciclo_id = c.id
                  LEFT JOIN instituciones i ON e.institucion_id = i.id
                  WHERE e.id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getImages($evidencia_id) {
        $query = "SELECT * FROM " . $this->table_imagenes . " 
                  WHERE evidencia_id = :evidencia_id
                  ORDER BY created_at";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":evidencia_id", $evidencia_id);
        $stmt->execute();
        return $stmt;
    }
	
	public function countImages($evidencia_id) {
    try {
        $query = "SELECT COUNT(*) as total FROM evidencia_imagenes WHERE evidencia_id = :evidencia_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':evidencia_id', $evidencia_id, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['total'];
    } catch (PDOException $e) {
        error_log("Error countImages: " . $e->getMessage());
        return 0;
    }
}

/**
 * Obtener todas las evidencias de una institución con conteo de imágenes
 * @param int $institucion_id ID de la institución
 * @return PDOStatement|null
 */
public function getByInstitucionWithCount($institucion_id) {
    try {
        $query = "SELECT e.*, 
                         COUNT(ei.id) as total_imagenes
                  FROM evidencias e
                  LEFT JOIN evidencia_imagenes ei ON e.id = ei.evidencia_id
                  WHERE e.institucion_id = :institucion_id
                  GROUP BY e.id
                  ORDER BY e.fecha DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':institucion_id', $institucion_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt;
    } catch (PDOException $e) {
        error_log("Error getByInstitucionWithCount: " . $e->getMessage());
        return null;
    }
}
}
?>