var time = 1;
function change() {
    setInterval(function change1() {
    if (time == 0) {
         document.getElementById("WNdate").innerHTML = "3/18/2016";
         document.getElementById("WNa").href = "Revisiting-Pokemon-Red-Rescue-Team-The-Adventures-of-Team-LANmolas.html";
         document.getElementById("WNimg").src = "images/PokemonRedRescueTeamBox.jpg";
         document.getElementById("WNp").innerHTML = "Revisiting Pokemon Red Rescue Team: The Adventures of Team LANmolas<br><br>Nicole revisits her favorite childhood game, Pokemon Red Rescue Team, and comically finds it wasn't anywhere close to as good as she remembered.";
    }
    if (time == 1) {
         document.getElementById("WNdate").innerHTML = "2/24/2016";
         document.getElementById("WNa").href = "https://www.youtube.com/watch?v=wAdqNVmy3Ks";
         document.getElementById("WNimg").src = "images/FinalFantasy4BossCover.png";
         document.getElementById("WNp").innerHTML = "Final Fantasy 4 - Boss + Zeromus Cover<br><br>Xander's dual cover of the standard and final boss themes from Final Fantasy 4, accompanied by a sweet (though slightly cheesy) RPG style battle video!";
    }
    if (time == 2) {
         document.getElementById("WNdate").innerHTML = "12/24/2016";
         document.getElementById("WNa").href = "How-to-Play-Jangle-Bells.html";
         document.getElementById("WNimg").src = "images/DKC3merry.png";
         document.getElementById("WNp").innerHTML = "How to Play Jangle Bells<br>(DKC3 MERRY Cheat)";
    }     
    if (time == 3) {
         document.getElementById("WNdate").innerHTML = "7/27/2016";
         document.getElementById("WNa").href = "art.html";
         document.getElementById("WNimg").src = "images/Venusorcerer.png";
         document.getElementById("WNp").innerHTML = "The Venusorcerer<br><br>A chalk drawing by Xander and Chloe based on Nicole's love of Venusaur's and wordplay (everyone's in on this one!).";
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

