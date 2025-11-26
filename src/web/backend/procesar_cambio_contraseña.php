<?php
session_start();

// Conexión correcta
$conexion = new mysqli("localhost", "root", "", "tetramindb");
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Verificar sesión activa
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../signin_page/signin.html");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

// Recibir datos
$contraseña_actual = $_POST['contraseña_actual'] ?? '';
$nueva_contraseña = $_POST['nueva_contraseña'] ?? '';
$confirmar_contraseña = $_POST['confirmar_contraseña'] ?? '';

// Validar coincidencia
if ($nueva_contraseña !== $confirmar_contraseña) {
    die("La nueva contraseña y su confirmación no coinciden.");
}

// Obtener la contraseña actual desde la base
$sql = "SELECT contraseña FROM usuarios WHERE id_usuario = ?";
$stmt = $conexion->prepare($sql);

if (!$stmt) {
    die("Error preparando consulta: " . $conexion->error);
}

$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$stmt->bind_result($hash_actual);

if (!$stmt->fetch()) {
    $stmt->close();
    die("El usuario no existe.");
}
$stmt->close();

// Verificar la contraseña actual
if (!password_verify($contraseña_actual, $hash_actual)) {
    die("La contraseña actual es incorrecta.");
}

// Generar hash nuevo
$nuevo_hash = password_hash($nueva_contraseña, PASSWORD_DEFAULT);

// Actualizar contraseña
$sql_update = "UPDATE usuarios SET contraseña = ? WHERE id_usuario = ?";
$stmt = $conexion->prepare($sql_update);

if (!$stmt) {
    die("Error preparando actualización: " . $conexion->error);
}

$stmt->bind_param("si", $nuevo_hash, $id_usuario);

if ($stmt->execute()) {
    echo "Contraseña actualizada correctamente.";
} else {
    echo "Hubo un error al actualizar la contraseña: " . $stmt->error;
}

$stmt->close();
$conexion->close();
?>
