import { TetrominosBag } from "/Tetris-front/tetramin/scripts/tetromino.js";
import { BoardTetris, BoardNext, BoardHold } from "/Tetris-front/tetramin/scripts/boardTetris.js";
import { getSRSKicks } from "/Tetris-front/tetramin/scripts/srs.js";

export class Game{
    constructor(canvas, rows, cols, cellSize, space, canvasNext, canvasHold, canvasLines, canvasLevel, canvasTime, scoreSpan){
        this.boardTetris = new BoardTetris (canvas, rows, cols, cellSize, space);
        this.tetrominosBag = new TetrominosBag (canvas, cellSize);
        this.currentTetromino = this.tetrominosBag.nextTetromino();
        this.keyboard();
        this.keys = {up: false, down: false};

        this.next = new BoardNext(canvasNext, 8, 4, cellSize, space, this.tetrominosBag.getThreeNextTetrominos());
        this.hold = new BoardHold(canvasHold, 2, 4, cellSize, space);
        this.canHold = true;

        this.score = 0;
        this.combo = -1;
        this.totalLines = 0;
        this.level = 1;

        this.startTime = Date.now();
        this.tickTime = 1000;            // ms  
        this.lockDelay = 200;            // tiempo máximo antes de fijarse
        this.maxLockResets = 15;         // límite de resets por pieza
        this.lockResetsUsed = 0;         // reinicios usados
        this.lockStartTime = null;       // cuándo empezó el contacto
        this.finalTimeFormatted = null;
        this.lastTime = 0;

        this.isGameOver = false;

        this.canvasLinesCtx = canvasLines.getContext("2d");
        this.updateTextCanvas(this.canvasLinesCtx, this.totalLines);
        this.canvasLevelCtx = canvasLevel.getContext("2d");
        this.updateTextCanvas(this.canvasLevelCtx, this.level);
        this.canvasScoreCtx = scoreSpan.getContext("2d");
        this.updateTextCanvas(this.canvasScoreCtx, this.score);
        this.canvasTimeCtx = canvasTime.getContext("2d");
    }
    update() {
        const currentTime = Date.now();
        const deltaTime = currentTime - this.lastTime;

        // Actualizar tiempo visible
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

        // Baja automática por tickTime
        if (deltaTime >= this.tickTime) {
            this.autoMoveTetrominoDown();
            this.lastTime = currentTime;
        }

        // Siempre dibujar
        this.boardTetris.draw();
        this.drawTetrominoGhost();
        this.currentTetromino.draw(this.boardTetris);
        this.next.draw2();
        this.hold.draw2();

        // Si se presiona abajo, mover
        if (this.keys.down) {
            this.moveTetrominoDown();
        }
        //console logs
        console.log(this.tickTime, timeFormatted);
    }
    autoMoveTetrominoDown() {
        this.currentTetromino.move(1, 0);
        if (this.blockedTetromino()) {
            this.currentTetromino.move(-1, 0);

            if (this.lockStartTime === null) {
                this.lockStartTime = Date.now();
                this.lockResetsUsed = 0;
            }

            const now = Date.now();
            if (now - this.lockStartTime > this.lockDelay) {
                this.placeTetromino();
                this.lockStartTime = null;
            }
        } else {
            this.lockStartTime = null;
        }
    }
    moveTetrominoDown(){
        this.currentTetromino.move(1,0);
        if(this.blockedTetromino()) {
            this.currentTetromino.move(-1,0);
        }
    this.currentTetromino.lastMoveWasRotation = false;
    }
    moveTetrominoLeft() {
        this.currentTetromino.move(0, -1);
        if (this.blockedTetromino()) this.currentTetromino.move(0, 1);
        this.currentTetromino.lastMoveWasRotation = false;
        if (this.lockResetsUsed < this.maxLockResets) {
            this.lockStartTime = Date.now();
            this.lockResetsUsed++;
        }
    }
    moveTetrominoRight() {
        this.currentTetromino.move(0, 1);
        if (this.blockedTetromino()) this.currentTetromino.move(0, -1);
        this.currentTetromino.lastMoveWasRotation = false;
        if (this.lockResetsUsed < this.maxLockResets) {
            this.lockStartTime = Date.now();
            this.lockResetsUsed++;
        }
    }
    rotateTetrominoCW() {
        const nextRotation = (this.currentTetromino.rotation + 1) % this.currentTetromino.shapes.length;
        this.attemptSRSRotation(nextRotation);
        this.currentTetromino.lastMoveWasRotation = true;

        if (this.lockResetsUsed < this.maxLockResets) {
            this.lockStartTime = Date.now();
            this.lockResetsUsed++;
        }
    }
    rotateTetrominoCCW() {
        const nextRotation = (this.currentTetromino.rotation - 1 + this.currentTetromino.shapes.length) % this.currentTetromino.shapes.length;
        this.attemptSRSRotation(nextRotation);
        this.currentTetromino.lastMoveWasRotation = true;

        if (this.lockResetsUsed < this.maxLockResets) {
            this.lockStartTime = Date.now();
            this.lockResetsUsed++;
        }
    }
    attemptSRSRotation(newRotation) {
        const fromRotation = this.currentTetromino.rotation;
        const toRotation = newRotation;
        const kicks = getSRSKicks(this.currentTetromino.id, fromRotation, toRotation);

        const originalRow = this.currentTetromino.position.row;
        const originalCol = this.currentTetromino.position.col;

        this.currentTetromino.rotation = toRotation;

        for (const kick of kicks) {
            this.currentTetromino.position.row = originalRow + kick.y;
            this.currentTetromino.position.col = originalCol + kick.x;

            if (!this.blockedTetromino()) {
                return; // éxito
            }
        }

        // Si ninguno funcionó, revertimos
        this.currentTetromino.rotation = fromRotation;
        this.currentTetromino.position.row = originalRow;
        this.currentTetromino.position.col = originalCol;
    }
    placeTetromino() {
        // coloca las pieza actual en la matriz
        const positions = this.currentTetromino.currentPositions();
        for (let i = 0; i < positions.length; i++) {
            this.boardTetris.matriz[positions[i].row][positions[i].col] = this.currentTetromino.id;
        }

        let isTSpin = false;
        let isMini = false;

        if (this.currentTetromino.id === 1 && this.currentTetromino.lastMoveWasRotation) {
            const spinResult = this.isTSpinType(this.currentTetromino.position, this.currentTetromino.rotation);
            isTSpin = spinResult.isTSpin;
            isMini = spinResult.isMini;
        }

        // Limpieza de líneas y perfect clear
        const linesCleared = this.boardTetris.clearFullRows();
        const perfectClear = this.boardTetris.matriz.flat().every(v => v === 0);

        // Puntuación
        this.calculateScore(linesCleared, isTSpin, isMini, perfectClear);
        this.totalLines += linesCleared;
        this.updateTextCanvas(this.canvasLinesCtx, this.totalLines);

        // actualiza nivel y velocidad
        const newLevel = Math.floor(this.totalLines / 10) + 1;
        if (newLevel !== this.level) {
            this.level = newLevel;
            this.tickTime = Math.max(100, 1000 - (this.level - 1) * 100);
        }
        this.updateTextCanvas(this.canvasLevelCtx, this.level);

        // preparar nueva pieza
        this.currentTetromino = this.tetrominosBag.nextTetromino();

        if (this.blockedTetromino()) {
            this.isGameOver = true;
            return true;
        }

        // actualiza siguientes y permitir hold nuevamente
        this.next.listTetrominos = this.tetrominosBag.getThreeNextTetrominos();
        this.next.updateMatriz();
        this.canHold = true;

        // Reiniciar lock delay
        this.lockResetsUsed = 0;
        this.lockStartTime = null;
    }
    blockedTetromino(){
        const tetrominoPositions = this.currentTetromino.currentPositions();
        for (let i = 0; i< tetrominoPositions.length; i++) {
            if(!this.boardTetris.isEmpty(tetrominoPositions[i].row, tetrominoPositions[i].col)){
                return true;
            }
        }
        return false;
    }
    dropDistance(position){
        let distance = 0;
        while(this.boardTetris.isEmpty(position.row + distance, position.col)){
            distance++;
        }
        return distance - 1; // -1 porque la ultima posicion no es valida
    }
    tetrominoDropDistance(){
        let drop = this.boardTetris.rows;
        const tetrominoPositions = this.currentTetromino.currentPositions();
        for (let i = 0; i < tetrominoPositions.length; i++) {
            drop = Math.min(drop, this.dropDistance(tetrominoPositions[i]));
        }
        return drop;
    }
    dropBlock(){
        this.currentTetromino.move(this.tetrominoDropDistance(), 0);
        this.placeTetromino();
        this.currentTetromino.lastMoveWasRotation = false;
    }
    holdTetromino() {
        if (!this.canHold) return;
        if (this.hold.tetromino === null) {
            this.hold.tetromino = this.currentTetromino;
            this.currentTetromino = this.tetrominosBag.nextTetromino();
            this.next.listTetrominos = this.tetrominosBag.getThreeNextTetrominos();
            this.next.updateMatriz();
        } else {
            [this.hold.tetromino, this.currentTetromino] = [this.currentTetromino, this.hold.tetromino];
        }
        this.hold.updateMatriz();
        this.canHold = false;
    }
    drawTetrominoGhost(){
        const dropDistance = this.tetrominoDropDistance();
        const tetrominoPositions = this.currentTetromino.currentPositions();
        for (let i = 0; i < tetrominoPositions.length; i++) {
            const position = this.boardTetris.getCoordinates(tetrominoPositions[i].col, tetrominoPositions[i].row + dropDistance);
            this.boardTetris.drawSquere(position.x, position.y, this.boardTetris.cellSize, "#000", "white", 20);
        }
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
    calculateScore(lines, isTSpin, isMini, isPerfectClear) {
        let baseScore = 0;
        const lvl = this.level;

        if (isTSpin) {
            if (lines === 0) baseScore = isMini ? 100 : 400;
            else if (lines === 1) baseScore = isMini ? 200 : 800;
            else if (lines === 2) baseScore = isMini ? 400 : 1200;
            else if (lines === 3) baseScore = 1600;
        } else {
            if (lines === 1) baseScore = 100;
            else if (lines === 2) baseScore = 300;
            else if (lines === 3) baseScore = 500;
            else if (lines === 4) baseScore = 800; // Tetris
        }

        let score = baseScore * lvl;

        if (lines >= 4 || (isTSpin && lines > 0)) {
            this.b2b = (this.b2b || 0) + 1;
            if (this.b2b > 1) score = Math.floor(score * 1.5);
        } else {
            this.b2b = 0;
        }

        this.combo++;
        if (this.combo > 0 && lines > 0) {
            score += 50 * this.combo * lvl;
        }
        if (lines === 0) {
            this.combo = -1; // Se rompe el combo
        }

        if (isPerfectClear) {
            const pcScore = [800, 1200, 1800, 2000, 3200][lines - 1] || 0;
            score += pcScore * lvl;
        }

        this.score += score;
        this.updateTextCanvas(this.canvasScoreCtx, this.score);
    }
    isTSpinType(position, rotation) {
        const row = position.row + 1;
        const col = position.col + 1;

        // 4 esquinas alrededor del centro de la T
        const corners = [
            { r: -1, c: -1 },
            { r: -1, c: 1 },
            { r: 1, c: -1 },
            { r: 1, c: 1 },
        ];

        let occupiedCorners = 0;
        for (const offset of corners) {
            const r = row + offset.r;
            const c = col + offset.c;
            if (!this.boardTetris.isInside(r, c) || !this.boardTetris.isEmpty(r, c)) {
                occupiedCorners++;
            }
        }

        // Lados frontales: dependen de la rotación
        const frontCells = {
            0: [{ r: 0, c: -1 }, { r: 0, c: 1 }], // up
            1: [{ r: -1, c: 0 }, { r: 1, c: 0 }], // right
            2: [{ r: 0, c: -1 }, { r: 0, c: 1 }], // down
            3: [{ r: -1, c: 0 }, { r: 1, c: 0 }], // left
        };

        let frontBlocked = 0;
        for (const f of frontCells[rotation]) {
            const r = row + f.r;
            const c = col + f.c;
            if (!this.boardTetris.isInside(r, c) || !this.boardTetris.isEmpty(r, c)) {
                frontBlocked++;
            }
        }

        const isTSpin = occupiedCorners >= 3 && frontBlocked === 2;
        const isMini = occupiedCorners >= 3 && frontBlocked === 1;

        return { isTSpin, isMini };
    }
    keyboard(){
        window.addEventListener("keydown", (evt) => {
            if (evt.key === "ArrowLeft") {
                this.moveTetrominoLeft();
            }
            if (evt.key === "ArrowRight") {
                this.moveTetrominoRight();
            }
            if (evt.key === "ArrowUp" && !this.keys.up) {
                this.rotateTetrominoCW();
                this.keys.up = true;
            }
            if (evt.key === "ArrowDown") {
                this.keys.down = true;
            }
            if (evt.key === " " && !this.keys.up) {
                this.dropBlock();
                this.keys.up = true;
            }
            if (evt.key === "c" || evt.key === "C") {
                this.holdTetromino();
            }

        });
        window.addEventListener("keyup", (evt) => {
            if (evt.key === "ArrowUp") {
                this.keys.up = false;
            }
            if (evt.key === "ArrowDown") {
                this.keys.down = false;
            }
            if (evt.key === " ") {
                this.keys.up = false;
            }
        });
    }
}