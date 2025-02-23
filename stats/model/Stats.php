<?php

require_once __DIR__ . '/../database_connection/database.php';

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
                FOREIGN KEY (userID) REFERENCES users(userID),
                FOREIGN KEY (levelID) REFERENCES level(levelID)
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

    public function insertStats($userID, $levelID, $results) {
        $levelHistoryID = $this->insertLevelHistory($userID, $levelID);
        $scoreCheckID = null;
    
        if (!$levelHistoryID) {
            echo "Failed to insert LevelHistory.";
            return;
        }
    
        // Iterate over the results array and insert each word and score into the ScoreCheck table
        foreach ($results as $result) {
            $word = $result['question'];
            $score = $result['points'];
            $scoreCheckID = $this->insertScoreCheck($levelHistoryID, $word, $score);
            
            if (!$scoreCheckID) {
            echo "Failed to insert ScoreCheck for word: $word.";
            continue;
            }

            $lastAttempt = end($result['attempts']);
            $this->insertAnswer($scoreCheckID, $lastAttempt['spelled_word']);
        }
    }

    private function insertLevelHistory($userID, $levelID) {
        $sql = "INSERT INTO LevelHistory (userID, levelID) VALUES (:userID, :levelID)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
        $stmt->bindParam(':levelID', $levelID, PDO::PARAM_INT);

        try {
            $stmt->execute();
            $levelHistoryID = $this->pdo->lastInsertId();
            return $levelHistoryID;
        } catch (PDOException $e) {
            echo "Error inserting stats: " . $e->getMessage();
            return false;
        }
    }

    private function insertScoreCheck($levelHistoryID, $word, $score) {
        $sql = "INSERT INTO ScoreCheck (levelHistoryID, word, score) VALUES (:levelHistoryID, :word, :score)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':levelHistoryID', $levelHistoryID, PDO::PARAM_INT);
        $stmt->bindParam(':word', $word, PDO::PARAM_STR);
        $stmt->bindParam(':score', $score, PDO::PARAM_INT);

        try {
            $stmt->execute();
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            echo "Error inserting score check: " . $e->getMessage();
            return false;
        }
    }

    private function insertAnswer($scoreCheckID, $answer) {
        $sql = "INSERT INTO Answer (scoreCheckID, answer) VALUES (:scoreCheckID, :answer)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':scoreCheckID', $scoreCheckID, PDO::PARAM_INT);
        $stmt->bindParam(':answer', $answer, PDO::PARAM_STR);

        try {
            $stmt->execute();
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            echo "Error inserting answer: " . $e->getMessage();
            return false;
        }
    }
}

?>
