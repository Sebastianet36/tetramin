<?php
// 1. La sesión debe durar solo hasta cerrar el navegador
ini_set('session.cookie_lifetime', 0);

// 2. Iniciar sesión
session_start();

include_once __DIR__ . '/conn.php';

// 3. Autologin si NO hay sesión activa pero SI hay cookie de recordarme
if (!isset($_SESSION['id_usuario']) && isset($_COOKIE['recordarme'])) {

    $token = $_COOKIE['recordarme'];

    // Buscar usuario con ese token
    $stmt = $conn->prepare("SELECT id_usuario, nombre_usuario FROM usuarios WHERE token_recordar = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $fila = $resultado->fetch_assoc();

        // RECREAR SESIÓN
        $_SESSION['id_usuario'] = $fila['id_usuario'];
        $_SESSION['nombre_usuario'] = $fila['nombre_usuario'];

    } else {
        // Token inválido → borrar cookie
        setcookie("recordarme", "", time() - 3600, "/");
    }
}

?>
