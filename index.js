var time = 0;
function change() {
    setInterval(function change1() {
    if (time == 0) {
         document.getElementById("WNdate").innerHTML = "5/28/2015";
         document.getElementById("WNa").href = "starFoxReview.html";
         document.getElementById("WNimg").src = "images/starFoxCorneria.jpeg";
         document.getElementById("WNp").innerHTML = "Star Fox Review<br>My review of the SNES space-shooter, <i>Star Fox</i>.";
         document.getElementById("WN").style.width = "361px";
    }
    if (time == 1) {
         document.getElementById("WNdate").innerHTML = "5/11/2015";
         document.getElementById("WNa").href = "legendOfZeldaStoryFillInTheBlanks.html";
         document.getElementById("WNimg").src = "images/tLoZStorySmall.jpg";
         document.getElementById("WNp").innerHTML = "Fill in the Blanks: <br>The Legend of Zelda Intro Story<br>A fill in the blanks game featuring the <br>story from <i>The Legend of Zelda</i>.";
         document.getElementById("WN").style.width = "281px"; 
    }
    if (time == 2) {
         document.getElementById("WNdate").innerHTML = "5/10/2015";
         document.getElementById("WNa").href = "theLegendOfZeldaMusic.html";
         document.getElementById("WNimg").src = "images/theLegendOfZeldaMusic.png";
         document.getElementById("WNp").innerHTML = "The Legend of Zelda Music<br>All of the music from <i>The Legend of Zelda</i>.";
         document.getElementById("WN").style.width = "318px";
    }
    if (time == 3) {
         document.getElementById("WNdate").innerHTML = "5/29/2015";
         document.getElementById("WNa").href = "SNESlegendOfZeldaReferences.html";
         document.getElementById("WNimg").src = "images/KSStriforceSmall.png";
         document.getElementById("WNp").innerHTML = "SNES Legend of Zelda<br>Series References<br>All of the references to the Legend of Zelda series in SNES games.";
         document.getElementById("WN").style.width = "318px";
         time = -1;
    }
    time+=1;
    }, 4000)
}

