<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// Conexión a base de datos
$conexion = new mysqli("localhost", "root", "", "tetrisdb"); // reemplazá con tu nombre de base

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Recibir datos del formulario
$usuario = $_POST['nombre_usuario'];
$contraseña = $_POST['contraseña'];

// Buscar usuario
$stmt = $conexion->prepare("SELECT * FROM Usuarios WHERE nombre_usuario = ?");
$stmt->bind_param("s", $usuario);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 1) {
    $fila = $resultado->fetch_assoc();

    if ($contraseña === $fila['contraseña']) {
        // Guardar nombre de usuario en sesión
        $_SESSION['nombre_usuario'] = $fila['nombre_usuario'];
        header("Location: /html-css/main_page_tetris/main_registrados.php");
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
