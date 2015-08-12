var time = 0;
function change() {
    setInterval(function change1() {
    if (time == 0) {
         document.getElementById("WNdate").innerHTML = "8/7/2015";
         document.getElementById("WNa").href = "Jigsaw-Puzzle-Bring-on-the-Super-Ability.html";
         document.getElementById("WNimg").src = "images/superAbilitiesSmall.jpg";
         document.getElementById("WNp").innerHTML = "Jigsaw Puzzle: <br>Bring on the Super Ability!<br><br>A jigsaw puzzle using the official artwork for Kirby's Super Abilities from <i>Kirby's Return to Dreamland</i>.";
    }
    if (time == 1) {
         document.getElementById("WNdate").innerHTML = "8/4/2015";
         document.getElementById("WNa").href = "Kirby-Level-Name-Acronyms.html";
         document.getElementById("WNimg").src = "images/lollipopLand.png";
         document.getElementById("WNp").innerHTML = "Kirby Level Name Acronyms<br><br>Investigating the secrets of Kirby series level names.";
    }
    if (time == 2) {
         document.getElementById("WNdate").innerHTML = "7/30/2015";
         document.getElementById("WNa").href = "The-Mount-Rushmore-of-Video-Gaming.html";
         document.getElementById("WNimg").src = "images/mountRushmoreOfGamingSmaller.jpg";
         document.getElementById("WNp").innerHTML = 'The Mount Rushmore of Video Games<br>By Greg Betancourt Jr.<br><br> Greg discusses what video game characters he would carve onto a "Mount Rushmore of Video Gaming".';
    }
    if (time == 3) {
         document.getElementById("WNdate").innerHTML = "7/11/2015";
         document.getElementById("WNa").href = "songOfStormsRemix.html";
         document.getElementById("WNimg").src = "images/songOfStormsRemix.png";
         document.getElementById("WNp").innerHTML = "Song of Storms Remix<br><br> My remix of the Song of Storms from <i>The Legend of Zelda: Ocarina of Time</i> played on the computer program Synthesia.";
    }
    if (time == 4) {
         document.getElementById("WNdate").innerHTML = "8/12/2015";
         document.getElementById("WNa").href = "How-to-Play-Midnas-Lament.html";
         document.getElementById("WNimg").src = "images/midnasLament.jpg";
         document.getElementById("WNp").innerHTML = "How to Play Midna's Lament";
         time = -1;
    }
    time+=1;
    }, 4444)
}

