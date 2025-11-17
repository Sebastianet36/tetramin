<?php
include_once __DIR__ . '/../backend/session_init.php';

// Modo inicial
$modo = isset($_GET['modo']) ? intval($_GET['modo']) : 1;

// Mapear id → nombre
$modo_nombres = [
    1 => "Clásico",
    2 => "Carrera",
    3 => "Cheese",
    4 => "Supervivencia"
];

if (!isset($modo_nombres[$modo])) {
    $modo = 1;
}

// Detectar si está logueado o es invitado
$esta_logueado = isset($_SESSION['nombre_usuario']);

// Definir página de regreso según login
$pagina_atras = $esta_logueado
    ? "/tetramin-main/src/web/main_page/main_registrados.php"
    : "/tetramin-main/src/web/main_page/modo_invitado.html";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Leaderboard</title>

    <link rel="stylesheet" href="leaderboard.css">
</head>
<body>

    <div class="top-container">

        <!-- BOTÓN ATRÁS (dentro del recuadro) -->
        <div class="inside-header">
            <a href="<?php echo $pagina_atras; ?>" class="back-button">Atrás</a>
        </div>

        <div class="mode-switch">
            <button id="btn-prev" class="switch-btn">◀ Modo</button>
            <h2 id="titulo-modo" data-text="<?php echo $modo_nombres[$modo]; ?>">
                <?php echo $modo_nombres[$modo]; ?>
            </h2>
            <button id="btn-next" class="switch-btn">Modo ▶</button>
        </div>

        <ol id="leaderboard-list">
            <!-- Rankings dinámicos -->
        </ol>

    </div>

    <script>
    let modoActual = <?php echo $modo; ?>;

    const modoNombres = {
        1: "Clásico",
        2: "Carrera",
        3: "Cheese",
        4: "Supervivencia"
    };

    function cargarLeaderboard() {
        fetch(`leaderboard_data.php?modo=${modoActual}`)
            .then(res => res.json())
            .then(data => {
                const lista = document.getElementById("leaderboard-list");
                lista.innerHTML = "";

                if (!data || data.length === 0) {
                    lista.innerHTML = "<li>No hay datos disponibles.</li>";
                    return;
                }

                let rank = 1;
                data.forEach(row => {
                    // Crear contenedor li con la estructura correcta
                    const li = document.createElement("li");

                    // Rank
                    const spanRank = document.createElement("span");
                    spanRank.className = "rank";
                    spanRank.textContent = `#${rank}`;

                    // Name
                    const spanName = document.createElement("span");
                    spanName.className = "name";
                    spanName.textContent = row.nombre_usuario;

                    // Meta (dos columnas a la derecha)
                    const divMeta = document.createElement("div");
                    divMeta.className = "meta";

                    const spanPrimary = document.createElement("span");
                    spanPrimary.className = "primary";

                    const spanSecondary = document.createElement("span");
                    spanSecondary.className = "secondary";

                    if (modoActual === 2) {
                        // Sprint40: ordenar por tiempo asc (ya hecho en backend).
                        // Mostrar: tiempo (primario) + puntos (secundario)
                        spanPrimary.textContent = row.tiempo; // e.g. 01:25.3
                        spanSecondary.textContent = `${row.puntaje} pts`;
                    } else {
                        // Otros modos: ordenar por puntaje desc (backend)
                        // Mostrar: puntaje (primario) + lineas (secundario)
                        spanPrimary.textContent = `${row.puntaje} pts`;
                        spanSecondary.textContent = `${row.lineas} líneas`;
                    }

                    divMeta.appendChild(spanPrimary);
                    divMeta.appendChild(spanSecondary);

                    // Append all to li
                    li.appendChild(spanRank);
                    li.appendChild(spanName);
                    li.appendChild(divMeta);

                    lista.appendChild(li);
                    rank++;
                });

                // Actualizar título
                const titulo = document.getElementById("titulo-modo");
                const nuevoNombre = modoNombres[modoActual];
                titulo.textContent = nuevoNombre;
                titulo.setAttribute("data-text", nuevoNombre);

                // Reiniciar glitch
                titulo.classList.remove("glitch-reset");
                void titulo.offsetWidth;
                titulo.classList.add("glitch-reset");
            })
            .catch(err => {
                console.error("Error cargando leaderboard:", err);
                const lista = document.getElementById("leaderboard-list");
                lista.innerHTML = "<li>Error cargando datos.</li>";
            });
    }

    // Botón anterior
    document.getElementById("btn-prev").addEventListener("click", () => {
        modoActual--;
        if (modoActual < 1) modoActual = 4;
        cargarLeaderboard();
    });

    // Botón siguiente
    document.getElementById("btn-next").addEventListener("click", () => {
        modoActual++;
        if (modoActual > 4) modoActual = 1;
        cargarLeaderboard();
    });

    // Cargar al inicio
    cargarLeaderboard();
    </script>
</body>
</html>
