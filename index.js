var time = 0;
function change() {
    setInterval(function change1() {
    if (time == 0) {
         document.getElementById("WNdate").innerHTML = "6/7/2015";
         document.getElementById("WNa").href = "superMarioGalaxyReferences.html";
         document.getElementById("WNimg").src = "images/pokeballPlanet.jpg";
         document.getElementById("WNp").innerHTML = "Super Mario Galaxy References<br>All of the references to other Nintendo games in <i>Super Mario Galaxy</i>.";
         document.getElementById("WN").style.width = "320px";
    }
    if (time == 1) {
         document.getElementById("WNdate").innerHTML = "6/4/2015";
         document.getElementById("WNa").href = "oathToOrderCover.html";
         document.getElementById("WNimg").src = "images/oathToOrderCover.png";
         document.getElementById("WNp").innerHTML = "Oath to Order Cover<br>My cover of Oath to Order from<br><i>The Legend of Zelda: Majora's Mask</i>.";
         document.getElementById("WN").style.width = "362px";
    }
    if (time == 2) {
         document.getElementById("WNdate").innerHTML = "5/11/2015";
         document.getElementById("WNa").href = "legendOfZeldaStoryFillInTheBlanks.html";
         document.getElementById("WNimg").src = "images/tLoZStorySmall.jpg";
         document.getElementById("WNp").innerHTML = "Fill in the Blanks: <br>The Legend of Zelda Intro Story<br>A fill in the blanks game featuring the <br>story from <i>The Legend of Zelda</i>.";
         document.getElementById("WN").style.width = "281px"; 
    }
    if (time == 3) {
         document.getElementById("WNdate").innerHTML = "6/8/2015";
         document.getElementById("WNa").href = "retroGamingMyHomeWithinMyHome.html";
         document.getElementById("WNimg").src = "images/atari2600ArticleSize.jpeg";
         document.getElementById("WNp").innerHTML = "Retro gaming: My home within my home <br>By Greg Betancourt Jr.<br>Greg Betancourt Jr. discusses why retro games are so amazing.";
         document.getElementById("WN").style.width = "370px";
         time = -1;
    }
    time+=1;
    }, 4444)
}

