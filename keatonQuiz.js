 // Array of trivia data
        var TriviaData = new Array(30)
        createTwoDimensionalArray(3);
 
        // Variables to track state of the game
        // i.e. number questions asked, current correct total and current question
        var questionsAsked = 0;
        var totalCorrect = 0;
        var currentQuestion = 100000;
        var selectionValid = false;
        var correctOrNot = "?"
 
        // Questions
        TriviaData[0][0] = "How old is Tingle, the map salesman?";
        TriviaData[1][0] = "How many members are there in the Zora band, The Indigo-Go's?";
        TriviaData[2][0] = "What weapon does Romani, the girl at the ranch, use to practice?";
        TriviaData[3][0] = "What is the name of the mayor of Clock Town?";
        TriviaData[4][0] = "What is the name of the festival that is to be held in Clock Town?";
        TriviaData[5][0] = "What instrument does the Skull Kid play?";
        TriviaData[6][0] = "What time does Romani, the girl at the ranch, go to bed?";
        TriviaData[7][0] = "How many tiny cow figurines are there in Clock Town?";
        TriviaData[8][0] = "What is the name given to you by Romani, the girl at the ranch?";
        TriviaData[9][0] = "Who is the leader of the Bombers gang?";
        TriviaData[10][0] = "What does the owner of the Bomb Shop call his mother?";
        TriviaData[11][0] = "What is the name of the singer in the Zora band, The Indigo-Go's?";
        TriviaData[12][0] = "How many mailboxes are there in Clock Town?";
        TriviaData[13][0] = "Is Tingle the mapmaker left-handed or right-handed?";
        TriviaData[14][0] = "What is the name of the song that Romani, the girl at the ranch, teaches you?";
        TriviaData[15][0] = "What is Anju, the innkeeper, bad at doing?";
        TriviaData[16][0] = "How many cows are there at Romani Ranch?";
        TriviaData[17][0] = "What is the name of the vintage milk sold at the Milk Bar?";
        TriviaData[18][0] = "What color of trunks does Tingle the mapmaker wear?";
        TriviaData[19][0] = "What is the name of Anju's father?";
        TriviaData[20][0] = "What bad habit does Anju, the innkeeper, have?";
        TriviaData[21][0] = "What is the name of Clock Town's inn?";
        TriviaData[22][0] = "Mikau is of which Race?";
        TriviaData[23][0] = "How many cuccos are there in the barn at Romani Ranch?";
        TriviaData[24][0] = "At what time does Romani, the ranch girl, wake up?";
        TriviaData[25][0] = "Darmani is of which race?";
        TriviaData[26][0] = "How many balloons does Romani, the girl at the ranch, use during practice?";
        TriviaData[27][0] = "Where does Cremia, manager of Romani Ranch, try to deliver her milk?";
        TriviaData[28][0] = "Question: Once it's completed, how tall will the festival tower at the carnival be?";
        TriviaData[29][0] = "What are the magic words that Tingle created?  Tingle, Tingle... what?";

   
        // Answers
        TriviaData[0][1] = "C";
        TriviaData[1][1] = "B";
        TriviaData[2][1] = "B";
        TriviaData[3][1] = "C";
        TriviaData[4][1] = "B";
        TriviaData[5][1] = "B";
        TriviaData[6][1] = "B";
        TriviaData[7][1] = "C";
        TriviaData[8][1] = "C";
        TriviaData[9][1] = "C";
        TriviaData[10][1] = "C";
        TriviaData[11][1] = "B";
        TriviaData[12][1] = "B";
        TriviaData[13][1] = "B";
        TriviaData[14][1] = "A";
        TriviaData[15][1] = "C";
        TriviaData[16][1] = "B";
        TriviaData[17][1] = "B";
        TriviaData[18][1] = "C";
        TriviaData[19][1] = "B";
        TriviaData[20][1] = "A";
        TriviaData[21][1] = "C";
        TriviaData[22][1] = "C";
        TriviaData[23][1] = "A";
        TriviaData[24][1] = "A";
        TriviaData[25][1] = "B";
        TriviaData[26][1] = "A";
        TriviaData[27][1] = "B";
        TriviaData[28][1] = "B";
        TriviaData[29][1] = "C";

 
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
        TriviaData[10][2] = "no";
        TriviaData[11][2] = "no";
        TriviaData[12][2] = "no";
        TriviaData[13][2] = "no";
        TriviaData[14][2] = "no";
        TriviaData[15][2] = "no";
        TriviaData[16][2] = "no";
        TriviaData[17][2] = "no";
        TriviaData[18][2] = "no";
        TriviaData[19][2] = "no";
        TriviaData[20][2] = "no";
        TriviaData[21][2] = "no";
        TriviaData[22][2] = "no";
        TriviaData[23][2] = "no";
        TriviaData[24][2] = "no";
        TriviaData[25][2] = "no";
        TriviaData[26][2] = "no";
        TriviaData[27][2] = "no";
        TriviaData[28][2] = "no";
        TriviaData[29][2] = "no";

        //A
        TriviaData[0][3] = "15";   
        TriviaData[1][3] = "Four";
        TriviaData[2][3] = "Slingshot";
        TriviaData[3][3] = "Babour";
        TriviaData[4][3] = "Carnival of the Moon";
        TriviaData[5][3] = "Tin Whistle";
        TriviaData[6][3] = "Seven";
        TriviaData[7][3] = "Eight";
        TriviaData[8][3] = "Butterfly";
        TriviaData[9][3] = "Gorman";   
        TriviaData[10][3] = "Old Lady";   
        TriviaData[11][3] = "Toto";
        TriviaData[12][3] = "Four";
        TriviaData[13][3] = "Left-Handed";
        TriviaData[14][3] = "Epona's Song";
        TriviaData[15][3] = "Cleaning";
        TriviaData[16][3] = "Two";
        TriviaData[17][3] = "Romani Rum";
        TriviaData[18][3] = "Yellow";
        TriviaData[19][3] = "Padre";     
        TriviaData[20][3] = "She's quick to apologize";   
        TriviaData[21][3] = "Stockpile Inn";
        TriviaData[22][3] = "Deku Scrub";
        TriviaData[23][3] = "One";
        TriviaData[24][3] = "Six";
        TriviaData[25][3] = "Deku Scrub";
        TriviaData[26][3] = "One";
        TriviaData[27][3] = "Curiosity Shop";
        TriviaData[28][3] = "Two Stories";
        TriviaData[29][3] = "Abracadabra!";     


        //B
        TriviaData[0][4] = "25";   
        TriviaData[1][4] = "Five";
        TriviaData[2][4] = "Bow";
        TriviaData[3][4] = "Cagour";
        TriviaData[4][4] = "Carnival of Time";
        TriviaData[5][4] = "Flute";
        TriviaData[6][4] = "Eight";
        TriviaData[7][4] = "Nine";
        TriviaData[8][4] = "Cricket";
        TriviaData[9][4] = "Viscen";  
        TriviaData[10][4] = "Mother";   
        TriviaData[11][4] = "Lulu";
        TriviaData[12][4] = "Five";
        TriviaData[13][4] = "Right-Handed";
        TriviaData[14][4] = "Song of Healing";
        TriviaData[15][4] = "Writing Letters";
        TriviaData[16][4] = "Three";
        TriviaData[17][4] = "Chateau Romani";
        TriviaData[18][4] = "He doesn't wear any";
        TriviaData[19][4] = "Tortus";       
        TriviaData[20][4] = "She's quick to get angry";   
        TriviaData[21][4] = "Stop On Inn";
        TriviaData[22][4] = "Goron";
        TriviaData[23][4] = "Two";
        TriviaData[24][4] = "Seven";
        TriviaData[25][4] = "Goron";
        TriviaData[26][4] = "Two";
        TriviaData[27][4] = "Milk Bar";
        TriviaData[28][4] = "Four Stories";
        TriviaData[29][4] = "Kookoo-Tingle-Rama!";       


        //C
        TriviaData[0][5] = "35";   
        TriviaData[1][5] = "Six";
        TriviaData[2][5] = "She doesn't use one";
        TriviaData[3][5] = "Dotour";
        TriviaData[4][5] = "Carnival of Masks";
        TriviaData[5][5] = "Ocarina";
        TriviaData[6][5] = "She doesn't sleep";
        TriviaData[7][5] = "Ten";
        TriviaData[8][5] = "Grasshopper";
        TriviaData[9][5] = "Jim";  
        TriviaData[10][5] = "Mommy";   
        TriviaData[11][5] = "Ruto";
        TriviaData[12][5] = "Six";
        TriviaData[13][5] = "Ambidextrous";
        TriviaData[14][5] = "Song of the Field";
        TriviaData[15][5] = "Cooking";
        TriviaData[16][5] = "Four";
        TriviaData[17][5] = "Chateau Moroni";
        TriviaData[18][5] = "Red";
        TriviaData[19][5] = "Tetral";       
        TriviaData[20][5] = "She's quick to break into tears";   
        TriviaData[21][5] = "Stock Pot Inn";
        TriviaData[22][5] = "Zora";
        TriviaData[23][5] = "There are none";
        TriviaData[24][5] = "She never gets up";
        TriviaData[25][5] = "Zora";
        TriviaData[26][5] = "She doesn't use balloons";
        TriviaData[27][5] = "Stock Pot Inn";
        TriviaData[28][5] = "Six Stories";
        TriviaData[29][5] = "Kooloo-Limpah!";       
     
 
        // Sets question text and indicator so that we know the question has been displayed
        function setQuestion() {
            selectionValid = false; // Flag to make sure question has not been asked yet
            while (selectionValid == false) {
                currentQuestion = Math.floor(Math.random() * 30); // randomly select starting question
                if (TriviaData[currentQuestion][2] == "no") {
                    selectionValid = true;
                }
            }
            document.getElementById("TriviaQuestion").innerHTML = TriviaData[currentQuestion][0];
            document.getElementById("atext").innerHTML = TriviaData[currentQuestion][3];
            document.getElementById("btext").innerHTML = TriviaData[currentQuestion][4];
            document.getElementById("ctext").innerHTML = TriviaData[currentQuestion][5];
            TriviaData[currentQuestion][2] = "yes";
            questionsAsked += 1;
        }
        setQuestion();
 
        function processAnswer(myAnswer) {
            if (TriviaData[currentQuestion][1] == myAnswer) {
                // answer correct
                totalCorrect = totalCorrect + 1;
                correctOrNot = "Correct!"
            }
            else {
                correctOrNot = "Wrong!"
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
                document.getElementById("info").innerHTML = correctOrNot;
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
