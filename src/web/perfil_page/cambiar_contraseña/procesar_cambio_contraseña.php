<?php
session_start();

$conexion = new mysqli("localhost", "root", "", "tetramindb");
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

if (!isset($_SESSION['id_usuario'])) {
  header("Location: ../../signin_page\signin.html");
  exit();
}

$id_usuario = $_SESSION['id_usuario'];
$contraseña_actual = $_POST['contraseña_actual'] ?? '';
$nueva_contraseña = $_POST['nueva_contraseña'] ?? '';
$confirmar_contraseña = $_POST['confirmar_contraseña'] ?? '';

// Validar coincidencia
if ($nueva_contraseña !== $confirmar_contraseña) {
  die("La nueva contraseña y su confirmación no coinciden.");
}

// Obtener la contraseña actual desde la base de datos
$sql = "SELECT contraseña FROM usuarios WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$stmt->bind_result($hash_actual);
$stmt->fetch();
$stmt->close();

// Verificar la contraseña actual
if (!password_verify($contraseña_actual, $hash_actual)) {
  die("La contraseña actual es incorrecta.");
}

// Encriptar la nueva contraseña y actualizar
$nuevo_hash = password_hash($nueva_contraseña, PASSWORD_DEFAULT);
$sql_update = "UPDATE usuarios SET contraseña = ? WHERE id_usuario = ?";
$stmt = $conn->prepare($sql_update);
$stmt->bind_param("si", $nuevo_hash, $id_usuario);

if ($stmt->execute()) {
  echo "Contraseña actualizada correctamente.";
} else {
  echo "Hubo un error al actualizar la contraseña.";
}

$stmt->close();
$conn->close();
?>
