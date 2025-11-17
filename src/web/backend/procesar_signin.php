<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include_once __DIR__ . '/../backend/conn.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['nombre_usuario']);
    $contraseña = $_POST['contraseña'];
    $recordarme = isset($_POST['recordarme']);

    $stmt = $conn->prepare("SELECT id_usuario, nombre_usuario, contraseña FROM usuarios WHERE nombre_usuario = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $fila = $resultado->fetch_assoc();

        if (password_verify($contraseña, $fila['contraseña'])) {

            $_SESSION['id_usuario'] = $fila['id_usuario'];
            $_SESSION['nombre_usuario'] = $fila['nombre_usuario'];

            if ($recordarme) {
                $token = bin2hex(random_bytes(32));

                $update = $conn->prepare("UPDATE usuarios SET token_recordar = ? WHERE id_usuario = ?");
                $update->bind_param("si", $token, $fila['id_usuario']);
                $update->execute();

                setcookie(
                    "recordarme",
                    $token,
                    time() + (86400 * 30),
                    "/",
                    "",
                    false,
                    true
                );
            }

            header("Location: ../main_page/main_registrados.php");
            exit();
        }
    }

    header("Location: ../signin_page/signin.html?error=1");
    exit();
}
?>
