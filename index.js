var time = 0;
function change() {
    setInterval(function change1() {
    if (time == 0) {
         document.getElementById("WNdate").innerHTML = "6/15/2015";
         document.getElementById("WNa").href = "windWakerTrivia.html";
         document.getElementById("WNimg").src = "images/windWakerStatues.png";
         document.getElementById("WNp").innerHTML = "Wind Waker Trivia<br>Trivia related to <i>The Legend of Zelda: Wind Waker</i>.";
         document.getElementById("WN").style.width = "345px";
    }
    if (time == 1) {
         document.getElementById("WNdate").innerHTML = "6/8/2015";
         document.getElementById("WNa").href = "retroGamingMyHomeWithinMyHome.html";
         document.getElementById("WNimg").src = "images/atari2600ArticleSize.jpeg";
         document.getElementById("WNp").innerHTML = "Retro gaming: My home within my home <br>By Greg Betancourt Jr.<br>Greg Betancourt Jr. discusses why retro games are so amazing.";
         document.getElementById("WN").style.width = "370px";
    }
    if (time == 2) {
         document.getElementById("WNdate").innerHTML = "5/11/2015";
         document.getElementById("WNa").href = "legendOfZeldaStoryFillInTheBlanks.html";
         document.getElementById("WNimg").src = "images/tLoZStorySmall.jpg";
         document.getElementById("WNp").innerHTML = "Fill in the Blanks: <br>The Legend of Zelda Intro Story<br>A fill in the blanks game featuring the <br>story from <i>The Legend of Zelda</i>.";
         document.getElementById("WN").style.width = "281px"; 
    }
    if (time == 3) {
         document.getElementById("WNdate").innerHTML = "6/18/2015";
         document.getElementById("WNa").href = "koopasThemeGuitarCover.html";
         document.getElementById("WNimg").src = "images/koopasThemeGuitarCover.png";
         document.getElementById("WNp").innerHTML = "Koopa's Theme Guitar Cover<br>My guitar cover of Koopa's Theme from <i>Super Mario 64</i>.";
         document.getElementById("WN").style.width = "362px";
         time = -1;
    }
    time+=1;
    }, 4444)
}

