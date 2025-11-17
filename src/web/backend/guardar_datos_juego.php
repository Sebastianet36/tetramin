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

// Verificar autenticación
if (!isset($_SESSION['nombre_usuario'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Usuario no autenticado']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit();
}

// Recibir datos
$puntaje = isset($_POST['puntaje']) ? (int)$_POST['puntaje'] : 0;
$tiempo = isset($_POST['tiempo']) ? trim($_POST['tiempo']) : '00:00:00';
$nivel = isset($_POST['nivel']) ? (int)$_POST['nivel'] : 1;
$lineas = isset($_POST['lineas']) ? (int)$_POST['lineas'] : 0;
$id_modo = isset($_POST['id_modo']) ? (int)$_POST['id_modo'] : 1;

$nombre_usuario = $_SESSION['nombre_usuario'];

// Obtener id_usuario
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
$row_usuario = $result_usuario->fetch_assoc();
$id_usuario = (int)$row_usuario['id_usuario'];

// FUNCION: convertir tiempo string a segundos (int)
function convertirTiempoASegundos($tiempoStr) {
    $tiempoStr = trim($tiempoStr);
    // Formato MM:SS:CC  (centésimas en CC) o MM:SS o número
    if (preg_match('/^(\d{1,2}):(\d{1,2}):(\d{1,2})$/', $tiempoStr, $m)) {
        $mm = (int)$m[1];
        $ss = (int)$m[2];
        $cc = (int)$m[3];
        $total = $mm * 60 + $ss + ($cc / 100.0);
        return (int)round($total);
    } elseif (preg_match('/^(\d{1,2}):(\d{1,2})$/', $tiempoStr, $m)) {
        $mm = (int)$m[1];
        $ss = (int)$m[2];
        $total = $mm * 60 + $ss;
        return (int)round($total);
    } elseif (is_numeric($tiempoStr)) {
        return (int)round((float)$tiempoStr);
    } else {
        return 0;
    }
}

$duracion_segundos = convertirTiempoASegundos($tiempo);

// Obtener record previo (si existe)
$previous_record = null;
$stmt_prev = $conn->prepare("SELECT puntaje, duracion, nivel, lineas, fecha_jugada FROM record WHERE id_usuario = ? AND id_modo = ? LIMIT 1");
if ($stmt_prev) {
    $stmt_prev->bind_param("ii", $id_usuario, $id_modo);
    $stmt_prev->execute();
    $res_prev = $stmt_prev->get_result();
    if ($res_prev && $res_prev->num_rows > 0) {
        $previous_record = $res_prev->fetch_assoc();
    }
    $stmt_prev->close();
}

// Llamada al procedimiento almacenado
$es_nuevo_record = null;
$stmt = $conn->prepare("CALL GuardarRecord(?, ?, ?, ?, ?, ?, @es_nuevo_record)");
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error preparando llamada al procedimiento almacenado']);
    exit();
}

$stmt->bind_param("iiiiii", $id_usuario, $puntaje, $duracion_segundos, $nivel, $lineas, $id_modo);

$ok = $stmt->execute();
$stmt->close();

if (!$ok) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error ejecutando procedimiento almacenado']);
    exit();
}

// Obtener variable de salida
$result_out = $conn->query("SELECT @es_nuevo_record AS es_nuevo_record");
if ($result_out) {
    $row_out = $result_out->fetch_assoc();
    $es_nuevo_record = isset($row_out['es_nuevo_record']) ? (bool)$row_out['es_nuevo_record'] : null;
}

// Recuperar el record actual (después del SP)
$current_record = null;
$stmt_cur = $conn->prepare("SELECT puntaje, duracion, nivel, lineas, fecha_jugada FROM record WHERE id_usuario = ? AND id_modo = ? LIMIT 1");
if ($stmt_cur) {
    $stmt_cur->bind_param("ii", $id_usuario, $id_modo);
    $stmt_cur->execute();
    $res_cur = $stmt_cur->get_result();
    if ($res_cur && $res_cur->num_rows > 0) {
        $current_record = $res_cur->fetch_assoc();
    }
    $stmt_cur->close();
}

// Responder con JSON incluyendo previous_record y current_record
echo json_encode([
    'success' => true,
    'nuevo_record' => $es_nuevo_record ? true : false,
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

// Cerrar conexión
if (isset($conn)) $conn->close();
exit();
?>
