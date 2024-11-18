<?php
require 'database/init.php';
include 'navigation.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Words - Vocabulary Learning App</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <?php require_once 'navigation.php'; ?>

    <div class="container">
        <div class="card">
            <h2>Available Words</h2>
            <form method="post" action="update_words.php">
                <div class="word-list">
                    <?php
                    require_once 'database/init.php';
                    $db = new Database();
                    $result = $db->getInactiveWords();

                    while ($row = $result->fetchArray(SQLITE3_ASSOC)):
                        ?>
                        <div class="word-item">
                            <input type="checkbox" name="words[]" value="<?= $row['id'] ?>" id="word-<?= $row['id'] ?>">
                            <label for="word-<?= $row['id'] ?>">
                                <strong><?= htmlspecialchars($row['word']) ?></strong> -
                                <em><?= htmlspecialchars($row['kind']) ?></em> -
                                <?= htmlspecialchars($row['meaning']) ?>
                            </label>
                        </div>
                    <?php endwhile; ?>
                </div>

                <button type="submit" class="btn">Add Selected to Practice</button>
            </form>
        </div>
    </div>
</body>

</html>