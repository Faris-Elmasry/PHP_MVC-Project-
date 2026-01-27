<?php

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

try {
    $host = $_ENV['DB_HOST'] ?? '127.0.0.1';
    $port = $_ENV['DB_PORT'] ?? 3306;
    $user = $_ENV['DB_USERNAME'] ?? 'root';
    $pass = $_ENV['DB_PASSWORD'] ?? '';
    $dbname = $_ENV['DB_DATABASE'] ?? 'mvc_db';

    echo "Attempting connection to MySQL server at $host:$port as user '$user'...\n";

    // 1. Test Credentials (No DB)
    $dsn = "mysql:host=$host;port=$port";
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "✔ Credentials CORRECT!\n";

    // 2. Test Database Existence
    echo "Checking if database '$dbname' exists...\n";
    $stmt = $pdo->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$dbname'");
    if ($stmt->fetch()) {
        echo "✔ Database '$dbname' exists.\n";
    } else {
        echo "✘ Database '$dbname' DOES NOT EXIST.\n";
        echo "Attempting to create database '$dbname'...\n";
        try {
            $pdo->exec("CREATE DATABASE `$dbname`");
            echo "✔ Database '$dbname' created successfully.\n";
        } catch (Exception $e) {
            echo "✘ Failed to create database: " . $e->getMessage() . "\n";
        }
    }

} catch (PDOException $e) {
    echo "✘ Connection Failed: " . $e->getMessage() . "\n";
    if ($e->getCode() == 1045) {
        echo "   -> Access Denied. Please double-check DB_USERNAME and DB_PASSWORD in .env\n";
    }
}
