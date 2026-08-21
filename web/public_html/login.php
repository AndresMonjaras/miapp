<?php
require_once 'config.php';
require_once 'funciones.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            throw new Exception("Todos los campos son obligatorios.");
        }


        $stmt = $pdo->prepare("SELECT id, nombre, password FROM usuarios WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $usuario = $stmt->fetch();

    
        if (!$usuario || !password_verify($password, $usuario['password'])) {
            throw new Exception("Credenciales incorrectas.");
        }

        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['nombre'] = $usuario['nombre'];
        redirigir('crud.php');
        exit; 

    } catch (Exception $e) {
        
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Login</title></head>
<body>
    <h2>Iniciar sesión</h2>
    <?php if (isset($error)) echo "<p style='color:red'>$error</p>"; ?>
    <form method="post">
        <label>Email: <input type="email" name="email" required></label><br>
        <label>Contraseña: <input type="password" name="password" required></label><br>
        <input type="submit" value="Entrar">
    </form>
</body>
</html>
