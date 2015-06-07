var time = 0;
function change() {
    setInterval(function change1() {
    if (time == 0) {
         document.getElementById("WNdate").innerHTML = "6/4/2015";
         document.getElementById("WNa").href = "oathToOrderCover.html";
         document.getElementById("WNimg").src = "images/oathToOrderCover.png";
         document.getElementById("WNp").innerHTML = "Oath to Order Cover<br>My cover of Oath to Order from<br><i>The Legend of Zelda: Majora's Mask</i>.";
         document.getElementById("WN").style.width = "362px";
    }
    if (time == 1) {
         document.getElementById("WNdate").innerHTML = "5/28/2015";
         document.getElementById("WNa").href = "starFoxReview.html";
         document.getElementById("WNimg").src = "images/starFoxCorneria.jpeg";
         document.getElementById("WNp").innerHTML = "Star Fox Review<br>My review of the SNES space-shooter, <i>Star Fox</i>.";
         document.getElementById("WN").style.width = "361px";
    }
    if (time == 2) {
         document.getElementById("WNdate").innerHTML = "5/11/2015";
         document.getElementById("WNa").href = "legendOfZeldaStoryFillInTheBlanks.html";
         document.getElementById("WNimg").src = "images/tLoZStorySmall.jpg";
         document.getElementById("WNp").innerHTML = "Fill in the Blanks: <br>The Legend of Zelda Intro Story<br>A fill in the blanks game featuring the <br>story from <i>The Legend of Zelda</i>.";
         document.getElementById("WN").style.width = "281px"; 
    }
    if (time == 3) {
         document.getElementById("WNdate").innerHTML = "6/7/2015";
         document.getElementById("WNa").href = "superMarioGalaxyReferences.html";
         document.getElementById("WNimg").src = "images/pokeballPlanet.jpg";
         document.getElementById("WNp").innerHTML = "Super Mario Galaxy References<br>All of the references to other Nintendo games in <i>Super Mario Galaxy</i>.";
         document.getElementById("WN").style.width = "320px";
         time = -1;
    }
    time+=1;
    }, 4444)
}

