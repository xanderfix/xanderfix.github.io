var time = 1;
function change() {
    setInterval(function change1() {
    if (time == 0) {
         document.getElementById("WNdate").innerHTML = "12/25/2016";
         document.getElementById("WNa").href = "Toy-Day-Cover.html";
         document.getElementById("WNimg").src = "images/ToyDayCover.png";
         document.getElementById("WNp").innerHTML = "Toy Day Cover (Animal Crossing)<br><br>Nick and Xander's quaint little cover of Toy Day from Animal Crossing. Happy holidays everyone!";
    }
    if (time == 1) {
         document.getElementById("WNdate").innerHTML = "12/24/2016";
         document.getElementById("WNa").href = "How-to-Play-Jangle-Bells.html";
         document.getElementById("WNimg").src = "images/DKC3merry.png";
         document.getElementById("WNp").innerHTML = "How to Play Jangle Bells<br>(DKC3 MERRY Cheat)";
    }
    if (time == 2) {
         document.getElementById("WNdate").innerHTML = "9/13/2016";
         document.getElementById("WNa").href = "2-Year-Anniversary.html";
         document.getElementById("WNimg").src = "images/Nicole.gif";
         document.getElementById("WNp").innerHTML = "2 Year Anniversary!<br><br>Celebrating the second anniversary of ZeldaRocks plus a preview of a cool new music project.";
    }     
    if (time == 3) {
         document.getElementById("WNdate").innerHTML = "7/27/2016";
         document.getElementById("WNa").href = "art.html";
         document.getElementById("WNimg").src = "images/Venusorcerer.png";
         document.getElementById("WNp").innerHTML = "The Venusorcerer<br><br>A chalk drawing by Xander and Chloe based on Nick's love of Venusaur's and wordplay (everyone's in on this one!).";
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

