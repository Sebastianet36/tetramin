class Position {
    constructor(row, col) {
        this.row = row;
        this.col = col;
    }
}

class Tetromino {
    constructor(canvas, cellsize, shapes = [], initPosition = new Position(), id=1) {
        this.canvas = canvas;
        this.ctx = canvas.getContext("2d");
        this.cellsize = cellsize;
        this.shapes = shapes;
        this.initPosition = initPosition;
        this.position = new Position(initPosition.row, initPosition.col);
        this.id = id;
        this.rotation = 0;
        this.lastMoveWasRotation = false;

    }
    drawSquare(x, y, sidze, color) {
        this.ctx.fillStyle = color;
        this.ctx.fillRect(x, y, sidze, sidze);
    }
    drawTringle(x1, y1, x2, y2, x3, y3, color) {
        this.ctx.beginPath();
        this.ctx.moveTo(x1, y1);
        this.ctx.lineTo(x2, y2);
        this.ctx.lineTo(x3, y3);
        this.ctx.closePath();
        this.ctx.fillStyle = color;
        this.ctx.fill();
    }
    getColorPalette(id) {
        const palette = {
            1:{
                rightTriangle: "#793785",
                leftTriangle: "#c44ed8",
                square: "#a039b2"
            },
            2:{
                rightTriangle: "#b89e14",
                leftTriangle: "#ecd453",
                square: "#ffd700"
            },
            3:{
                rightTriangle: "#00a8bc",
                leftTriangle: "#40ffff",
                square: "#00d1ec"
            },
            4:{
                rightTriangle: "#3bb127",
                leftTriangle: "#81e070",
                square: "#43e726"
            },
            5:{
                rightTriangle: "#b81414",
                leftTriangle: "#ec5353",
                square: "#FF0000"
            },
            6:{
                rightTriangle: "#1467b8",
                leftTriangle: "#53a0ec",
                square: "#0081ff"
            },
            7:{
                rightTriangle: "#b86514",
                leftTriangle: "#ec9e53",
                square: "#ff7e00"
            }
        };
        return palette[id] || palette[1];
    }
    drawBlock(x,y,id){
        const margin = this.cellsize / 8;
        const palette = this.getColorPalette(id);

        this.drawTringle(
            x,y,
            x+this.cellsize, y,
            x, y+this.cellsize,
            palette.leftTriangle
        );
        this.drawTringle(
            x+this.cellsize, y,
            x+this.cellsize, y+this.cellsize,
            x, y+this.cellsize,
            palette.rightTriangle
        );

        this.drawSquare(
            x+margin,
            y+margin,
            this.cellsize-(margin*2),
            palette.square
        );
    }
    currentShape() {
        return this.shapes[this.rotation];
    }
    draw(grid){
        const shape = this.currentShape();
        for (let i = 0; i < shape.length; i++) {
            const position = grid.getCoordinates(this.position.col + shape[i].col, this.position.row + shape[i].row);
            this.drawBlock(position.x, position.y, this.id);
        }
    }
    currentPositions() {
        const positions = [];
        const shape = this.currentShape();
        for (let i = 0; i<shape.length; i++) {
            positions.push(new Position(this.position.row + shape[i].row, this.position.col + shape[i].col));
        }
        return positions;
    }
    move(row, col) {
        this.position.row += row;
        this.position.col += col;
    }
    reset() {
        this.position.row = this.initPosition.row;
        this.position.col = this.initPosition.col;
        this.rotation = 0;
    }
}

