import { Game } from "./game.js";

const canvasTetris = document.getElementById("canvas-tetris");
const canvasNext = document.getElementById("canvas-next");
const canvasHold = document.getElementById("canvas-hold");
const canvasLines = document.getElementById("canvas-lines");
const canvasLevel = document.getElementById("canvas-level");
const canvasTime = document.getElementById("canvas-time");
const canvasScore = document.getElementById("canvas-score");

canvasLines.width = 130;
canvasLines.height = 110;
canvasLevel.width = 130;
canvasLevel.height = 110;
canvasTime.width = 280;
canvasTime.height = 110;
canvasScore.width = 280;
canvasScore.height = 110;
canvasNext.width = 130;
canvasNext.height = 300;

let rPressed = false;
let rStartTime = 0;
let rDuration = 1000;
let rFrameId = null;

const rows = 20;
const cols = 10;
const cellSize = 26;
const space = 2;

let game = new Game(
    canvasTetris,
    rows,
    cols,
    cellSize,
    space,
    canvasNext,
    canvasHold,
    canvasLines,
    canvasLevel,
    canvasTime,
    canvasScore
);

game.initializeHUD();
game.boardTetris.draw();
game.next.draw2();
game.hold.draw2();

let gameLoopId;
function gameLoop() {
  if (!game.isGameOver) {
    game.update();
    gameLoopId = requestAnimationFrame(gameLoop);
  } else {
    showGameOverOverlay();
  }
}



function drawCountdown(number) {
    const ctx = canvasTetris.getContext("2d");
    ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);
    ctx.save();
    ctx.font = "80px 'Orbitron', sans-serif";
    ctx.fillStyle = "#00ffff";
    ctx.textAlign = "center";
    ctx.textBaseline = "middle";
    ctx.shadowColor = "#00ffff";
    ctx.shadowBlur = 10;
    ctx.fillText(number, canvasTetris.width / 2, canvasTetris.height / 2);
    ctx.restore();
}

function fadeOutGO(callback) {
    const ctx = canvasTetris.getContext("2d");
    let opacity = 1;
    const fadeInterval = setInterval(() => {
        ctx.clearRect(0, 0, canvasTetris.width, canvasTetris.height);
        ctx.save();
        ctx.globalAlpha = opacity;
        ctx.font = "80px 'Orbitron', sans-serif";
        ctx.fillStyle = "#00ffff";
        ctx.textAlign = "center";
        ctx.textBaseline = "middle";
        ctx.shadowColor = "#00ffff";
        ctx.shadowBlur = 10;
        ctx.fillText("GO!", canvasTetris.width / 2, canvasTetris.height / 2);
        ctx.restore();

        opacity -= 0.1;
        if (opacity <= 0) {
            clearInterval(fadeInterval);
            ctx.clearRect(0, 0, canvasTetris.width, canvasTetris.height);
            callback();
        }
    }, 50);
}

function startCountdownAndGame() {
    let countdown = 3;
    drawCountdown(countdown);
    const interval = setInterval(() => {
        countdown--;
        if (countdown > 0) {
            drawCountdown(countdown);
        } else if (countdown === 0) {
            drawCountdown("GO!");
            setTimeout(() => {
                clearInterval(interval);
                fadeOutGO(() => {
                    game.startGame();
                    requestAnimationFrame(gameLoop);
                });
            }, 500);
        }
    }, 1000);
}

function showGameOverOverlay() {
    const goBox = document.getElementById("game-over");
    document.getElementById("go-score").textContent = game.score;
    document.getElementById("go-time").textContent = game.finalTimeFormatted || "00:00:00";
    document.getElementById("go-level").textContent = game.level;
    document.getElementById("go-lines").textContent = game.totalLines;
    goBox.style.display = "flex";
    
    // Enviar datos al servidor
    enviarDatosAlServidor();
}

//---------------------------------------------------------------
function enviarDatosAlServidor() {
    // Crear un formulario temporal para enviar los datos
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '../../backend/guardar_datos_juego.php';
    
    // Agregar los campos con los datos del juego
    const campos = [
        { name: 'puntaje', value: game.score },
        { name: 'tiempo', value: game.finalTimeFormatted || '00:00:00' },
        { name: 'nivel', value: game.level },
        { name: 'lineas', value: game.totalLines }
    ];
    
    campos.forEach(campo => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = campo.name;
        input.value = campo.value;
        form.appendChild(input);
    });
    
    // Agregar el formulario al DOM y enviarlo
    document.body.appendChild(form);
    form.submit();
    
    // Remover el formulario después de enviarlo
    setTimeout(() => {
        document.body.removeChild(form);
    }, 1000);
}
//---------------------------------------------------------------

// 🔄 Reiniciar partida completa
function restartGame() {
    // 1. Cancelar animación del loop anterior si existe
    if (typeof gameLoopId !== "undefined") {
        cancelAnimationFrame(gameLoopId);
    }

    // 2. Ocultar overlays (Game Over, Escape)
    const goPopup = document.getElementById("game-over");
    if (goPopup) goPopup.style.display = "none";

    const exitWarning = document.getElementById("exit-warning");
    if (exitWarning) {
        exitWarning.style.display = "none";
        const exitBar = exitWarning.querySelector(".exit-bar");
        if (exitBar) exitBar.style.width = "0%";
        const exitMessage = document.getElementById("exit-message");
        if (exitMessage) exitMessage.textContent = "Saliendo...";
    }

    // 3. Resetear flags de reinicio
    rPressed = false;
    if (typeof rFrameId !== "undefined") cancelAnimationFrame(rFrameId);

    // 4. Borrar instancia previa del juego
    game = null;

    // 5. Crear nueva instancia limpia
    game = new Game(
        canvasTetris,
        rows,
        cols,
        cellSize,
        space,
        canvasNext,
        canvasHold,
        canvasLines,
        canvasLevel,
        canvasTime,
        canvasScore
    );

    // 6. Mostrar estado inicial congelado
    game.initializeHUD();
    game.boardTetris.draw();
    game.next.draw2();
    game.hold.draw2();

    // 7. Esperar cuenta regresiva antes de empezar
    startCountdownAndGame();
}




//botones
document.getElementById("retry-button").addEventListener("click", () => {
    document.getElementById("game-over").style.display = "none";
    restartGame();
});

document.getElementById("exit-button").addEventListener("click", () => {
    window.location.href = "../main_page/main_registrados.php";
});


startCountdownAndGame();