const canvas = document.getElementById("canvas");  
const ctx = canvas.getContext("2d");  
  
// Define variables  
let gameState = "menu";  
let birdX = 50;  
let birdY = 250;  
let birdSize = 50;  
let birdDY = 0;  
let gravity = 0.5;  
let spacePressed = false;  
let level = 1;  
let obstacleX = 500;  
let obstacleY = 0;  
let obstacleGap = 200;  
let obstacleWidth = 30;  
let obstacleHeight = 150;  
let obstacleSpeed = 5; // New variable for obstacle speed  
let score = 0;  
let highScore = localStorage.getItem("flappyHawkHighScore") || 0;  
let scaling = 0;  
  
const birdImage = new Image();  
birdImage.src = "../Assets/flappyhawk/newmoutainhawk.png";  
const level1 = new Image();  
level1.src = "../Assets/flappyhawk/level1.jpg";  
const level2 = new Image();  
level2.src = "../Assets/flappyhawk/level2.jpg";  
const level3 = new Image();  
level3.src = "../Assets/flappyhawk/level3.jpg";  
const level4 = new Image();  
level4.src = "../Assets/flappyhawk/level4.jpg";  
const level5 = new Image();  
level5.src = "../Assets/flappyhawk/level5.jpg";  
const level6 = new Image();  
level6.src = "../Assets/flappyhawk/level6.jpg";  
const level7 = new Image();  
level7.src = "../Assets/flappyhawk/level7.jpg";  
const level8 = new Image();  
level8.src = "../Assets/flappyhawk/level8.jpg";  
  
const menuimage = new Image()  
menuimage.src = "../Assets/flappyhawk/flappyhawkbackround.png"  
const funnyimage = new Image()  
funnyimage.src = "../Assets/flappyhawk/funnyimage.JPG"  
  
document.addEventListener("DOMContentLoaded", function () {  
  drawMenu();  
   level = 1;  
});  
  
document.addEventListener("keydown", (event) => {  
  if (event.code === "Space") {  
    spacePressed = true;  
  } else if (event.code === "Enter" && gameState === "menu") {  
    gameState = "playing";  
    ctx.clearRect(0, 0, canvas.width, canvas.height);  
    resetGame();  
    update();  
  }  
});  
  
document.addEventListener("keyup", (event) => {  
  if (event.code === "Space") {  
    spacePressed = false;  
  }  
});  
  
function resetGame() {  
  birdY = canvas.height / 2;  
  birdDY = 0;  
  obstacleX = 500;  
  obstacleY = 0;  
  score = 0;  
  level = 1; 
}  
  
function drawMenu() {  
  ctx.drawImage(menuimage, 0, 0, canvas.width, canvas.height);  
  ctx.font = "24px Arial";  
  ctx.fillStyle = "white";  
  ctx.fillText("Current Highscore: " + highScore, canvas.width / 2 - 100, canvas.height - 50);  
}  
  
