var time = 0;
function change() {
    setInterval(function change1() {
    if (time == 0) {
         document.getElementById("WNdate").innerHTML = "3/25/2016";
         document.getElementById("WNa").href = "Artwork-From-2015.html";
         document.getElementById("WNimg").src = "images/The Trials of Yoshi.png";
         document.getElementById("WNp").innerHTML = "Artwork from 2015<br><br>A collection of all of the artwork I created in 2015.";
    }
    if (time == 1) {
         document.getElementById("WNdate").innerHTML = "3/17/2016";
         document.getElementById("WNa").href = "Green-Hill-Zone-Remix.html";
         document.getElementById("WNimg").src = "images/GreenHillZoneRemixMusic.png";
         document.getElementById("WNp").innerHTML = "Green Hill Zone Remix";
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

