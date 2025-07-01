<!DOCTYPE html>
<html lang="en">
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
            <?php echo strtoupper(htmlspecialchars($_SESSION['nombre_usuario'])); ?>
            <a href="Tetris-front/main_page/main_registrados.php" class="back-button">Atras</a>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Left Sidebar -->
            <div class="sidebar">
                <!-- Profile Card -->
                <div class="profile-card">
                    <div class="profile-picture">
                        Profile Picture
                    </div>
                </div>

                <!-- Stats Card -->
                <div class="stats-card">
                    <div class="stat-item">
                        <span class="stat-label">Hours Played</span>
                        <span class="stat-value">247.5</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">User Level</span>
                        <span class="stat-value">Level 42</span>
                    </div>
                </div>
            </div>

            <!-- Records Section -->
            <div class="records-section">
                <!-- Record Mode 1 -->
                <div class="record-mode">
                    <div class="mode-title">Record Modo 1</div>
                    <div class="mode-stats">
                        <div class="mode-stat">
                            <div class="mode-stat-label">Total Pieces</div>
                            <div class="mode-stat-value">15,847</div>
                        </div>
                        <div class="mode-stat">
                            <div class="mode-stat-label">Pieces/Second</div>
                            <div class="mode-stat-value">4.2</div>
                        </div>
                        <div class="mode-stat">
                            <div class="mode-stat-label">Ranking</div>
                            <div class="mode-stat-value">#127</div>
                        </div>
                    </div>
                </div>

                <!-- Record Mode 2 -->
                <div class="record-mode">
                    <div class="mode-title">Record Modo 2</div>
                    <div class="mode-stats">
                        <div class="mode-stat">
                            <div class="mode-stat-label">Total Pieces</div>
                            <div class="mode-stat-value">23,156</div>
                        </div>
                        <div class="mode-stat">
                            <div class="mode-stat-label">Pieces/Second</div>
                            <div class="mode-stat-value">5.8</div>
                        </div>
                        <div class="mode-stat">
                            <div class="mode-stat-label">Ranking</div>
                            <div class="mode-stat-value">#89</div>
                        </div>
                    </div>
                </div>

                <!-- Record Mode 3 -->
                <div class="record-mode">
                    <div class="mode-title">Record Modo 3</div>
                    <div class="mode-stats">
                        <div class="mode-stat">
                            <div class="mode-stat-label">Total Pieces</div>
                            <div class="mode-stat-value">31,492</div>
                        </div>
                        <div class="mode-stat">
                            <div class="mode-stat-label">Pieces/Second</div>
                            <div class="mode-stat-value">6.7</div>
                        </div>
                        <div class="mode-stat">
                            <div class="mode-stat-label">Ranking</div>
                            <div class="mode-stat-value">#45</div>
                        </div>
                    </div>
                </div>

                <!-- Record Mode 4 -->
                <div class="record-mode">
                    <div class="mode-title">Record Modo 4</div>
                    <div class="mode-stats">
                        <div class="mode-stat">
                            <div class="mode-stat-label">Total Pieces</div>
                            <div class="mode-stat-value">18,763</div>
                        </div>
                        <div class="mode-stat">
                            <div class="mode-stat-label">Pieces/Second</div>
                            <div class="mode-stat-value">3.9</div>
                        </div>
                        <div class="mode-stat">
                            <div class="mode-stat-label">Ranking</div>
                            <div class="mode-stat-value">#156</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Configurations Section -->
        <div class="configurations">
            <h2 class="config-title">Configurations</h2>
        </div>
    </div>
</body>
</html>