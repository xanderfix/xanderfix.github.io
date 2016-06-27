var time = 1;
function change() {
    setInterval(function change1() {
    if (time == 0) {
         document.getElementById("WNdate").innerHTML = "6/27/2016";
         document.getElementById("WNa").href = "How-to-Play-CROWNED.html";
         document.getElementById("WNimg").src = "images/magolorSoul.png";
         document.getElementById("WNp").innerHTML = "How to Play CROWNED";
    }
    if (time == 1) {
        document.getElementById("WNdate").innerHTML = "6/24/2016";
         document.getElementById("WNa").href = "The-Penny-Arcade-in-Manitou-Springs.html";
         document.getElementById("WNimg").src = "images/PennyArcade3.png";
         document.getElementById("WNp").innerHTML = "The Penny Arcade<br>in Manitou Springs<br><br>One of the oldest arcades in the west, with everything from Frogger to skeeball to penny machines!";
    }
    if (time == 2) {
         document.getElementById("WNdate").innerHTML = "6/22/2016";
         document.getElementById("WNa").href = "The-Legend-of-Zelda-Medley.html";
         document.getElementById("WNimg").src = "images/GBApiano.png";
         document.getElementById("WNp").innerHTML = "The Legend of Zelda Medley<br><br> An amazing Zelda medley by Nick, the newest member of ZeldaRocks!<br>(In case you can't tell Xander is writing this).";
    }     
    if (time == 3) {
         document.getElementById("WNdate").innerHTML = "3/6/2016";
         document.getElementById("WNa").href = "Jigsaw-Puzzles-Pokemon-20th-Mythicals-and-Mega-Evolutions.html";
         document.getElementById("WNimg").src = "images/megaEvolutionsSmall.png";
         document.getElementById("WNp").innerHTML = "Jigsaw Puzzles: <br>Pokemon 20th - Mythicals and Mega Evolutions<br><br>3 cool jigsaw puzzles to celebrate the 20th anniversary of the Pokemon series.";
    }
    if (time == 4) {
         document.getElementById("WNdate").innerHTML = "11/27/2015";
         document.getElementById("WNa").href = "Stone-Kirby-Transformations-Easter-Eggs.html";
         document.getElementById("WNimg").src = "images/stoneKirbyDreamCollection.png";
         document.getElementById("WNp").innerHTML = "Stone Kirby Transformations Easter Eggs<br><br> Displaying all of the cool easter eggs related to stone Kirby's transformations.";
         time = -1;
    }
    time+=1;
    }, 5000)
}

