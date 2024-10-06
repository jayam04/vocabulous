<?php
include 'config.php';

// Fetch word statistics by date
function get_word_stats_by_date($pdo) {
    $stmt = $pdo->query("
        SELECT practice_date, 
           SUM(correct_count) AS correct_count, 
           SUM(incorrect_count) AS incorrect_count, 
           COUNT(*) AS total_count,
           SUM(CASE 
               WHEN (accuracy > 80) THEN 1 
               ELSE 0 
           END) AS high_accuracy,
           SUM(CASE 
               WHEN (accuracy BETWEEN 50 AND 80) THEN 1 
               ELSE 0 
           END) AS medium_accuracy,
           SUM(CASE 
               WHEN (accuracy < 50) THEN 1 
               ELSE 0 
           END) AS low_accuracy
        FROM (
            SELECT word_id, DATE(timestamp) as practice_date, 
                SUM(CASE WHEN correct = 1 THEN 1 ELSE 0 END) * 1.0 / COUNT(*) * 100 AS accuracy, 
                SUM(CASE WHEN correct = 1 THEN 1 ELSE 0 END) AS correct_count, 
                SUM(CASE WHEN correct = 0 THEN 1 ELSE 0 END) AS incorrect_count
            FROM practice_stats
            GROUP BY word_id, practice_date
        ) AS word_stats
        GROUP BY practice_date
        ORDER BY practice_date;
    ");


    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_different_practice_dates($pdo) {
    $stmt = $pdo->query("
        SELECT DISTINCT DATE(timestamp) AS practice_date
        FROM practice_stats
        ORDER BY practice_date;
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_practice_stats_by_date($pdo, $practice_date) {
    $stmt = $pdo->prepare("
            SELECT practice_date, 
           SUM(correct_count) AS correct_count, 
           SUM(incorrect_count) AS incorrect_count, 
           COUNT(*) AS total_count,
           SUM(CASE 
               WHEN (accuracy > 80) THEN 1 
               ELSE 0 
           END) AS high_accuracy,
           SUM(CASE 
               WHEN (accuracy BETWEEN 50 AND 80) THEN 1 
               ELSE 0 
           END) AS medium_accuracy,
           SUM(CASE 
               WHEN (accuracy < 50) THEN 1 
               ELSE 0 
           END) AS low_accuracy
        FROM (
            SELECT word_id, DATE(timestamp) as practice_date,
                    SUM(CASE WHEN correct = 1 THEN 1 ELSE 0 END) * 1.0 / COUNT(*) * 100 AS accuracy, 
                    SUM(CASE WHEN correct = 1 THEN 1 ELSE 0 END) AS correct_count, 
                    SUM(CASE WHEN correct = 0 THEN 1 ELSE 0 END) AS incorrect_count
            FROM practice_stats
            WHERE practice_date <= ?
            GROUP BY word_id
        ) as word_stats
    ");
    $stmt->execute([$practice_date]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function print_results_as_table($results) {
    echo "<table>";
    echo "<tr><th>Word ID</th><th>Correct</th><th>Timestamp</th></tr>";
    foreach ($results as $row) {
        echo "<tr>";
        echo "<td>" . $row['word_id'] . "</td>";
        echo "<td>" . $row['correct'] . "</td>";
        echo "<td>" . $row['timestamp'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

?>