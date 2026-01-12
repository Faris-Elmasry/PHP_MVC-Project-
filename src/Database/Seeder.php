<?php

namespace Elmasry\Database;

abstract class Seeder
{
    /**
     * Run the database seeder
     */
    abstract public function run(): void;

    /**
     * Call another seeder
     */
    protected function call(string|array $classes): void
    {
        $classes = is_array($classes) ? $classes : [$classes];

        foreach ($classes as $class) {
            $seeder = new $class();
            
            echo "Seeding: {$class}\n";
            $seeder->run();
            echo "Seeded: {$class}\n";
        }
    }

    /**
     * Insert multiple rows into a table
     */
    protected function insert(string $table, array $rows): void
    {
        if (empty($rows)) {
            return;
        }

        foreach ($rows as $row) {
            $columns = array_keys($row);
            $placeholders = array_fill(0, count($columns), '?');
            
            $sql = sprintf(
                "INSERT INTO %s (%s) VALUES (%s)",
                $table,
                implode(', ', $columns),
                implode(', ', $placeholders)
            );
            
            Database::execute($sql, array_values($row));
        }
    }

    /**
     * Truncate a table (delete all rows)
     */
    protected function truncate(string $table): void
    {
        Database::statement("DELETE FROM {$table}");
        // Reset auto-increment for SQLite
        Database::statement("DELETE FROM sqlite_sequence WHERE name='{$table}'");
    }
}
