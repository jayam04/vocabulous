<?php
// add_kind.php
require_once 'config.php';
require_once 'functions.php';
include 'header.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kind = $_POST['kind'];

    // Check if the kind is not empty
    if (!empty($kind)) {
        // Use a prepared statement to insert the kind
        $stmt = $db->prepare("INSERT INTO word_kind2 (kind) VALUES (:kind)");
        $stmt->bindValue(':kind', $kind, SQLITE3_TEXT);
        
        // Execute the statement and check if it succeeded
        if ($stmt->execute()) {
            $message = "<p class='success-message'>Word kind '{$kind}' added successfully!</p>";
        } else {
            $message = "<p class='error-message'>Failed to add word kind.</p>";
        }
    } else {
        $message = "<p class='error-message'>Please enter a word kind.</p>";
    }
}

// Fetch existing kinds
$stmt = "SELECT kind FROM word_kind2 ORDER BY kind";
$existing_kinds = $db->query($stmt);

?>

<h1 class="text-3xl font-bold mb-4">Word Kinds</h1>

<?php echo $message; ?>

<div class="bg-white p-6 rounded shadow-md mb-6">
    <h2 class="text-xl font-semibold mb-4">Add New Word Kind</h2>
    <form method="post" class="space-y-4">
        <div>
            <label for="kind" class="block text-sm font-medium text-gray-700">New Word Kind:</label>
            <input type="text" id="kind" name="kind" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
        </div>
        <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">Add Kind</button>
    </form>
</div>

<div class="bg-white p-6 rounded shadow-md">
    <h2 class="text-xl font-semibold mb-4">Existing Word Kinds</h2>
    <?php if (empty($existing_kinds)): ?>
        <p>No word kinds have been added yet.</p>
    <?php else: ?>
        <ul class="list-disc pl-5">
            <?php while ($kind = $existing_kinds->fetchArray(PDO::FETCH_ASSOC)) { ?>
                <li><?php echo htmlspecialchars($kind[0]); ?></li>
            <?php }; ?>
        </ul>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>