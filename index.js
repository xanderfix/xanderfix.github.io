var time = 0;
function change() {
    setInterval(function change1() {
    if (time == 0) {
         document.getElementById("WNdate").innerHTML = "4/13/2016";
         document.getElementById("WNa").href = "Interview-DKC-speedrunner-Antilles58.html";
         document.getElementById("WNimg").src = "images/antilles58DKC2.png";
         document.getElementById("WNp").innerHTML = "Interview:<br>Donkey Kong Country Speedrunner Antilles58<br><br>An interview with Antilles58, an expert Donkey Kong Country series speedrunner.";
    }
    if (time == 1) {
         document.getElementById("WNdate").innerHTML = "4/4/2016";
         document.getElementById("WNa").href = "How-to-Play-Dark-Minds-Theme.html";
         document.getElementById("WNimg").src = "images/darkMind.png";
         document.getElementById("WNp").innerHTML = "How to Play Dark Mind's Theme<br>(Dark Mind's Second Form)";
    }
    if (time == 2) {
         document.getElementById("WNdate").innerHTML = "3/6/2016";
         document.getElementById("WNa").href = "Jigsaw-Puzzles-Pokemon-20th-Mythicals-and-Mega-Evolutions.html";
         document.getElementById("WNimg").src = "images/megaEvolutionsSmall.png";
         document.getElementById("WNp").innerHTML = "Jigsaw Puzzles: <br>Pokemon 20th - Mythicals and Mega Evolutions<br><br>3 cool jigsaw puzzles to celebrate the 20th anniversary of the Pokemon series.";
    }
    if (time == 3) {
         document.getElementById("WNdate").innerHTML = "2/21/2016";
         document.getElementById("WNa").href = "Farewell-Hyrule-King-Piano-Cover.html";
         document.getElementById("WNimg").src = "images/FarewellHyruleKingPianoCover.png";
         document.getElementById("WNp").innerHTML = "Farewell Hyrule King Piano Cover<br><br> My piano cover of Farewell Hyrule King from the ending of Wind Waker.";
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

