var trivia = [["How many times is K. Rool(including King, Kaptain, and Baron) fought in the original Donkey Kong Country Trilogy?", "C", 3, 6, 5, 9, "no"]]

var questionNum=1;
var numCorrect=0;
var currentQuestion=0;

setQuestion();
function setQuestion() {
    selectionValid = false; // Flag to make sure question has not been asked yet
    while (selectionValid == false) {
    currentQuestion = 0; // randomly select starting question
        if (trivia[currentQuestion][6] == "no") {
            selectionValid = true;
        };
    };
    document.getElementById("TriviaQuestion").innerHTML = trivia[currentQuestion][0];
    document.getElementById("A").innerHTML = trivia[currentQuestion][2];
    document.getElementById("B").innerHTML = trivia[currentQuestion][3];
    document.getElementById("C").innerHTML = trivia[currentQuestion][4];
    document.getElementById("D").innerHTML = trivia[currentQuestion][5];
    trivia[currentQuestion][6] = "yes";
    questionsAsked = questionsAsked + 1;
};

function processAnswer(myAnswer) {
    if(myAnswer==trivia[currentQuestion[1]]) {
        numCorrect += 1;
    };
};

function checkAnswer() {
    if(document.getElementById("A").checked) {
        processAnswer("A");
    };
    else if(document.getElementById("B").checked) {
        processAnswer("B");
    };
    else if(document.getElementById("C").checked) {
        processAnswer("C");
    };
    else if(document.getElementById("D").checked) {
        processAnswer("D");
    };
    // get next question if not asked all yet
    if (questionsAsked <= 10) {
        setQuestion();
    };
    // final question asked - disable button and show final results
    else {
        alert("Quiz complete! You got " + totalCorrect + " correct out of 10.");
        document.getElementById("ButtonContinue").disabled = true;
    };
    // update totals
    document.getElementById("NumberAsked").innerHTML = questionsAsked;
    document.getElementById("NumberCorrect").innerHTML = totalCorrect;
};
