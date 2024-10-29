<?php
session_start();
if ($_SESSION['perfil'] !== 'Empleado') {
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

// Manejar adición de producto
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['agregar'])) {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $id_categoria = $_POST['id_categoria'];
    $id_proveedor = $_POST['id_proveedor'];

    $stmt = $conn->prepare("INSERT INTO productos (nombre, descripcion, precio, stock, id_categoria, id_proveedor) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdiii", $nombre, $descripcion, $precio, $stock, $id_categoria, $id_proveedor);
    $stmt->execute();
    $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Manejar eliminación de producto
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['eliminar'])) {
    $id_producto = $_POST['id_producto'];
    
    $stmt = $conn->prepare("DELETE FROM productos WHERE id_producto = ?");
    $stmt->bind_param("i", $id_producto);
    $stmt->execute();
    $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Obtener productos con nombres de categoría y proveedor
$query = "
    SELECT p.*, c.nombre_categoria, pr.nombre AS nombre_proveedor
    FROM productos p
    JOIN categorias c ON p.id_categoria = c.id_categoria
    JOIN proveedores pr ON p.id_proveedor = pr.id_proveedor
";
$result = $conn->query($query);
$productos = $result->fetch_all(MYSQLI_ASSOC);

// Obtener todas las categorías y proveedores para el formulario
$categorias = $conn->query("SELECT * FROM categorias")->fetch_all(MYSQLI_ASSOC);
$proveedores = $conn->query("SELECT * FROM proveedores")->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos</title>
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

    <h2>Gestión de Productos</h2>
    <table>
        <thead>
            <tr>
                <th>ID Producto</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Precio</th>
                <th>Stock</th>
                <th>Categoría</th>
                <th>Proveedor</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($productos as $producto): ?>
                <tr>
                    <td><?= $producto['id_producto'] ?></td>
                    <td><?= htmlspecialchars($producto['nombre']) ?></td>
                    <td><?= htmlspecialchars($producto['descripcion']) ?></td>
                    <td>$<?= number_format($producto['precio'], 2) ?></td>
                    <td><?= $producto['stock'] ?></td>
                    <td><?= htmlspecialchars($producto['nombre_categoria']) ?></td>
                    <td><?= htmlspecialchars($producto['nombre_proveedor']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <form action="homee.php" method="POST"> <!-- Acción para regresar -->
        <button type="submit">Regresar</button> 
    </form>

</body>
</html>
