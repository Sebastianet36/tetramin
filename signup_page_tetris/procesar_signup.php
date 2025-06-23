<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$servidor = "localhost";
$usuario = "root";
$clave = "";
$baseDeDatos = "tetrisdb";

$enlace = mysqli_connect($servidor, $usuario, $clave, $baseDeDatos);

if ($enlace->connect_error) {
    die("Error de conexión: " . $enlace->connect_error);
}

if (isset($_POST['crear'])) {
    $nombre = $_POST['nombre_usuario'];
    $email = $_POST['email'];
    $contraseña = $_POST['contraseña'];
    $passHash = password_hash($contraseña, PASSWORD_BCRYPT);
    $ubicacion = $_POST['ubicacion'];
    $fecha = date("Y-m-d H:i:s");

    $stmt = $enlace->prepare("INSERT INTO Usuarios (nombre_usuario, email, contraseña, fecha_registro, es_admin, usuario_activo, ubicacion) VALUES (?, ?, ?, ?, 0, 1, ?)");
    $stmt->bind_param("sssss", $nombre, $email, $passHash, $fecha, $ubicacion);

    if ($stmt->execute()) {
        header("Location: /Tetris-front-page/signin_page_tetris/signin.html");
        exit();
    } else {
        echo "Error al registrar el usuario: " . $stmt->error;
    }

    $stmt->close();
}
$enlace->close();
?>
