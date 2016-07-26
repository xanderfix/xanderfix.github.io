var time = 1;
function change() {
    setInterval(function change1() {
    if (time == 0) {
         document.getElementById("WNdate").innerHTML = "7/26/2016";
         document.getElementById("WNa").href = "Why-Nintendo-Vans-Are-Awesome.html";
         document.getElementById("WNimg").src = "images/nintendoVans2.jpg";
         document.getElementById("WNp").innerHTML = "Xander explains why Nintendo Vans are awesome and you should definitely get some.";
    }
    if (time == 1) {
         document.getElementById("WNdate").innerHTML = "7/10/2016";
         document.getElementById("WNa").href = "Eight-Melodies-Earthbound-Beginnings-Cover.html";
         document.getElementById("WNimg").src = "images/eightMelodiesCover.png";
         document.getElementById("WNp").innerHTML = "Eight Melodies (Earthbound Beginnings/Mother) Cover<br><br>Xander's cover of the Eight Melodies from Earthbound Beginnings/Mother performed on piano and acoustic guitar; plus a metal Mt. Itoi cover at the end.";
    }
    if (time == 2) {
         document.getElementById("WNdate").innerHTML = "7/2/2016";
         document.getElementById("WNa").href = "art.html";
         document.getElementById("WNimg").src = "images/smashMural3.jpg";
         document.getElementById("WNp").innerHTML = "Super Smash Bros. Mural";
    }     
    if (time == 3) {
         document.getElementById("WNdate").innerHTML = "6/27/2016";
         document.getElementById("WNa").href = "How-to-Play-CROWNED.html";
         document.getElementById("WNimg").src = "images/magolorSoul.png";
         document.getElementById("WNp").innerHTML = "How to Play CROWNED";
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

