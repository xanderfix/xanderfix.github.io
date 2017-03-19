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
        TriviaData[0][0] = "How many times is K. Rool(including King, Kaptain, and Baron) fought in the original Donkey Kong Country Trilogy?";
        TriviaData[1][0] = "In an infamous Donkey Kong Country easter egg that causes Rambi to change shape in the first level, what does Rambi turn into?";
        TriviaData[2][0] = "What is the name of the Brother Bear that lives in Donkey Kong Country 3's Lost World?";
        TriviaData[3][0] = "In Donkey Kong Country 2, what information does Wrinkly Kong give about herself?";
        TriviaData[4][0] = "In Donkey Kong Country Tropical Freeze, what happens when Donkey Kong re-enters the plane in the first level and pounds next to the TVs?";
        TriviaData[5][0] = "Which of these is NOT a Donkey Kong Country level?";
        TriviaData[6][0] = "Which of these characters did NOT compete in Cranky's Video Game Hero competition?";
        TriviaData[7][0] = "What is the scientific name of the Banana Bird?";
        TriviaData[8][0] = "What does Baron K. Roolestien tell the Kongs he made KAOS out of?";
        TriviaData[9][0] = "Which of these instruments is NOT a Donkey Kong Country Returns boss?";
   
        // Answers
        TriviaData[0][1] = "C";
        TriviaData[1][1] = "B";
        TriviaData[2][1] = "A";
        TriviaData[3][1] = "C";
        TriviaData[4][1] = "D";
        TriviaData[5][1] = "A";
        TriviaData[6][1] = "D";
        TriviaData[7][1] = "B";
        TriviaData[8][1] = "A";
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
        TriviaData[0][3] = "3";   
        TriviaData[1][3] = "A gold Winky";
        TriviaData[2][3] = "Boomer";
        TriviaData[3][3] = "\"I give the best advice.\"";
        TriviaData[4][3] = "The TVs play the Donkey Kong Country TV show";
        TriviaData[5][3] = "Aquatic Ambiance";
        TriviaData[6][3] = "Link";
        TriviaData[7][3] = "Birdus Bananus";
        TriviaData[8][3] = "His wife's best pots and pans";
        TriviaData[9][3] = "Xylophone";     

        //B
        TriviaData[0][4] = "6";   
        TriviaData[1][4] = "A silver Donkey Kong";
        TriviaData[2][4] = "Bjorn";
        TriviaData[3][4] = "\"I'm over 100 years old.\"";
        TriviaData[4][4] = "Nothing happens";
        TriviaData[5][4] = "Trick Track Trek";
        TriviaData[6][4] = "Mario";
        TriviaData[7][4] = "Bananus Goldus Flutterus";
        TriviaData[8][4] = "Titanium";
        TriviaData[9][4] = "Accordion";       

        //C
        TriviaData[0][5] = "5";   
        TriviaData[1][5] = "A bronze Expresso";
        TriviaData[2][5] = "Ba-Boom";
        TriviaData[3][5] = "\"Only one peice of information I give is useless.\"";
        TriviaData[4][5] = "The Tvs play Super Mario Bros";
        TriviaData[5][5] = "Loopy Lights";
        TriviaData[6][5] = "Yoshi";
        TriviaData[7][5] = "Bananus Avialae Aureus";
        TriviaData[8][5] = "Carbon steel";
        TriviaData[9][5] = "Harp";       

        //D
        TriviaData[0][6] = "9";   
        TriviaData[1][6] = "None of the above";
        TriviaData[2][6] = "Blue";
        TriviaData[3][6] = "\"I've taught to every kind of kremling.\"";
        TriviaData[4][6] = "The Tvs play the Donkey Kong Country Returns title screen";
        TriviaData[5][6] = "Temple Tempest";
        TriviaData[6][6] = "Kirby";
        TriviaData[7][6] = "Avialae Bananus Goldus";
        TriviaData[8][6] = "Lead";
        TriviaData[9][6] = "Panflute";   

        //info
        TriviaData[0][7] = "He's fought as King K. Rool in Donkey Kong Country's Ganplank Galleon, as Kaptain K. Rool in K. Rool Duel and Krocodile Kore in Donkey Kong Country 2, and as Baron K. Roolestien in Kastle Kaos and The Knautalis in Donkey Kong Country 3.";   
        TriviaData[1][7] = "If you bring Rambi to the level's beginning, grab the hidden oil drum, throw it at the tree, jump on it, then dismount when you touch Rambi, he will turn into a silver version of Donkey Kong.";
        TriviaData[2][7] = "Boomer lives in the lost world in his bomb shelter and if you bring him bonus coins, he blow up the piles of rocks preventing access to the levels.";
        TriviaData[3][7] = "The information she gives about herself is that only one piece of information she gives out is useless.  If you then buy the information for Kaptain K. Rool, she just tells you to bring a lot of extra lives.";
        TriviaData[4][7] = "The TVs will begin to play the Donkey Kong Country Returns title screen.  This can be read on the Donkey Kong Country Tropical Freeze Easter Eggs page in the Trivia section of this website.";
        TriviaData[5][7] = "Aquatic Ambiance is the name of the game's underwater theme, not a level.";
        TriviaData[6][7] = "After beating Kaptain K. Rool, Diddy's place in the Video Game Heroes competition is shown.  To win he needs 40 DK coins to beat Link with 19, Yoshi with 29, and Mario with 39.";
        TriviaData[7][7] = "The scientific name of the Banana Bird, Bananus Goldus Flutterus, is told to Dixie and Kiddy by Bramble after they trade him a Flupperius Petallus Pongus(Red Flower) for a Banana Bird";
        TriviaData[8][7] = "Once Dixie and Kiddy defeat KAOS, Baron K. Roolestien complains that he used his wife's best pots and pans to make him.";
        TriviaData[9][7] = "Panflute is the Forest boss, Xylophone is the Cliff boss and Accordion is the Factory boss.  There is no harp.";       
 
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
