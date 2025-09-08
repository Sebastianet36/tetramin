export default {
  name: "Cheese",
  description: "Limpia las líneas de basura lo más rápido posible",

  init(game) {
    this.targetLines = 120;
    this.garbageId = 99;

    game.totalLines = 0;
    game.startTime = Date.now();

    //paleta gris propia para basura
    const garbagePalette = {
      rightTriangle: "#555555",
      leftTriangle:  "#999999",
      square:        "#777777"
    };

    //hook de paleta: id=99 => gris
    const originalGetPalette = game.boardTetris.block.getColorPalette.bind(game.boardTetris.block);
    game.boardTetris.block.getColorPalette = (id) => {
      if (id === this.garbageId) return garbagePalette;
      return originalGetPalette(id);
    };

    this.normalizeBoard(game);
    this.ensureGarbageRows(game, 10);

    game.boardTetris.draw();

    this.lastTotalLines = 0;
    this.pendingWin = false;
    this.garbageDelay = 0; //nuevo contador de delay
  },

  normalizeBoard(game) {
    const M = game.boardTetris.matriz;
    for (let r = 0; r < M.length; r++) {
      for (let c = 0; c < M[r].length; c++) {
        if (M[r][c] == null) M[r][c] = 0;
      }
    }
  },

  countBottomGarbageRows(game) {
    let count = 0;
    for (let r = game.boardTetris.rows - 1; r >= 0; r--) {
      const row = game.boardTetris.matriz[r];
      const hasGarbage = row.some(cell => cell === this.garbageId);
      if (hasGarbage) count++;
      else break;
    }
    return count;
  },

  ensureGarbageRows(game, desired) {
    const current = this.countBottomGarbageRows(game);
    if (current < desired) {
      this.addGarbage(game, desired - current);
    }
  },

  addGarbage(game, count = 1) {
    for (let i = 0; i < count; i++) {
      for (let row = 0; row < game.boardTetris.rows - 1; row++) {
        game.boardTetris.matriz[row] = game.boardTetris.matriz[row + 1].slice();
      }
      this.fillGarbageRow(game, game.boardTetris.rows - 1);
    }
    this.normalizeBoard(game);
  },

  fillGarbageRow(game, row) {
    const hole = Math.floor(Math.random() * game.boardTetris.cols);
    for (let col = 0; col < game.boardTetris.cols; col++) {
      game.boardTetris.matriz[row][col] = (col === hole) ? 0 : this.garbageId;
    }
  },

  update(game) {
    //victoria con espera de colocación
    if (!this.pendingWin && game.totalLines >= this.targetLines) {
      this.pendingWin = true;
    }
    if (this.pendingWin && !game.currentPiece) {
      game.isGameOver = true;
      const elapsed = Date.now() - game.startTime;
      const minutes = Math.floor(elapsed / 60000).toString().padStart(2, "0");
      const seconds = Math.floor((elapsed % 60000) / 1000).toString().padStart(2, "0");
      const ms = Math.floor((elapsed % 1000) / 10).toString().padStart(2, "0");
      game.finalTimeFormatted = `${minutes}:${seconds}:${ms}`;
      return;
    }

    const current = this.countBottomGarbageRows(game);

    //reposición mínima inmediata
    if (current < 7) {
      this.addGarbage(game, 7 - current);
      return; //no seguimos, ya rellenamos
    }

    //detectar limpieza de líneas
    if (game.totalLines > this.lastTotalLines) {
      this.lastTotalLines = game.totalLines;

      //si quedó entre 7 y 9 → activar delay
      if (current < 10 && this.garbageDelay <= 0) {
        this.garbageDelay = 2; // 2 piezas de espera
      }
    }

    //control del delay
    if (!game.currentPiece && game.lastPiecePlaced) {
      if (this.garbageDelay > 0) {
        this.garbageDelay--;
        if (this.garbageDelay === 0) {
          //cuando se cumple el delay, siempre rellenar hasta 10
          const toAdd = 10 - this.countBottomGarbageRows(game);
          if (toAdd > 0) this.addGarbage(game, toAdd);
        }
      }
    }
  },

  isWin(game) {
    return game.totalLines >= this.targetLines && !game.currentPiece;
  },

  onGameOver(game) {
    if (this.isWin(game)) {
      alert(`¡Cheese completado en ${game.finalTimeFormatted}!`);
    } else {
      alert("No lograste completar el Cheese 😢");
    }
  }
};
