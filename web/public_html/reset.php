<?php
require_once 'config.php';

try {
    // 1. Generar el hash real de PHP para "1234"
    $nuevo_hash = password_hash('1234', PASSWORD_DEFAULT);
    
    // 2. Actualizar el usuario en la base de datos
    $stmt = $pdo->prepare("UPDATE usuarios SET password = :hash WHERE email = 'admin@miapp.local'");
    $stmt->execute(['hash' => $nuevo_hash]);
    
    // 3. Comprobar si funcionó
    echo "<h3>¡Éxito!</h3>";
    echo "La contraseña de admin@miapp.local ha sido actualizada a <strong>1234</strong>.<br>";
    echo "El hash generado por tu propio servidor es: " . $nuevo_hash;
    
} catch (PDOException $e) {
    echo "Error de base de datos: " . $e->getMessage();
}
?>
