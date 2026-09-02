<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 1. Ruta al .env
$envPath = __DIR__ . '/../../.env';

if (file_exists($envPath)) {
    $env = parse_ini_file($envPath);
} else {
    die("Error: No se encontró el archivo .env");
}

// 2. Leemos TODOS los datos desde el .env
// Si por alguna razón no lee MYSQL_HOST, fuerza 127.0.0.1
define('DB_HOST', isset($env['MYSQL_HOST']) ? $env['MYSQL_HOST'] : '127.0.0.1');
define('DB_NAME', $env['MYSQL_DATABASE']); // bd_22030873
define('DB_USER', $env['MYSQL_USER']);     // u22030873
define('DB_PASS', $env['MYSQL_PASSWORD']); // 22030873

// 3. Conexión
try {
    // Al pasarle 127.0.0.1, PDO deja de buscar el archivo y usa la red
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>
