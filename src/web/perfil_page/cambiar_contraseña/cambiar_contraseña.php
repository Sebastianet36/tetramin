<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cambiar contraseña</title>
  <link rel="stylesheet" href="cambiar_contraseña.css">
</head>
<body>
  <div class="container">
    <div class="form-container">
      <h2 class="form-title">Cambiar contraseña</h2>
      <form method="POST" action="../../backend/procesar_cambio_contraseña.php">
        <label class="form-label">Contraseña actual:</label><br>
        <input type="password" name="contraseña_actual" class="form-input" required><br><br>

        <label class="form-label">Nueva contraseña:</label><br>
        <input type="password" name="nueva_contraseña" class="form-input" required><br><br>

        <label class="form-label">Confirmar nueva contraseña:</label><br>
        <input type="password" name="confirmar_contraseña" class="form-input" required><br><br>

        <button type="submit" class="submit-btn">Actualizar contraseña</button>
        <a href="../../main_page/main_registrados.php" class="back-button">Atras</a>
      </form>
    </div>
  </div>
</body>
</html>
<script>
    document.querySelector('form').addEventListener('submit', function(event) {
        var nueva = document.querySelector('[name="nueva_contraseña"]');
        var confirmar = document.querySelector('[name="confirmar_contraseña"]');
        if (nueva.value !== confirmar.value) {
            alert('Las contraseñas no coinciden.');
            event.preventDefault();  // Prevent form submission
        }
    });
</script>
