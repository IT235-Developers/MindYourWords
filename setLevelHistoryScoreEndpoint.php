<?php
session_start();
include("auth/auth.php");
include("connection.php");
include("functions.php");

$userID = $_SESSION['user']['userID'];

$data = json_decode(file_get_contents('php://input'), true);
$levelID = $data['levelID'];
$score = $data['score'];

$sqlUpdateLevelHistory = "UPDATE level_history SET score = '$score' WHERE levelID = '$levelID' AND userID = '$userID'";

$resUpdateLevelHistory = $con->query($sqlUpdateLevelHistory);

$getUserStats = getUserStats($con, $userID);

if($getUserStats){
    if($getUserStats->num_rows == 0){
        insertUserStats($con, $userID);
    }

    else{
        updateUserStats($con, $userID);
    }
    
}

else{
    echo "Query for getting user stats failed";
}

?>