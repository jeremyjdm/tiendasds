<?php
session_start();

// Conexión a la base de datos
$host = "localhost";
$user = "root"; // Cambiar si es necesario
$pass = "root";     // Cambiar si es necesario
$dbname = "bdtienda";

$conn = new mysqli($host, $user, $pass, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$error_message = ""; // Variable para almacenar mensajes de error

// Procesar el formulario de inicio de sesión
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar si los campos están definidos
    $nombre_usuario = isset($_POST['nombre_usuario']) ? $_POST['nombre_usuario'] : '';
    $contrasena = isset($_POST['contrasena']) ? $_POST['contrasena'] : '';
    $perfil = isset($_POST['perfil']) ? $_POST['perfil'] : '';

    // Consulta para verificar las credenciales del usuario
    $sql = "SELECT * FROM usuarios WHERE nombre_usuario = ? AND contrasena = ? AND perfil = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $nombre_usuario, $contrasena, $perfil);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Usuario autenticado correctamente
        $_SESSION['nombre_usuario'] = $nombre_usuario;
        $_SESSION['perfil'] = $perfil;

        // Redirigir a la página correspondiente
        switch ($perfil) {
            case 'Root':
                header("Location: homer.php");
                break;
            case 'secretaria':
                header("Location: homes.php");
                break;
            case 'gerente':
                header("Location: homeg.php");
                break;
            case 'Empleado':
                header("Location: homee.php");
                break;
            default:
                $error_message = "Perfil no reconocido.";
        }
        exit(); // Asegúrate de llamar a exit después de header
    } else {
        // Credenciales incorrectas
        $error_message = "Usuario o contraseña incorrectos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - basetienda</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0; /* Fondo claro */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh; /* Altura completa de la ventana */
            margin: 0;
        }

        .login-container {
            background-color: #fff; /* Fondo blanco */
            border-radius: 10px; /* Bordes redondeados */
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); /* Sombra suave */
            padding: 40px; /* Espaciado interno */
            width: 300px; /* Ancho del contenedor */
            text-align: center;
        }

        h2 {
            margin-bottom: 20px; /* Espacio inferior del título */
            color: #333; /* Color del texto */
        }

        label {
            display: block; /* Hace que cada etiqueta ocupe una línea */
            margin-bottom: 5px; /* Espacio inferior de las etiquetas */
            text-align: left; /* Alinea el texto a la izquierda */
            color: #555; /* Color de texto más claro */
        }

        input[type="text"],
        input[type="password"],
        select {
            width: 100%; /* Ancho completo */
            padding: 10px; /* Espaciado interno */
            margin-bottom: 20px; /* Espacio inferior entre campos */
            border: 1px solid #ccc; /* Borde gris claro */
            border-radius: 5px; /* Bordes redondeados */
            box-sizing: border-box; /* Incluye el padding y el border en el ancho total */
        }

        input[type="submit"] {
            background-color: #5cb85c; /* Color de fondo del botón */
            color: white; /* Color del texto */
            border: none; /* Sin borde */
            padding: 10px; /* Espaciado interno */
            border-radius: 5px; /* Bordes redondeados */
            cursor: pointer; /* Cambia el cursor al pasar sobre el botón */
            transition: background-color 0.3s; /* Transición suave para el color de fondo */
        }

        input[type="submit"]:hover {
            background-color: #4cae4c; /* Color del botón al pasar el mouse */
        }

        /* Estilo para el mensaje de error */
        .error {
            color: red; /* Color rojo para los errores */
            margin-top: 10px; /* Espacio superior */
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Inicio de Sesión</h2>
        <form method="POST" action="">
            <label for="nombre">Usuario:</label>
            <input type="text" id="nombre" name="nombre_usuario" required>

            <label for="contraseña">Contraseña:</label>
            <input type="password" id="contrasena" name="contrasena" required>

            <label for="perfil">Perfil:</label>
            <select id="perfil" name="perfil" required>
                <option value="selecciona">Selecciona perfil</option>
                <option value="Root">Root</option>
                <option value="secretaria">Secretaria</option>
                <option value="gerente">Gerente</option>
                <option value="Empleado">Empleado</option>
            </select>

            <input type="submit" value="Iniciar Sesión">
            <!-- Mensaje de error (opcional) -->
            <div class="error" id="error-message" style="display: none;"></div>
        </form>
    </div>
</body>
</html>
