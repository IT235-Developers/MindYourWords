<?php
    require_once __DIR__ .  "/../components/flash_message.php";

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['user'])) {
        setFlashMessage("danger", "Authentication required: Kindly log in to access this content.");
        header("Location: login.php");
        exit();
    }
?>
