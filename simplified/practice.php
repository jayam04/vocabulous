<?php
// practice.php
require_once 'config.php';
require_once 'database/init.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Head content remains the same -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vocabulary Learning App</title>

</head>

<body>
    <?php require_once 'navigation.php'; ?>

    <div class="container">
        <?php
        $db = new Database();
        // Modified to use getRandomActiveWord() instead of direct query
        $word = $db->getRandomActiveWord();

        if ($word):
            ?>
            <div class="card" id="flashcard">
                <div class="word"><?= htmlspecialchars($word['word']) ?></div>
                <div class="meaning hidden" id="meaning">
                    <p><strong>Type:</strong> <?= htmlspecialchars($word['kind']) ?></p>
                    <p><strong>Definition:</strong> <?= htmlspecialchars($word['meaning']) ?></p>
                </div>
                <div class="actions">
                    <button class="btn" id="reveal-btn" onclick="revealMeaning()">Reveal <img class="button-img" src="./img/letter-r.png"/></button>
                    <div class="response-buttons hidden" id="response-buttons">
                        <button class="btn btn-correct" onclick="submitResponse(<?= $word['id'] ?>, true)">Correct <img class="button-img" src="./img/letter-c.png"/></button>
                        <button class="btn btn-incorrect"
                            onclick="submitResponse(<?= $word['id'] ?>, false)">Incorrect <img class="button-img" src="./img/letter-i.png"/></button>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="card">
                <div class="no-words">
                    <h2>No active words found</h2>
                    <p>Please add some words to practice from the Manage Words page.</p>
                    <p style="margin-top: 1rem;">
                        <a href="words.php" class="btn">Go to Manage Words</a>
                    </p>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function revealMeaning() {
            document.getElementById('meaning').classList.remove('hidden');
            document.getElementById('response-buttons').classList.remove('hidden');
            document.getElementById('reveal-btn').classList.add('hidden');
        }

        function submitResponse(wordId, correct) {
            fetch('update_progress.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `wordId=${wordId}&correct=${correct}`
            }).then(() => {
                window.location.reload();
            });
        }

        document.addEventListener('keydown', function (event) {
            let wordId = "<?= $word['id'] ?>";
            switch (event.key) {
                case 'ArrowDown':
                case 'r':
                    revealMeaning();
                    break;
                case 'ArrowLeft':
                case 'c':
                    submitResponse(wordId, true);
                    break;
                case 'ArrowRight':
                case 'i':
                    submitResponse(wordId, false);
                    break;
                case 'n':
                    location.reload();
                    break;
                default:
                    break;
            }
        })
    </script>
</body>

</html>