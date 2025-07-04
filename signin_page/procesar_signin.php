<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

$conexion = new mysqli("localhost", "root", "", "tetrisdb");
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$usuario = $_POST['nombre_usuario'];
$contraseña = $_POST['contraseña'];

$stmt = $conexion->prepare("SELECT nombre_usuario, contraseña FROM usuarios WHERE nombre_usuario = ?");
$stmt->bind_param("s", $usuario);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 1) {
    $fila = $resultado->fetch_assoc();
    $hash_almacenado = $fila['contraseña'];

    if (password_verify($contraseña, $hash_almacenado)) {
        $_SESSION['nombre_usuario'] = $fila['nombre_usuario'];
        header("Location: /Tetris-front/main_page/main_registrados.php");
        exit();
    } else {
        echo "Contraseña incorrecta.";
    }
} else {
    echo "Usuario no encontrado.";
}

$stmt->close();
$conexion->close();
?>

$stmt->close();
$conexion->close();
?>
