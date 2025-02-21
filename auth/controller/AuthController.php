<?php
// AuthController.php
session_start();
require_once __DIR__ . '/../model/User.php';
require_once __DIR__ . '/../../components/flash_message.php';

class AuthController {
    private $userModel;

    public function __construct($pdo) {
        $this->userModel = new User($pdo);
    }

    public function signup($username, $email, $password) {
        if ($this->userModel->register($username, $email, $password)) {
            setFlashMessage("success", "Account registered successfully");
            header("Location: login.php");  // Redirect to login after successful signup
        } else {
            setFlashMessage("danger", "Sign-up registration failed. Please ensure that the database tables and initial data are properly set up.");
        }
    }

    public function login($email, $password) {
        $user = $this->userModel->login($email, $password);
        if ($user) {
            $_SESSION['user'] = $user;
            $role = $user["roleID"];
            
            if ($role === 1) {
                header("Location: admin_homepage.php");
            } else if ($role === 2) {
                header("Location: user_homepage.php");
            } 

        } else {
            setFlashMessage("danger", "Invalid email or password. Please try again.");
        }
    }

    public function logout() {
        unset($_SESSION['user']);
        setFlashMessage("success", "You have been logged out successfully.");
        header("Location: ../login.php");
    }
}
?>
