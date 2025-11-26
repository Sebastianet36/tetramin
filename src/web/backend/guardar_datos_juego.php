<?php
// guardar_datos_juego.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

session_start();
include_once 'conn.php';

// Disable HTML errors in output
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

// ============================
// Verificar autenticación
// ============================
if (!isset($_SESSION['nombre_usuario'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Usuario no autenticado']);
    exit();
}

// ============================
// Verificar método HTTP
// ============================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit();
}

// ============================
// Recibir datos
// ============================
$puntaje = isset($_POST['puntaje']) ? (int)$_POST['puntaje'] : 0;
$tiempo = isset($_POST['tiempo']) ? trim($_POST['tiempo']) : '00:00:00';
$nivel = isset($_POST['nivel']) ? (int)$_POST['nivel'] : 1;
$lineas = isset($_POST['lineas']) ? (int)$_POST['lineas'] : 0;
$id_modo = isset($_POST['id_modo']) ? (int)$_POST['id_modo'] : 1;

$nombre_usuario = $_SESSION['nombre_usuario'];

// ============================
// Conversión de tiempo
// Soporta: MM:SS:CC  |  MM:SS.D | MM:SS | SS.D | SS
// ============================
function convertirTiempoASegundos($str) {
    $str = trim($str);

    // Formato MM:SS:CC (centésimas)
    if (preg_match('/^(\d{1,2}):(\d{1,2}):(\d{1,2})$/', $str, $m)) {
        $min = intval($m[1]);
        $sec = intval($m[2]);
        $cen = intval($m[3]); // centésimas
        return $min * 60 + $sec + ($cen / 100);
    }

    // Formato MM:SS.D
    if (preg_match('/^(\d{1,2}):(\d{1,2})\.(\d)$/', $str, $m)) {
        return intval($m[1]) * 60 + intval($m[2]) + (intval($m[3]) / 10);
    }

    // Formato MM:SS
    if (preg_match('/^(\d{1,2}):(\d{1,2})$/', $str, $m)) {
        return intval($m[1]) * 60 + intval($m[2]);
    }

    // Formato SS.D
    if (preg_match('/^(\d+)\.(\d)$/', $str, $m)) {
        return intval($m[1]) + intval($m[2]) / 10;
    }

    // Formato SS
    if (preg_match('/^(\d+)$/', $str, $m)) {
        return intval($m[1]);
    }

    return floatval($str);
}

$duracion_segundos = convertirTiempoASegundos($tiempo);

// ============================
// Obtener id_usuario
// ============================
$sql_usuario = "SELECT id_usuario FROM usuarios WHERE nombre_usuario = ?";
$stmt_usuario = $conn->prepare($sql_usuario);

if (!$stmt_usuario) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error preparando consulta de usuario']);
    exit();
}

$stmt_usuario->bind_param("s", $nombre_usuario);
$stmt_usuario->execute();
$result_usuario = $stmt_usuario->get_result();

if ($result_usuario->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Usuario no encontrado']);
    exit();
}

$id_usuario = (int)$result_usuario->fetch_assoc()['id_usuario'];

// ============================
// Obtener record previo
// ============================
$previous_record = null;
$stmt_prev = $conn->prepare("
    SELECT puntaje, duracion, nivel, lineas, fecha_jugada 
    FROM record 
    WHERE id_usuario = ? AND id_modo = ? 
    LIMIT 1
");

if ($stmt_prev) {
    $stmt_prev->bind_param("ii", $id_usuario, $id_modo);
    $stmt_prev->execute();
    $res_prev = $stmt_prev->get_result();
    if ($res_prev && $res_prev->num_rows > 0) {
        $previous_record = $res_prev->fetch_assoc();
    }
    $stmt_prev->close();
}

// ============================
// Ejecutar Stored Procedure
// NOTA IMPORTANTE: el 3er parámetro ahora es "d" (double)
// ============================
$stmt = $conn->prepare("CALL GuardarRecord(?, ?, ?, ?, ?, ?, @es_nuevo_record)");

if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error preparando llamada al procedimiento almacenado']);
    exit();
}

$stmt->bind_param("iidiii",
    $id_usuario,
    $puntaje,
    $duracion_segundos,  // <— AHORA DECIMAL REAL
    $nivel,
    $lineas,
    $id_modo
);

$ok = $stmt->execute();
$stmt->close();

if (!$ok) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error ejecutando procedimiento almacenado']);
    exit();
}

// ============================
// Obtener variable de salida
// ============================
$res_out = $conn->query("SELECT @es_nuevo_record AS es_nuevo_record");
$row_out = $res_out->fetch_assoc();
$es_nuevo_record = isset($row_out['es_nuevo_record']) ? (bool)$row_out['es_nuevo_record'] : false;

// ============================
// Obtener record actualizado
// ============================
$current_record = null;
$stmt_cur = $conn->prepare("
    SELECT puntaje, duracion, nivel, lineas, fecha_jugada 
    FROM record 
    WHERE id_usuario = ? AND id_modo = ?
    LIMIT 1
");

if ($stmt_cur) {
    $stmt_cur->bind_param("ii", $id_usuario, $id_modo);
    $stmt_cur->execute();
    $res_cur = $stmt_cur->get_result();
    if ($res_cur && $res_cur->num_rows > 0) {
        $current_record = $res_cur->fetch_assoc();
    }
    $stmt_cur->close();
}

// ============================
// Enviar respuesta final
// ============================
echo json_encode([
    'success' => true,
    'nuevo_record' => $es_nuevo_record,
    'message' => $es_nuevo_record ? '¡Nuevo récord guardado!' : 'No se superó el récord anterior.',
    'previous_record' => $previous_record,
    'record' => $current_record,
    'data' => [
        'puntaje' => $puntaje,
        'tiempo' => $tiempo,
        'nivel' => $nivel,
        'lineas' => $lineas,
        'duracion_segundos' => $duracion_segundos,
        'id_modo' => $id_modo
    ]
]);

$conn->close();
exit();
?>
