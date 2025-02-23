<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>MindYourWords User Level</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
        <link rel="stylesheet" type="text/css" href="styles/admin_styles.css">
    </head>
    <body>
        <div class="container-fluid main_container">
            <div class="row">
                <img src="images/myw-secondary-logo.svg" class="secondary_logo">
                <?php
                include("connection.php");
                include("functions.php");
                include("components/flash_message.php");
                session_start();

                $userID = $_SESSION['user']['userID'];

                if (isset($_POST['txt_levelHID']) && isset($_POST['txt_categoryHID'])) {
                    $_SESSION['levelID'] = $_POST['txt_levelHID'];
                    $_SESSION['categoryID'] = $_POST['txt_categoryHID'];
                }

                if (isset($_SESSION['levelID']) && isset($_SESSION['categoryID'])) {
                    $levelID = $_SESSION['levelID'];
                    $categoryID = $_SESSION['categoryID'];
                } else {
                    echo "<p class='text-danger'>Level ID or Category ID is missing.</p>";
                    header("Location: admin_category.php");
                    exit();
                }

                
                $sqlCheckLevelID = "SELECT levelID FROM level_history WHERE levelID = '$levelID' AND userID = '$userID'";
                $sqlCheckLevelQuestions = "SELECT * FROM questions WHERE levelID = '$levelID'";

                $resCheckLevelID = $con->query($sqlCheckLevelID);
                $resCheckLevelQuestions = $con->query($sqlCheckLevelQuestions);

                //check if the levelID already exists in the level_history table
                if($resCheckLevelID->num_rows > 0){
                    getLevelHistoryID($con, $userID, $levelID);
                    header("Location: user_questions_completed.php");
                    exit;
                }

                //check if the current level contain questions
                else if($resCheckLevelQuestions->num_rows == 0){
                    setFlashMessage("danger", "No questions available");
                    header("Location: user_category.php");
                    exit;
                }

            
                //Whenever the user enters a level, create a history for that
                $sqlInsertLevelHistory = "INSERT INTO level_history(userID, levelID, score) 
                VALUES('$userID','$levelID', 0)";

                $resInsertLevelHistory = $con->query($sqlInsertLevelHistory);

                if($resInsertLevelHistory){
                    getLevelHistoryID($con, $userID, $levelID);
                }

                else{
                    echo "failed!";
                }

                $sqlCategoryLevelName = "SELECT c.categoryName, l.levelName FROM category c
                                        JOIN level l ON c.categoryID = l.categoryID 
                                        WHERE c.categoryID = $categoryID AND l.levelID = $levelID";

                $resCategoryLevelName = $con->query($sqlCategoryLevelName);

                if ($resCategoryLevelName->num_rows > 0) {
                    $row = $resCategoryLevelName->fetch_assoc();
                    echo "<h3 class='category_header'>" . $row['categoryName'] . " - " . $row['levelName'] . "</h3>";
                }

                else{
                    //This is subject to change in the future
                    echo "Failed to fetch category name and level name";
                }

                $getQuestions = "SELECT * FROM questions WHERE levelID = $levelID";
                $resQuestions = $con->query($getQuestions);

                $questions = [];
                if ($resQuestions->num_rows > 0) {
                    while ($row = $resQuestions->fetch_assoc()) {
                        $questions[] = [
                            "word" => $row['word'],
                            "sampleSentence" => $row['sampleSentence'],
                            "definition" => $row['definition']
                        ];
                    }
                }

                echo "<script> const questions = " . json_encode($questions) . "; </script>";
                ?>
            </div>

            <div id="question-container" class="mt-4">
                <div class="d-flex align-items-center mb-2">
                    <h6 class="mb-0 me-2">Spell the word:</h6>
                    <button id="wordButton" class="btn_tts me-4"><img src="images/sound.svg"></button>

                    <h6 class="mb-0 me-2">Example Sentence:</h6>
                    <button id="sentenceButton" class="btn_tts"><img src="images/sound.svg"></button>
                </div>

                <p id="definition"></p>

                <div class="d-flex align-items-center mt-3">
                    <input type="text" id="userInput" class="form-control me-2" placeholder="Spell the word here">
                    <button id="submitButton" class="btn btn-primary">Submit</button>
                </div>

                <p id="feedback" class="mt-1"></p>
            </div>

            <form action="user_category.php" method="POST">
                <button type="submit" class="btn delete mt-3 float-start" name="btn_cancel" id="btn_cancel">Cancel</a>
            </form>
        </div>

        <script type="text/javascript">
            let currentQuestionIndex = 0;
            let selectedVoice = null;
            let attempts = 0;
            let score = 0; // Initialize score

            const userInputField = document.getElementById("userInput");
            
            const btn_cancel = document.getElementById("btn_cancel");
            const submitButton = document.getElementById("submitButton");
            const sentenceButton = document.getElementById("sentenceButton");
            const wordButton = document.getElementById("wordButton");
            
            const feedback = document.getElementById("feedback");
            const definition = document.getElementById("definition");
            const question_container = document.getElementById("question-container");

            const correctSound = new Audio("resources/audios/correct_audio.mp3");
            const wrongSound = new Audio("resources/audios/wrong_audio.mp3");
            const questionCompletedSound = new Audio("resources/audios/question_completed.wav");

            let attemptsArray = [];

            function cancelOngoingSpeech(){
                window.speechSynthesis.cancel();
            }

            // Load voices and set the selectedVoice
            function initializeVoices() {
                return new Promise((resolve, reject) => {
                    let voices = window.speechSynthesis.getVoices();
                    if (voices.length > 0) {
                        selectedVoice = voices.find(voice => voice.name === "Google US English") ||
                                        voices.find(voice => voice.lang === "en-US" && voice.name.toLowerCase().includes("female")) ||
                                        voices.find(voice => voice.lang === "en-US") ||
                                        voices[0];
                        resolve();
                    } else {
                        window.speechSynthesis.onvoiceschanged = () => {
                            voices = window.speechSynthesis.getVoices();
                            if(voices.length > 0){
                                selectedVoice = voices.find(voice => voice.name === "Google US English") ||
                                            voices.find(voice => voice.lang === "en-US" && voice.name.toLowerCase().includes("female")) ||
                                            voices.find(voice => voice.lang === "en-US") ||
                                            voices[0];
                                resolve();
                            }

                            else{
                                reject("No voices available");
                            }
                            
                        };
                    }
                });
            }

            // Function to load the current question
            function loadQuestion(index) {
                if (index < questions.length) {
                    definition.innerHTML = `
                    <h6 style="display: inline;">Definition:</h6>
                    <span>${questions[index].definition}</span>`;
                    wordButton.setAttribute("data-text", questions[index].word);
                    sentenceButton.setAttribute("data-text", questions[index].sampleSentence);
                    userInputField.value = ""; // Clear input field
                    feedback.innerText = ""; // Clear feedback

                    const payload = {
                        word: questions[index].word,
                    };

                    // Send the current word to score_check_endpoint.php
                    fetch('score_check_endpoint.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    })
                    .then(response => response.text())
                    .then(result => {
                        console.log('score_check record insertion successfull', result);
                    })
                    .catch(error => console.error('score_check record insertion failed:', error));


                    textToSpeech(questions[index].word); // Read the word
                        setTimeout(() => {
                            textToSpeech(questions[index].sampleSentence); // Read the sample sentence
                        }, 1000);

                } else {
                    const levelID = <?php echo json_encode($levelID); ?>;

                    const payload = {
                        levelID: levelID,
                        score: score,
                    };

                    // Send the current levelID and score to setLevelHistoryScoreEndpoint.php
                    fetch('setLevelHistoryScoreEndpoint.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    })
                    .then(response => response.text())
                    .then(result => {
                        console.log('Level history score updated', result);
                        window.location.href = 'user_questions_completed.php';
                    })
                    .catch(error => console.error('Error updating level history score:', error));
                    
                    questionCompletedSound.play();
                }
            }

            // Function to handle text-to-speech
            function textToSpeech(text) {
                let speech = new SpeechSynthesisUtterance();
                speech.text = text;
                speech.volume = 1;
                speech.rate = 0.8;
                speech.pitch = 1;
                speech.lang = "en-US";
                if (selectedVoice) {
                    speech.voice = selectedVoice;
                }
                window.speechSynthesis.speak(speech);
            }

            //Randomize questions
            function shuffleArray(array) {
                for (let i = array.length - 1; i > 0; i--) {
                    const j = Math.floor(Math.random() * (i + 1));
                    [array[i], array[j]] = [array[j], array[i]];
                }
            }

            //Function to submit answer
            function submitAnswer(){
                const userInput = userInputField.value.trim().toLowerCase();
                const correctWord = questions[currentQuestionIndex].word.toLowerCase();

                // Record the attempt
                attemptsArray.push(userInput === "" ? "(blank)" : userInput);

                console.log(attemptsArray);

                userInputField.classList.remove("correct", "incorrect");

                //reset back the wrongSound
                wrongSound.pause();
                wrongSound.currentTime = 0;

                if (userInput === correctWord) {
                    correctSound.play();
                    cancelOngoingSpeech();

                    let points = 3 - attempts; // Calculate points based on attempts
                    score += points; // Update score
                    feedback.innerHTML = `<span class='text-success'>Nicely done! 🎉 You earned ${points} point(s).</span>`;
                    userInputField.classList.add("correct");

                    // Send the answer to answer_endpoint.php
                    const payload = { 
                        attemptsArray: attemptsArray,
                        points: points
                    };
                    fetch('answer_endpoint.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    })
                    .then(response => response.text())
                    .then(result => console.log('correct answer recorded:', result))
                    .catch(error => console.error('error recording correct answer:', error));

                    // Disable the submit button whenever you get the correct answer
                    submitButton.disabled = true;

                    currentQuestionIndex++;
                    attempts = 0; // Reset attempts
                    attemptsArray = []; // Clear attempts for the next question

                    setTimeout(() => {
                        loadQuestion(currentQuestionIndex);
                        userInputField.classList.remove("correct");

                        //re-enable submit button after successful arrival at the next question
                        submitButton.disabled = false;
                    }, 1500);
                } else {
                    wrongSound.play();
                    attempts++;
                    if (attempts < 3) {
                        feedback.innerHTML = `<span class='text-danger'>Try again. You have ${3 - attempts} attempt(s) left.</span>`;
                    } else {
                        cancelOngoingSpeech();
                        feedback.innerHTML = `<span class='text-danger'>Nice try! The correct spelling is <strong>'${correctWord}'</strong>.</span>`;
                        currentQuestionIndex++;
                        attempts = 0; // Reset attempts

                        // Send the answer to answer_endpoint.php
                        const payload = { 
                            attemptsArray: attemptsArray,
                            points: 0
                        };
                        fetch('answer_endpoint.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify(payload)
                        })
                        .then(response => response.text())
                        .then(result => console.log('wrong nswer recorded:', result))
                        .catch(error => console.error('error recording wrong answer:', error));

                        // Disable the submit button whenever you get the wrong answer
                        submitButton.disabled = true;
                        attemptsArray = []; // Clear attempts for the next question

                        setTimeout(() => {
                            loadQuestion(currentQuestionIndex);

                            //re-enable submit button after successful arrival at the next question
                            submitButton.disabled = false;
                        }, 5000);
                    }
                    userInputField.classList.add("incorrect");
                    setTimeout(() => {
                        userInputField.classList.remove("incorrect");
                    }, 500);
                }
            }

            btn_cancel.addEventListener("click", cancelOngoingSpeech);

            userInput.addEventListener("keydown", function(event) {
                // Check if the pressed key is Enter
                if (event.key === "Enter") {
                    if(!submitButton.disabled){
                        submitAnswer();
                    }
                }
            });

            // Event listeners for the word and sentence buttons
            wordButton.addEventListener("click", function () {
                let word = this.getAttribute("data-text");
                textToSpeech(word);
            });

            sentenceButton.addEventListener("click", function () {
                let sentence = this.getAttribute("data-text");
                textToSpeech(sentence);
            });

            // Event listener for the "Submit" button
            submitButton.addEventListener("click", submitAnswer);

            // Initialize the voices and load the first question
            initializeVoices()
                .then(() => {
                    shuffleArray(questions);
                    loadQuestion(currentQuestionIndex);
                })
                .catch((error) => {
                    //This is subject to change in the future
                    console.error("Error initializing voices:", error);
                });
        </script>
    </body>
</html>
