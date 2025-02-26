<?php
// User.php
require_once __DIR__ . '/../database_connection/database.php';
require_once __DIR__ . '/../../components/flash_message.php';

class User {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function register($username, $email, $password) {
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
