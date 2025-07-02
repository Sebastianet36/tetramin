import { Game } from "/Tetris-front/tetramin/scripts/game.js";

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
game.drawTetrominoGhost();
game.currentTetromino.draw(game.boardTetris);

function gameLoop() {
    if (!game.isGameOver) {
        game.update();
        requestAnimationFrame(gameLoop);
    } else {
        console.log("Game Over");
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

startCountdownAndGame();