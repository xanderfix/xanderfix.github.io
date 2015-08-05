var time = 0;
function change() {
    setInterval(function change1() {
    if (time == 0) {
         document.getElementById("WNdate").innerHTML = "7/30/2015";
         document.getElementById("WNa").href = "The-Mount-Rushmore-of-Video-Gaming.html";
         document.getElementById("WNimg").src = "images/mountRushmoreOfGamingSmaller.jpg";
         document.getElementById("WNp").innerHTML = 'The Mount Rushmore of Video Games<br>By Greg Betancourt Jr.<br> Greg discusses what video game characters he would carve onto a "Mount Rushmore of Video Gaming".';
         document.getElementById("WN").style.width = "362px";
    }
    if (time == 1) {
         document.getElementById("WNdate").innerHTML = "7/2/2015";
         document.getElementById("WNa").href = "5-coolest-things-from-super-mario-sunshine-video.html";
         document.getElementById("WNimg").src = "images/5-coolest-things-from-super-mario-sunshine-video.png";
         document.getElementById("WNp").innerHTML = "5 Coolest Things from<br>Super Mario Sunshine (Video)<br>My video of the 5 coolest things <br>from <i>Super Mario Sunshine</i>.";
         document.getElementById("WN").style.width = "362px";
    }
    if (time == 2) {
         document.getElementById("WNdate").innerHTML = "5/11/2015";
         document.getElementById("WNa").href = "legendOfZeldaStoryFillInTheBlanks.html";
         document.getElementById("WNimg").src = "images/legendOFZeldaStoryFillInTheBlanks.jpg";
         document.getElementById("WNp").innerHTML = "Fill in the Blanks: <br>The Legend of Zelda Intro Story<br>A fill in the blanks game featuring the <br>story from <i>The Legend of Zelda</i>.";
         document.getElementById("WN").style.width = "281px"; 
    }
    if (time == 3) {
         document.getElementById("WNdate").innerHTML = "8/4/2015";
         document.getElementById("WNa").href = "Kirby-Level-Name-Acronyms.html";
         document.getElementById("WNimg").src = "images/lollipopLand.png";
         document.getElementById("WNp").innerHTML = "Kirby Level Name Acronyms<br>Investigating the secrets of Kirby series level names.";
         document.getElementById("WN").style.width = "420px";
         time = -1;
    }
    time+=1;
    }, 4444)
}

