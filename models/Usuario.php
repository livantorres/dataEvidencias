<?php
class Usuario {
    private $conn;
    private $table = "usuarios";

    public $id;
    public $username;
    public $email;
    public $password;
    public $nombre_completo;
    public $rol_id;
    public $activo;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function login($username, $password) {
        $query = "SELECT u.*, r.nombre as rol_nombre FROM " . $this->table . " u
                  LEFT JOIN roles r ON u.rol_id = r.id
                  WHERE u.username = :username AND u.activo = 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->execute();
        
        if ($stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verificar contraseña
            if (password_verify($password, $row['password'])) {
                return $row;
            }
        }
        return false;
    }

    public function checkUsernameExists($username) {
        $query = "SELECT id FROM " . $this->table . " WHERE username = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function checkEmailExists($email) {
        $query = "SELECT id FROM " . $this->table . " WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  SET username = :username, 
                      email = :email, 
                      password = :password, 
                      nombre_completo = :nombre_completo,
                      rol_id = :rol_id,
                      activo = :activo";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":nombre_completo", $this->nombre_completo);
        $stmt->bindParam(":rol_id", $this->rol_id);
        $stmt->bindParam(":activo", $this->activo);
        
        return $stmt->execute();
    }

    public function getAll() {
        $query = "SELECT u.*, r.nombre as rol_nombre 
                  FROM " . $this->table . " u
                  LEFT JOIN roles r ON u.rol_id = r.id
                  ORDER BY u.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function getById($id) {
        $query = "SELECT u.*, r.nombre as rol_nombre 
                  FROM " . $this->table . " u
                  LEFT JOIN roles r ON u.rol_id = r.id
                  WHERE u.id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>