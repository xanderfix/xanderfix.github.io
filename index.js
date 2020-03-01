var time = 1;
function change() {
    setInterval(function change1() {
    if (time == 0) {
		 document.getElementById("WNdate").innerHTML = "3/01/2020";
         document.getElementById("WNa").href = "Launch-Octopus-Music.html";
         document.getElementById("WNimg").src = "images/Thumbnail - Launch Octopus.png";
         document.getElementById("WNp").innerHTML = "Mega Man X - Launch Octopus";
    }
    if (time == 1) {
         document.getElementById("WNdate").innerHTML = "9/18/2017";
         document.getElementById("WNa").href = "The-Super-Ninhundred.html";
         document.getElementById("WNimg").src = "images/SNESgames.jpg";
         document.getElementById("WNp").innerHTML = "The Super Ninhundred<br><br>One man's quest to beat 100 of the best SNES games, 100%. AKA Playing With Super Power!";
    }
    if (time == 2) {
         document.getElementById("WNdate").innerHTML = "6/1/2017";
         document.getElementById("WNa").href = "Zelda-II-Boss-Battle-Cover.html";
         document.getElementById("WNimg").src = "images/ZeldaIIBossBattleThumbnail.png";
         document.getElementById("WNp").innerHTML = "Zelda II - Boss Battle Cover<br><br>Xander's synth cover of Boss Battle from Zelda II: The Adventure of Link for the new Zelda tribute album Hylian Downfall.";
    }     
    if (time == 3) {
         document.getElementById("WNdate").innerHTML = "7/27/2016";
         document.getElementById("WNa").href = "art.html";
         document.getElementById("WNimg").src = "images/Venusorcerer.png";
         document.getElementById("WNp").innerHTML = "The Venusorcerer<br><br>A chalk drawing by Xander and Chloe based on Nicole's love of Venusaur's and wordplay (everyone's in on this one!).";
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

