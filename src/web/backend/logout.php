<?php
include_once __DIR__ . '/session_init.php';

// Destruir variables de sesión
$_SESSION = [];
session_unset();
session_destroy();

// Borrar cookie recordarme
if (isset($_COOKIE['recordarme'])) {
    setcookie("recordarme", "", time() - 3600, "/");
}

// Redirigir a login
header("Location: ../signin_page/signin.html");
exit();
?>
