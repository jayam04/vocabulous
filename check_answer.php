<?php
session_start();
require_once 'config.php';
require_once 'functions.php';
include 'header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $word_id = $_POST['word_id'];
    $kind_id = $_POST['kind_id'];
    $is_skipped = false;
    if ($kind_id === "") {
        $is_skipped  = true;
    }

    // Get the correct kind for the word from the database
    $stmt = $pdo->prepare("SELECT kind, meaning FROM words2 WHERE id = :id");
    $stmt->execute([':id' => $word_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $kind = $row['kind'];
    $meaning = $row['meaning'];

    // Check if word was skipped
    if ($is_skipped) {
        $correct = false;
    } else {
        // Check if the selected kind matches the correct kind
        $correct = ($kind == $kind_id);
    }

    // Update the interval based on the correctness of the answer
    update_word_interval($db, $word_id, $correct);

    // Set the feedback message and background color
    if ($correct) {
        $message = "Correct! You selected the right kind.";
        $bg_color = "lightgreen";
    } else {
        $message = $is_skipped ? "Skipped! This word was marked as incorrect." : "Incorrect! You selected the wrong kind.";
        $bg_color = "lightcoral";
    }
}
?>

<style>
    .result-container {
        margin: 50px auto;
        padding: 20px;
        max-width: 500px;
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        text-align: center;
        background-color: <?php echo $bg_color; ?>;
    }

    .result-container p {
        font-size: 24px;
        font-weight: bold;
        color: #333;
    }

    .button-container {
        margin-top: 20px;
    }

    .button-container a {
        padding: 10px 20px;
        background-color: #007BFF;
        color: white;
        text-decoration: none;
        border-radius: 5px;
    }
</style>

<div class="result-container">
    <p><?php echo $message; ?></p>
    <h1><?php echo $kind; ?></h1>
    <p class="text-md text-gray-600"><?php echo $meaning; ?></p>
    <div class="button-container">
        <a href="practice.php">Practice Another Word</a>
    </div>
</div>

<?php include 'footer.php'; ?>