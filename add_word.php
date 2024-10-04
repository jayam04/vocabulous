<?php
// add_word.php
require_once 'config.php';
require_once 'functions.php';
include 'header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $word = $_POST['word'];
    $kind_id = $_POST['kind_id'];
    $meaning = $_POST['meaning'];

    $stmt = $pdo->prepare("INSERT INTO words (word, kind_id, meaning) VALUES (?, ?, ?)");
    $stmt->execute([$word, $kind_id, $meaning]);

    echo "<p class='text-green-600 font-semibold'>Word added successfully!</p>";
}

$kinds = get_all_kinds($pdo);
?>

<h1 class="text-3xl font-bold mb-4">Add New Word</h1>
<form method="post" class="bg-white p-6 rounded shadow-md max-w-md mx-auto">
    <div class="mb-4">
        <label for="word" class="block text-sm font-medium text-gray-700">Word:</label>
        <input type="text" id="word" name="word" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
    </div>
    <div class="mb-4">
        <label for="kind" class="block text-sm font-medium text-gray-700">Kind:</label>
        <select name="kind_id" id="kind" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            <?php foreach ($kinds as $kind): ?>
                <option value="<?php echo $kind['kind']; ?>"><?php echo htmlspecialchars($kind['kind']); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="mb-4">
        <label for="meaning" class="block text-sm font-medium text-gray-700">Meaning:</label>
        <textarea id="meaning" name="meaning" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
    </div>
    <button type="submit" class="w-full bg-blue-500 text-white px-4 py-2 rounded hover:bg-gradient-to-r from-blue-600 to-yellow-600">Add Word</button>
</form>

<?php include 'footer.php'; ?>