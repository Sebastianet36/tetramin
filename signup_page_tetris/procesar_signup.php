<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    $servidor = "localhost";
    $usuario = "root";
    $clave = "";
    $baseDeDatos = "tetrisdb";

    $enlace = mysqli_connect($servidor, $usuario, $clave, $baseDeDatos);

    if(isset($_POST['crear'])){
        $nombre= $_POST ['nombre_usuario'];
        $email= $_POST ['email'];
        $contraseña= $_POST ['contraseña'];
        $ubicacion= $_POST ['ubicacion'];
        $fecha = date("Y-m-d H:i:s");
        $insertarDatos = "INSERT INTO Usuarios (nombre_usuario,email,contraseña,fecha_registro,es_admin,usuario_activo,ubicacion) VALUES('$nombre','$email','$contraseña','$fecha',0,1,'$ubicacion')";
        $ejecutarInsertar = mysqli_query($enlace,$insertarDatos);
        header("Location: /html-css/signin_page_tetris/signin.html");
        exit();
    }
?>
