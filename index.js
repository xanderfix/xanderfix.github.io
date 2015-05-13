var time = 0;
function change() {
    setInterval(function change1() {
    if (time == 0) {
         document.getElementById("WNdate").innerHTML = "5/10/2015";
         document.getElementById("WNa").href = "theLegendOfZeldaMusic.html";
         document.getElementById("WNimg").src = "images/theLegendOfZeldaMusic.png";
         document.getElementById("WNp").innerHTML = "The Legend of Zelda Music<br>All of the music from <i>The Legend of Zelda</i>.";
         document.getElementById("WN").style.width = "318px";
    }
    if (time == 1) {
         document.getElementById("WNdate").innerHTML = "4/20/2015";
         document.getElementById("WNa").href = "DKC3cheats.html";
         document.getElementById("WNimg").src = "images/DKC3color.png";
         document.getElementById("WNp").innerHTML = "Donkey Kong Country 3 Cheats<br>Information about all of the cheats <br>from <i>Donkey Kong Country 3: Dixie Kong's Double Trouble</i> plus<br>a poll where you can vote for your favorite.";
         document.getElementById("WN").style.width = "276px";
    }
    if (time == 2) {
         document.getElementById("WNdate").innerHTML = "4/08/2015";
         document.getElementById("WNa").href = "legendOfZeldaTrivia.html";
         document.getElementById("WNimg").src = "images/ZELDAmaps.jpg";
         document.getElementById("WNp").innerHTML = "The Legend of Zelda Trivia<br>Five interesting pieces of trivia about <i>The Legend of Zelda</i>.";
         document.getElementById("WN").style.width = "366px";
    }
    if (time == 3) {
         document.getElementById("WNdate").innerHTML = "5/11/2015";
         document.getElementById("WNa").href = "legendOfZeldaStoryFillInTheBlanks.html";
         document.getElementById("WNimg").src = "images/tLoZStorySmall.jpg";
         document.getElementById("WNp").innerHTML = "Fill in the Blanks: <br>The Legend of Zelda Intro Story<br>A fill in the blanks game featuring the <br>story from <i>The Legend of Zelda</i>.";
         document.getElementById("WN").style.width = "281px"; 
         time = -1;
    }
    time+=1;
    }, 4000)
}

