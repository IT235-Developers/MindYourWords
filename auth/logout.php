<?php
require_once 'controller/AuthController.php';
require_once 'database_connection/database.php'; // Ensure you have the database connection

$authController = new AuthController($pdo);
$authController->logout();
?>
