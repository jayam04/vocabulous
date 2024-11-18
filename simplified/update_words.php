<?php
require_once 'database/init.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['words'])) {
    $db = new Database();
    foreach ($_POST['words'] as $wordId) {
        $db->updateWordStatus($wordId, 1);
    }
    header('Location: words.php');
    exit;
}