function update() {  
  if (gameState === "menu") {  
    drawMenu();  
    return;  
  }  
  
  ctx.clearRect(0, 0, canvas.width, canvas.height);  
  
  if (score >= 5 && score < 10) {  
    level = 2;  
  } else if (score >= 10 && score < 15) {  
    level = 3;  
  } else if (score >= 15 && score < 20) {  
    level = 4;  
  } else if (score >= 20 && score < 25) {  
    level = 5;  
  } else if (score >= 25 && score < 30) {  
    level = 6;  
  } else if (score >= 30 && score < 35) {  
    level = 7;  
  } else if (score >= 35 && score < 100) {  
    level = 8;  
  } else if (score >= 100) {  
    level = 100;  
  } else {  
    level = 1;  
  }  
  
  // Draw background based on level  
  if (level == 2) {  
    ctx.drawImage(level2, 0, 0, canvas.width, canvas.height);  
    obstacleSpeed = 6;  
  } else if (level == 3) {  
    ctx.drawImage(level3, 0, 0, canvas.width, canvas.height);  
    obstacleSpeed = 7;  
  } else if (level == 4) {  
    ctx.drawImage(level4, 0, 0, canvas.width, canvas.height);  
    obstacleSpeed = 8;  
  } else if (level == 5) {  
    ctx.drawImage(level5, 0, 0, canvas.width, canvas.height);  
    obstacleSpeed = 9;  
  } else if (level == 6) {  
    ctx.drawImage(level6, 0, 0, canvas.width, canvas.height);  
    obstacleSpeed = 10;  
  } else if (level == 7) {  
    ctx.drawImage(level7, 0, 0, canvas.width, canvas.height);  
    obstacleSpeed = 11;  
  } else if (level == 8) {  
    ctx.drawImage(level8, 0, 0, canvas.width, canvas.height);  
    obstacleSpeed = 12;  
  } else if (level == 100) {  
    ctx.drawImage(funnyimage, 0, 0, canvas.width, canvas.height);  
    obstacleSpeed = 10;  
    scaling = 1;  
  } else {  
    ctx.drawImage(level1, 0, 0, canvas.width, canvas.height);  
  }  
  
  if (scaling == 1) {  
    obstacleSpeed = obstacleSpeed + score / 10;  
  }  
  
  ctx.save();  
  ctx.globalAlpha = 0.7;  
  ctx.beginPath();  
  ctx.arc(  
    birdX + birdSize / 2,  
    birdY + birdSize / 2,  
    birdSize / 2 + 5,  
    0,  
    Math.PI * 2  
  );  
  ctx.fillStyle = "blue";  
  ctx.fill();  
  ctx.restore();  
  ctx.drawImage(birdImage, birdX, birdY, birdSize, birdSize);  
  
  birdDY += gravity;  
  birdY += birdDY;  
  
  if (birdY + birdSize > canvas.height) {  
    birdY = canvas.height - birdSize;  
    birdDY = 0;  
  } else if (birdY < 0) {  
    birdY = 0;  
    birdDY = 0;  
  }  
  
  ctx.fillStyle = "red";  
  ctx.fillRect(obstacleX, 0, obstacleWidth, obstacleY);  
  ctx.fillRect(  
    obstacleX,  
    obstacleY + obstacleHeight + obstacleGap,  
    obstacleWidth,  
    canvas.height - obstacleY - obstacleHeight - obstacleGap  
  );  
  
  obstacleX -= obstacleSpeed;  
  
  if (obstacleX + obstacleWidth < 0) {  
    obstacleX = canvas.width;  
    obstacleY = Math.random() * (canvas.height - obstacleGap - obstacleHeight);  
    score++;  
  
    if (score > highScore) {  
      highScore = score;  
      localStorage.setItem("flappyHawkHighScore", highScore);  
    }  
  }  
  
  const scoreText = `Score: ${score}`;  
  const highScoreText = `High Score: ${highScore}`;  
  const boxWidthScore = ctx.measureText(scoreText).width + 20;  
  const boxWidthHighScore = ctx.measureText(highScoreText).width + 20;  
  
  ctx.fillStyle = "white";  
  ctx.fillRect(10, 10, boxWidthScore, 30);  
  ctx.fillRect(10, 50, boxWidthHighScore, 30);  
  
  ctx.fillStyle = "black";  
  ctx.font = "24px Arial";  
  ctx.fillText(scoreText, 20, 30);  
  ctx.fillText(highScoreText, 20, 70);  
  
  if (  
    birdX + birdSize > obstacleX &&  
    birdX < obstacleX + obstacleWidth &&  
    (birdY < obstacleY || birdY + birdSize > obstacleY + obstacleHeight + obstacleGap)  
  ) {  
    gameState = "menu";  
  }  
  
  if (spacePressed) {  
    birdDY = -8;  
  }  
  
  setTimeout(update, 1000 / 60);  
}  
  
update();  
