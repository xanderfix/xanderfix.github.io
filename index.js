var time = 0;
function change() {
    setInterval(function change1() {
    if (time == 0) {
         document.getElementById("WNdate").innerHTML = "5/16/2016";
         document.getElementById("WNa").href = "How-to-Play-the-Yoshis-Island-Story-Theme.html";
         document.getElementById("WNimg").src = "images/yoshisIslandStoryTheme.png";
         document.getElementById("WNp").innerHTML = "How to Play the Yoshi's Island Story Theme";
    }
    if (time == 1) {
         document.getElementById("WNdate").innerHTML = "5/11/2016";
         document.getElementById("WNa").href = "Yoshis-Island-Story-Theme-Cover.html";
         document.getElementById("WNimg").src = "images/yoshisIslandStoryThemeCover.png";
         document.getElementById("WNp").innerHTML = "Yoshi's Island Story Theme Cover<br><br> My cover of the story theme from Super Mario World 2: Yoshi's Island.";
    }
    if (time == 2) {
         document.getElementById("WNdate").innerHTML = "4/13/2016";
         document.getElementById("WNa").href = "Interview-DKC-speedrunner-Antilles58.html";
         document.getElementById("WNimg").src = "images/antilles58DKC2.png";
         document.getElementById("WNp").innerHTML = "Interview:<br>Donkey Kong Country Speedrunner Antilles58<br><br>An interview with Antilles58, an expert Donkey Kong Country series speedrunner.";
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

