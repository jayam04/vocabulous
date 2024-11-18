<?php
require_once 'config.php';

class Database
{
    private $db;

    public function __construct()
    {
        $this->db = new SQLite3(Config::$dbPath);
        $this->createTables();
    }

    private function createTables()
    {
        // Words table
        $this->db->exec('
            CREATE TABLE IF NOT EXISTS words (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                word TEXT NOT NULL,
                kind TEXT NOT NULL,
                meaning TEXT NOT NULL,
                frequency FLOAT DEFAULT 1024.0,
                is_active INTEGER DEFAULT 0
            )
        ');

        // Stats table
        $this->db->exec('
            CREATE TABLE IF NOT EXISTS stats (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                word_id INTEGER,
                correct INTEGER,
                timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (word_id) REFERENCES words(id)
            )
        ');
    }

    public function getRandomActiveWord()
    {
        // Modified to use frequency as direct probability multiplier
        $result = $this->db->query('
            WITH weighted_words AS (
                SELECT 
                    *,
                    (ROW_NUMBER() OVER (ORDER BY RANDOM()) / CAST(frequency AS FLOAT)) as weight_rank
                FROM words 
                WHERE is_active = 1 AND frequency > 1
            )
            SELECT * FROM weighted_words 
            ORDER BY weight_rank 
            LIMIT 1
        ')->fetchArray(SQLITE3_ASSOC);

        return $result;
    }

    public function importCSV($filepath)
    {
        if (($handle = fopen($filepath, "r")) !== FALSE) {
            while (($data = fgetcsv($handle)) !== FALSE) {
                $stmt = $this->db->prepare('
                    INSERT INTO words (word, kind, meaning) 
                    VALUES (:word, :kind, :meaning)
                ');
                $stmt->bindValue(':word', $data[0], SQLITE3_TEXT);
                $stmt->bindValue(':kind', $data[1], SQLITE3_TEXT);
                $stmt->bindValue(':meaning', $data[2], SQLITE3_TEXT);
                $stmt->execute();
            }
            fclose($handle);
        }
    }

    public function getInactiveWords()
    {
        return $this->db->query('
            SELECT * FROM words 
            WHERE is_active = 0
        ');
    }

    public function getNumberOfInactiveWords()
    {
        $result = $this->db->query('
            SELECT COUNT(*) FROM words
            WHERE is_active = 0
        ');
        return $result->fetchArray()[0];
    }
    public function getNumberOfActiveWords()
    {
        $result = $this->db->query('
            SELECT COUNT(*) FROM words
            WHERE is_active = 1
        ');
        return $result->fetchArray()[0];
    }

    public function updateWordStatus($wordId, $status)
    {
        $stmt = $this->db->prepare('
            UPDATE words 
            SET is_active = :status 
            WHERE id = :id
        ');
        $stmt->bindValue(':status', $status, SQLITE3_INTEGER);
        $stmt->bindValue(':id', $wordId, SQLITE3_INTEGER);
        $stmt->execute();
    }

    public function updateWordFrequency($wordId, $correct)
    {
        $multiplier = $correct ? 0.5 : 2.0;
        $stmt = $this->db->prepare('
            UPDATE words 
            SET frequency = frequency * :multiplier 
            WHERE id = :id
        ');
        $stmt->bindValue(':multiplier', $multiplier, SQLITE3_FLOAT);
        $stmt->bindValue(':id', $wordId, SQLITE3_INTEGER);
        $stmt->execute();

        // Record stats
        $stmt = $this->db->prepare('
            INSERT INTO stats (word_id, correct) 
            VALUES (:word_id, :correct)
        ');
        $stmt->bindValue(':word_id', $wordId, SQLITE3_INTEGER);
        $stmt->bindValue(':correct', $correct ? 1 : 0, SQLITE3_INTEGER);
        $stmt->execute();
    }
}
