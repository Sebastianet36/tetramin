import { Grid } from "/Tetris-front/tetramin/scripts/grid.js";

export class BoardTetris extends Grid {
    constructor(canvas, rows, cols, cellSize, space) {
        super(canvas, rows, cols, cellSize, space);
    }
    isInside(row, col){
        return row>=0 && row<this.rows && col>=0 && col<this.cols;
    }
    isEmpty(row, col){
        return this.isInside(row,col) && this.matriz[row][col] === 0;
    }
    isRowFull(row){
        return this.matriz[row].every(element => element !== 0);
    }
    isRowEmpty(row){
        return this.matriz[row].every(element => element === 0);
    }
    clearRow(row){
        this.matriz[row].fill(0);
    }
    moveRowDown(row, numRows){
        this.matriz[row +numRows] = this.matriz[row].slice();
        this.clearRow(row);
    }
    clearFullRows(){
        let cont = 0;

        for (let row = this.rows -1; row>=0; row--) {
            if(this.isRowFull(row)){
                this.clearRow(row);
                cont++;
            } else if (cont > 0){
                this.moveRowDown(row, cont);
            }       
        }
        return cont;
    }
    gameOver(){
        return !(this.isRowEmpty(0));
    }
}
export class BoardNext extends Grid {
    constructor(canvas, rows, cols, cellSize, space, listTetrominos) {
        super(canvas, rows, cols, cellSize, space);
        this.listTetrominos = listTetrominos;
        this.updateMatriz();
    }
    updateMatriz() {
        this.restartMatriz();
        let cont = 0;
        for(let i = 0; i< this.listTetrominos.length; i++) {
            const shape = this.listTetrominos[i].currentShape();
            for (let j = 0; j < shape.length; j++) {
                this.matriz[shape[j].row+cont][shape[j].col] = this.listTetrominos[i].id;
            }
            cont+=3;
        }
    }
}
export class BoardHold extends Grid {
    constructor(canvas, rows, cols, cellSize, space, ) {
        super(canvas, rows, cols, cellSize, space);
        this.tetromino = null;
        this.updateMatriz();
    }
    updateMatriz() {
        if(this.tetromino==null) return;
        this.tetromino.reset();
        this.restartMatriz();
        const shape = this.tetromino.currentShape();
        for (let j = 0; j < shape.length; j++) {
            this.matriz[shape[j].row][shape[j].col] = this.tetromino.id;
        }
    }
}