<?php

namespace Elmasry\Database;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;
    private static array $config = [];

    /**
     * Prevent direct instantiation
     */
    private function __construct() {}

    /**
     * Prevent cloning
     */
    private function __clone() {}

    /**
     * Initialize database configuration
     */
    public static function configure(array $config): void
    {
        self::$config = $config;
    }

    /**
     * Get the PDO instance (singleton)
     */
    public static function connection(): PDO
    {
        if (self::$instance === null) {
            self::$instance = self::createConnection();
        }

        return self::$instance;
    }

    /**
     * Create a new PDO connection
     */
    private static function createConnection(): PDO
    {
        if (empty(self::$config)) {
            self::$config = require __DIR__ . '/../../config/database.php';
        }

        try {
            $driver = self::$config['driver'] ?? 'sqlite';
            
            if ($driver === 'sqlite') {
                $dsn = "sqlite:" . self::$config['database'];
                $pdo = new PDO($dsn);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                $pdo->exec('PRAGMA foreign_keys = ON;');
            } else {
                // MySQL/PostgreSQL support
                $dsn = sprintf(
                    "%s:host=%s;port=%s;dbname=%s",
                    $driver,
                    self::$config['host'] ?? 'localhost',
                    self::$config['port'] ?? 3306,
                    self::$config['database']
                );
                $pdo = new PDO(
                    $dsn,
          
                    self::$config['password'] ?? ''
                );
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            }

            return $pdo;
        } catch (PDOException $e) {
            throw new PDOException("Database connection failed: " . $e->getMessage());
        }
    }

    /**
     * Reset the connection (useful for testing)
     */
    public static function disconnect(): void
    {
        self::$instance = null;
    }

    /**
     * Execute a raw SQL statement
     */
    public static function statement(string $sql): bool
    {
        return self::connection()->exec($sql) !== false;
    }

    /**
     * Run a select query and return results
     */
    public static function select(string $sql, array $bindings = []): array
    {
        $stmt = self::connection()->prepare($sql);
        $stmt->execute($bindings);
        return $stmt->fetchAll();
    }

    /**
     * Run an insert/update/delete and return affected rows
     */
    public static function execute(string $sql, array $bindings = []): int
    {
        $stmt = self::connection()->prepare($sql);
        $stmt->execute($bindings);
        return $stmt->rowCount();
    }

    /**
     * Get the last inserted ID
     */
    public static function lastInsertId(): string
    {
        return self::connection()->lastInsertId();
    }

    /**
     * Begin a transaction
     */
    public static function beginTransaction(): bool
    {
        return self::connection()->beginTransaction();
    }

    /**
     * Commit a transaction
     */
    public static function commit(): bool
    {
        return self::connection()->commit();
    }

    /**
     * Rollback a transaction
     */
    public static function rollback(): bool
    {
        return self::connection()->rollBack();
    }
}
