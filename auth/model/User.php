<?php
// User.php
require_once __DIR__ . '/../database_connection/database.php';
require_once __DIR__ . '/../../components/flash_message.php';

class User {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function register($username, $email, $password, $cpassword) {
        // Validate that all inputs are provided
        if (empty($username) || empty($email) || empty($password) || empty($cpassword)) {
            setFlashMessage("danger", "All fields are required");
            return false;
        }

        // Validate that the email is in the correct format and not too long
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 254) {
            setFlashMessage("danger", "Invalid email format or email is too long");
            return false;
        }

        // Validate username format and length (3-20 characters, alphanumeric and underscores only)
        if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
            setFlashMessage("danger", "Username must be 3-20 characters long and contain only letters, numbers, and underscores");
            return false;
        }

        // Validate that the password and confirmation password match
        if ($password !== $cpassword) {
            setFlashMessage("danger", "Password does not match");
            return false;
        }

        // Validate password strength
        if (strlen($password) < 8) {
            setFlashMessage("danger", "Password must be at least 8 characters long");
            return false;
        }
        if (!preg_match('/[A-Z]/', $password)) {
            setFlashMessage("danger", "Password must contain at least one uppercase letter");
            return false;
        }
        if (!preg_match('/[a-z]/', $password)) {
            setFlashMessage("danger", "Password must contain at least one lowercase letter");
            return false;
        }
        if (!preg_match('/[0-9]/', $password)) {
            setFlashMessage("danger", "Password must contain at least one number");
            return false;
        }
        if (!preg_match('/[\W]/', $password)) {
            setFlashMessage("danger", "Password must contain at least one special character");
            return false;
        }

        try {
            // Check if username or email already exists since email and username must be unique
            // to all users
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            $count = $stmt->fetchColumn();

            if ($count > 0) {
                // Username or email already exists
                return false;
            }

            // Begin transaction
            $this->pdo->beginTransaction();

            // Insert new user
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$username, $email, $hashedPassword]);

            // Commit transaction
            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            // Rollback transaction on error whenever there is a problem in inserting data after the transaction
            $this->pdo->rollBack();
            return false;
        }
    }

    public function login($email, $password) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                return $user;  // Return user data on successful login
            }
        } catch (PDOException $e) {
            return false;  // Login failed
        }
    }
}
?>
