var time = 1;
function change() {
    setInterval(function change1() {
    if (time == 0) {
         document.getElementById("WNdate").innerHTML = "7/2/2016";
         document.getElementById("WNa").href = "art.html";
         document.getElementById("WNimg").src = "images/smashMural3.jpg";
         document.getElementById("WNp").innerHTML = "Super Smash Bros. Mural";
    }
    if (time == 1) {
         document.getElementById("WNdate").innerHTML = "7/1/2016";
         document.getElementById("WNa").href = "Hyrule-Market-Theme-Cover.html";
         document.getElementById("WNimg").src = "images/HyruleMarketThemeCover.png";
         document.getElementById("WNp").innerHTML = "Hyrule Market Theme Cover<br><br>A new cover by Xander of the Hyrule Market Theme from Ocarina of Time performed on piano and acoustic guitar.";
    }
    if (time == 2) {
         document.getElementById("WNdate").innerHTML = "6/27/2016";
         document.getElementById("WNa").href = "How-to-Play-CROWNED.html";
         document.getElementById("WNimg").src = "images/magolorSoul.png";
         document.getElementById("WNp").innerHTML = "How to Play CROWNED";
    }     
    if (time == 3) {
         document.getElementById("WNdate").innerHTML = "6/24/2016";
         document.getElementById("WNa").href = "The-Penny-Arcade-in-Manitou-Springs.html";
         document.getElementById("WNimg").src = "images/PennyArcade3.png";
         document.getElementById("WNp").innerHTML = "The Penny Arcade<br>in Manitou Springs<br><br>One of the oldest arcades in the west, with everything from Frogger to skeeball to penny machines!";
    }
    if (time == 4) {
         document.getElementById("WNdate").innerHTML = "3/6/2016";
         document.getElementById("WNa").href = "Jigsaw-Puzzles-Pokemon-20th-Mythicals-and-Mega-Evolutions.html";
         document.getElementById("WNimg").src = "images/megaEvolutionsSmall.png";
         document.getElementById("WNp").innerHTML = "Jigsaw Puzzles: <br>Pokemon 20th - Mythicals and Mega Evolutions<br><br>3 cool jigsaw puzzles to celebrate the 20th anniversary of the Pokemon series.";
    }
    if (time == 5) {
         document.getElementById("WNdate").innerHTML = "11/27/2015";
         document.getElementById("WNa").href = "Stone-Kirby-Transformations-Easter-Eggs.html";
         document.getElementById("WNimg").src = "images/stoneKirbyDreamCollection.png";
         document.getElementById("WNp").innerHTML = "Stone Kirby Transformations Easter Eggs<br><br> Displaying all of the cool easter eggs related to stone Kirby's transformations.";
         time = -1;
    }
    time+=1;
    }, 5500)
}

