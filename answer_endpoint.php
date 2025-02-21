<?php
session_start();
include("connection.php");

$data = json_decode(file_get_contents('php://input'), true);
$answer = $data['answer'];

$getScoreCheckID = "SELECT scoreCheckID FROM score_check ORDER BY scoreCheckID DESC LIMIT 1;";

$restgetScoreCheckID = $con->query($getScoreCheckID)->fetch_assoc()['scoreCheckID'];

$sqlInsertAnswer = "INSERT INTO answer(scoreCheckID, answer) 
VALUES('$restgetScoreCheckID', '$answer')";

$resInsertAnswer = $con->query($sqlInsertAnswer);

?>