<?php
// AuthController.php
session_start();
require_once __DIR__ . '/../model/User.php';
require_once __DIR__ . '/../../components/flash_message.php';

class AuthController {
    private $userModel;
    private $user;

    public function __construct($pdo) {
        $this->userModel = new User($pdo);
    }

    public function signup($username, $email, $password, $cpassword) {
        if ($this->userModel->register($username, $email, $password, $cpassword)) {
            setFlashMessage("success", "Account registered successfully");
            header("Location: ../login.php");  // Redirect to login after successful signup
        } else {
            header("Location: ../sign_up.php");
        }
    }

    public function login($email, $password) {
        $this->user = $this->userModel->login($email, $password);
        if ($this->user) {
            $this->setUserSession();

            if ($this->checkIfAdmin()) {
                header("Location: admin_homepage.php");
            } else {
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
    
    public function checkIfAdmin() {
        $role = $_SESSION['user']["roleID"];
        
        if ($role === 1) {
            return true;
        } else if ($role === 2) {
            return false;
        } 
    }

    public function isLoggedIn() {
        if (isset($_SESSION["user"])) {
            return true;
        }
        return false;
    }

    public function setUserSession() {
        $_SESSION['user'] = $this->user;
    }
}
?>
