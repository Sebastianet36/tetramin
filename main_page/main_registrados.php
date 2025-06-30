<?php
session_start();
if (!isset($_SESSION['nombre_usuario'])) {
    header("Location: /Tetris-front/signin_page/signin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard Interface</title>
    <link rel="stylesheet" href="main_page.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-left">Leaderboard</div>
            <div class="header-right">
                <div class="account-name">
                    <?php echo htmlspecialchars($_SESSION['nombre_usuario']); ?>
                </div>
                <div class="profile"><a href="/Tetris-front/p.config_page/profile.html">Perfil</a></div>
            </div>
        </div>
        
        <div class="main-content">
            <div class="sidebar">
                <h3><a href="/Tetris-front/leaderboard_page/leaderboard.html">Top 10 globalest</a></h3>
            </div>
            
            <div class="content">
                <div class="mode"><a href="/Tetris-front/tetramin/index.html">Modo 1</a></div>
                <div class="mode">Modo 2</div>
                <div class="mode">Modo 3</div>
                <div class="mode">Modo 4</div>
                <div class="mode"><a href="/Tetris-front/g.confi_page/config.html">Configuraciones</a></div>
            </div>
        </div>
        <footer>
            <p>&copy; 2025 Mi Sitio Web. Todos los derechos reservados.</p>
            <p>Contacta con nosotros: <a href="mailto:info@misiito.com">info@misiito.com</a></p>
            <nav>
                <ul>
                <li><a href="/acerca-de">Acerca de</a></li>
                <li><a href="/contacto">Contacto</a></li>
                <li><a href="/politica-de-privacidad">Política de Privacidad</a></li>
                </ul>
            </nav>
        </footer>
    </div>
</body>
</html>
