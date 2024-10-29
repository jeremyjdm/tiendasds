<?php
session_start();
if ($_SESSION['perfil'] !== 'Gerente') {
    header("Location: login.php");
    exit();
}

?>
<?php
session_start();
if ($_SESSION['perfil'] !== 'Root') {
    header("Location: login.php");
    exit();
}

?>
<?php
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "bdtienda";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Manejar adición de usuario
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['agregar'])) {
    $nombre_usuario = $_POST['nombre_usuario'] ?? '';
    $contrasena = $_POST['contrasena'] ?? '';
    $perfil = $_POST['perfil'] ?? '';

    // Validar los datos antes de insertarlos
    if ($nombre_usuario && $contrasena && $perfil) {
        $stmt = $conn->prepare("INSERT INTO usuarios (nombre_usuario, contrasena, perfil) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nombre_usuario, $contrasena, $perfil);
        $stmt->execute();
        $stmt->close();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Manejar eliminación de usuario
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['eliminar'])) {
    $id_usuario = $_POST['id_usuario'] ?? '';
    
    // Validar el ID del usuario
    if ($id_usuario) {
        $stmt = $conn->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $stmt->close();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Obtener usuarios
$result = $conn->query("SELECT * FROM usuarios");
$usuarios = $result->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
            padding: 8px;
        }
        table {
            width: 100%;
            margin-bottom: 16px;
        }
        th {
            background-color: #f2f2f2;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 8px;
            max-width: 300px;
        }
        .button-container {
            margin-bottom: 16px;
        }
    </style>
</head>
<body>

    <h2>Gestión de Usuarios</h2>
    <table>
        <thead>
            <tr>
                <th>ID Usuario</th>
                <th>Nombre Usuario</th>
                <th>Contraseña</th>
                <th>Perfil</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($usuarios)): ?>
                <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td><?= $usuario['id_usuario'] ?></td>
                        <td><?= htmlspecialchars($usuario['nombre_usuario']) ?></td>
                        <td><?= htmlspecialchars($usuario['contrasena']) ?></td>
                        <td><?= htmlspecialchars($usuario['perfil']) ?></td>
                        <td>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="id_usuario" value="<?= $usuario['id_usuario'] ?>">
                                <button type="submit" name="eliminar">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No hay usuarios registrados.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <h3>Agregar Usuario</h3>
    <form method="post">
        <input type="text" name="nombre_usuario" placeholder="Nombre Usuario" required>
        <input type="password" name="contrasena" placeholder="Contraseña" required>
        <input type="text" name="perfil" placeholder="Perfil" required>
        <button type="submit" name="agregar">Agregar Usuario</button>
    </form>

    <form action="homee.php" method="POST"> <!-- Acción para regresar -->
        <button type="submit">Regresar</button> 
    </form>

</body>
</html>
