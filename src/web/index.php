<?php
session_start();

// Si el usuario está logueado, redirigir a la página principal
if (isset($_SESSION['nombre_usuario'])) {
    header('Location: main_page/main_registrados.php');
    exit();
}

// Si no está logueado, redirigir a la página de login
header('Location: signin_page/signin.html');
exit();
?> 