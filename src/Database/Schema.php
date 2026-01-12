<?php

namespace Elmasry\Database;

use Closure;

class Schema
{
    /**
     * Create a new table
     */
    public static function create(string $table, Closure $callback): void
    {
        $blueprint = new Blueprint($table);
        $callback($blueprint);
        
        Database::statement($blueprint->toSql());
    }

    /**
     * Drop a table if it exists
     */
    public static function dropIfExists(string $table): void
    {
        Database::statement("DROP TABLE IF EXISTS {$table}");
    }

    /**
     * Drop a table
     */
    public static function drop(string $table): void
    {
        Database::statement("DROP TABLE {$table}");
    }

    /**
     * Check if a table exists
     */
    public static function hasTable(string $table): bool
    {
        $result = Database::select(
            "SELECT name FROM sqlite_master WHERE type='table' AND name=?",
            [$table]
        );
        
        return !empty($result);
    }

    /**
     * Check if a column exists in a table
     */
    public static function hasColumn(string $table, string $column): bool
    {
        $result = Database::select("PRAGMA table_info({$table})");
        
        foreach ($result as $col) {
            if ($col['name'] === $column) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Rename a table
     */
    public static function rename(string $from, string $to): void
    {
        Database::statement("ALTER TABLE {$from} RENAME TO {$to}");
    }

    /**
     * Get all tables in the database
     */
    public static function getTables(): array
    {
        $result = Database::select(
            "SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'"
        );
        
        return array_column($result, 'name');
    }

    /**
     * Get column info for a table
     */
    public static function getColumns(string $table): array
    {
        return Database::select("PRAGMA table_info({$table})");
    }
}
