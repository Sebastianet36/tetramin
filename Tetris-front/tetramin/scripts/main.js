import { Game } from "/Tetris-front/tetramin/scripts/game.js";

let isPaused = false;
let animationId = null;
let game;
let countdown = 3;

const canvasTetris = document.getElementById("canvas-tetris");
const canvasNext = document.getElementById("canvas-next");
const canvasHold = document.getElementById("canvas-hold");
const canvasLines = document.getElementById("canvas-lines");
const canvasLevel = document.getElementById("canvas-level");
const canvasTime = document.getElementById("canvas-time");
const canvasScore = document.getElementById("canvas-score");

const ctx = canvasTetris.getContext("2d");

function gameLoop() {
    if (isPaused) return;

    if (!game.isGameOver) {
        game.update();
        animationId = requestAnimationFrame(gameLoop);
    }
}

function togglePause() {
    if (!isPaused) {
        isPaused = true;
        cancelAnimationFrame(animationId);
        game.setPaused(true);
    } else {
        isPaused = false;
        game.setPaused(false);
        requestAnimationFrame(gameLoop);
    }
}

function drawCountdown(number) {
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

function fadeOutGO() {
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
            togglePause(); // ← arranca el juego real
        }
    }, 50);
}

function startCountdown() {
    const interval = setInterval(() => {
        if (countdown > 0) {
            drawCountdown(countdown);
        } else if (countdown === 0) {
            drawCountdown("GO!");
            setTimeout(() => fadeOutGO(), 100);
        }

        countdown--;
        if (countdown < 0) {
            clearInterval(interval);
        }
    }, 1000);
}

window.addEventListener("DOMContentLoaded", () => {
    canvasLines.width = 130;
    canvasLines.height = 110;
    canvasLevel.width = 130;
    canvasLevel.height = 110;
    canvasTime.width = 280;
    canvasTime.height = 110;
    canvasScore.width = 280;
    canvasScore.height = 110;

    // Crear el juego desde el principio pero en pausa
    game = new Game(
        canvasTetris,
        20, 10, 26, 2,
        canvasNext,
        canvasHold,
        canvasLines,
        canvasLevel,
        canvasTime,
        canvasScore
    );

    isPaused = true;
    game.setPaused(true);
    startCountdown(); // Mostrar 3, 2, 1, GO!
});