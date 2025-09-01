<?php
// Establecer headers para JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

session_start();
include_once 'conn.php';

// Habilitar reporte de errores para debugging
ini_set('display_errors', 0); // Cambiado a 0 para evitar output HTML en JSON
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

// Verificar si el usuario está autenticado
if (!isset($_SESSION['nombre_usuario'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Usuario no autenticado']);
    exit();
}

// Verificar si es una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit();
}

// Obtener y validar los datos del formulario
$puntaje = isset($_POST['puntaje']) ? (int)$_POST['puntaje'] : 0;
$tiempo = isset($_POST['tiempo']) ? $_POST['tiempo'] : '00:00:00';
$nivel = isset($_POST['nivel']) ? (int)$_POST['nivel'] : 1;
$lineas = isset($_POST['lineas']) ? (int)$_POST['lineas'] : 0;
$id_modo = isset($_POST['id_modo']) ? (int)$_POST['id_modo'] : 1; // Por defecto modo 1

// Obtener el id_usuario desde la base de datos usando el nombre_usuario
$nombre_usuario = $_SESSION['nombre_usuario'];
$sql_usuario = "SELECT id_usuario FROM usuarios WHERE nombre_usuario = ?";
$stmt_usuario = $conn->prepare($sql_usuario);

if (!$stmt_usuario) {
    http_response_code(500);
    echo json_encode(['error' => 'Error en la preparación de la consulta de usuario']);
    exit();
}

$stmt_usuario->bind_param("s", $nombre_usuario);
$stmt_usuario->execute();
$result_usuario = $stmt_usuario->get_result();

if ($result_usuario->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['error' => 'Usuario no encontrado en la base de datos']);
    exit();
}

$row_usuario = $result_usuario->fetch_assoc();
$id_usuario = $row_usuario['id_usuario'];

// Convertir el tiempo de formato MM:SS:ms a segundos
$duracion_segundos = 0;
if (preg_match('/^(\d{1,2}):(\d{1,2}):(\d{1,2})$/', $tiempo, $matches)) {
    // Formato MM:SS:ms
    $duracion_segundos = (int)$matches[1] * 60 + (int)$matches[2] + (int)$matches[3] / 100;
} elseif (preg_match('/^(\d{1,2}):(\d{1,2})$/', $tiempo, $matches)) {
    // Formato MM:SS
    $duracion_segundos = (int)$matches[1] * 60 + (int)$matches[2];
} else {
    // Si no coincide con ningún formato, intentar convertir directamente
    $duracion_segundos = (int)$tiempo;
}

try {
    // Llamar al procedimiento almacenado GuardarRecord
    $stmt = $conn->prepare("CALL GuardarRecord(?, ?, ?, ?, ?, ?, @es_nuevo_record)");
    $stmt->bind_param("iiiiii", $id_usuario, $puntaje, $duracion_segundos, $nivel, $lineas, $id_modo);
    $stmt->execute();
    $stmt->close();

    // Obtener el valor de salida
    $result = $conn->query("SELECT @es_nuevo_record AS es_nuevo_record");
    $row = $result->fetch_assoc();
    $es_nuevo_record = $row['es_nuevo_record'];

    if ($es_nuevo_record) {
        echo json_encode([
            'success' => true,
            'nuevo_record' => true,
            'message' => '¡Nuevo récord guardado!',
            'data' => [
                'puntaje' => $puntaje,
                'tiempo' => $tiempo,
                'nivel' => $nivel,
                'lineas' => $lineas,
                'duracion_segundos' => $duracion_segundos,
                'id_modo' => $id_modo
            ]
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'nuevo_record' => false,
            'message' => 'No se superó el récord anterior.',
            'data' => [
                'puntaje' => $puntaje,
                'tiempo' => $tiempo,
                'nivel' => $nivel,
                'lineas' => $lineas,
                'duracion_segundos' => $duracion_segundos,
                'id_modo' => $id_modo
            ]
        ]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Error interno del servidor',
        'message' => $e->getMessage()
    ]);
}

// Cerrar las conexiones
if (isset($stmt_usuario)) $stmt_usuario->close();
if (isset($conn)) $conn->close();
?> 