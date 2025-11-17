<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tetramindb";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode([]);
    exit();
}

$modo = isset($_GET['modo']) ? intval($_GET['modo']) : 1;

$sql = "SELECT u.nombre_usuario, r.puntaje AS max_puntaje
        FROM record r
        JOIN usuarios u ON r.id_usuario = u.id_usuario
        WHERE r.id_modo = ?
        ORDER BY r.puntaje DESC
        LIMIT 10";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $modo);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
