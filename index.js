var time = 0;
function change() {
    setInterval(function change1() {
    if (time == 0) {
         document.getElementById("WNdate").innerHTML = "10/16/2015";
         document.getElementById("WNa").href = "How-to-Play-the-Heavy-Lobster-Theme.html";
         document.getElementById("WNimg").src = "images/How to Play the Heavy Lobster Theme.png";
         document.getElementById("WNp").innerHTML = "How to Play the Heavy Lobster Theme";
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
         document.getElementById("WNdate").innerHTML = "10/24/2015";
         document.getElementById("WNa").href = "A-Beginners-Guide-to-Splatoon-Part-3-Clothing-and-Weapons.html";
         document.getElementById("WNimg").src = "images/ammoKnights.jpg";
         document.getElementById("WNp").innerHTML = "Part 3 Clothing and Weapons<br><br> The third part of my beginner's guide to <i>Splatoon</i> explaining the functions and purchasing of clothing and weapons."
         time = -1;
    }
    time+=1;
    }, 4444)
}

