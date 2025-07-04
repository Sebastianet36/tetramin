<?php
session_start();
if (!isset($_SESSION['nombre_usuario'])) {
    header("Location: /Tetris-front/signin_page/signin.html");
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
                <a href="/Tetris-front/perfil_page/perfil.php" style="text-decoration: none;">
                    <div class="profile">Perfil</div>
                </a>
            </div>
        </div>
        
        <div class="main-content">
            <a href="/Tetris-front/leaderboard_page/leaderboard.html">
                <button class="sidebar">
                    <h3>Top 10 globalest</h3>
                </button>
            </a>
            
            <div class="content">
                <div class="modes-grid">
                    <a href="/Tetris-front/tetramin/index.html" class="mode-link">
                        <button class="mode mode-grid-item">
                            <div class="mode-content">
                                <div class="mode-title">Clásico</div>
                            </div>
                        </button>
                    </a>
                    <a href="#" class="mode-link">
                        <button class="mode mode-grid-item">
                            <div class="mode-content">
                                <div class="mode-title">Carrera</div>
                            </div>
                        </button>
                    </a>
                    <a href="#" class="mode-link">
                        <button class="mode mode-grid-item">
                            <div class="mode-content">
                                <div class="mode-title">Excavar</div>
                            </div>
                        </button>
                    </a>
                    <a href="#" class="mode-link">
                        <button class="mode mode-grid-item">
                            <div class="mode-content">
                                <div class="mode-title">Chill-Out</div>
                            </div>
                        </button>
                    </a>
                </div>
                
                <div class="config-section">
                    <a href="/Tetris-front/g.config_page/config.html" class="config-link">
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



