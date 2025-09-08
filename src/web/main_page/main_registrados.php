<?php
include_once __DIR__ . '/../backend/conn.php';
session_start();
if (!isset($_SESSION['nombre_usuario'])) {
    header("Location: ../signin_page/signin.html");
    exit();
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard Interface</title>
    <link rel="stylesheet" href="main_registrados.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-left">TETRAMIN</div>
            <div class="header-right">
                <div class="account-name">
                    <?php echo htmlspecialchars($_SESSION['nombre_usuario']); ?>
                </div>
                <a href="../perfil_page/perfil.php" style="text-decoration: none;">
                    <div class="profile">Perfil</div>
                </a>
            </div>
        </div>
        
        <div class="main-content">
            <a href="../leaderboard_page/leaderboard.php">
                <button class="sidebar">
                    <h3>Top 10 globalest</h3>
                </button>
            </a>
            
            <div class="content">
                <div class="modes-grid">
                    <a href="../games/index.html?mode=classic" class="mode-link">
                        <button class="mode mode-grid-item">
                            <div class="mode-content">
                                <div class="mode-title">Clásico</div>
                            </div>
                        </button>
                    </a>
                    <a href="../games/index.html?mode=sprint40" class="mode-link">
                        <button class="mode mode-grid-item">
                            <div class="mode-content">
                                <div class="mode-title">Carrera</div>
                            </div>
                        </button>
                    </a>
                    <a href="../games/index.html?mode=cheese" class="mode-link">
                        <button class="mode mode-grid-item">
                            <div class="mode-content">
                                <div class="mode-title">Cheese!</div>
                            </div>
                        </button>
                    </a>
                    <a href="../games/index.html?mode=survival" class="mode-link">
                        <button class="mode mode-grid-item">
                            <div class="mode-content">
                                <div class="mode-title">Supervivencia</div>
                            </div>
                        </button>
                    </a>
                </div>
                
                <div class="config-section">
                    <a href="../g.confi_page/config.html" class="config-link">
                        <button class="mode mode-config">
                            <div class="config-content">
                                <div class="config-title">Configuraciones</div>
                            </div>
                        </button>
                    </a>
                </div>
            </div>
        </div>
        <footer>
            <p>&copy; 2025 Mi Sitio Web. Todos los derechos reservados.</p>
            <p>Contacta con nosotros: <a href="mailto:info@misiito.com">info@misiito.com</a></p>
            <nav>
                <ul>
                <li><a href="/acerca-de">Acerca de nosotros</a></li>
                <li><a href="/contacto">Contacto</a></li>
                <li><a href="/politica-de-privacidad">Política de Privacidad</a></li>
                <li><a href="https://github.com/Sebastianet36/Tetris-front">Github</a></li>
                </ul>
            </nav>
        </footer>
    </div>
</body>
</html>




