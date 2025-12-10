<?php
class Institucion {
    private $conn;
    private $table = "instituciones";

    public $id;
    public $nombre;
    public $ciudad;
    public $activo;

    public function __construct($db) {
        $this->conn = $db;
    }
    
  public function getAll($limit = null, $offset = 0) {
    $query = "SELECT 
                i.id,
                i.nombre,
                i.ciudad,
                i.activo,
                MAX(CASE WHEN e.ciclo_id = :ciclo_id THEN e.fecha END) as ultima_evidencia_fecha,
                COUNT(CASE WHEN e.ciclo_id = :ciclo_id THEN e.id END) as total_evidencias,
                COALESCE(SUM(ei.total_imagenes), 0) as total_imagenes
              FROM " . $this->table . " i
              LEFT JOIN evidencias e ON i.id = e.institucion_id 
                -- Filtrar evidencias solo del ciclo actual
              LEFT JOIN (
                  SELECT evidencia_id, COUNT(*) as total_imagenes 
                  FROM evidencia_imagenes 
                  WHERE evidencia_id IN (
                      SELECT id FROM evidencias WHERE ciclo_id = :ciclo_id2
                  )
                  GROUP BY evidencia_id
              ) ei ON e.id = ei.evidencia_id
              WHERE i.activo = 1
              GROUP BY i.id
              ORDER BY 
                CASE 
                    WHEN MAX(CASE WHEN e.ciclo_id = :ciclo_id3 THEN e.fecha END) IS NULL THEN 0 
                    ELSE 1 
                END DESC,
                MAX(CASE WHEN e.ciclo_id = :ciclo_id4 THEN e.fecha END) DESC,
                i.nombre ASC";
    
    // Agregar límite si se especifica
    if ($limit !== null) {
        $query .= " LIMIT :limit OFFSET :offset";
    }
    
    $stmt = $this->conn->prepare($query);
    
    // Obtener ciclo_id de la sesión
    $ciclo_id = isset($_SESSION['ciclo_actual']) ? $_SESSION['ciclo_actual'] : 0;
    
    // Vincular ciclo_id - necesitamos diferentes nombres por parámetro
    $stmt->bindValue(":ciclo_id", (int)$ciclo_id, PDO::PARAM_INT);
    $stmt->bindValue(":ciclo_id2", (int)$ciclo_id, PDO::PARAM_INT);
    $stmt->bindValue(":ciclo_id3", (int)$ciclo_id, PDO::PARAM_INT);
    $stmt->bindValue(":ciclo_id4", (int)$ciclo_id, PDO::PARAM_INT);
    
    if ($limit !== null) {
        $stmt->bindValue(":limit", (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(":offset", (int)$offset, PDO::PARAM_INT);
    }
    
    $stmt->execute();
    return $stmt;
}

public function search($keyword, $limit = null, $offset = 0) {
    $query = "SELECT 
                i.id,
                i.nombre,
                i.ciudad,
                i.activo,
                MAX(CASE WHEN e.ciclo_id = :ciclo_id THEN e.fecha END) as ultima_evidencia_fecha,
                COUNT(CASE WHEN e.ciclo_id = :ciclo_id2 THEN e.id END) as total_evidencias,
                COALESCE(SUM(ei.total_imagenes), 0) as total_imagenes
              FROM " . $this->table . " i
              LEFT JOIN evidencias e ON i.id = e.institucion_id 
                -- Filtrar evidencias solo del ciclo actual
              LEFT JOIN (
                  SELECT evidencia_id, COUNT(*) as total_imagenes 
                  FROM evidencia_imagenes 
                  WHERE evidencia_id IN (
                      SELECT id FROM evidencias WHERE ciclo_id = :ciclo_id3
                  )
                  GROUP BY evidencia_id
              ) ei ON e.id = ei.evidencia_id
              WHERE (i.nombre LIKE :keyword OR i.ciudad LIKE :keyword) 
                AND i.activo = 1
              GROUP BY i.id
              ORDER BY 
                CASE 
                    WHEN MAX(CASE WHEN e.ciclo_id = :ciclo_id4 THEN e.fecha END) IS NULL THEN 0 
                    ELSE 1 
                END DESC,
                MAX(CASE WHEN e.ciclo_id = :ciclo_id5 THEN e.fecha END) DESC,
                i.nombre ASC";
    
    // Agregar límite si se especifica
    if ($limit !== null) {
        $query .= " LIMIT :limit OFFSET :offset";
    }
    
    $stmt = $this->conn->prepare($query);
    $keyword = "%" . $keyword . "%";
    $stmt->bindParam(":keyword", $keyword);
    
    // Obtener ciclo_id de la sesión
    $ciclo_id = isset($_SESSION['ciclo_actual']) ? $_SESSION['ciclo_actual'] : 0;
    
    // Vincular ciclo_id - necesitamos diferentes nombres por parámetro
    $stmt->bindValue(":ciclo_id", (int)$ciclo_id, PDO::PARAM_INT);
    $stmt->bindValue(":ciclo_id2", (int)$ciclo_id, PDO::PARAM_INT);
    $stmt->bindValue(":ciclo_id3", (int)$ciclo_id, PDO::PARAM_INT);
    $stmt->bindValue(":ciclo_id4", (int)$ciclo_id, PDO::PARAM_INT);
    $stmt->bindValue(":ciclo_id5", (int)$ciclo_id, PDO::PARAM_INT);
    
    if ($limit !== null) {
        $stmt->bindValue(":limit", (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(":offset", (int)$offset, PDO::PARAM_INT);
    }
    
    $stmt->execute();
    return $stmt;
}

// Métodos de conteo actualizados
public function countAll() {
    $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE activo = 1";
    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
}

public function countSearch($keyword) {
    $query = "SELECT COUNT(*) as total FROM " . $this->table . " 
              WHERE (nombre LIKE :keyword OR ciudad LIKE :keyword) 
              AND activo = 1";
    $stmt = $this->conn->prepare($query);
    $keyword = "%" . $keyword . "%";
    $stmt->bindParam(":keyword", $keyword);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
}

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  SET nombre = :nombre, ciudad = :ciudad, activo = :activo";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":ciudad", $this->ciudad);
        $stmt->bindParam(":activo", $this->activo);
        
        return $stmt->execute();
    }
}
?>