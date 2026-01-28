<?php

$config = require __DIR__ . '/database.php';

try {
    $driver = $config['driver'] ?? 'sqlite';
    
    if ($driver === 'sqlite') {
        $dsn = "sqlite:" . $config['database'];
        $pdo = new PDO($dsn);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        // Enable foreign key constraints for SQLite
        $pdo->exec('PRAGMA foreign_keys = ON;');
    } else {
        // MySQL/PostgreSQL support
        $dsn = sprintf(
            "%s:host=%s;port=%s;dbname=%s",
            $driver,
            $config['host'] ?? 'localhost',
            $config['port'] ?? 3306,
            $config['database']
        );
        $pdo = new PDO(
            $dsn,
            $config['username'] ?? 'root',
            $config['password'] ?? ''
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    die($e->getMessage());
}

return $pdo;
