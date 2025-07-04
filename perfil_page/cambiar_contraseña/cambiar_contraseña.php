<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
  header("Location: login.php");
  exit();
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Cambiar contraseña</title>
</head>
<body>
  <h2>Cambiar contraseña</h2>
  <form method="POST" action="procesar_cambio_contraseña.php">
    <label>Contraseña actual:</label><br>
    <input type="password" name="contraseña_actual" required><br><br>

    <label>Nueva contraseña:</label><br>
    <input type="password" name="nueva_contraseña" required><br><br>

    <label>Confirmar nueva contraseña:</label><br>
    <input type="password" name="confirmar_contraseña" required><br><br>

    <button type="submit">Actualizar contraseña</button>
  </form>
</body>
</html>