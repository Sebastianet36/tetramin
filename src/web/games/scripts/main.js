import { Game } from "./game.js";
import { enviarDatosAlServidor } from "../../backend/datos_partida.js";

// ----------------- PARAMETROS Y MODOS -----------------
const urlParams = new URLSearchParams(window.location.search);
const selectedMode = urlParams.get("mode") || "classic";
let currentMode = null;

async function loadMode() {
  if (selectedMode !== "classic") {
    try {
      // subir un nivel porque modes/ no está dentro de scripts/
      currentMode = await import(`../modes/${selectedMode}.js`).then(m => m.default);
      console.log(`Modo cargado: ${currentMode.name}`);
    } catch (err) {
      console.error("Error cargando el modo:", err);
    }
  } else {
    console.log("Modo clásico seleccionado");
  }
}


// ----------------- CANVAS -----------------
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

// ----------------- VARIABLES GLOBALES -----------------
let game;
let gameLoopId;

// ----------------- FUNCIONES PRINCIPALES -----------------
function initGame() {
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
  game.initializeHUD();
  game.boardTetris.draw();
  game.next.draw2();
  game.hold.draw2();
}

function gameLoop() {
  if (!game.isGameOver) {
    game.update();
    if (currentMode && currentMode.update) {
      currentMode.update(game);
    }
    gameLoopId = requestAnimationFrame(gameLoop);
  } else {
    if (currentMode && currentMode.onGameOver) {
      currentMode.onGameOver(game);
    }
    showGameOverOverlay();
  }
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
          if (currentMode && currentMode.init) currentMode.init(game);
          requestAnimationFrame(gameLoop);
        });
      }, 500);
    }
  }, 1000);
}

function restartGame() {
  cancelAnimationFrame(gameLoopId);
  initGame();
  game.startGame();
  if (currentMode && currentMode.init) currentMode.init(game);
  requestAnimationFrame(gameLoop);
}

// ----------------- UI: COUNTDOWN -----------------
function drawCountdown(text) {
  const ctx = canvasTetris.getContext("2d");
  ctx.clearRect(0, 0, canvasTetris.width, canvasTetris.height);
  ctx.fillStyle = "rgba(0, 0, 0, 0.7)";
  ctx.fillRect(0, 0, canvasTetris.width, canvasTetris.height);
  ctx.fillStyle = "#fff";
  ctx.font = "48px Arial";
  ctx.textAlign = "center";
  ctx.fillText(text, canvasTetris.width / 2, canvasTetris.height / 2);
}

function fadeOutGO(callback) {
  const ctx = canvasTetris.getContext("2d");
  let opacity = 1;
  const fade = () => {
    ctx.clearRect(0, 0, canvasTetris.width, canvasTetris.height);
    ctx.fillStyle = `rgba(0, 0, 0, ${opacity})`;
    ctx.fillRect(0, 0, canvasTetris.width, canvasTetris.height);
    opacity -= 0.05;
    if (opacity > 0) {
      requestAnimationFrame(fade);
    } else {
      callback();
    }
  };
  fade();
}

// ----------------- UI: GAME OVER -----------------
function showGameOverOverlay() {
  const goBox = document.getElementById("game-over");

  // Detectar si se ganó o perdió
  const isWin = (currentMode && currentMode.isWin && currentMode.isWin(game));

  document.querySelector("#game-over h2").textContent = isWin ? "¡Ganaste!" : "¡Perdiste!";

  document.getElementById("go-score").textContent = game.score;
  document.getElementById("go-time").textContent = game.finalTimeFormatted || "00:00:00";
  document.getElementById("go-level").textContent = game.level;
  document.getElementById("go-lines").textContent = game.totalLines;

  goBox.style.display = "flex";

  enviarDatosAlServidor(
    {
      puntaje: game.score,
      tiempo: game.finalTimeFormatted || "00:00:00",
      nivel: game.level,
      lineas: game.totalLines,
      modo: selectedMode // datos_partida.js mapeará a id_modo
    },
    "/tetramin-main/src/web/backend/guardar_datos_juego.php"
  ).then(resp => {
    if (!resp || !resp.success) return;
    // Si existe previous_record, mostrar comparación
    const prev = resp.previous_record;
    const cur = resp.record;
    if (resp.nuevo_record) {
      // ejemplo simple: mostrar en overlay
      const el = document.getElementById('go-prev-record');
      if (el) el.textContent = prev ? `Anterior: ${prev.puntaje}` : 'Anterior: -';
      const el2 = document.getElementById('go-new-record');
      if (el2) el2.textContent = `Nuevo: ${cur ? cur.puntaje : game.score}`;
    } else {
      // mostrar mensaje con anterior y actual
      const el = document.getElementById('go-prev-record');
      if (el) el.textContent = prev ? `Record actual: ${prev.puntaje}` : 'Record actual: -';
    }
  });
}


// ----------------- BOTONES -----------------
document.getElementById("retry-button").addEventListener("click", () => {
  document.getElementById("game-over").style.display = "none";
  restartGame();
});

document.getElementById("exit-button").addEventListener("click", () => {
  window.location.replace("../main_page/main_registrados.php");
});

// ----------------- ARRANQUE -----------------
(async () => {
  await loadMode();
  initGame();
  startCountdownAndGame();
})();
