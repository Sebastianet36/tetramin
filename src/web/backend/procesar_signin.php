<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

include_once __DIR__ . '/../backend/conn.php';

$usuario = $_POST['nombre_usuario'];
$contraseña = $_POST['contraseña'];

$stmt = $conn->prepare("SELECT nombre_usuario, contraseña FROM usuarios WHERE nombre_usuario = ?");
$stmt->bind_param("s", $usuario);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 1) {
    $fila = $resultado->fetch_assoc();
    $hash_almacenado = $fila['contraseña'];

    if (password_verify($contraseña, $hash_almacenado)) {
        $_SESSION['nombre_usuario'] = $fila['nombre_usuario'];
        header("Location: ../main_page/main_registrados.php");
        exit();
    } else {
        echo "Contraseña incorrecta.";
    }
} else {
    echo "Usuario no encontrado.";
}

$stmt->close();
$conn->close();
?>
