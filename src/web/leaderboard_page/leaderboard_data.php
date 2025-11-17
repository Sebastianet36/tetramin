<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tetramindb";

$conn = new mysqli($servername, $username, $password, $dbname);

// -------- FORMATEAR DURACIÓN --------
function formatearDuracion($seg) {
    if ($seg === null) return "00:00.0";
    $seg = floatval($seg);
    $min = floor($seg / 60);
    $sec = floor($seg % 60);
    $dec = floor(($seg - floor($seg)) * 10);
    return sprintf("%02d:%02d.%d", $min, $sec, $dec);
}

if ($conn->connect_error) {
    echo json_encode([]);
    exit();
}

$modo = isset($_GET['modo']) ? intval($_GET['modo']) : 1;

// -------- SQL DIFERENTE PARA SPRINT 40 --------
if ($modo === 2) {
    // Sprint 40 → ordenar por tiempo ASC
    $sql = "
        SELECT u.nombre_usuario, r.puntaje, r.lineas, r.duracion
        FROM record r
        JOIN usuarios u ON r.id_usuario = u.id_usuario
        WHERE r.id_modo = ?
        ORDER BY r.duracion ASC
        LIMIT 10
    ";
} else {
    // Otros modos → ordenar por puntaje DESC
    $sql = "
        SELECT u.nombre_usuario, r.puntaje, r.lineas, r.duracion
        FROM record r
        JOIN usuarios u ON r.id_usuario = u.id_usuario
        WHERE r.id_modo = ?
        ORDER BY r.puntaje DESC
        LIMIT 10
    ";
}

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $modo);
$stmt->execute();
$res = $stmt->get_result();

$data = [];

while ($row = $res->fetch_assoc()) {
    $row['tiempo'] = formatearDuracion($row['duracion']);
    $data[] = $row;
}

echo json_encode($data);
