<?php
class Ciclo {
    private $conn;
    private $table = "ciclos";

    public $id;
    public $descripcion;
    public $activo;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        try {
            error_log("=== CICLO::getAll() INICIADO ===");
            
            // Primero, verificar si la tabla existe
            $checkTable = $this->conn->query("SHOW TABLES LIKE '{$this->table}'");
            if ($checkTable->rowCount() == 0) {
                error_log("ERROR: La tabla '{$this->table}' no existe");
                return false;
            }
            
            $query = "SELECT * FROM " . $this->table . " ORDER BY descripcion DESC";
            error_log("Query: " . $query);
            
            $stmt = $this->conn->prepare($query);
            
            if (!$stmt) {
                $errorInfo = $this->conn->errorInfo();
                error_log("Error al preparar consulta: " . print_r($errorInfo, true));
                return false;
            }
            
            $result = $stmt->execute();
            
            if (!$result) {
                $errorInfo = $stmt->errorInfo();
                error_log("Error en execute(): " . print_r($errorInfo, true));
                return false;
            }
            
            $rowCount = $stmt->rowCount();
            error_log("Consulta ejecutada correctamente. Filas: " . $rowCount);
            
            // Verificar que hay resultados
            if ($rowCount > 0) {
                error_log("Hay " . $rowCount . " ciclos en la base de datos");
            } else {
                error_log("No hay ciclos en la base de datos");
            }
            
            return $stmt;
            
        } catch (PDOException $e) {
            error_log("PDOException en getAll(): " . $e->getMessage());
            error_log("Código de error: " . $e->getCode());
            error_log("Archivo: " . $e->getFile() . " Línea: " . $e->getLine());
            return false;
        } catch (Exception $e) {
            error_log("Exception en getAll(): " . $e->getMessage());
            return false;
        }
    }

    public function getActiveCiclo() {
        try {
            error_log("Ejecutando getActiveCiclo() en Ciclo model");
            $query = "SELECT * FROM " . $this->table . " WHERE activo = 1 LIMIT 1";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                error_log("Ciclo activo encontrado: ID=" . $result['id'] . ", Descripción=" . $result['descripcion']);
            } else {
                error_log("No se encontró ciclo activo");
            }
            
            return $result;
            
        } catch (PDOException $e) {
            error_log("PDOException en getActiveCiclo(): " . $e->getMessage());
            return null;
        } catch (Exception $e) {
            error_log("Exception en getActiveCiclo(): " . $e->getMessage());
            return null;
        }
    }

    public function getById($id) {
        try {
            $query = "SELECT * FROM " . $this->table . " WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en getById(): " . $e->getMessage());
            return null;
        }
    }

    public function create() {
        try {
            $query = "INSERT INTO " . $this->table . " 
                      SET descripcion = :descripcion, activo = :activo";
            
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(":descripcion", $this->descripcion);
            $stmt->bindParam(":activo", $this->activo, PDO::PARAM_INT);
            
            return $stmt->execute();
            
        } catch (Exception $e) {
            error_log("Error en create(): " . $e->getMessage());
            return false;
        }
    }

    public function update() {
        try {
            $query = "UPDATE " . $this->table . " 
                      SET descripcion = :descripcion, activo = :activo 
                      WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(":descripcion", $this->descripcion);
            $stmt->bindParam(":activo", $this->activo, PDO::PARAM_INT);
            $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
            
            return $stmt->execute();
            
        } catch (Exception $e) {
            error_log("Error en update(): " . $e->getMessage());
            return false;
        }
    }

    public function delete() {
        try {
            $query = "DELETE FROM " . $this->table . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error en delete(): " . $e->getMessage());
            return false;
        }
    }
}
?>