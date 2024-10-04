<?php
include "config.php";
include "header.php";
include "functions.php";
?>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sort_by = $_GET['sort_by'] ?? 'id';
    $descending = $_GET['desc'] ?? 'false';

    if (!in_array($sort_by, ['id', 'word', 'kind', 'meaning', 'last_practice', 'interval', 'correct_percentage'])) {
        $sort_by = 'id';
    }

    if ($descending) {
        $sort_by = $sort_by . ' DESC';
    }
}
?>
<h1>
    Word Status
</h1>

<table style="border: 1px solid black;">
    <thead>
        <th>Word</th>
        <th>Kind</th>
        <th>Meaning</th>
        <th>Last Reviewed</th>
        <th>Interval</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // $result = $db->query("SELECT * FROM words2 ORDER BY $sort_by");
        $result = $db->query("SELECT 
    w.*,
    COALESCE(100.0 * SUM(ps.correct) / COUNT(ps.id), 0) AS correct_percentage
FROM 
    words2 w
LEFT JOIN 
    practice_stats ps ON w.id = ps.word_id
GROUP BY 
    w.kind, w.id
ORDER BY 
    $sort_by;
");
        while ($row = $result->fetchArray()) {
            $last_reviewed = $row['last_practice'];
            $interval = $row['interval'];
            $kind = $row['kind'];
            $meaning = $row['meaning'];
            $word = $row['word'];
            $id = $row['id'];
            $last_reviewed_date = date('Y-m-d', $last_reviewed);
            $interval_days = $interval > 0 ? $interval : 'N/A';
            $correct_percentage = $row['correct_percentage'];
        ?>
            <tr style="border: 1px solid black;">
                <td><?php echo $word; ?></td>
                <td><?php echo $kind; ?></td>
                <td><?php echo $meaning; ?></td>
                <td><?php echo $last_reviewed_date; ?></td>
                <td><?php echo $interval_days; ?></td>
                <td><?php echo $correct_percentage ?></td>
            </tr>
        <?php } ?>
</table>

<?php include "footer.php"; ?>