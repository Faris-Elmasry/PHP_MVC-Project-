<?php

$config = require __DIR__ . '/database.php';

$dsn = "sqlite:" . $config['database'];

try {
    $pdo = new PDO($dsn);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

 // Enable foreign key constraints
    $pdo->exec('PRAGMA foreign_keys = ON;');

} catch (PDOException $e) {
    die($e->getMessage());
}

return $pdo;
