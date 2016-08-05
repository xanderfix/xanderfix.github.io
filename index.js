var time = 1;
function change() {
    setInterval(function change1() {
    if (time == 0) {
         document.getElementById("WNdate").innerHTML = "8/5/2016";
         document.getElementById("WNa").href = "Interview-Someone-Who-Actually-Caught-Em-All.html";
         document.getElementById("WNimg").src = "images/completePokedex.png";
         document.getElementById("WNp").innerHTML = "Interview:<br>Someone Who Actually Caught 'Em All<br><br>An interview with someone who completed the amazing feat of catching all 721 Pokemon!";
    }
    if (time == 1) {
         document.getElementById("WNdate").innerHTML = "7/27/2016";
         document.getElementById("WNa").href = "art.html";
         document.getElementById("WNimg").src = "images/Venusorcerer.png";
         document.getElementById("WNp").innerHTML = "The Venusorcerer<br><br>A chalk drawing by Xander and Chloe based on Nick's love of Venusaur's and wordplay (everyone's in on this one!).";
    }
    if (time == 2) {
         document.getElementById("WNdate").innerHTML = "7/26/2016";
         document.getElementById("WNa").href = "Home-Undertale-Ness-House-Cover.html";
         document.getElementById("WNimg").src = "images/homeUndertaleCover.png";
         document.getElementById("WNp").innerHTML = "Home (Undertale)/Ness's House Cover<br><br>Xander and Nick's duet arrangement of Home from Undertale and Ness's House (Home Sweet Home) from Earthbound.";
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

