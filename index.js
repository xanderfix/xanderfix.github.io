var time = 0;
function change() {
    setInterval(function change1() {
    if (time == 0) {
         document.getElementById("WNdate").innerHTML = "12/3/2015";
         document.getElementById("WNa").href = "Epic-Zelda-Guitar-Medley-Performed-by-Nimrod16.html";
         document.getElementById("WNimg").src = "images/epicZeldaGuitarMedleyNim16Cropped.png";
         document.getElementById("WNp").innerHTML = "Epic Zelda Guitar Medley<br>Performed by Nimrod16<br><br> An amazing guitar medley of the best songs from Ocarina of Time and A Link to the Past.";
    }
    if (time == 1) {
         document.getElementById("WNdate").innerHTML = "11/27/2015";
         document.getElementById("WNa").href = "Stone-Kirby-Transformations-Easter-Eggs.html";
         document.getElementById("WNimg").src = "images/stoneKirbyRTDL.jpg";
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
         document.getElementById("WNdate").innerHTML = "12/8/2015";
         document.getElementById("WNa").href = "Mega-Man-Unlimited.html";
         document.getElementById("WNimg").src = "images/megaManUnlimited.jpg";
         document.getElementById("WNp").innerHTML = "Mega Man Unlimited<br><br> An amazing, NES-inspired, Mega Man fan game.";
         time = -1;
    }
    time+=1;
    }, 4444)
}

