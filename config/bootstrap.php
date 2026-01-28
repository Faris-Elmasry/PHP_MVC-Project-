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
            "%s:host=%s;port=%s;dbname=%s;charset=utf8mb4",
            $driver,
            $config['host'] ?? 'localhost',
            $config['port'] ?? 3306,
            $config['database']
        );
        
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];
        
        // For MySQL 8.0+ caching_sha2_password support
        if ($driver === 'mysql' && defined('PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT')) {
            $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
        }
        
        $pdo = new PDO(
            $dsn,
            $config['username'] ?? 'root',
            $config['password'] ?? '',
            $options
        );
    }
} catch (PDOException $e) {
    die($e->getMessage());
}

return $pdo;
