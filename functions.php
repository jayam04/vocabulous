<?php
// functions.php
include 'config.php';

function get_words_to_practice($pdo)
{
    $stmt = $pdo->prepare("SELECT * FROM words WHERE datetime(last_learn, '+' || interval || ' days') < datetime('now')");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function update_word_interval($db, $word_id, $correct)
{

    $stmt = $db->query("SELECT interval FROM words2 WHERE id = $word_id");
    $current_interval = $stmt->fetchArray()['interval'];

    $new_interval = $correct ? $current_interval * 2 : max(1, $current_interval / 2);

    $t1 = $correct ? 1 : 0;
    $stmt = $db->exec("UPDATE words2 SET interval = $new_interval WHERE id = $word_id");
    $stmt = $db->exec("UPDATE words2 SET correct = $t1 WHERE id = $word_id");
    $stmt = $db->exec("UPDATE words2 SET last_practice = strftime('%s', 'now') WHERE id = $word_id");

    $stmt = $db->exec("INSERT INTO practice_stats (word_id, correct) VALUES ($word_id, $t1)");
}

function count_total_words($pdo)
{
    $stmt = $pdo->query("SELECT COUNT(*) FROM words2");
    return $stmt->fetchColumn();
}

function count_words_to_practice($pdo)
{
    $stmt = $pdo->query("SELECT COUNT(*) FROM words2 WHERE last_practice + interval * 3600 - strftime('%s', 'now') <= 0");
    return $stmt->fetchColumn();
}

function count_new_words($pdo)
{
    $stmt = $pdo->query("SELECT COUNT(*) FROM words2 WHERE interval IS NULL");
    return $stmt->fetchColumn();
}

function get_new_words($db)
{
    $result = $db->query("SELECT * FROM words2 WHERE interval = null");
    return $result->fetchAll();
}

function group_words_by_kind($words)
{
    $grouped = [];
    foreach ($words as $word) {
        $kind = $word['kind_id'];
        if (!isset($grouped[$kind])) {
            $grouped[$kind] = [];
        }
        $grouped[$kind][] = $word;
    }
    return $grouped;
}

function get_all_kinds($pdo)
{
    $stmt = $pdo->query("SELECT * FROM word_kind2");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_kind_name($pdo, $kind_id)
{
    $stmt = $pdo->prepare("SELECT kind FROM word_kind WHERE id = ?");
    $stmt->execute([$kind_id]);
    return $stmt->fetchColumn();
}

function get_correct_percentage($pdo, $word_id)
{
    $stmt = $pdo->prepare("
        SELECT 100.0 * SUM(correct) / COUNT(*) AS correct_percentage 
        FROM practice_stats 
        WHERE word_id = :word_id
    ");
    $stmt->execute([':word_id' => $word_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Return percentage or 0 if no results
    return $result ? $result['correct_percentage'] : 0;
}

function get_total_by_knowledge($pdo)
{
    $stmt = $pdo->query("
    SELECT
        SUM(CASE 
            WHEN percentage > 80 THEN 1 
            ELSE 0 
        END) AS high,
        
        SUM(CASE 
            WHEN percentage BETWEEN 50 AND 80 THEN 1 
            ELSE 0 
        END) AS mid,
        
        SUM(CASE 
            WHEN percentage < 50 THEN 1 
            ELSE 0 
        END) AS low
    FROM (
        SELECT 
            w.id,
            COALESCE(100.0 * SUM(ps.correct) / COUNT(ps.id), 0) AS percentage
        FROM 
            words2 w
        LEFT JOIN 
            practice_stats ps ON w.id = ps.word_id
        GROUP BY 
            w.id
    ) AS word_stats;
");

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result;
}
