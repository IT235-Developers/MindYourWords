<?php
require_once __DIR__ . '/model/Stats.php';
require_once __DIR__ . '/database_connection/database.php';

$data = json_decode(file_get_contents('php://input'), true);

$userID = $data['userID'];
$levelID = $data['levelID'];
$results = $data['results'];

$stats = new Stats($pdo);
$stats->insertStats($userID, $levelID, $results);

echo json_encode(['status' => 'success']);
?>
