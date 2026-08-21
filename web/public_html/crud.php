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

        if (empty($accion)) {
            throw new Exception("Acción no válida.");
        }

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
                $password = $_POST['password'] ?? ''; // Opcional al editar

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
                
                if (empty($id_eliminar)) {
                    throw new Exception("No se especificó qué usuario eliminar.");
                }

                if ($id_eliminar == $_SESSION['usuario_id']) {
                    throw new Exception("¡No puedes eliminar tu propia cuenta en sesión!");
                }

                $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = :id");
                $stmt->execute(['id' => $id_eliminar]);
                
                $mensaje = "Usuario eliminado correctamente.";
                break;
                
            default:
                throw new Exception("Acción desconocida.");
        }
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $error = "Ese correo electrónico ya está en uso por otro usuario.";
        } else {
            $error = "Error de base de datos: " . $e->getMessage();
        }
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
</head>
<body>
    <h2>Bienvenido al panel, <?php echo htmlspecialchars($_SESSION['nombre']); ?></h2>
    <p><a href="logout.php">Cerrar sesión</a></p>
    <hr>

    <?php if ($error) echo "<p style='color:red; font-weight:bold;'>$error</p>"; ?>
    <?php if ($mensaje) echo "<p style='color:green; font-weight:bold;'>$mensaje</p>"; ?>

     <?php if ($usuario_a_editar): ?>
        <h3>Editar Usuario (ID: <?php echo $usuario_a_editar['id']; ?>)</h3>
        <form method="post" action="crud.php">
            <input type="hidden" name="accion" value="editar">
            <input type="hidden" name="id" value="<?php echo $usuario_a_editar['id']; ?>">
            <label>Nombre: <input type="text" name="nombre" value="<?php echo htmlspecialchars($usuario_a_editar['nombre']); ?>" required></label><br>
            <label>Email: <input type="email" name="email" value="<?php echo htmlspecialchars($usuario_a_editar['email']); ?>" required></label><br>
            <label>Nueva Contraseña: <input type="password" name="password" placeholder="Dejar en blanco para no cambiar"></label><br><br>
            <button type="submit">Actualizar Usuario</button>
            <a href="crud.php"><button type="button">Cancelar</button></a>
        </form>
    <?php else: ?>
        <h3>Agregar Nuevo Usuario</h3>
        <form method="post" action="crud.php">
            <input type="hidden" name="accion" value="crear">
            <label>Nombre: <input type="text" name="nombre" required></label><br>
            <label>Email: <input type="email" name="email" required></label><br>
            <label>Contraseña: <input type="password" name="password" required></label><br><br>
            <button type="submit">Guardar Usuario</button>
        </form>
    <?php endif; ?>

    <hr>

  
    <h3>Lista de Usuarios</h3>
    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Fecha de Registro</th>
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
                     
                        <a href="crud.php?editar=<?php echo $user['id']; ?>" style="text-decoration: none;">
                            <button type="button" style="color:blue;">Editar</button>
                        </a>
                        
                     
                        <form method="post" style="display:inline;" onsubmit="return confirm('¿Seguro que deseas eliminar este usuario?');">
                            <input type="hidden" name="accion" value="eliminar">
                            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                            <button type="submit" style="color:red;">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
