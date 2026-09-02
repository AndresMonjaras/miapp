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
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <style>
        body {
            font-family: 'Verdana', sans-serif;
            color: #fff;
            background-color: #1a4f76;
            background-image: url('imagenes/login.jpg'); /* Tu imagen de fondo */
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .habbo-box {
            background-color: rgba(14, 91, 139, 0.9);
            border: 2px solid #2389c9;
            border-radius: 5px;
            padding: 30px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.5);
            width: 100%;
            max-width: 350px;
        }
        .habbo-box h2 {
            text-transform: uppercase;
            border-bottom: 2px solid #2389c9;
            padding-bottom: 10px;
            margin-top: 0;
            text-align: center;
        }
        label { font-size: 14px; font-weight: bold; }
        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 8px 0 15px 0;
            border: 2px solid #0a3d5f;
            border-radius: 3px;
            box-sizing: border-box;
            background-color: #f1f1f1;
        }
        .btn-green {
            background-color: #4eb539;
            color: white;
            border: 2px solid #287a17;
            padding: 10px;
            width: 100%;
            font-weight: bold;
            text-transform: uppercase;
            border-radius: 4px;
            cursor: pointer;
            box-shadow: inset 0 2px 0 rgba(255,255,255,0.3);
        }
        .btn-green:hover { background-color: #56c93f; }
        .error-msg { color: #ff6b6b; font-weight: bold; background: rgba(0,0,0,0.3); padding: 10px; border-radius: 3px; }
    </style>
</head>
<body>
    <div class="habbo-box">
        <h2>Iniciar sesión</h2>
        <?php if (isset($error)) echo "<div class='error-msg'>$error</div><br>"; ?>
        <form method="post">
            <label>Email:</label>
            <input type="email" name="email" required>
            
            <label>Contraseña:</label>
            <input type="password" name="password" required>
            
            <input type="submit" class="btn-green" value="Entrar">
        </form>
    </div>
</body>
</html>
