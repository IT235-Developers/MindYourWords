<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MindYourWords Completed</title>
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

                $getQuestions = "SELECT COUNT(*) AS questionCount FROM questions WHERE levelID = $levelID";

                $resCategoryLevelName = $con->query($sqlCategoryLevelName);
                $resQuestions = $con->query($getQuestions);

                if ($resCategoryLevelName->num_rows > 0) {
                    $row = $resCategoryLevelName->fetch_assoc();

                    echo "<h2 class='fs-3 pt-4 questions_completed_header'>" . $row['categoryName'] . " - " . 
                    $row['levelName'] . "</h2>";

                    if($resQuestions->num_rows > 0){
                        $accuracy = 0;
                        $questionsCount = (int) $resQuestions->fetch_assoc()['questionCount'];
                        $maximumPoints = $questionsCount *= 3;

                        $getCurrentLevelScore = "SELECT score FROM level_history WHERE levelID = '$levelID'
                        AND userID = '$userID';";

                        $resGetCurrentLevelScore = $con->query($getCurrentLevelScore);

                        if($resGetCurrentLevelScore->num_rows > 0){
                            $score = (int) $resGetCurrentLevelScore->fetch_assoc()['score'];
                            $accuracy = round($score / $maximumPoints * 100, 2);
                        }

                        else{
                            echo "Something went wrong!";
                        }

                        echo "<h2 class='fw-bold fs-3 questions_completed_header'>". $accuracy ."% Accuracy</h2>";
                    }

                }

                else{
                    //This is subject to change in the future
                    echo "Failed to fetch category name and level name";
                }
            ?>

            <table>
                <tr>
                    <th>#</th>
                    <th>Word</th>
                    <th>User Spelling</th>
                    <th>Mark</th>
                </tr>
                <?php
                    include("connection.php");

                    $sqlGetAnswerWord = "SELECT answer, word FROM answer AS a INNER JOIN score_check AS sc
                    ON a.scoreCheckID = sc.scoreCheckID AND sc.levelHistoryID = '$levelHistoryID';";

                    $resGetAnswerWord = $con->query($sqlGetAnswerWord);
                    $wordNumber = 1;

                    if($resGetAnswerWord->num_rows > 0){
                        while($row = $resGetAnswerWord->fetch_assoc()){
                            $mark = strtolower($row["word"]) == strtolower($row["answer"]) ? "correct" : "wrong";
                            echo "
                                <tr>
                                    <td>" .$wordNumber. "</td>
                                    <td>". strtolower($row["word"]) ."</td>
                                    <td>". strtolower($row["answer"]) ."</td>
                                    <td>". $mark ."</td>
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