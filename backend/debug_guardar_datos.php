<?php
// Archivo de debug para guardar_datos_juego.php
header('Content-Type: application/json');

// Log de debug
$debug_log = [];

// Capturar todos los datos de entrada
$debug_log['timestamp'] = date('Y-m-d H:i:s');
$debug_log['method'] = $_SERVER['REQUEST_METHOD'];
$debug_log['post_data'] = $_POST;
$debug_log['session_data'] = isset($_SESSION) ? $_SESSION : 'No session';

// Verificar sesión
session_start();
$debug_log['session_after_start'] = $_SESSION;

// Verificar conexión a base de datos
include_once 'conn.php';
$debug_log['db_connection'] = $conn->connect_error ? 'Error: ' . $conn->connect_error : 'OK';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['nombre_usuario'])) {
    $debug_log['auth_status'] = 'Usuario no autenticado';
    http_response_code(401);
    echo json_encode([
        'error' => 'Usuario no autenticado',
        'debug' => $debug_log
    ]);
    exit();
}

$debug_log['auth_status'] = 'Usuario autenticado: ' . $_SESSION['nombre_usuario'];

// Verificar datos POST
$puntaje = isset($_POST['puntaje']) ? (int)$_POST['puntaje'] : 0;
$tiempo = isset($_POST['tiempo']) ? $_POST['tiempo'] : '00:00:00';
$nivel = isset($_POST['nivel']) ? (int)$_POST['nivel'] : 1;
$lineas = isset($_POST['lineas']) ? (int)$_POST['lineas'] : 0;

$debug_log['processed_data'] = [
    'puntaje' => $puntaje,
    'tiempo' => $tiempo,
    'nivel' => $nivel,
    'lineas' => $lineas
];

// Buscar usuario
$nombre_usuario = $_SESSION['nombre_usuario'];
$sql_usuario = "SELECT id_usuario FROM usuarios WHERE nombre_usuario = ?";
$stmt_usuario = $conn->prepare($sql_usuario);

if (!$stmt_usuario) {
    $debug_log['user_query_error'] = 'Error en preparación: ' . $conn->error;
    http_response_code(500);
    echo json_encode([
        'error' => 'Error en la preparación de la consulta de usuario',
        'debug' => $debug_log
    ]);
    exit();
}

$stmt_usuario->bind_param("s", $nombre_usuario);
$stmt_usuario->execute();
$result_usuario = $stmt_usuario->get_result();

if ($result_usuario->num_rows === 0) {
    $debug_log['user_not_found'] = 'Usuario no encontrado: ' . $nombre_usuario;
    http_response_code(404);
    echo json_encode([
        'error' => 'Usuario no encontrado en la base de datos',
        'debug' => $debug_log
    ]);
    exit();
}

$row_usuario = $result_usuario->fetch_assoc();
$id_usuario = $row_usuario['id_usuario'];
$debug_log['user_id'] = $id_usuario;

// Convertir tiempo
$duracion_segundos = 0;
if (preg_match('/^(\d{1,2}):(\d{1,2}):(\d{1,2})$/', $tiempo, $matches)) {
    $duracion_segundos = (int)$matches[1] * 60 + (int)$matches[2] + (int)$matches[3] / 100;
} elseif (preg_match('/^(\d{1,2}):(\d{1,2})$/', $tiempo, $matches)) {
    $duracion_segundos = (int)$matches[1] * 60 + (int)$matches[2];
} else {
    $duracion_segundos = (int)$tiempo;
}

$debug_log['time_conversion'] = [
    'original' => $tiempo,
    'seconds' => $duracion_segundos
];

// Intentar inserción
try {
    $sql = "INSERT INTO record (id_usuario, fecha_jugada, puntaje, duracion, nivel, lineas, id_modo) 
            VALUES (?, NOW(), ?, ?, ?, ?, 1)";
    
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        throw new Exception("Error en la preparación de la consulta: " . $conn->error);
    }
    
    $stmt->bind_param("iiiii", $id_usuario, $puntaje, $duracion_segundos, $nivel, $lineas);
    
    if ($stmt->execute()) {
        $debug_log['insert_success'] = true;
        $debug_log['inserted_id'] = $conn->insert_id;
        
        $response = [
            'success' => true,
            'message' => 'Datos guardados correctamente',
            'data' => [
                'puntaje' => $puntaje,
                'tiempo' => $tiempo,
                'nivel' => $nivel,
                'lineas' => $lineas,
                'duracion_segundos' => $duracion_segundos,
                'record_id' => $conn->insert_id
            ],
            'debug' => $debug_log
        ];
        echo json_encode($response);
    } else {
        throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
    }
    
} catch (Exception $e) {
    $debug_log['exception'] = $e->getMessage();
    http_response_code(500);
    $response = [
        'error' => 'Error interno del servidor',
        'message' => $e->getMessage(),
        'debug_info' => [
            'puntaje' => $puntaje,
            'tiempo' => $tiempo,
            'nivel' => $nivel,
            'lineas' => $lineas,
            'duracion_segundos' => $duracion_segundos,
            'id_usuario' => $id_usuario
        ],
        'debug' => $debug_log
    ];
    echo json_encode($response);
}

// Cerrar las conexiones
if (isset($stmt)) $stmt->close();
if (isset($stmt_usuario)) $stmt_usuario->close();
if (isset($conn)) $conn->close();
?> 