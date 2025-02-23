<?php

require_once __DIR__ . '/database_connection/database.php';

class Stats {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->initializeTables();
    }

    private function initializeTables() {
        $queries = [
            "CREATE TABLE IF NOT EXISTS LevelHistory (
                levelHistoryID INT PRIMARY KEY AUTO_INCREMENT,
                userID INT,
                levelID INT,
                FOREIGN KEY (userID) REFERENCES Users(userID),
                FOREIGN KEY (levelID) REFERENCES Levels(levelID)
            )",
            "CREATE TABLE IF NOT EXISTS ScoreCheck (
                scoreCheckID INT PRIMARY KEY AUTO_INCREMENT,
                levelHistoryID INT,
                word VARCHAR(50) NOT NULL,
                score INT,
                FOREIGN KEY (levelHistoryID) REFERENCES LevelHistory(levelHistoryID)
            )",
            "CREATE TABLE IF NOT EXISTS Answer (
                answerID INT PRIMARY KEY AUTO_INCREMENT,
                scoreCheckID INT,
                answer VARCHAR(50) NOT NULL,
                FOREIGN KEY (scoreCheckID) REFERENCES ScoreCheck(scoreCheckID)
            )"
        ];

        foreach ($queries as $query) {
            try {
                $this->pdo->exec($query);
            } catch (PDOException $e) {
                echo "Error creating table: " . $e->getMessage();
            }
        }
    }
}

?>
