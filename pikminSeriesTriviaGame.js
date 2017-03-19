 // Array of trivia data
        var TriviaData = new Array(10)
        createTwoDimensionalArray(3);
 
        // Variables to track state of the game
        // i.e. number questions asked, current correct total and current question
        var questionsAsked = 0;
        var totalCorrect = 0;
        var currentQuestion = 100000;
        var selectionValid = false;
 
        // Questions
        TriviaData[0][0] = "What family does the Dwarf Red Bulborb belong to?";
        TriviaData[1][0] = "Which of these ship parts IS required for Olimar to succesfully take off?";
        TriviaData[2][0] = "How many onions fly after Olimar in the perfect ending of Pikmin?";
        TriviaData[3][0] = "In Pikmin 3, according to Olimar, what are the flowers that are found in caves and only bloom when exposed to bright light called?";
        TriviaData[4][0] = "In Pikmin, will Pikmin die if a Yellow Waggywol falls on them?";
        TriviaData[5][0] = "In Pikmin 2, how many of the game's treasures are found in caves?";
        TriviaData[6][0] = "What color are the stripes on a Winged Pikmin?";
        TriviaData[7][0] = "In Pikmin 3, what fruit is dropped by the Armored Mawdad?";
        TriviaData[8][0] = "Prior to Pikmin 2, what happened to the Golden PikPik Carrots?";
        TriviaData[9][0] = "Which of these is NOT the scientific name of a creature in the Piklopedia?";
   
        // Answers
        TriviaData[0][1] = "B";
        TriviaData[1][1] = "D";
        TriviaData[2][1] = "A";
        TriviaData[3][1] = "C";
        TriviaData[4][1] = "D";
        TriviaData[5][1] = "B";
        TriviaData[6][1] = "C";
        TriviaData[7][1] = "A";
        TriviaData[8][1] = "B";
        TriviaData[9][1] = "C";
 
        // Has question been asked
        // -- necessary because we are asking in random order
        TriviaData[0][2] = "no";
        TriviaData[1][2] = "no";
        TriviaData[2][2] = "no";
        TriviaData[3][2] = "no";
        TriviaData[4][2] = "no";
        TriviaData[5][2] = "no";
        TriviaData[6][2] = "no";
        TriviaData[7][2] = "no";
        TriviaData[8][2] = "no";
        TriviaData[9][2] = "no";

        //A
        TriviaData[0][3] = "Grub-Dog";   
        TriviaData[1][3] = "Nova Blaster";
        TriviaData[2][3] = "14";
        TriviaData[3][3] = "Illuminus pathus";
        TriviaData[4][3] = "Yes";
        TriviaData[5][3] = "201";
        TriviaData[6][3] = "Brown";
        TriviaData[7][3] = "Fiery Feast";
        TriviaData[8][3] = "A Ravenous Space Bunny ate them";
        TriviaData[9][3] = "Shiropedes anacondii";     

        //B
        TriviaData[0][4] = "Breadbug";   
        TriviaData[1][4] = "Secret Safe";
        TriviaData[2][4] = "11";
        TriviaData[3][4] = "Bloominus Illuminus";
        TriviaData[4][4] = "No";
        TriviaData[5][4] = "175";
        TriviaData[6][4] = "There are no stripes";
        TriviaData[7][4] = "Feast of Flame";
        TriviaData[8][4] = "Louie ate them";
        TriviaData[9][4] = "Vulpes cauda";       

        //C
        TriviaData[0][5] = "Kettlebug";   
        TriviaData[1][5] = "UV Lamp";
        TriviaData[2][5] = "8";
        TriviaData[3][5] = "Bloominus Stemple";
        TriviaData[4][5] = "Sometimes, it varies due to glitches";
        TriviaData[5][5] = "147";
        TriviaData[6][5] = "Dark Pink";
        TriviaData[7][5] = "Flamboyant Fire";
        TriviaData[8][5] = "The ship carrying them crashed";
        TriviaData[9][5] = "Oculus kageyamii napalmens";       

        //D
        TriviaData[0][6] = "Unknown";   
        TriviaData[1][6] = "Libra";
        TriviaData[2][6] = "12";
        TriviaData[3][6] = "Illuminus Stemple";
        TriviaData[4][6] = "There's no such thing as a Yellow Waggywol!";
        TriviaData[5][6] = "26";
        TriviaData[6][6] = "Black";
        TriviaData[7][6] = "Crimson Banquet";
        TriviaData[8][6] = "None of the above";
        TriviaData[9][6] = "Parastacoidea reptantia";   

        //info
        TriviaData[0][7] = "According to Captain Olimar, the Dwarf Red Bulborb is actually a species of Breadbug that survives by mimicking Red Bulborbs.";   
        TriviaData[1][7] = "Though it's only a gem with a small portion of the hull attached, Olimar will crash if he has not collected the Libra by the 30th day.";
        TriviaData[2][7] = "Upon collecting all 30 ship parts and leaving the atomsphere, Olimar looks behind him and sees five Pink Onions, four Black/Grey Onions, two Cyan Onions, one Green Onion, one Orange Onion, and one Purple Onion.";
        TriviaData[3][7] = "A data file can be found in the Formidable Oak where Olimar states that he has found several Bloominus Stemple which only bloom when exposed to bright light and if he could only produce bright light in the cavern he may be able to escape.";
        TriviaData[4][7] = "There is no such thing as Yellow Waggywol!  This does happen with dying Wollywogs and Yellow Wollywogs though.  It's commonly refered to as the crushing glitch because the Pikmin die without ghosts or a sound effect.";
        TriviaData[5][7] = "Out of the game's 201 treasures, 175 are found in caves.";
        TriviaData[6][7] = "Winged Pikmin have dark pink stripes on their bodies.  This also makes them polychromatic, unlike other Pikmin species";
        TriviaData[7][7] = "The Fiery Feast, worth 2 1/2 cups of juice, is dropped.";
        TriviaData[8][7] = "Revealed upon getting a pink flower on every challenge mode stage, Louie got hungry and ate all of them.";
        TriviaData[9][7] = "Oculus kageyamii napalmens is a combination of the Red Bulborb, Oculus kageyamii russus, and the Fiery Dweevil, Mandarachnia napalmens.";       
 
        // Sets question text and indicator so that we know the question has been displayed
        function setQuestion() {
            selectionValid = false; // Flag to make sure question has not been asked yet
            while (selectionValid == false) {
                currentQuestion = Math.floor(Math.random() * 10); // randomly select starting question
                if (TriviaData[currentQuestion][2] == "no") {
                    selectionValid = true;
                }
            }
            document.getElementById("TriviaQuestion").innerHTML = TriviaData[currentQuestion][0];
            document.getElementById("atext").innerHTML = TriviaData[currentQuestion][3];
            document.getElementById("btext").innerHTML = TriviaData[currentQuestion][4];
            document.getElementById("ctext").innerHTML = TriviaData[currentQuestion][5];
            document.getElementById("dtext").innerHTML = TriviaData[currentQuestion][6];
            TriviaData[currentQuestion][2] = "yes";
            questionsAsked += 1;
        }
        setQuestion();
 
        function processAnswer(myAnswer) {
            if (TriviaData[currentQuestion][1] == myAnswer) {
                // answer correct
                totalCorrect = totalCorrect + 1;
            }
        }
 
        // This function creates our two dimensional array
        function createTwoDimensionalArray(arraySize) {
            for (i = 0; i < TriviaData.length; ++i)
                TriviaData[i] = new Array(arraySize);
        }
 
        // This function checks the answer, updates correct total
        // and randomly selects the next question
        function checkAnswer() {
            if(questionsAsked > 0) {
                if (document.getElementById("A").checked) {
                    processAnswer("A");
                }
                else if (document.getElementById("B").checked) {
                    processAnswer("B");
                }
                else if (document.getElementById("C").checked) {
                    processAnswer("C");
                }
                else if (document.getElementById("D").checked) {
                    processAnswer("D");
                }
                document.getElementById("info").innerHTML = TriviaData[currentQuestion][7];
            }
            // get next question if not asked all yet
            if (questionsAsked < 10) {
                setQuestion();
            }
            // final question asked - disable button and show final results
            else {
                alert("Quiz complete! You got " + totalCorrect + " correct out of 10.");
                document.getElementById("ButtonContinue").disabled = true;
            }
            // update totals
            document.getElementById("NumberAsked").innerHTML = questionsAsked;
            document.getElementById("NumberCorrect").innerHTML = totalCorrect;
        }
