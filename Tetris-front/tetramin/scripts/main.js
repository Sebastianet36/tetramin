import { Game } from "/Tetris-front/tetramin/scripts/game.js";

const canvasTetris = document.getElementById("canvas-tetris");
const canvasNext = document.getElementById("canvas-next");
const canvasHold = document.getElementById("canvas-hold");
const canvasLines = document.getElementById("canvas-lines");
const canvasLevel = document.getElementById("canvas-level");
const canvasTime = document.getElementById("canvas-time");
const canvasScore = document.getElementById("canvas-score");

// Ajuste de resolución interna sin cambiar tamaño visual
canvasLines.width = 130;
canvasLines.height = 110;
canvasLevel.width = 130;
canvasLevel.height = 110;
canvasTime.width = 280;
canvasTime.height = 110;
canvasScore.width = 280;
canvasScore.height = 110;

const rows = 20;
const cols = 10;
const cellSize = 26;
const space = 2;

const game = new Game(
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

// === Bucle principal ===
function gameLoop() {
  if (!game.isGameOver) {
    game.update();
    requestAnimationFrame(gameLoop);
  } else {
    // Podés mostrar mensaje de fin de juego o reiniciar
    console.log("Game Over");
  }
}

requestAnimationFrame(gameLoop);
