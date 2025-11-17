<?php
include_once __DIR__ . '/../backend/session_init.php';
include_once __DIR__ . '/../backend/conn.php';

// 🔹 FUNCIÓN PARA FORMATEAR DURACIÓN
function formatearDuracion($seg) {
    if ($seg === null) return "00:00.0";
    $seg = floatval($seg);
    $min = floor($seg / 60);
    $sec = floor($seg % 60);
    $dec = floor(($seg - floor($seg)) * 10);
    return sprintf("%02d:%02d.%d", $min, $sec, $dec);
}

// AUTLOGIN (si existe cookie)
if (!isset($_SESSION['id_usuario']) && isset($_COOKIE['recordarme'])) {
    $token = $_COOKIE['recordarme'];

    $stmt = $conn->prepare("SELECT id_usuario, nombre_usuario FROM usuarios WHERE token_recordar = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {
        $fila = $res->fetch_assoc();
        $_SESSION['id_usuario'] = $fila['id_usuario'];
        $_SESSION['nombre_usuario'] = $fila['nombre_usuario'];
    }
}

// Si NO está logueado → redirigir
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../signin_page/signin.html");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];
$nombre_usuario = $_SESSION['nombre_usuario'];

/* ------------------------------ */
/* 🟦 1) OBTENER DATOS DEL USUARIO */
/* ------------------------------ */
$sql_user = "SELECT email, fecha_registro, ubicacion 
             FROM usuarios 
             WHERE id_usuario = ?";
$stmt = $conn->prepare($sql_user);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$datos_usuario = $stmt->get_result()->fetch_assoc();

/* ------------------------------ */
/* 🟩 2) OBTENER RECORD POR MODO   */
/* ------------------------------ */
$modos = [
    1 => "Clásico",
    2 => "Carrera",
    3 => "Cheese",
    4 => "Supervivencia"
];

$records = [];

$sql_record = "
SELECT r.*, 
       (SELECT COUNT(*) + 1
        FROM record r2
        WHERE r2.id_modo = r.id_modo
        AND r2.puntaje > r.puntaje
       ) AS ranking
FROM record r
WHERE r.id_usuario = ? AND r.id_modo = ?
";

$stmt_record = $conn->prepare($sql_record);

foreach ($modos as $modo_id => $nombre_modo) {
    $stmt_record->bind_param("ii", $id_usuario, $modo_id);
    $stmt_record->execute();
    $res = $stmt_record->get_result();

    $records[$modo_id] = ($res->num_rows === 1) ? $res->fetch_assoc() : null;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cyberpunk Profile</title>
    <link rel="stylesheet" href="perfil.css">
</head>
<body>
    <div class="container">
        
        <!-- Header -->
        <div class="header">
            <?php echo htmlspecialchars($nombre_usuario); ?>
            <a href="../main_page/main_registrados.php" class="back-button">Atras</a>
        </div>

        <div class="main-content">
            
            <!-- LEFT SIDEBAR -->
            <div class="sidebar">

                <!-- Profile Card -->
                <div class="profile-card">
                    <div class="profile-picture">Profile Picture</div>
                </div>

                <!-- Stats Card -->
                <div class="stats-card">

                    <div class="stat-item">
                        <span class="stat-label">Email</span>
                        <span class="stat-value"><?php echo $datos_usuario['email']; ?></span>
                    </div>

                    <div class="stat-item">
                        <span class="stat-label">Registrado</span>
                        <span class="stat-value"><?php echo $datos_usuario['fecha_registro']; ?></span>
                    </div>

                    <div class="stat-item">
                        <span class="stat-label">Ubicación</span>
                        <span class="stat-value"><?php echo $datos_usuario['ubicacion']; ?></span>
                    </div>

                    <div class="stat-item">
                        <a href="./cambiar_contraseña/cambiar_contraseña.php" class="stat-label">Cambiar contraseña</a>
                    </div>

                    <div class="stat-item">
                        <a href="./p_config_page/p_config.html" class="stat-label">Configuración</a>
                    </div>

                    <div class="stat-item">
                        <a href="../backend/logout.php" class="stat-label">Cerrar Sesión</a>
                    </div>

                </div>
            </div>

            <!-- RECORDS SECTION -->
            <div class="records-section">

                <?php foreach ($modos as $id_modo => $nombre_modo): 
                    $r = $records[$id_modo]; ?>

                <div class="record-mode">
                    <div class="mode-title">Record <?php echo $nombre_modo; ?></div>

                    <?php if ($r): ?>
                    <div class="mode-stats">

                        <div class="mode-stat">
                            <div class="mode-stat-label">Puntaje</div>
                            <div class="mode-stat-value"><?php echo $r['puntaje']; ?></div>
                        </div>

                        <div class="mode-stat">
                            <div class="mode-stat-label">Nivel</div>
                            <div class="mode-stat-value"><?php echo $r['nivel']; ?></div>
                        </div>

                        <div class="mode-stat">
                            <div class="mode-stat-label">Líneas</div>
                            <div class="mode-stat-value"><?php echo $r['lineas']; ?></div>
                        </div>

                        <div class="mode-stat">
                            <div class="mode-stat-label">Tiempo</div>
                            <div class="mode-stat-value">
                                <?php echo formatearDuracion($r['duracion']); ?>
                            </div>
                        </div>

                        <div class="mode-stat">
                            <div class="mode-stat-label">Ranking</div>
                            <div class="mode-stat-value">#<?php echo $r['ranking']; ?></div>
                        </div>
                    </div>

                    <?php else: ?>
                    <p style="color:#0ff; padding:10px;">Sin registros aún</p>
                    <?php endif; ?>

                </div>

                <?php endforeach; ?>

            </div>

        </div>
    </div>
</body>
</html>
