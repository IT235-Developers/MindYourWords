
<?php 

function archiveCategoryIfNotExist($categoryID, $con, $con2) {
    $sqlCheckCategoryIfExist = "SELECT * FROM category WHERE categoryID = $categoryID";
    $resCheckCategory = $con2->query($sqlCheckCategoryIfExist);

    // Checks if the category being checked exist in the archive database
    if ($resCheckCategory->num_rows == 0) {
        // Get the row details of the current category
        $sqlGetCurrentCategory = "SELECT * FROM category WHERE categoryID = $categoryID";
        $res = $con->query($sqlGetCurrentCategory);
        $row = $res->fetch_assoc();
        $categoryName = mysqli_real_escape_string($con, $row['categoryName']);
        $sqlInsertCategoryToArchive = "INSERT INTO category(categoryID, categoryName) VALUES({$row['categoryID']}, '$categoryName')";
        
        // Might be needed later for error handling. For now, this is fine
        if ($con2->query($sqlInsertCategoryToArchive) === TRUE) {
            return true;
        } else {
            return false;
        }
    }
}

function archiveLevel($levelID, $con, $con2) {
    $sqlCheckLevelIfExist = "SELECT * FROM level WHERE levelID = $levelID";
    $resCheckLevel = $con->query($sqlCheckLevelIfExist);

    if ($resCheckLevel->num_rows > 0) {
        $row = $resCheckLevel->fetch_assoc();

        $sqlCheckLevelInArchive = "SELECT * FROM level WHERE levelID = {$row['levelID']}";
        $resCheckLevelInArchive = $con2->query($sqlCheckLevelInArchive);

        if ($resCheckLevelInArchive->num_rows == 0) {
            $levelName = mysqli_real_escape_string($con, $row['levelName']);
            $sqlInsertLevelToArchive = "INSERT INTO level(levelID, categoryID, levelName) VALUES({$row['levelID']}, {$row['categoryID']}, '$levelName')";
            
            if ($con2->query($sqlInsertLevelToArchive) === TRUE) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function archiveLevels($categoryID, $con, $con2) {
    $sqlGetAllLevels = "SELECT * FROM level WHERE categoryID = '$categoryID'";
    $resOfAllLevels = $con->query($sqlGetAllLevels);

    if ($resOfAllLevels->num_rows > 0) {
        while ($row = $resOfAllLevels->fetch_assoc()) {
            $sqlCheckLevelInArchive = "SELECT * FROM level WHERE levelID = {$row['levelID']}";
            $resCheckLevelInArchive = $con2->query($sqlCheckLevelInArchive);

            if ($resCheckLevelInArchive->num_rows == 0) {
                $levelName = mysqli_real_escape_string($con, $row['levelName']);

                $sqlInsertLevelIDToArchive = "INSERT INTO level(levelID, categoryID, levelName) VALUES({$row['levelID']}, {$row['categoryID']}, '$levelName')";
                $con2->query($sqlInsertLevelIDToArchive);
            }
        }
    }
}

function archiveQuestion($questionID, $con, $con2) {
    $sqlGet = "SELECT * FROM questions WHERE questionID = $questionID";
    $resGet = $con->query($sqlGet);


    if ($resGet->num_rows > 0) {
        $row = $resGet->fetch_assoc();

        $word = mysqli_real_escape_string($con, $row['word']);
        $sampleSentence = mysqli_real_escape_string($con, $row['sampleSentence']);
        $definition = mysqli_real_escape_string($con, $row['definition']);
        $sqlInsert = "INSERT INTO questions (levelID, word, sampleSentence, definition) VALUES ({$row['levelID']}, '$word', '$sampleSentence', '$definition')";

        $con2->query($sqlInsert);
    } else {
        setFlashMessage('warning', 'Question not found for deletion.');
    }
}

function archiveQuestionsByLevelId($levelID, $con, $con2) {
    $getQuestions = "SELECT * FROM questions WHERE levelID = $levelID";
    $resQuestions = $con->query($getQuestions);

    if ($resQuestions->num_rows > 0) {
        while ($row = $resQuestions->fetch_assoc()) {
            $word = mysqli_real_escape_string($con2, $row['word']);
            $sampleSentence = mysqli_real_escape_string($con2, $row['sampleSentence']);
            $definition = mysqli_real_escape_string($con2, $row['definition']);
            $sqlCheckQuestionInArchive = "SELECT * FROM questions WHERE questionID = {$row['questionID']}";
            $resCheckQuestionInArchive = $con2->query($sqlCheckQuestionInArchive);

            if ($resCheckQuestionInArchive->num_rows == 0) {
                $sqlInsertQuestionsToArchive = "INSERT INTO questions(questionID, levelID, word, sampleSentence, definition) VALUES({$row['questionID']}, {$row['levelID']}, '$word', '$sampleSentence', '$definition')";
                $con2->query($sqlInsertQuestionsToArchive);
            }
        }
    }
}

function archiveAllQuestions($categoryID, $con, $con2) {
    $sqlRetrieveLevels = "SELECT levelID FROM level WHERE categoryID = $categoryID";
    $resOfAllLevels = $con->query($sqlRetrieveLevels);

    if ($resOfAllLevels->num_rows > 0) {
        while ($row = $resOfAllLevels->fetch_assoc()) {
            archiveQuestionsByLevelId($row['levelID'], $con, $con2);
        }
    }
}

function deleteLevelFromMainDb($levelID, $con) {
    $deleteLevelsFromMainDb = "DELETE FROM level WHERE levelID = $levelID";
    if ($con->query($deleteLevelsFromMainDb)) {
        setFlashMessage('success', 'Level deleted successfully'); 
    } else {
        setFlashMessage('warning', 'Something went wrong');
    }
}

function deleteCategoryFromMainDb($categoryID, $con) {
    $deleteCategoryFromMainDb = "DELETE FROM category WHERE categoryID = $categoryID";
    if ($con->query($deleteCategoryFromMainDb)) {
        setFlashMessage('success', 'Category deleted successfully'); 
    } else {
        setFlashMessage('warning', 'Something went wrong');
    }
}

function deleteQuestionFromMainDb($questionID, $con) {
    $sqlDelete = "DELETE FROM questions WHERE questionID = '$questionID'";

    if ($con->query($sqlDelete) === TRUE) {
        setFlashMessage('success', 'Question deleted successfully.');
    } else {
        setFlashMessage('danger', 'Failed to delete question.');
    }
}

function getLevelHistoryID($con, $userID, $levelID){
    $getLevelHistoryID = "SELECT levelHistoryID FROM level_history WHERE userID = '$userID' AND
    levelID = '$levelID';";

    $resLevelHistoryID = $con->query($getLevelHistoryID);

    if($resLevelHistoryID){
        if($resLevelHistoryID->num_rows > 0){
            $_SESSION['levelHistoryID'] = $resLevelHistoryID->fetch_assoc()['levelHistoryID'];
        }

        else{
            //replace this with flash message because echoing before header() could cause issues
            echo "Failed to fetch levelHistoryID";
        }
    }

    else{
        //replace this with flash message because echoing before header() could cause issues
        echo "An error occured while executing the query";
    }

}

function getUserStats($con, $userID){
    $sqlGetUserStats = "SELECT * FROM user_stats WHERE userID = '$userID'";

    $resGetUserStats = $con->query($sqlGetUserStats);

    if($resGetUserStats){
        return $resGetUserStats;
    }

    else{
        return false;
    }
}

function getAverageScore($con, $userID){
    $sqlGetAverageScore = "SELECT AVG(score) AS average_score FROM level_history WHERE userID = '$userID'";
    
    $resGetAverageScore = $con->query($sqlGetAverageScore);

    if($resGetAverageScore){
        return $resGetAverageScore;
    }

    else{
        return false;
    }
}

function getHighestScore($con, $userID){
    $sqlGetHighestScore = "SELECT MAX(score) AS highest_score FROM level_history WHERE userID = '$userID'";

    $resGetHighestScore = $con->query($sqlGetHighestScore);

    if($resGetHighestScore){
        return $resGetHighestScore;
    }

    else{
        return false;
    }
}

function getTotalGamesPlayed($con, $userID){
    $sqlGetTotalGamesPlayed = "SELECT COUNT(*) AS total_games_played FROM level_history WHERE userID = '$userID'";

    $resGetTotalGamesPlayed = $con->query($sqlGetTotalGamesPlayed);

    if($resGetTotalGamesPlayed){
        return $resGetTotalGamesPlayed;
    }

    else{
        return false;
    }
}

function getWinningRate($con, $userID){
    $sqlGetWinningRate = "SELECT lh.userID, COUNT(a.answerID) AS total_questions, SUM(a.points) AS total_points,
        (SUM(a.points) / (COUNT(a.answerID) * 3)) * 100 AS winning_rate_percentage FROM answer a
        JOIN score_check sc ON a.scoreCheckID = sc.scoreCheckID
        JOIN level_history lh ON sc.levelHistoryID = lh.levelHistoryID
        WHERE lh.userID = '$userID'
        GROUP BY lh.userID;";

    $resGetWinningRate = $con->query($sqlGetWinningRate);

    if($resGetWinningRate){
        return $resGetWinningRate;
    }

    else{
        return false;
    }
}

function insertUserStats($con, $userID){
    $getAverageScore = getAverageScore($con, $userID); //object or false
    $getHighestScore = getHighestScore($con, $userID); //object or false
    $getTotalGamesPlayed = getTotalGamesPlayed($con, $userID); //object or false
    $getWinningRate = getWinningRate($con, $userID);

    if($getAverageScore && $getHighestScore && $getTotalGamesPlayed && $getWinningRate){
        if($getAverageScore->num_rows > 0 && $getHighestScore->num_rows > 0 && $getTotalGamesPlayed->num_rows > 0 &&
        $getWinningRate->num_rows > 0){
            $averageScore = $getAverageScore->fetch_assoc()['average_score'];
            $highestScore = $getHighestScore->fetch_assoc()['highest_score'];
            $totalGamesPlayed = $getTotalGamesPlayed->fetch_assoc()['total_games_played'];
            $winningRate = $getWinningRate->fetch_assoc()['winning_rate_percentage'];
            
            $sqlInsertUserStats = "INSERT INTO user_stats (userID, averageScore, highestScore, totalGamesPlayed,
            winningRate) VALUES ('$userID', '$averageScore', '$highestScore', '$totalGamesPlayed', '$winningRate')";

            $resInsertUserStats = $con->query($sqlInsertUserStats);

            if(!$resInsertUserStats){
                echo "User stats record insertion failed";
            }
        }

        else{
            echo "No average score, highest score or total games played record found";
        }
    }

    else{
        return "Query for getting the average, highest score, or total games played failed to execute";
    }


}

function updateUserStats($con, $userID){
    $getAverageScore = getAverageScore($con, $userID); //object or false
    $getHighestScore = getHighestScore($con, $userID); //object or false
    $getTotalGamesPlayed = getTotalGamesPlayed($con, $userID); //object or false
    $getWinningRate = getWinningRate($con, $userID);

    if($getAverageScore && $getHighestScore && $getTotalGamesPlayed && $getWinningRate){
        if($getAverageScore->num_rows > 0 && $getHighestScore->num_rows > 0 && $getTotalGamesPlayed->num_rows > 0 &&
        $getWinningRate->num_rows > 0){
            $averageScore = $getAverageScore->fetch_assoc()['average_score'];
            $highestScore = $getHighestScore->fetch_assoc()['highest_score'];
            $totalGamesPlayed = $getTotalGamesPlayed->fetch_assoc()['total_games_played'];
            $winningRate = $getWinningRate->fetch_assoc()['winning_rate_percentage'];
            
            $sqlUpdateUserStats = "UPDATE user_stats
                SET averageScore = '$averageScore',
                    highestScore = '$highestScore',
                    totalGamesPlayed = '$totalGamesPlayed',
                    winningRate = '$winningRate'
                WHERE userID = '$userID'
            ";

            $resUpdateUserStats = $con->query($sqlUpdateUserStats);

            if(!$resInsertUserStats){
                echo "User stats alteration failed";
            }
        }

        else{
            echo "No average score, highest score or total games played record found";
        }
    }

    else{
        return "Query for getting the average, highest score, or total games played failed to execute";
    }
}

function removeStats($con, $userID){
    $sqlGetLevelHistory = "SELECT * FROM level_history WHERE userID = '$userID'";

    $resGetLevelHistory = $con->query($sqlGetLevelHistory);

    if($resGetLevelHistory){
        if($resGetLevelHistory->num_rows == 0){
            $sqlRemoveUserStats = "DELETE FROM user_stats WHERE userID = '$userID'";
            
            //handle error later
            $con->query($sqlRemoveUserStats);
        }
    }

    else{
        echo "Query for getting level history failed to execute";
    }
}

?>
