<?php
require_once 'config.php';
require_once 'funciones.php';

if (!estaAutenticado()) {
    redirigir('login.php');
    exit;
}

$mensaje = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $accion = $_POST['accion'] ?? '';
        if (empty($accion)) throw new Exception("Acción no válida.");

        switch ($accion) {
            case 'crear':
                $nombre = trim($_POST['nombre'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $password = $_POST['password'] ?? '';

                if (empty($nombre) || empty($email) || empty($password)) {
                    throw new Exception("Todos los campos son obligatorios para crear.");
                }

                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password) VALUES (:nombre, :email, :password)");
                $stmt->execute(['nombre' => $nombre, 'email' => $email, 'password' => $passwordHash]);
                $mensaje = "Usuario registrado exitosamente.";
                break;

            case 'editar':
                $id_editar = $_POST['id'] ?? '';
                $nombre = trim($_POST['nombre'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $password = $_POST['password'] ?? ''; 

                if (empty($id_editar) || empty($nombre) || empty($email)) {
                    throw new Exception("Nombre y Email son obligatorios para actualizar.");
                }

                if (!empty($password)) {
                    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE usuarios SET nombre = :nombre, email = :email, password = :password WHERE id = :id");
                    $stmt->execute(['nombre' => $nombre, 'email' => $email, 'password' => $passwordHash, 'id' => $id_editar]);
                } else {
                    $stmt = $pdo->prepare("UPDATE usuarios SET nombre = :nombre, email = :email WHERE id = :id");
                    $stmt->execute(['nombre' => $nombre, 'email' => $email, 'id' => $id_editar]);
                }
                $mensaje = "Usuario actualizado correctamente.";
                break;

            case 'eliminar':
                $id_eliminar = $_POST['id'] ?? '';
                if (empty($id_eliminar)) throw new Exception("No se especificó qué usuario eliminar.");
                if ($id_eliminar == $_SESSION['usuario_id']) throw new Exception("¡No puedes eliminar tu propia cuenta en sesión!");

                $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = :id");
                $stmt->execute(['id' => $id_eliminar]);
                $mensaje = "Usuario eliminado correctamente.";
                break;

            default:
                throw new Exception("Acción desconocida.");
        }
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) $error = "Ese correo electrónico ya está en uso.";
        else $error = "Error de base de datos: " . $e->getMessage();
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

$usuario_a_editar = null;
if (isset($_GET['editar'])) {
    $stmt = $pdo->prepare("SELECT id, nombre, email FROM usuarios WHERE id = :id");
    $stmt->execute(['id' => $_GET['editar']]);
    $usuario_a_editar = $stmt->fetch(PDO::FETCH_ASSOC);
}

$stmt = $pdo->query("SELECT id, nombre, email, fecha_registro FROM usuarios ORDER BY id DESC");
$lista_usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel CRUD</title>
    <style>
        body {
            font-family: 'Verdana', sans-serif;
            color: #fff;
            background-color: #1a4f76;
            background-image: url('imagenes/crud.jpg'); /* Tu imagen de fondo */
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            margin: 0;
            padding: 20px;
        }
        .container { max-width: 900px; margin: 0 auto; }
        
        .habbo-box {
            background-color: rgba(14, 91, 139, 0.9);
            border: 2px solid #2389c9;
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.5);
        }
        .habbo-box h3, .habbo-box h2 {
            text-transform: uppercase;
            border-bottom: 2px solid #2389c9;
            padding-bottom: 10px;
            margin-top: 0;
        }
        
        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%; padding: 8px; margin: 5px 0 15px 0;
            border: 2px solid #0a3d5f; border-radius: 3px; box-sizing: border-box;
        }
        
        .btn {
            color: white; padding: 8px 15px; font-weight: bold; text-transform: uppercase;
            border-radius: 4px; cursor: pointer; border: 2px solid; box-shadow: inset 0 2px 0 rgba(255,255,255,0.3);
            text-decoration: none; display: inline-block;
        }
        .btn-green { background-color: #4eb539; border-color: #287a17; }
        .btn-green:hover { background-color: #56c93f; }
        
        .btn-blue { background-color: #2389c9; border-color: #0c4871; }
        .btn-blue:hover { background-color: #2b9ee6; }
        
        .btn-red { background-color: #d13a3a; border-color: #8c1e1e; }
        .btn-red:hover { background-color: #e84444; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #2389c9; padding: 10px; text-align: left; }
        th { background-color: rgba(35, 137, 201, 0.8); text-transform: uppercase; font-size: 13px; }
        tr:nth-child(even) { background-color: rgba(255, 255, 255, 0.1); }
        
        .msg-error { color: #ff6b6b; font-weight: bold; background: rgba(0,0,0,0.3); padding: 10px; margin-bottom: 15px; }
        .msg-success { color: #82e06c; font-weight: bold; background: rgba(0,0,0,0.3); padding: 10px; margin-bottom: 15px; }
        .header-top { display: flex; justify-content: space-between; align-items: center; }
    </style>
</head>
<body>
    <div class="container">
        <!-- CABECERA -->
        <div class="habbo-box header-top">
            <h2 style="border:none; margin:0; padding:0;">Hola, <?php echo htmlspecialchars($_SESSION['nombre']); ?></h2>
            <a href="logout.php" class="btn btn-red">Desconectarse</a>
        </div>

        <?php if ($error) echo "<div class='msg-error'>$error</div>"; ?>
        <?php if ($mensaje) echo "<div class='msg-success'>$mensaje</div>"; ?>

        <!-- FORMULARIO -->
        <div class="habbo-box">
            <?php if ($usuario_a_editar): ?>
                <h3>Editar Usuario (ID: <?php echo $usuario_a_editar['id']; ?>)</h3>
                <form method="post" action="crud.php">
                    <input type="hidden" name="accion" value="editar">
                    <input type="hidden" name="id" value="<?php echo $usuario_a_editar['id']; ?>">
                    <label>Nombre:</label> <input type="text" name="nombre" value="<?php echo htmlspecialchars($usuario_a_editar['nombre']); ?>" required>
                    <label>Email:</label> <input type="email" name="email" value="<?php echo htmlspecialchars($usuario_a_editar['email']); ?>" required>
                    <label>Nueva Contraseña (Opcional):</label> <input type="password" name="password" placeholder="Dejar en blanco para no cambiar">
                    
                    <button type="submit" class="btn btn-green">Actualizar Usuario</button>
                    <a href="crud.php" class="btn btn-red">Cancelar</a>
                </form>
            <?php else: ?>
                <h3>Agregar Nuevo Usuario</h3>
                <form method="post" action="crud.php">
                    <input type="hidden" name="accion" value="crear">
                    <label>Nombre:</label> <input type="text" name="nombre" required>
                    <label>Email:</label> <input type="email" name="email" required>
                    <label>Contraseña:</label> <input type="password" name="password" required>
                    
                    <button type="submit" class="btn btn-green">Guardar Usuario</button>
                </form>
            <?php endif; ?>
        </div>

        <!-- TABLA -->
        <div class="habbo-box">
            <h3>Lista de Usuarios</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lista_usuarios as $user): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo htmlspecialchars($user['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo $user['fecha_registro']; ?></td>
                            <td>
                                <a href="crud.php?editar=<?php echo $user['id']; ?>" class="btn btn-blue" style="padding: 5px 10px; font-size: 12px;">Editar</a>
                                <form method="post" style="display:inline;" onsubmit="return confirm('¿Seguro que deseas eliminar este usuario?');">
                                    <input type="hidden" name="accion" value="eliminar">
                                    <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                    <button type="submit" class="btn btn-red" style="padding: 5px 10px; font-size: 12px;">X</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
