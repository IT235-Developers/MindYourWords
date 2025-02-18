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
                <input type="text" id="userInput" class="form-control me-2 mt-1" placeholder="Spell the word here">
                <button id="submitButton" class="btn btn-primary mb-2">Submit</button>
            </div>

            <p id="feedback" class="mt-1"></p>
        </div>

        <form method="POST">
            <a class="btn delete mt-3 float-start" href="user_category.php">Cancel</a>
        </form>
    </div>

    <script type="text/javascript">
        let currentQuestionIndex = 0;
        let selectedVoice = null;

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
                textToSpeech(questions[index].word);
            } else {
                document.getElementById("question-container").innerHTML = "<h4>All questions completed!</h4>";
                document.getElementById("submitButton").style.display = "none";
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
        document.getElementById("submitButton").addEventListener("click", () => {
            const userInput = document.getElementById("userInput").value.trim().toLowerCase();
            const correctWord = questions[currentQuestionIndex].word.toLowerCase();

            if (userInput === correctWord) {
                document.getElementById("feedback").innerHTML = "<span class='text-success'>Correct! 🎉</span>";
                currentQuestionIndex++;
                setTimeout(() => loadQuestion(currentQuestionIndex), 1500); // Load next question after 1.5 seconds
            } else {
                document.getElementById("feedback").innerHTML = "<span class='text-danger'>Incorrect. Try again.</span>";
            }
        });

        // Initialize the voices and load the first question
        initializeVoices().then(() => {
            loadQuestion(currentQuestionIndex);
        });
    </script>
</body>
</html>