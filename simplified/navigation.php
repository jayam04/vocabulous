<nav class="nav">
    <div class="nav-container">
        <div class="nav-brand"><a href="index.php">Vocabulus - Simplified</a></div>
        <div class="nav-links">
            <a href="practice.php" <?= basename($_SERVER['PHP_SELF']) === 'practice.php' ? 'class="active"' : '' ?>>Practice</a>
            <a href="words.php" <?= basename($_SERVER['PHP_SELF']) === 'words.php' ? 'class="active"' : '' ?>>Manage
                Words</a>
            <a href="import.php" <?= basename($_SERVER['PHP_SELF']) === 'import.php' ? 'class="active"' : '' ?>>Import
                CSV</a>
            <a href="/index.php">Go to default Version</a>
        </div>
    </div>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,200..800&display=swap"
        rel="stylesheet">

    <link href="./styles.css" rel="stylesheet" />
</nav>