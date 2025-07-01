import { TetrominosBag } from "/Tetris-front/tetramin/scripts/tetromino.js";
import { BoardTetris, BoardNext, BoardHold } from "/Tetris-front/tetramin/scripts/boardTetris.js";
import { getSRSKicks } from "/Tetris-front/tetramin/scripts/srs.js";

export class Game{
    constructor(canvas, rows, cols, cellSize, space, canvasNext, canvasHold, canvasLines, canvasLevel, canvasTime, scoreSpan){
        this.boardTetris = new BoardTetris(canvas, rows, cols, cellSize, space);
        this.tetrominosBag = new TetrominosBag(canvas, cellSize);
        this.currentTetromino = this.tetrominosBag.nextTetromino();
        this.keys = { up: false, down: false };

        this.next = new BoardNext(canvasNext, 8, 4, cellSize, space, this.tetrominosBag.getThreeNextTetrominos());
        this.hold = new BoardHold(canvasHold, 2, 4, cellSize, space);
        this.canHold = true;

        this.score = 0;
        this.combo = -1;
        this.totalLines = 0;
        this.level = 1;

        this.startTime = Date.now();
        this.tickTime = 1000;
        this.lockDelay = 200;
        this.maxLockResets = 15;
        this.lockResetsUsed = 0;
        this.lockStartTime = null;
        this.finalTimeFormatted = null;
        this.lastTime = 0;

        this.isGameOver = false;
        this.isPaused = false; // Nueva bandera de pausa

        // Inicializar textos en 0
        this.canvasLinesCtx = canvasLines.getContext("2d");
        this.updateTextCanvas(this.canvasLinesCtx, this.totalLines);
        this.canvasLevelCtx = canvasLevel.getContext("2d");
        this.updateTextCanvas(this.canvasLevelCtx, this.level);
        this.canvasScoreCtx = scoreSpan.getContext("2d");
        this.updateTextCanvas(this.canvasScoreCtx, this.score);
        this.canvasTimeCtx = canvasTime.getContext("2d");
        this.updateTextCanvas(this.canvasTimeCtx, "00:00:00");

        this.keyboard();
    }

    // Método para pausar o reanudar externamente
    setPaused(state) {
        this.isPaused = state;
    }

    update() {
        if (this.isPaused) return; // No actualizar si está en pausa

        const currentTime = Date.now();
        const deltaTime = currentTime - this.lastTime;

        // Actualizar tiempo
        let timeFormatted;
        if (!this.isGameOver) {
            let elapsed = currentTime - this.startTime;
            let minutes = Math.floor(elapsed / 60000).toString().padStart(2, '0');
            let seconds = Math.floor((elapsed % 60000) / 1000).toString().padStart(2, '0');
            let milliseconds = Math.floor((elapsed % 1000) / 10).toString().padStart(2, '0');
            timeFormatted = `${minutes}:${seconds}:${milliseconds}`;
            this.finalTimeFormatted = timeFormatted;
        } else {
            timeFormatted = this.finalTimeFormatted;
        }
        this.updateTextCanvas(this.canvasTimeCtx, timeFormatted);

        // Baja automática
        if (deltaTime >= this.tickTime) {
            this.autoMoveTetrominoDown();
            this.lastTime = currentTime;
        }

        // Dibujado
        this.boardTetris.draw();
        this.drawTetrominoGhost();
        this.currentTetromino.draw(this.boardTetris);
        this.next.draw2();
        this.hold.draw2();

        if (this.keys.down) this.moveTetrominoDown();
    }

    // ... resto de métodos sin cambios, excepto respetar isPaused cuando corresponda ...

    keyboard() {
        window.addEventListener("keydown", (evt) => {
            if (this.isPaused) return;
            switch (evt.key) {
                case "ArrowLeft": this.moveTetrominoLeft(); break;
                case "ArrowRight": this.moveTetrominoRight(); break;
                case "ArrowUp":
                    if (!this.keys.up) { this.rotateTetrominoCW(); this.keys.up = true; }
                    break;
                case "ArrowDown": this.keys.down = true; break;
                case " ":
                    if (!this.keys.up) { this.dropBlock(); this.keys.up = true; }
                    break;
                case "c": case "C": this.holdTetromino(); break;
            }
        });
        window.addEventListener("keyup", (evt) => {
            if (this.isPaused) return;
            if (evt.key === "ArrowUp" || evt.key === " ") this.keys.up = false;
            if (evt.key === "ArrowDown") this.keys.down = false;
        });
    }

    updateTextCanvas(ctx, text) {
        ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);
        ctx.font = "48px 'Orbitron', sans-serif";
        ctx.fillStyle = "#00ffff";
        ctx.textAlign = "center";
        ctx.textBaseline = "middle";
        ctx.shadowColor = "#00ffff";
        ctx.shadowBlur = 5;
        ctx.fillText(text.toString(), ctx.canvas.width / 2, ctx.canvas.height / 2);
    }
}
