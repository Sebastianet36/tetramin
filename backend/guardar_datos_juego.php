<?php
session_start();
include_once 'conn.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['nombre_usuario'])) {
    echo "Error: Usuario no autenticado";
    exit();
}

// Verificar si es una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "Error: Método no permitido";
    exit();
}

// Obtener los datos del formulario
$puntaje = isset($_POST['puntaje']) ? (int)$_POST['puntaje'] : 0;
$tiempo = isset($_POST['tiempo']) ? $_POST['tiempo'] : '00:00:00';
$nivel = isset($_POST['nivel']) ? (int)$_POST['nivel'] : 1;
$lineas = isset($_POST['lineas']) ? (int)$_POST['lineas'] : 0;

// Obtener el id_usuario desde la base de datos usando el nombre_usuario
$nombre_usuario = $_SESSION['nombre_usuario'];
$sql_usuario = "SELECT id_usuario FROM usuarios WHERE nombre_usuario = ?";
$stmt_usuario = $conn->prepare($sql_usuario);
$stmt_usuario->bind_param("s", $nombre_usuario);
$stmt_usuario->execute();
$result_usuario = $stmt_usuario->get_result();

if ($result_usuario->num_rows === 0) {
    echo "Error: Usuario no encontrado en la base de datos";
    exit();
}

$row_usuario = $result_usuario->fetch_assoc();
$id_usuario = $row_usuario['id_usuario'];

// Convertir el tiempo de formato MM:SS:ms a segundos
$tiempo_partes = explode(':', $tiempo);
$duracion_segundos = 0;
if (count($tiempo_partes) >= 2) {
    $duracion_segundos = (int)$tiempo_partes[0] * 60 + (int)$tiempo_partes[1];
    if (count($tiempo_partes) >= 3) {
        $duracion_segundos += (int)$tiempo_partes[2] / 100; // centésimas de segundo
    }
}

try {
    // Insertar el record en la tabla record
    $sql = "INSERT INTO record (id_usuario, fecha_jugada, puntaje, duracion, nivel, lineas, id_modo) 
            VALUES (?, NOW(), ?, ?, ?, ?, 1)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiiii", $id_usuario, $puntaje, $duracion_segundos, $nivel, $lineas);
    
    if ($stmt->execute()) {
        echo "Datos guardados correctamente";
    } else {
        echo "Error al guardar los datos";
    }
    
} catch (Exception $e) {
    echo "Error interno del servidor: " . $e->getMessage();
}

$stmt->close();
$stmt_usuario->close();
$conn->close();
?> 