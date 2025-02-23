<?php
session_start();
include("connection.php");

$userID = $_SESSION['user']['userID'];

$data = json_decode(file_get_contents('php://input'), true);
$levelID = $data['levelID'];
$score = $data['score'];

$sqlUpdateLevelHistory = "UPDATE level_history SET score = '$score' WHERE levelID = '$levelID' AND userID = '$userID'";

$resUpdateLevelHistory = $con->query($sqlUpdateLevelHistory);

?>