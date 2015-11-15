var time = 0;
function change() {
    setInterval(function change1() {
    if (time == 0) {
         document.getElementById("WNdate").innerHTML = "11/5/2015";
         document.getElementById("WNa").href = "How-to-Play-Duel-in-the-Darkened-Sky.html";
         document.getElementById("WNimg").src = "images/darkMatterSwordsman.png";
         document.getElementById("WNp").innerHTML = "How to Play Duel in the Darkened Sky<br>(Dark Matter Battle Phase 1)";
    }
    if (time == 1) {
         document.getElementById("WNdate").innerHTML = "10/15/2015";
         document.getElementById("WNa").href = "Heavy-Lobster-Guitar-Cover.html";
         document.getElementById("WNimg").src = "images/Heavy Lobster Guitar Cover.png";
         document.getElementById("WNp").innerHTML = "Heavy Lobster Guitar Cover<br><br>My electric guitar cover of the Heavy Lobster theme from <i>Kirby Super Star Ultra</i>.";
    }
    if (time == 2) {
         document.getElementById("WNdate").innerHTML = "9/25/2015";
         document.getElementById("WNa").href = "A-Link-to-the-Past-Trivia.html";
         document.getElementById("WNimg").src = "images/cucooLady.png";
         document.getElementById("WNp").innerHTML = "A Link to the Past Trivia<br><br> Cool A Link to the Past trivia; including a secret about the Cucoos.";
    }
    if (time == 3) {
         document.getElementById("WNdate").innerHTML = "8/12/2015";
         document.getElementById("WNa").href = "Jigsaw-Puzzle-Super-Mario-Bros-3-World-Maps.html";
         document.getElementById("WNimg").src = "images/SMB3W1.png";
         document.getElementById("WNp").innerHTML = "Jigsaw Puzzle: <br>Super Mario Bros. 3 World Maps<br><br>Jigsaw puzzles for the <i>Super Mario Bros. 3</i> world maps.";
    }
    if (time == 4) {
         document.getElementById("WNdate").innerHTML = "11/15/2015";
         document.getElementById("WNa").href = "Amazing-Artwork-by-Arrow-Valley.html";
         document.getElementById("WNimg").src = "images/ArrowValleyTwinmold.jpg";
         document.getElementById("WNp").innerHTML = "Amazing Artwork by Arrow Valley<br><br> Showcasing some amazing Nintendo artwork by YouTube channel Arrow Valley.";
         time = -1;
    }
    time+=1;
    }, 4444)
}

