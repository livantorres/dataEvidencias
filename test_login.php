<?php
// test_login.php
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

// Verificar si existe el usuario admin
$query = "SELECT * FROM usuarios WHERE username = 'admin'";
$stmt = $db->prepare($query);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "Usuario encontrado:<br>";
    echo "ID: " . $user['id'] . "<br>";
    echo "Username: " . $user['username'] . "<br>";
    echo "Password Hash: " . $user['password'] . "<br>";
    echo "Hash length: " . strlen($user['password']) . "<br>";
    
    // Probar contraseña
    $password = 'admin123';
    if (password_verify($password, $user['password'])) {
        echo "<span style='color: green;'>✅ Contraseña válida</span><br>";
    } else {
        echo "<span style='color: red;'>❌ Contraseña inválida</span><br>";
        
        // Generar nuevo hash
        $new_hash = password_hash($password, PASSWORD_DEFAULT);
        echo "Nuevo hash: " . $new_hash . "<br>";
        
        // Actualizar en base de datos
        $update_query = "UPDATE usuarios SET password = :password WHERE id = :id";
        $update_stmt = $db->prepare($update_query);
        $update_stmt->bindParam(':password', $new_hash);
        $update_stmt->bindParam(':id', $user['id']);
        
        if ($update_stmt->execute()) {
            echo "<span style='color: green;'>✅ Hash actualizado en la base de datos</span><br>";
        }
    }
} else {
    echo "❌ Usuario admin no encontrado";
}
?>