<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include_once __DIR__ . '/../backend/conn.php';

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

if (isset($_POST['crear'])) {
    $nombre = $_POST['nombre_usuario'];
    $email = $_POST['email'];
    $contraseña = $_POST['contraseña'];
    $passHash = password_hash($contraseña, PASSWORD_BCRYPT);
    $ubicacion = $_POST['ubicacion'];
    $fecha = date("Y-m-d H:i:s");

    $stmt = $conn->prepare("INSERT INTO usuarios (nombre_usuario, email, contraseña, fecha_registro, es_admin, usuario_activo, ubicacion) VALUES (?, ?, ?, ?, 0, 1, ?)");
    $stmt->bind_param("sssss", $nombre, $email, $passHash, $fecha, $ubicacion);

    if ($stmt->execute()) {
        header("Location: /Tetris-front/signin_page/signin.html");
        exit();
    } else {
        echo "Error al registrar el usuario: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();
?>
