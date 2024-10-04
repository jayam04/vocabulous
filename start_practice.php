<?php
session_start();
require_once 'config.php';
require_once 'functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected_words = $_POST['word_id'];
    $_SESSION['selected_words'] = $selected_words;
    $_SESSION['current_word_index'] = 0;

    header('Location: practice.php');
    exit();
}
?>
