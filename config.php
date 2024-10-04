<?php
// config.php
$db_path = __DIR__ . '/words.db';
$pdo = new PDO('sqlite:' . $db_path);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$db = new SQLite3($db_path);
$db->exec('PRAGMA journal_mode = wal;');
