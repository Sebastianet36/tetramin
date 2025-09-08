export default {
  name: "Dig Challenge",
  description: "Sobrevive mientras aparecen líneas de basura desde abajo",

  init(game) {
    this.garbageId = 99;
    this.interval = 5000; // cada 5 segundos sube una fila
    this.lastGarbageTime = Date.now();

    // Paleta gris propia
    const garbagePalette = {
      rightTriangle: "#555555",
      leftTriangle:  "#999999",
      square:        "#777777"
    };

    // Hook a paleta: id=99 → gris
    const originalGetPalette = game.boardTetris.block.getColorPalette.bind(game.boardTetris.block);
    game.boardTetris.block.getColorPalette = (id) => {
      if (id === this.garbageId) return garbagePalette;
      return originalGetPalette(id);
    };

    game.totalLines = 0;
    game.startTime = Date.now();
    game.boardTetris.draw();
  },

  // Generar fila de basura con hueco
  fillGarbageRow(game, row) {
    const hole = Math.floor(Math.random() * game.boardTetris.cols);
    for (let col = 0; col < game.boardTetris.cols; col++) {
      game.boardTetris.matriz[row][col] = (col === hole) ? 0 : this.garbageId;
    }
  },

  // Subir N filas
  addGarbage(game, count = 1) {
    for (let i = 0; i < count; i++) {
      // desplazar hacia arriba
      for (let row = 0; row < game.boardTetris.rows - 1; row++) {
        game.boardTetris.matriz[row] = game.boardTetris.matriz[row + 1].slice();
      }
      // nueva fila abajo
      this.fillGarbageRow(game, game.boardTetris.rows - 1);
    }
  },

  update(game) {
    const now = Date.now();

    // cada intervalo, añadir basura
    if (now - this.lastGarbageTime >= this.interval) {
      this.addGarbage(game, 1);
      this.lastGarbageTime = now;
    }
  },

  isWin(game) {
    return false; // modo survival no tiene victoria
  },

  onGameOver(game) {
    const elapsed = Date.now() - game.startTime;
    const seconds = Math.floor(elapsed / 1000);
    alert(`Sobreviviste ${seconds} segundos en Dig Challenge`);
  }
};
