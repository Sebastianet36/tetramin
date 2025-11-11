<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

include_once __DIR__ . '/../backend/conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['nombre_usuario']);
    $contraseña = $_POST['contraseña'];

    $stmt = $conn->prepare("SELECT nombre_usuario, contraseña FROM usuarios WHERE nombre_usuario = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $fila = $resultado->fetch_assoc();
        if (password_verify($contraseña, $fila['contraseña'])) {
            $_SESSION['nombre_usuario'] = $fila['nombre_usuario'];
            header("Location: ../main_page/main_registrados.php");
            exit();
        }
    }

    // Si llega aquí, es error
    header("Location: ../signin_page/signin.html?error=1");
    exit();
}

$stmt->close();
$conn->close();
?>
