<?php
// index.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Bienvenido</title>
    <style>
        body {
            font-family: 'Verdana', sans-serif;
            color: #fff;
            background-color: #1a4f76;
            background-image: url('imagenes/index.jpg'); /* Tu imagen de fondo */
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
            text-align: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.5);
            max-width: 400px;
        }
        .habbo-box h1 {
            text-transform: uppercase;
            border-bottom: 2px solid #2389c9;
            padding-bottom: 15px;
            margin-top: 0;
            font-size: 24px;
        }
        .btn-green {
            background-color: #4eb539;
            color: white;
            border: 2px solid #287a17;
            padding: 12px 20px;
            font-weight: bold;
            text-transform: uppercase;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-top: 15px;
            box-shadow: inset 0 2px 0 rgba(255,255,255,0.3);
        }
        .btn-green:hover { background-color: #56c93f; }
    </style>
</head>
<body>
    <div class="habbo-box">
        <h1>Hola Mojarra</h1>
        <p>Bienvenido a nuestra comunidad. Por favor, identifícate para continuar.</p>
        <a href="login.php" class="btn-green">Ir al Login</a>
    </div>
</body>
</html>
