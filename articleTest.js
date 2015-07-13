function change() {
setInterval(function change1() {
    if (time == 0) {
         document.getElementById("WNdate").innerHTML = "6/26/2015";
         document.getElementById("WNa").href = "5-coolest-things-from-super-mario-sunshine.html";
         document.getElementById("WNimg").src = "images/sirenaBeachGamecubeControllerSmall.jpg";
         document.getElementById("WNp").innerHTML = "5 Coolest Things from<br>Super Mario Sunshine<br>My list of the 5 coolest things <br>from <i>Super Mario Sunshine</i>.";
         document.getElementById("WN").style.width = "320px";
         document.getElementById("main").background-color = red;
    }
    if (time == 1) {
         document.getElementById("WNdate").innerHTML = "6/15/2015";
         document.getElementById("WNa").href = "windWakerTrivia.html";
         document.getElementById("WNimg").src = "images/windWakerStatues.png";
         document.getElementById("WNp").innerHTML = "Wind Waker Trivia<br>Trivia related to <i>The Legend of Zelda: Wind Waker</i>.";
         document.getElementById("WN").style.width = "345px";
    }
    if (time == 2) {
         document.getElementById("WNdate").innerHTML = "5/11/2015";
         document.getElementById("WNa").href = "legendOfZeldaStoryFillInTheBlanks.html";
         document.getElementById("WNimg").src = "images/tLoZStorySmall.jpg";
         document.getElementById("WNp").innerHTML = "Fill in the Blanks: <br>The Legend of Zelda Intro Story<br>A fill in the blanks game featuring the <br>story from <i>The Legend of Zelda</i>.";
         document.getElementById("WN").style.width = "281px"; 
    }
    if (time == 3) {
         document.getElementById("WNdate").innerHTML = "7/2/2015";
         document.getElementById("WNa").href = "5-coolest-things-from-super-mario-sunshine-video.html";
         document.getElementById("WNimg").src = "images/5-coolest-things-from-super-mario-sunshine-video.png";
         document.getElementById("WNp").innerHTML = "5 Coolest Things from<br>Super Mario Sunshine (Video)<br>My video of the 5 coolest things <br>from <i>Super Mario Sunshine</i>.";
         document.getElementById("WN").style.width = "362px";
         time = -1;
    }
    time+=1;
    }, 4444)
}