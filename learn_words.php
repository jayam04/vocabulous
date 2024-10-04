<?php
// learn_words.php
require_once 'config.php';
require_once 'functions.php';
include 'header.php';

// Fetch all words where the interval is null and order by kind
$result = $db->query("SELECT * FROM words2 WHERE `interval` IS NULL ORDER BY kind");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update the interval for the selected word
    $selected_word_id = $_POST['word_id'];
    $stmt = $pdo->prepare("UPDATE words2 SET `interval` = :interval WHERE id = :id");
    $stmt->execute([':interval' => 2, ':id' => $selected_word_id]);
}
?>

<h1 class="text-3xl font-bold mb-4">Learn New Words</h1>

<!-- Loop through the result set and display words grouped by kind -->
<div class="words-container">
    <?php
    // Initialize a variable to track the current kind
    $current_kind = null;

    // Loop through the result set
    while ($word = $result->fetchArray(SQLITE3_ASSOC)) {
        // Check if the current word's kind is different from the last one
        if ($current_kind !== $word['kind']) {
            // Update the current kind and display the kind name as a heading
            $current_kind = $word['kind'];
            echo '<h2 class="kind-heading">' . htmlspecialchars($current_kind) . '</h2>';
        }

        // Display each word, its meaning, and the square button with a tick symbol
        ?>
        <div class="word-item">
            <form method="post" action="learn_words.php" class="inline-block">
                <input type="hidden" name="word_id" value="<?php echo $word['id']; ?>">
                <button type="submit" class="tick-button">&#10003;</button>
            </form>
            <span class="word-meaning">
                <p class="font-bold"><?php echo htmlspecialchars($word['word']) ?> </p>
                <p class="text-sm"><?php echo $word['meaning']; ?></p>
            </span>
        </div>
        <?php
    }
    ?>
</div>

<?php include 'footer.php'; ?>