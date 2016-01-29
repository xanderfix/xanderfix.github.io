var time = 0;
function change() {
    setInterval(function change1() {
    if (time == 0) {
         document.getElementById("WNdate").innerHTML = "1/18/2015";
         document.getElementById("WNa").href = "How-to-Play-Green-Hill-Zone.html";
         document.getElementById("WNimg").src = "images/greenHillZone.png";
         document.getElementById("WNp").innerHTML = "How to Play Green Hill Zone";
    }
    if (time == 1) {
         document.getElementById("WNdate").innerHTML = "12/17/2015";
         document.getElementById("WNa").href = "Super-Mario-Galaxy-The-Girls-Sadness-Piano-Cover.html";
         document.getElementById("WNimg").src = "images/Super Mario Galaxy The Girl's Sadness Piano Cover.png";
         document.getElementById("WNp").innerHTML = "Super Mario Galaxy<br>The Girl's Sadness Piano Cover<br><br> My piano cover of the sad storybook theme from Super Mario Galaxy.";
    }
    if (time == 2) {
         document.getElementById("WNdate").innerHTML = "11/27/2015";
         document.getElementById("WNa").href = "Stone-Kirby-Transformations-Easter-Eggs.html";
         document.getElementById("WNimg").src = "images/stoneKirbyDreamCollection.png";
         document.getElementById("WNp").innerHTML = "Stone Kirby Transformations Easter Eggs<br><br> Displaying all of the cool easter eggs related to stone Kirby's transformations.";
    }
    if (time == 3) {
         document.getElementById("WNdate").innerHTML = "8/12/2015";
         document.getElementById("WNa").href = "Jigsaw-Puzzle-Super-Mario-Bros-3-World-Maps.html";
         document.getElementById("WNimg").src = "images/SMB3W1.png";
         document.getElementById("WNp").innerHTML = "Jigsaw Puzzle: <br>Super Mario Bros. 3 World Maps<br><br>Jigsaw puzzles for the <i>Super Mario Bros. 3</i> world maps.";
    }
    if (time == 4) {
         document.getElementById("WNdate").innerHTML = "1/29/2015";
         document.getElementById("WNa").href = "DLC-Just-Right-Too-Much-or-Not-Enough.html";
         document.getElementById("WNimg").src = "images/sonicAndKnuckles.png";
         document.getElementById("WNp").innerHTML = "DLC: Just Right, Too Much, or Not Enough?<br>By Greg Betancourt Jr.<br><br>Greg examines the pros and cons of DLC and how it's changed gaming.";
         time = -1;
    }
    time+=1;
    }, 5000)
}

