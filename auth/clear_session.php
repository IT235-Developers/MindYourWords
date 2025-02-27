<?php
require_once 'controller/AuthController.php';

$auth = new AuthController($pdo);
$auth->clearUserInformation();

echo json_encode(['status' => 'success']);
?>
