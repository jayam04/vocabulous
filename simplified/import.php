<?php
require 'database/init.php'
    ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import CSV - Vocabulary Learning App</title>
</head>

<body>
    <?php require_once 'navigation.php'; ?>

    <div class="container">
        <div class="card">
            <h2>Import Words from CSV</h2>
            <form method="post" enctype="multipart/form-data">
                <div>
                    <input type="file" name="csv_file" accept=".csv" required>
                </div>
                <button type="submit" class="btn">Import Words</button>
            </form>

            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {
                    $db = new Database();
                    $db->importCSV($_FILES['csv_file']['tmp_name']);
                    echo "<p>Words imported successfully!</p>";
                }
            }
            ?>
        </div>
    </div>
</body>

</html>