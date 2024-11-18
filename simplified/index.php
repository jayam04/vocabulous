<?php
// index.php
require_once 'config.php';
require_once 'database/init.php';
include 'navigation.php';

$db = new Database();
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

    <table>
    <tr><td>Active Words</td><td><?= $db->getNumberOfActiveWords() ?></td></tr>
    <tr><td>Inactive Words</td><td><?= $db->getNumberOfInactiveWords() ?></td></tr>
    <tr><td>Total Words</td><td><?= $db->getNumberOfActiveWords() + $db->getNumberOfInactiveWords() ?></td></tr>
    </table>
</body>

</html>