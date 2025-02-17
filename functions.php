<?php function archiveCategoryIfNotExist($categoryID, $con, $con2) {
    $sqlCheckCategoryIfExist = "SELECT * FROM category WHERE categoryID = $categoryID";
    $resCheckCategory = $con2->query($sqlCheckCategoryIfExist);

    // Checks if the category being checked exist in the archive database
    if ($resCheckCategory->num_rows == 0) {
        // Get the row details of the current category
        $sqlGetCurrentCategory = "SELECT * FROM category WHERE categoryID = $categoryID";
        $res = $con->query($sqlGetCurrentCategory);
        $row = $res->fetch_assoc();

        $sqlInsertCategoryToArchive = "INSERT INTO category(categoryID, categoryName) VALUES({$row['categoryID']}, '{$row['categoryName']}')";
        
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

        $sqlInsertLevelToArchive = "INSERT INTO level(levelID, categoryID, levelName) VALUES({$row['levelID']}, {$row['categoryID']}, '{$row['levelName']}')";
        
        if ($con2->query($sqlInsertLevelToArchive) === TRUE) {
            return true;
        } else {
            return false;
        }
    } else {

    }
}

function archiveLevels($categoryID, $con, $con2) {
    $sqlGetAllLevels = "SELECT * FROM level WHERE categoryID = '$categoryID'";
    $resOfAllLevels = $con->query($sqlGetAllLevels);

    if ($resOfAllLevels->num_rows > 0) {
        while ($row = $resOfAllLevels->fetch_assoc()) {
            $sqlInsertLevelIDToArchive = "INSERT INTO level(levelID, categoryID, levelName) VALUES({$row['levelID']}, {$row['categoryID']}, '{$row['levelName']}')";
            $con2->query($sqlInsertLevelIDToArchive);
        }
    }
}

function archiveQuestion($questionID, $con, $con2) {
    $sqlGet = "SELECT * FROM questions WHERE questionID = $questionID";
    $resGet = $con->query($sqlGet);


    if ($resGet->num_rows > 0) {
        $row = $resGet->fetch_assoc();
        $sqlInsert = "INSERT INTO questions (levelID, word, sampleSentence, definition) VALUES ({$row['levelID']}, '{$row['word']}', '{$row['sampleSentence']}', '{$row['definition']}')";
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
            $sqlInsertQuestionsToArchive = "INSERT INTO questions(questionID, levelID, word, sampleSentence, definition) VALUES({$row['questionID']}, {$row['levelID']}, '{$row['word']}', '{$row['sampleSentence']}', '{$row['definition']}')";
            $con2->query($sqlInsertQuestionsToArchive);
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

?>
