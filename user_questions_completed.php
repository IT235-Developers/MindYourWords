<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MindYourWords Completed</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="styles/admin_styles.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    
    <div class="container-fluid main_container">
        <div class="row">
            <img src="images/myw-secondary-logo.svg" class="secondary_logo">

            <h1 class="fs-1 text-center text-success">Congratulations!</h1>

            <?php
                session_start();
                include("connection.php");

                $userID = $_SESSION['user']['userID'];
                $levelID = $_SESSION['levelID'];
                $levelHistoryID = $_SESSION['levelHistoryID'];
                $categoryID = $_SESSION['categoryID'];

                $sqlCategoryLevelName = "SELECT c.categoryName, l.levelName FROM category c
                                        JOIN level l ON c.categoryID = l.categoryID 
                                        WHERE c.categoryID = $categoryID AND l.levelID = $levelID";

                //get the total questions per round based on the number of words inserted in score_check table
                //to prevent conflict when modifying the questions in the admin side
                $getTotalQuestions = "SELECT COUNT(*) AS wordCount FROM score_check WHERE levelHistoryID = $levelHistoryID";

                $resCategoryLevelName = $con->query($sqlCategoryLevelName);
                $resQuestions = $con->query($getTotalQuestions);

                if ($resCategoryLevelName->num_rows > 0) {
                    $row = $resCategoryLevelName->fetch_assoc();

                    echo "<h2 class='fs-3 questions_completed_header text-center'>" . $row['categoryName'] . " - " . 
                    $row['levelName'] . "</h2>";

                    if($resQuestions->num_rows > 0){
                        $accuracy = 0;
                        $questionsCount = (int) $resQuestions->fetch_assoc()['wordCount'];
                        $maximumPoints = $questionsCount *= 3;

                        $getCurrentLevelScore = "SELECT score FROM level_history WHERE levelID = '$levelID'
                        AND userID = '$userID';";

                        $resGetCurrentLevelScore = $con->query($getCurrentLevelScore);

                        if($resGetCurrentLevelScore->num_rows > 0){
                            $score = (int) $resGetCurrentLevelScore->fetch_assoc()['score'];
                            $accuracy = round($score / $maximumPoints * 100);
                        }

                        else{
                            echo "Something went wrong!";
                        }

                        echo "<h2 class='fw-bold mt-3 mb-5 fs-3 questions_completed_header' style='text-align: center;'>". $accuracy ."% Accuracy</h2>";
                    }

                }

                else{
                    //This is subject to change in the future
                    echo "Failed to fetch category name and level name";
                }
            ?>

            <table>
                <tr>
                    <th class="text-center">#</th>
                    <th class="text-center">Word</th>
                    <th class="text-center">User Spelling</th>
                    <th class="text-center">Points</th>
                </tr>
                <?php
                    include("connection.php");

                    $sqlGetAnswerWord = "SELECT answer1, answer2, answer3, points, word FROM answer AS a 
                    INNER JOIN score_check AS sc ON a.scoreCheckID = sc.scoreCheckID AND 
                    sc.levelHistoryID = '$levelHistoryID';";

                    $resGetAnswerWord = $con->query($sqlGetAnswerWord);
                    $wordNumber = 1;

                    if($resGetAnswerWord->num_rows > 0){
                        $correctIcon = "<i class='bi bi-check text-white fs-3'></i>";
                        $wrongIcon = "<i class='bi bi-x text-white fs-3'></i>";

                        while($row = $resGetAnswerWord->fetch_assoc()){
                            $answer1 = "";
                            if (!empty(trim($row["answer1"]))) {
                                $answer1 = (strtolower($row["word"]) == strtolower($row["answer1"]))
                                    ? "<div class='d-flex justify-content-center align-items-center bg-success rounded 
                                    text-light p-1 text-center'>" . $correctIcon . "" . strtolower($row["answer1"]) . "</div>"
                                    : "<div class='bg-danger rounded text-light d-flex justify-content-center align-items-center 
                                    p-1 text-center'>" . $wrongIcon . "" . strtolower($row["answer1"]) . "</div>";
                            }
                            
                            $answer2 = "";
                            if (!empty(trim($row["answer2"]))) {
                                $answer2 = (strtolower($row["word"]) == strtolower($row["answer2"]))
                                    ? "<div class='d-flex justify-content-center align-items-center bg-success rounded 
                                    text-light p-1 text-center'>" . $correctIcon . "" . strtolower($row["answer2"]) . "</div>"
                                    : "<div class='bg-danger rounded text-light d-flex justify-content-center align-items-center 
                                    p-1 text-center'>" . $wrongIcon . "" . strtolower($row["answer2"]) . "</div>";
                            }
                            
                            $answer3 = "";
                            if (!empty(trim($row["answer3"]))) {
                                $answer3 = (strtolower($row["word"]) == strtolower($row["answer3"]))
                                    ? "<div class='d-flex justify-content-center align-items-center bg-success rounded 
                                    text-light p-1 text-center'>" . $correctIcon . "" . strtolower($row["answer3"]) . "</div>"
                                    : "<div class='bg-danger rounded text-light d-flex justify-content-center align-items-center 
                                    p-1 text-center'>" . $wrongIcon . "" . strtolower($row["answer3"]) . "</div>";
                            }

                            echo "
                                <tr>
                                    <td class='text-center'>" .$wordNumber. "</td>
                                    <td class='text-center'>". strtolower($row["word"]) ."</td>
                                    <td>
                                        <div>".$answer1."</div>
                                        <div class='mt-1'>".$answer2."</div>
                                        <div class='mt-1'>".$answer3."</div>
                                    </td>
                                    <td class='text-center'>". ($row['points'] > 0 ? "<span class='text-success'>".$row['points']." pts</span>" : "<span class='text-danger'>0 pts</span>") ."</td>
                                </tr>
                            ";

                            $wordNumber++;
                        }
                    }

                    else{
                        echo 'No records found!';
                    }
                ?>
            </table>
        </div>
        <a class="btn back float-end mt-3 me-2" href="user_homepage.php">Back</a>
    </div>

</body>
</html>
