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

    public function getAll() {
        $query = "SELECT * FROM " . $this->table . " WHERE activo = 1 ORDER BY nombre";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function search($keyword) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE (nombre LIKE :keyword OR ciudad LIKE :keyword) 
                  AND activo = 1 
                  ORDER BY nombre";
        $stmt = $this->conn->prepare($query);
        $keyword = "%" . $keyword . "%";
        $stmt->bindParam(":keyword", $keyword);
        $stmt->execute();
        return $stmt;
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