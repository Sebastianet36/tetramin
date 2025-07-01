<?php
// Configuración de la base de datos
$servername = "localhost"; // Reemplaza con tu host de base de datos
$username = "root"; // Reemplaza con tu nombre de usuario de la base de datos
$password = ""; // Reemplaza con tu contraseña de la base de datos
$dbname = "tetrisdb"; // Tu nombre de la base de datos

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Consulta SQL para obtener el top 10 de usuarios con su puntaje máximo de la tabla 'record'
// Se une record con usuariomodojuego por id_modo, y luego con usuarios por id_usuario
// Esto permite asociar los registros de juego con los usuarios.
// Se agrupa por nombre_usuario para obtener el puntaje más alto de cada jugador.
$sql = "SELECT u.nombre_usuario, MAX(r.puntaje) AS max_puntaje
        FROM record r
        JOIN usuariomodojuego umj ON r.id_modo = umj.id_modo
        JOIN usuarios u ON umj.id_usuario = u.id_usuario
        GROUP BY u.nombre_usuario
        ORDER BY max_puntaje DESC
        LIMIT 10";

$result = $conn->query($sql);

$leaderboard_data = [];
if ($result->num_rows > 0) {
    // Recorrer los resultados y almacenar en un array
    while($row = $result->fetch_assoc()) {
        $leaderboard_data[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Top 10 Global</title>
    <!-- Asegúrate de que leaderboard.css esté en la misma carpeta o ajusta la ruta -->
    <link rel="stylesheet" href="leaderboard.css">
</head>
<body>
    <div class="top-container">
        <h2>Top 10 Global</h2>
        <ol>
            <?php
            $rank = 1;
            // Verificar si hay datos en el leaderboard_data
            if (!empty($leaderboard_data)) {
                foreach ($leaderboard_data as $player) {
                    echo '<li><span class="rank">#' . $rank . '</span> ' . htmlspecialchars($player["nombre_usuario"]) . ' <span>' . htmlspecialchars($player["max_puntaje"]) . ' pts</span></li>';
                    $rank++;
                }
            } else {
                echo '<li>No hay datos de ranking disponibles.</li>';
            }
            ?>
        </ol>
    </div>
</body>
</html>
