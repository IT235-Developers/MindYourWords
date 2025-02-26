<?php
require_once 'controller/AuthController.php';
require_once 'database_connection/database.php'; // Ensure you have the database connection

$auth = new AuthController($pdo);
$auth->signup($_POST['txt_username'], $_POST['txt_email'], $_POST['txt_password'], $_POST['txt_cpassword']);
?>
