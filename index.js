var time = 0;
function change() {
    setInterval(function change1() {
    if (time == 0) {
         document.getElementById("WNdate").innerHTML = "12/17/2015";
         document.getElementById("WNa").href = "Super-Mario-Galaxy-The-Girls-Sadness-Piano-Cover.html";
         document.getElementById("WNimg").src = "images/Super Mario Galaxy The Girl's Sadness Piano Cover.png";
         document.getElementById("WNp").innerHTML = "Super Mario Galaxy<br>The Girl's Sadness Piano Cover<br><br> My piano cover of the sad storybook theme from Super Mario Galaxy.";
    }
    if (time == 1) {
         document.getElementById("WNdate").innerHTML = "11/27/2015";
         document.getElementById("WNa").href = "Stone-Kirby-Transformations-Easter-Eggs.html";
         document.getElementById("WNimg").src = "images/stoneKirbyDreamCollection.png";
         document.getElementById("WNp").innerHTML = "Stone Kirby Transformations Easter Eggs<br><br> Displaying all of the cool easter eggs related to stone Kirby's transformations.";
    }
    if (time == 2) {
         document.getElementById("WNdate").innerHTML = "11/5/2015";
         document.getElementById("WNa").href = "How-to-Play-Duel-in-the-Darkened-Sky.html";
         document.getElementById("WNimg").src = "images/darkMatterSwordsman.png";
         document.getElementById("WNp").innerHTML = "How to Play Duel in the Darkened Sky<br>(Dark Matter Battle Phase 1)";
    }
    if (time == 3) {
         document.getElementById("WNdate").innerHTML = "8/12/2015";
         document.getElementById("WNa").href = "Jigsaw-Puzzle-Super-Mario-Bros-3-World-Maps.html";
         document.getElementById("WNimg").src = "images/SMB3W1.png";
         document.getElementById("WNp").innerHTML = "Jigsaw Puzzle: <br>Super Mario Bros. 3 World Maps<br><br>Jigsaw puzzles for the <i>Super Mario Bros. 3</i> world maps.";
    }
    if (time == 4) {
         document.getElementById("WNdate").innerHTML = "12/19/2015";
         document.getElementById("WNa").href = "Interview-with-Wanderweird.html";
         document.getElementById("WNimg").src = "images/mandalaSuperMetroid.jpg";
         document.getElementById("WNp").innerHTML = "Interview with Wanderweird<br><br> An interview with an amazing artist who creates retro video game mandalas.";
         time = -1;
    }
    time+=1;
    }, 5000)
}

