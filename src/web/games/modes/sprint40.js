export default {
  name: "Sprint 40",
  description: "Clear 40 lines as fast as you can",

  init(game) {
    this.targetLines = 40;
    game.totalLines = 0;
    game.startTime = Date.now();
  },

  update(game) {
    if (game.totalLines >= this.targetLines) {
      game.isGameOver = true;
      const elapsed = Date.now() - game.startTime;
      const minutes = Math.floor(elapsed / 60000).toString().padStart(2, "0");
      const seconds = Math.floor((elapsed % 60000) / 1000).toString().padStart(2, "0");
      const ms = Math.floor((elapsed % 1000) / 10).toString().padStart(2, "0");
      game.finalTimeFormatted = `${minutes}:${seconds}:${ms}`;
    }
  },

  isWin(game) {
    return game.totalLines >= this.targetLines;
  },

  onGameOver(game) {
    if (this.isWin(game)) {
      alert(`¡Sprint 40 terminado en ${game.finalTimeFormatted}!`);
    }
  }
};
