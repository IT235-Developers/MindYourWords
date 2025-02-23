<?php
session_start();
include("connection.php");

$data = json_decode(file_get_contents('php://input'), true);
$answer = $data['answer'];
$points = $data['points'];

$getScoreCheckID = "SELECT scoreCheckID FROM score_check ORDER BY scoreCheckID DESC LIMIT 1;";

$restgetScoreCheckID = $con->query($getScoreCheckID)->fetch_assoc()['scoreCheckID'];

$answer1 = isset($attempts[0]) ? $con->real_escape_string($attempts[0]) : '';
$answer2 = isset($attempts[1]) ? $con->real_escape_string($attempts[1]) : '';
$answer3 = isset($attempts[2]) ? $con->real_escape_string($attempts[2]) : '';

$sqlInsertAnswer = "INSERT INTO answer(scoreCheckID, answer1, answer2, answer3, points) 
VALUES('$restgetScoreCheckID', '$answer1', '$answer2', '$answer3', '$points')";

$resInsertAnswer = $con->query($sqlInsertAnswer);

?>