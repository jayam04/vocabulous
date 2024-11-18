<?php
require_once 'database/init.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new Database();
    $wordId = $_POST['wordId'];
    $correct = $_POST['correct'] === 'true';
    $db->updateWordFrequency($wordId, $correct);
    header('Location: index.php');
    exit;
}
