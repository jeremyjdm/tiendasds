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

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Manejar adición de proveedor
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['agregar'])) {
    $nombre = $_POST['nombre'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $email = $_POST['email'] ?? '';
    $direccion = $_POST['direccion'] ?? '';

    // Validar los datos antes de insertarlos
    if ($nombre && $telefono && $email && $direccion) {
        $stmt = $conn->prepare("INSERT INTO proveedores (nombre, telefono, email, direccion) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nombre, $telefono, $email, $direccion);
        $stmt->execute();
        $stmt->close();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Manejar eliminación de proveedor
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['eliminar'])) {
    $id_proveedor = $_POST['id_proveedor'] ?? '';
    
    // Validar el ID del proveedor
    if ($id_proveedor) {
        $stmt = $conn->prepare("DELETE FROM proveedores WHERE id_proveedor = ?");
        $stmt->bind_param("i", $id_proveedor);
        $stmt->execute();
        $stmt->close();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Obtener proveedores
$result = $conn->query("SELECT * FROM proveedores");
$proveedores = $result->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Proveedores</title>
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

    <h2>Gestión de Proveedores</h2>
    <table>
        <thead>
            <tr>
                <th>ID Proveedor</th>
                <th>Nombre</th>
                <th>Teléfono</th>
                <th>Email</th>
                <th>Dirección</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($proveedores as $proveedor): ?>
                <tr>
                    <td><?= $proveedor['id_proveedor'] ?></td>
                    <td><?= htmlspecialchars($proveedor['nombre']) ?></td>
                    <td><?= htmlspecialchars($proveedor['telefono']) ?></td>
                    <td><?= htmlspecialchars($proveedor['email']) ?></td>
                    <td><?= htmlspecialchars($proveedor['direccion']) ?></td>
                    <td>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="id_proveedor" value="<?= $proveedor['id_proveedor'] ?>">
                            <button type="submit" name="eliminar">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h3>Agregar Proveedor</h3>
    <form method="post">
        <input type="text" name="nombre" placeholder="Nombre" required>
        <input type="text" name="telefono" placeholder="Teléfono" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="text" name="direccion" placeholder="Dirección" required>
        <button type="submit" name="agregar">Agregar Proveedor</button>
    </form>

    <form action="homee.php" method="POST"> <!-- Acción para regresar -->
        <button type="submit">Regresar</button> 
    </form>

</body>
</html>
