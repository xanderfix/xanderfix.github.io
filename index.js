var time = 0;
function change() {
    setInterval(function change1() {
    if (time == 0) {
         document.getElementById("WNdate").innerHTML = "9/23/2015";
         document.getElementById("WNa").href = "A-Beginners-Guide-to-Splatoon-Part-2-Turf-Wars.html";
         document.getElementById("WNimg").src = "images/splatoonBattle.jpg";
         document.getElementById("WNp").innerHTML = "A Beginner's Guide to Splatoon:<br> Part 2 Turf Wars<br><br> The second part of my beginner's guide to <i>Splatoon</i> giving some handy tips for the game's main mode, Turf Wars."
    }
    if (time == 1) {
         document.getElementById("WNdate").innerHTML = "8/30/2015";
         document.getElementById("WNa").href = "Lets-Play-Kirbys-Return-to-Dreamland.html";
         document.getElementById("WNimg").src = "images/Let's Play Kirby's Return to Dreamland 1.jpg";
         document.getElementById("WNp").innerHTML = "Let's Play Kirby's Return to Dreamland<br><br>My sister and I's first 'Let's Play', <i>Kirby's Return to Dreamland</i>."
    }
    if (time == 2) {
         document.getElementById("WNdate").innerHTML = "8/12/2015";
         document.getElementById("WNa").href = "Jigsaw-Puzzle-Super-Mario-Bros-3-World-Maps.html";
         document.getElementById("WNimg").src = "images/SMB3W1.png";
         document.getElementById("WNp").innerHTML = "Jigsaw Puzzle: <br>Super Mario Bros. 3 World Maps<br><br>Jigsaw puzzles for the <i>Super Mario Bros. 3</i> world maps.";
    }
    if (time == 3) {
         document.getElementById("WNdate").innerHTML = "8/12/2015";
         document.getElementById("WNa").href = "How-to-Play-Midnas-Lament.html";
         document.getElementById("WNimg").src = "images/midnasLament.jpg";
         document.getElementById("WNp").innerHTML = "How to Play Midna's Lament";
    }
    if (time == 4) {
         document.getElementById("WNdate").innerHTML = "9/25/2015";
         document.getElementById("WNa").href = "A-Link-to-the-Past-Trivia.html";
         document.getElementById("WNimg").src = "images/cucooLady.png";
         document.getElementById("WNp").innerHTML = "A Link to the Past Trivia<br><br> Cool A Link to the Past trivia; including a secret about the Cucoos.";
         time = -1;
    }
    time+=1;
    }, 4444)
}

