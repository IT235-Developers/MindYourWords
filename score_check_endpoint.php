<?php
session_start();
include("connection.php");

$userID = $_SESSION['user']['userID'];

$data = json_decode(file_get_contents('php://input'), true);
$word = $data['word'];

$getLevelHistoryID = "SELECT levelHistoryID FROM level_history WHERE userID = '$userID' 
ORDER BY levelHistoryID DESC LIMIT 1;";

$restLevelHistoryID = $con->query($getLevelHistoryID);

$levelHistoryID = $restLevelHistoryID->fetch_assoc()['levelHistoryID'];

$sqlInsertScoreCheck = "INSERT INTO score_check(levelHistoryID, word) 
                VALUES('$levelHistoryID', '$word')"; 

$resInsertScoreCheck = $con->query($sqlInsertScoreCheck);

$result = $con->query($query);

?>