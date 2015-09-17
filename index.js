var time = 0;
function change() {
    setInterval(function change1() {
    if (time == 0) {
         document.getElementById("WNdate").innerHTML = "8/30/2015";
         document.getElementById("WNa").href = "Lets-Play-Kirbys-Return-to-Dreamland.html";
         document.getElementById("WNimg").src = "images/Let's Play Kirby's Return to Dreamland 1.jpg";
         document.getElementById("WNp").innerHTML = "Let's Play Kirby's Return to Dreamland<br><br>My sister and I's first 'Let's Play', <i>Kirby's Return to Dreamland</i>."
    }
    if (time == 1) {
         document.getElementById("WNdate").innerHTML = "8/12/2015";
         document.getElementById("WNa").href = "Jigsaw-Puzzle-Super-Mario-Bros-3-World-Maps.html";
         document.getElementById("WNimg").src = "images/SMB3W1.png";
         document.getElementById("WNp").innerHTML = "Jigsaw Puzzle: <br>Super Mario Bros. 3 World Maps<br><br>Jigsaw puzzles for the <i>Super Mario Bros. 3</i> world maps.";
    }
    if (time == 2) {
         document.getElementById("WNdate").innerHTML = "8/12/2015";
         document.getElementById("WNa").href = "How-to-Play-Midnas-Lament.html";
         document.getElementById("WNimg").src = "images/midnasLament.jpg";
         document.getElementById("WNp").innerHTML = "How to Play Midna's Lament";
    }
    if (time == 3) {
         document.getElementById("WNdate").innerHTML = "8/4/2015";
         document.getElementById("WNa").href = "Kirby-Level-Name-Acronyms.html";
         document.getElementById("WNimg").src = "images/lollipopLand.png";
         document.getElementById("WNp").innerHTML = "Kirby Level Name Acronyms<br><br>Investigating the secrets of Kirby series level names.";
    }
    if (time == 4) {
         document.getElementById("WNdate").innerHTML = "9/17/2015";
         document.getElementById("WNa").href = "A-Beginners-Guide-to-Splatoon-Part-1-Inkopolis-Plaza.html";
         document.getElementById("WNimg").src = "images/inkopolisMap.jpg";
         document.getElementById("WNp").innerHTML = "A Beginner's Guide to Splatoon:<br> Part 1 Inkopolis Plaza<br><br> The first part of my beginner's guide to <i>Splatoon</i> covering  the game's hub area, Inkopolis Plaza."
         time = -1;
    }
    time+=1;
    }, 4444)
}

