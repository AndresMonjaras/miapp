<?php
// Iniciar sesion si no esta iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

define('DB_HOST', 'db');
define('DB_USER', 'miapp_user');
define('DB_PASS', 'miapp_pass');
define('DB_NAME', 'miapp_db');

try {
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>
