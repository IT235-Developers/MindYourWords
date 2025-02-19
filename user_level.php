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
                session_start();

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

                $sqlCategoryLevelName = "SELECT c.categoryName, l.levelName FROM category c
                                        JOIN level l ON c.categoryID = l.categoryID 
                                        WHERE c.categoryID = $categoryID AND l.levelID = $levelID";

                $resCategoryLevelName = $con->query($sqlCategoryLevelName);

                if ($resCategoryLevelName->num_rows > 0) {
                    $row = $resCategoryLevelName->fetch_assoc();
                    echo "<h3 class='category_header'>" . $row['categoryName'] . " - " . $row['levelName'] . "</h3>";
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

                echo "<script>const questions = " . json_encode($questions) . ";</script>";
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

            <form method="POST">
                <a class="btn delete mt-3 float-start" href="user_category.php" id="btn_cancel">Cancel</a>
            </form>
        </div>

        <script type="text/javascript">
            let currentQuestionIndex = 0;
            let selectedVoice = null;
            let attempts = 0;
            let score = 0; // Initialize score

            const submitButton = document.getElementById("submitButton");
            const btn_cancel = document.getElementById("btn_cancel");

            function cancelOngoingSpeech(){
                window.speechSynthesis.cancel();
            }

            // Load voices and set the selectedVoice
            function initializeVoices() {
                return new Promise((resolve) => {
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
                            selectedVoice = voices.find(voice => voice.name === "Google US English") ||
                                            voices.find(voice => voice.lang === "en-US" && voice.name.toLowerCase().includes("female")) ||
                                            voices.find(voice => voice.lang === "en-US") ||
                                            voices[0];
                            resolve();
                        };
                    }
                });
            }

            // Function to load the current question
            function loadQuestion(index) {
                if (index < questions.length) {
                    document.getElementById("definition").innerHTML = `
                    <h6 style="display: inline;">Definition:</h6>
                    <span>${questions[index].definition}</span>`;
                    document.getElementById("wordButton").setAttribute("data-text", questions[index].word);
                    document.getElementById("sentenceButton").setAttribute("data-text", questions[index].sampleSentence);
                    document.getElementById("userInput").value = ""; // Clear input field
                    document.getElementById("feedback").innerText = ""; // Clear feedback
                    textToSpeech(questions[index].word); // Read the word
                        setTimeout(() => {
                            textToSpeech(questions[index].sampleSentence); // Read the sample sentence
                        }, 2000);

                } else {
                    document.getElementById("question-container").innerHTML = `<h4>All questions completed!</h4><p>Your total score is: <strong>${score}</strong></p>`;
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

            btn_cancel.addEventListener("click", cancelOngoingSpeech);

            // Event listeners for the word and sentence buttons
            document.getElementById("wordButton").addEventListener("click", function () {
                let word = this.getAttribute("data-text");
                textToSpeech(word);
            });

            document.getElementById("sentenceButton").addEventListener("click", function () {
                let sentence = this.getAttribute("data-text");
                textToSpeech(sentence);
            });

            // Event listener for the "Submit" button
            submitButton.addEventListener("click", () => {
                const userInputField = document.getElementById("userInput");
                const userInput = userInputField.value.trim().toLowerCase();
                const correctWord = questions[currentQuestionIndex].word.toLowerCase();

                userInputField.classList.remove("correct", "incorrect");

                if (userInput === correctWord) {
                    let points = 3 - attempts; // Calculate points based on attempts
                    score += points; // Update score
                    document.getElementById("feedback").innerHTML = `<span class='text-success'>Nicely done! 🎉 You earned ${points} point(s).</span>`;
                    userInputField.classList.add("correct");

                    // Disable the submit button whenever you get the correct answer
                    submitButton.disabled = true;

                    currentQuestionIndex++;
                    attempts = 0; // Reset attempts

                    setTimeout(() => {
                        loadQuestion(currentQuestionIndex);
                        userInputField.classList.remove("correct");

                        //re-enable submit button after successful arrival at the next question
                        submitButton.disabled = false;
                    }, 1500);
                } else {
                    attempts++;
                    if (attempts < 3) {
                        document.getElementById("feedback").innerHTML = `<span class='text-danger'>Try again. You have ${3 - attempts} attempt(s) left.</span>`;
                    } else {
                        document.getElementById("feedback").innerHTML = `<span class='text-danger'>Nice try! The correct spelling is <strong>'${correctWord}'</strong>.</span>`;
                        currentQuestionIndex++;
                        attempts = 0; // Reset attempts

                        // Disable the submit button whenever you get the wrong answer
                        submitButton.disabled = true;

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
            });

            // Initialize the voices and load the first question
            initializeVoices().then(() => {
                loadQuestion(currentQuestionIndex);
            });
        </script>
    </body>
</html>
