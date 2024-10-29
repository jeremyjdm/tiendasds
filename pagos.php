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

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Manejar adición de método de pago
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['agregar'])) {
    $tipo_pago = $_POST['tipo_pago'] ?? '';

    // Validar los datos antes de insertarlos
    if ($tipo_pago) {
        $stmt = $conn->prepare("INSERT INTO metodos_pago (tipo_pago) VALUES (?)");
        $stmt->bind_param("s", $tipo_pago);
        $stmt->execute();
        $stmt->close();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Manejar eliminación de método de pago
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['eliminar'])) {
    $id_metodo_pago = $_POST['id_metodo_pago'] ?? '';
    
    // Validar el ID del método de pago
    if ($id_metodo_pago) {
        $stmt = $conn->prepare("DELETE FROM metodos_pago WHERE id_metodo_pago = ?");
        $stmt->bind_param("i", $id_metodo_pago);
        $stmt->execute();
        $stmt->close();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Obtener métodos de pago
$result = $conn->query("SELECT * FROM metodos_pago");
$metodos_pago = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Métodos de Pago</title>
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

    <h2>Gestión de Métodos de Pago</h2>
    <table>
        <thead>
            <tr>
                <th>ID Método</th>
                <th>Tipo de Pago</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($metodos_pago)): ?>
                <tr>
                    <td colspan="3">No hay métodos de pago disponibles.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($metodos_pago as $metodo): ?>
                    <tr>
                        <td><?= $metodo['id_metodo_pago'] ?></td>
                        <td><?= htmlspecialchars($metodo['tipo_pago']) ?></td>
                        <td>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="id_metodo_pago" value="<?= $metodo['id_metodo_pago'] ?>">
                                <button type="submit" name="eliminar">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <h3>Agregar Método de Pago</h3>
    <form method="post">
        <input type="text" name="tipo_pago" placeholder="Tipo de Pago" required>
        <button type="submit" name="agregar">Agregar Método de Pago</button>
    </form>

    <form action="homee.php" method="POST"> <!-- Acción para regresar -->
        <button type="submit">Regresar</button> 
    </form>

</body>
</html>
