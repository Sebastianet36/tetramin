<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    $servidor = "localhost";
    $usuario = "root";
    $clave = "";
    $baseDeDatos = "tetrisdb";

    $enlace = mysqli_connect ($servidor, $usuario, $clave, $baseDeDatos);
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>
<body>
  <div class="container">
    <header>
      <h1>Crea tu perfil</h1>
    </header>
    <div class="card">
        <form action="#" name="tetrisdb" method="post">
          <div class="input-group">
            <label for="firstName">Nombre de usuario</label>
            <input type="text" id="firstName" name="nombre_usuario" required />
          </div>
          <div class="input-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" required />
          </div>
          <div class="input-group">
            <label for="password">Contraseña</label>
            <input type="password" id="password" name="contraseña" required />
          </div>
          <div class="input-group">
            <label for="location">Location</label>
            <select id="location" name="ubicacion" required>
              <option disabled selected>Selecciona tu ciudad</option>
              <option>New York, NY</option>
              <option>San Francisco, CA</option>
              <option>Los Angeles, CA</option>
              <option>Chicago, IL</option>
              <option>Miami, FL</option>
            </select>
          </div>
          <div class="actions">
            <button type="submit" class="btn primary" name="crear">CREAR</button>
          </div>
        </form>
    </div>
  </div>
  <footer>
      <p>&copy; 2025 Todos los derechos reservados.</p>
      <p>Contacto: <a href="mailto:info@misiito.com">info@misiito.com</a></p>
      <nav class="links">
          <ul>
          <li><a href="/acerca-de">Acerca de</a></li>
          <li><a href="/contacto">Contacto</a></li>
          <li><a href="/politica-de-privacidad">Política de Privacidad</a></li>
          </ul>
      </nav>
  </footer>
  </div>
</body>
</html>



<?php

    if(isset($_POST['crear'])){
        $nombre= $_POST ['nombre_usuario'];
        $email= $_POST ['email'];
        $contraseña= $_POST ['contraseña'];
        $ubicacion= $_POST ['ubicacion'];
        $fecha = date("Y-m-d H:i:s");
        $insertarDatos= "INSERT INTO Usuarios VALUES('$nombre','$email','$contraseña','$fecha',0,1,'$ubicacion')";
        $ejecutarInsertar = mysqli_query ($enlace,$insertarDatos);
    }

?>

