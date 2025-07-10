<?php
// Archivo test
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once 'conn.php';

echo "<h2>Prueba de Conexión a Base de Datos</h2>";

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
} else {
    echo "<p style='color: green;'>✅ Conexión exitosa a la base de datos</p>";
}

// Verificar si las tablas existen
$tables = ['usuarios', 'record', 'modojuego', 'idioma', 'configuracion', 'usuariomodojuego'];

echo "<h3>Verificación de Tablas:</h3>";
foreach ($tables as $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    if ($result->num_rows > 0) {
        echo "<p style='color: green;'>✅ Tabla '$table' existe</p>";
        
        // Mostrar estructura de la tabla
        $structure = $conn->query("DESCRIBE $table");
        echo "<details><summary>Estructura de $table</summary><ul>";
        while ($row = $structure->fetch_assoc()) {
            echo "<li>{$row['Field']} - {$row['Type']} - {$row['Null']} - {$row['Key']}</li>";
        }
        echo "</ul></details>";
    } else {
        echo "<p style='color: red;'>❌ Tabla '$table' NO existe</p>";
    }
}

// Verificar datos en tablas importantes
echo "<h3>Datos en Tablas:</h3>";

// Verificar usuarios
$usuarios = $conn->query("SELECT COUNT(*) as count FROM usuarios");
if ($usuarios) {
    $count = $usuarios->fetch_assoc()['count'];
    echo "<p>👥 Usuarios registrados: $count</p>";
}

// Verificar modos de juego
$modos = $conn->query("SELECT * FROM modojuego");
if ($modos && $modos->num_rows > 0) {
    echo "<p>🎮 Modos de juego disponibles:</p><ul>";
    while ($row = $modos->fetch_assoc()) {
        echo "<li>ID: {$row['id_modo']} - {$row['nombre_modo']}</li>";
    }
    echo "</ul>";
}

// Verificar records
$records = $conn->query("SELECT COUNT(*) as count FROM record");
if ($records) {
    $count = $records->fetch_assoc()['count'];
    echo "<p>🏆 Records guardados: $count</p>";
}

// Probar inserción de datos de ejemplo
echo "<h3>Prueba de Inserción:</h3>";
if (isset($_POST['test_insert'])) {
    try {
        // Buscar un usuario existente
        $user_result = $conn->query("SELECT id_usuario FROM usuarios LIMIT 1");
        if ($user_result && $user_result->num_rows > 0) {
            $user = $user_result->fetch_assoc();
            $id_usuario = $user['id_usuario'];
            
            $sql = "INSERT INTO record (id_usuario, fecha_jugada, puntaje, duracion, nivel, lineas, id_modo) 
                    VALUES (?, NOW(), ?, ?, ?, ?, 1)";
            $stmt = $conn->prepare($sql);
            $puntaje = 1000;
            $duracion = 120;
            $nivel = 5;
            $lineas = 20;
            
            $stmt->bind_param("iiiii", $id_usuario, $puntaje, $duracion, $nivel, $lineas);
            
            if ($stmt->execute()) {
                echo "<p style='color: green;'>✅ Inserción de prueba exitosa</p>";
            } else {
                echo "<p style='color: red;'>❌ Error en inserción: " . $stmt->error . "</p>";
            }
            $stmt->close();
        } else {
            echo "<p style='color: orange;'>⚠️ No hay usuarios en la base de datos para probar la inserción</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    }
}

echo "<form method='post'><button type='submit' name='test_insert'>Probar Inserción de Datos</button></form>";

$conn->close();
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
details { margin: 10px 0; }
ul { margin: 5px 0; }
button { padding: 10px 20px; background: #007cba; color: white; border: none; border-radius: 5px; cursor: pointer; }
button:hover { background: #005a87; }
</style> 