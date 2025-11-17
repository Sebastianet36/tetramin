<?php
// Modo inicial
$modo = isset($_GET['modo']) ? intval($_GET['modo']) : 1;

// Mapear id → nombre
$modo_nombres = [
    1 => "Clásico",
    2 => "Sprint 40",
    3 => "Cheese",
    4 => "Supervivencia"
];

// Sanitizar
if (!isset($modo_nombres[$modo])) {
    $modo = 1;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard</title>

    <link rel="stylesheet" href="leaderboard.css">

    <style>
        .mode-switch {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .switch-btn {
            padding: 0.7rem 1.2rem;
            background: #000;
            border: 2px solid #00ffff;
            border-radius: 8px;
            color: #00ffff;
            text-transform: uppercase;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 700;
            transition: 0.3s ease;
            text-decoration: none;
        }

        .switch-btn:hover {
            background: linear-gradient(45deg, #00ffff, #ff00ff);
            color: #000;
            transform: scale(1.05);
            box-shadow: 0 0 20px rgba(0,255,255,0.5);
        }
    </style>
</head>
<body>

    <div class="header">
        <a href="leaderboard_menu.php" class="back-button">Atrás</a>
    </div>

    <div class="top-container">

        <div class="mode-switch">
            <button id="btn-prev" class="switch-btn">◀ Modo</button>
            <h2 id="titulo-modo" data-text="<?php echo $modo_nombres[$modo]; ?>">
                <?php echo $modo_nombres[$modo]; ?>
            </h2>
            <button id="btn-next" class="switch-btn">Modo ▶</button>
        </div>

        <ol id="leaderboard-list">
            <!-- Rankings cargados dinámicamente -->
        </ol>

    </div>

    <script>
        let modoActual = <?php echo $modo; ?>;

        const modoNombres = {
            1: "Clásico",
            2: "Sprint 40",
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
                        const li = document.createElement("li");
                        li.innerHTML = `
                            <span class="rank">#${rank}</span>
                            ${row.nombre_usuario}
                            <span>${row.max_puntaje} pts</span>
                        `;
                        lista.appendChild(li);
                        rank++;
                    });

                    // Actualizar título dinámico
                    const titulo = document.getElementById("titulo-modo");
                    titulo.textContent = modoNombres[modoActual];
                    titulo.setAttribute("data-text", modoNombres[modoActual]);
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
