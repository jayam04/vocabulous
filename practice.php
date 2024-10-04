<?php
// practice.php
require_once 'config.php';
require_once 'functions.php';
include 'header.php';

// Fetch the word to practice from the database
$result = $db->query("
    SELECT * FROM words2 
    WHERE  interval IS NOT NULL 
    AND (correct = 0 OR last_practice + interval * 3600 - strftime('%s', 'now') <= 0)
    ORDER BY RANDOM() 
    LIMIT 1;
");

// Check if a word is returned by the query
$current_word = $result->fetchArray(SQLITE3_ASSOC);

if (!$current_word) {
    // No words available for practice
    echo "<p>No words to practice right now. Great job!</p>";
} else {
    // Display the word for practice
    ?>

    <div class="bg-white p-8 rounded shadow-md max-w-md mx-auto">
        <h2 class="text-2xl font-bold mb-4">Practice</h2>
        <div class="mb-4">
            <p class="text-xl font-semibold"><?php echo htmlspecialchars($current_word['word']); ?></p>
        </div>
        <form method="post" action="check_answer.php">
            <input type="hidden" name="word_id" value="<?php echo $current_word['id']; ?>">
            <div class="mb-4">
                <label for="kind" class="block text-sm font-medium text-gray-700">Select the kind:</label>
                <select name="kind_id" id="kind"
                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 select2">
                    <option value="">Select a kind</option>
                    <?php
                    // Fetch all kinds
                    $kinds = get_all_kinds($pdo);
                    foreach ($kinds as $kind) {
                        echo "<option value=\"" . $kind['kind'] . "\">" . htmlspecialchars($kind['kind']) . "</option>";
                    }
                    ?>
                </select>

            </div>
            <button type="submit" class="w-full bg-blue-500 text-white px-4 py-2 rounded hover:bg-gradient-to-r from-blue-600 to-yellow-600">Submit</button>
        </form>
    </div>

    <?php
}
include 'footer.php';
?>