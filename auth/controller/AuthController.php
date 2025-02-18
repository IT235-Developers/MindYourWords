<?php
// AuthController.php
require_once __DIR__ . '/../model/User.php';
session_start();

class AuthController {
    private $userModel;

    public function __construct($pdo) {
        $this->userModel = new User($pdo);
    }

    public function signup($username, $email, $password) {
        if ($this->userModel->register($username, $email, $password)) {
            header("Location: login.php");  // Redirect to login after successful signup
        } else {
            echo "Signup failed!";
        }
    }

    public function login($email, $password) {
        $user = $this->userModel->login($email, $password);
        if ($user) {
            $_SESSION['user'] = $user;
            header("Location: user_homepage.php");
        } else {
            echo "Login failed!";
        }
    }

    public function logout() {
        session_destroy();
        header("Location: login.php");
    }
}
?>
