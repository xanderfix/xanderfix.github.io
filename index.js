var time = 0;
function change() {
    setInterval(function change1() {
    if (time == 0) {
         document.getElementById("WNdate").innerHTML = "2/25/2015";
         document.getElementById("WNa").href = "How-to-Play-the-Storm-Eagle-Stage-Theme.html";
         document.getElementById("WNimg").src = "images/stormEagle.jpg";
         document.getElementById("WNp").innerHTML = "How to Play the Storm Eagle Stage Theme";
    }
    if (time == 1) {
         document.getElementById("WNdate").innerHTML = "2/21/2015";
         document.getElementById("WNa").href = "Farewell-Hyrule-King-Piano-Cover.html";
         document.getElementById("WNimg").src = "images/FarewellHyruleKingPianoCover.png";
         document.getElementById("WNp").innerHTML = "Farewell Hyrule King Piano Cover<br><br> My piano cover of Farewell Hyrule King from the ending of Wind Waker.";
    }
    if (time == 2) {
         document.getElementById("WNdate").innerHTML = "2/13/2015";
         document.getElementById("WNa").href = "Emulators-Amazing-Tools-or-Piracy-Devices.html";
         document.getElementById("WNimg").src = "images/phoneEmulator.jpg";
         document.getElementById("WNp").innerHTML = "Emulators: Amazing Tools or Piracy Devices?<br><br>A look at the types and uses of emulators; and why there is so much controversy over their use.";
    }
    if (time == 3) {
         document.getElementById("WNdate").innerHTML = "11/27/2015";
         document.getElementById("WNa").href = "Stone-Kirby-Transformations-Easter-Eggs.html";
         document.getElementById("WNimg").src = "images/stoneKirbyDreamCollection.png";
         document.getElementById("WNp").innerHTML = "Stone Kirby Transformations Easter Eggs<br><br> Displaying all of the cool easter eggs related to stone Kirby's transformations.";
    }
    if (time == 4) {
         document.getElementById("WNdate").innerHTML = "3/6/2015";
         document.getElementById("WNa").href = "Jigsaw-Puzzles-Pokemon-20th-Mythicals-and-Mega-Evolutions.html";
         document.getElementById("WNimg").src = "images/megaEvolutions.png";
         document.getElementById("WNp").innerHTML = "Jigsaw Puzzles: <br>Pokemon 20th - Mythicals and Mega Evolutions<br><br>3 cool jigsaw puzzles to celebrate the 20th anniversary of the Pokemon series.";
         time = -1;
    }
    time+=1;
    }, 5000)
}