const TetrominoTypes = {
    T: {
        id: 1,
        initPosition: new Position(0, 3),
        shapes: [
            [new Position(0, 1), new Position(1, 0), new Position(1, 1), new Position(1, 2)],
            [new Position(0, 1), new Position(1, 1), new Position(1, 2), new Position(2, 1)],
            [new Position(1, 0), new Position(1, 1), new Position(1, 2), new Position(2, 1)],
            [new Position(0, 1), new Position(1, 0), new Position(1, 1), new Position(2, 1)]
        ]
    },
    O: {
        id: 2,
        initPosition: new Position(0, 4),
        shapes: [
            [new Position(0, 0), new Position(0, 1), new Position(1, 0), new Position(1, 1)]
        ]
    },
    I: {
        id: 3,
        initPosition: new Position(-1, 3),
        shapes: [
            [new Position(1, 0), new Position(1, 1), new Position(1, 2), new Position(1, 3)],
            [new Position(0, 2), new Position(1, 2), new Position(2, 2), new Position(3, 2)],
            [new Position(2, 0), new Position(2, 1), new Position(2, 2), new Position(2, 3)],
            [new Position(0, 1), new Position(1, 1), new Position(2, 1), new Position(3, 1)]
        ]
    },
    S: {
        id: 4,
        initPosition: new Position(0, 3),
        shapes: [
            [new Position(0, 1), new Position(0, 2), new Position(1, 0), new Position(1, 1)],
            [new Position(0, 1), new Position(1, 1), new Position(1, 2), new Position(2, 2)],
            [new Position(1, 1), new Position(1, 2), new Position(2, 0), new Position(2, 1)],
            [new Position(0, 0), new Position(1, 0), new Position(1, 1), new Position(2, 1)]
        ]
    },
    Z: {
        id: 5,
        initPosition: new Position(0, 3),
        shapes: [
            [new Position(0, 0), new Position(0, 1), new Position(1, 1), new Position(1, 2)],
            [new Position(0, 2), new Position(1, 1), new Position(1, 2), new Position(2, 1)],
            [new Position(1, 0), new Position(1, 1), new Position(2, 1), new Position(2, 2)],
            [new Position(0, 1), new Position(1, 0), new Position(1, 1), new Position(2, 0)]
        ]
    },
    J: {
        id: 6,
        initPosition: new Position(0, 3),
        shapes: [
            [new Position(0, 0), new Position(1, 0), new Position(1, 1), new Position(1, 2)],
            [new Position(0, 1), new Position(0, 2), new Position(1, 1), new Position(2, 1)],
            [new Position(1, 0), new Position(1, 1), new Position(1, 2), new Position(2, 2)],
            [new Position(0, 1), new Position(1, 1), new Position(2, 0), new Position(2, 1)]
        ]
    },
    L: {
        id: 7,
        initPosition: new Position(0, 3),
        shapes: [
            [new Position(0, 2), new Position(1, 0), new Position(1, 1), new Position(1, 2)],
            [new Position(0, 1), new Position(1, 1), new Position(2, 1), new Position(2, 2)],
            [new Position(1, 0), new Position(1, 1), new Position(1, 2), new Position(2, 0)],
            [new Position(0, 0), new Position(0, 1), new Position(1, 1), new Position(2, 1)]
        ]
    }
}

class TetrominosBag {
    constructor(canvas, cellSize){
        this.canvas = canvas;
        this.cellSize = cellSize;
        this.bag = [];
        this.threeNextTetrominos = [];
        this.init();
    }
    init(){
        for(let i = 0; i < 3; i++){
            this.threeNextTetrominos.push(this.getNextTetromino());
        }
    }
    fillBag(){
        const tetrominoTypes = [
            TetrominoTypes.T,
            TetrominoTypes.O,
            TetrominoTypes.I,
            TetrominoTypes.S,
            TetrominoTypes.Z,
            TetrominoTypes.J,
            TetrominoTypes.L,
        ]
        this.bag.length=0;
        
        tetrominoTypes.forEach((type) => {
            this.bag.push(new Tetromino(
                this.canvas, this.cellSize, type.shapes, type.initPosition, type.id
            ));
        });
        for (let i = this.bag.length -1; i > 0 ; i--){
            let j = Math.floor(Math.random() * (1+i));
            [this.bag[i],this.bag[j]] = [this.bag[j],this.bag[i]]
           
        }
    }
    getNextTetromino(){
        if(this.bag.length === 0){
            this.fillBag();
        }
        return this.bag.pop();
    }
    nextTetromino(){
        const next = this.threeNextTetrominos.shift();
        this.threeNextTetrominos.push(this.getNextTetromino());
        return next;
    }
    getThreeNextTetrominos() {
        return this.threeNextTetrominos;
    }
    reset() {
        this.bag = [];
        this.threeNextTetrominos = [];
        this.init();
    }
}

export {Position, Tetromino, TetrominoTypes, TetrominosBag};