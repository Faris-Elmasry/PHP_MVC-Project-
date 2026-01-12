<?php

namespace Elmasry\Database;

class Migrator
{
    protected string $migrationsPath;
    protected string $migrationsTable = 'migrations';

    public function __construct(?string $migrationsPath = null)
    {
        $this->migrationsPath = $migrationsPath ?? __DIR__ . '/../../App/Migrations';
        $this->ensureMigrationsTableExists();
    }

    /**
     * Create the migrations tracking table if it doesn't exist
     */
    protected function ensureMigrationsTableExists(): void
    {
        Database::statement("
            CREATE TABLE IF NOT EXISTS {$this->migrationsTable} (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                migration TEXT NOT NULL,
                batch INTEGER NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");
    }

    /**
     * Run all pending migrations
     */
    public function migrate(): array
    {
        $files = $this->getMigrationFiles();
        $ran = $this->getRanMigrations();
        $pending = array_diff($files, $ran);

        if (empty($pending)) {
            return ['status' => 'info', 'message' => 'Nothing to migrate.'];
        }

        $batch = $this->getNextBatchNumber();
        $migrated = [];

        foreach ($pending as $file) {
            $this->runMigration($file, 'up');
            $this->recordMigration($file, $batch);
            $migrated[] = $file;
        }

        return [
            'status' => 'success',
            'message' => 'Migrations completed.',
            'migrated' => $migrated
        ];
    }

    /**
     * Rollback the last batch of migrations
     */
    public function rollback(int $steps = 1): array
    {
        $migrations = $this->getMigrationsToRollback($steps);

        if (empty($migrations)) {
            return ['status' => 'info', 'message' => 'Nothing to rollback.'];
        }

        $rolledBack = [];

        foreach ($migrations as $migration) {
            $this->runMigration($migration['migration'], 'down');
            $this->removeMigrationRecord($migration['migration']);
            $rolledBack[] = $migration['migration'];
        }

        return [
            'status' => 'success',
            'message' => 'Rollback completed.',
            'rolled_back' => $rolledBack
        ];
    }

    /**
     * Rollback all migrations and re-run them
     */
    public function refresh(): array
    {
        $this->reset();
        return $this->migrate();
    }

    /**
     * Rollback all migrations
     */
    public function reset(): array
    {
        $migrations = Database::select(
            "SELECT migration FROM {$this->migrationsTable} ORDER BY batch DESC, id DESC"
        );

        if (empty($migrations)) {
            return ['status' => 'info', 'message' => 'Nothing to reset.'];
        }

        $reset = [];

        foreach ($migrations as $migration) {
            $this->runMigration($migration['migration'], 'down');
            $this->removeMigrationRecord($migration['migration']);
            $reset[] = $migration['migration'];
        }

        return [
            'status' => 'success',
            'message' => 'Reset completed.',
            'reset' => $reset
        ];
    }

    /**
     * Get the status of all migrations
     */
    public function status(): array
    {
        $files = $this->getMigrationFiles();
        $ran = $this->getRanMigrations();

        $status = [];

        foreach ($files as $file) {
            $status[] = [
                'migration' => $file,
                'status' => in_array($file, $ran) ? 'Ran' : 'Pending'
            ];
        }

        return $status;
    }

    /**
     * Run a single migration file
     */
    protected function runMigration(string $file, string $method): void
    {
        $path = $this->migrationsPath . '/' . $file . '.php';
        
        if (!file_exists($path)) {
            throw new \RuntimeException("Migration file not found: {$path}");
        }

        // Use require to get the returned migration instance
        // Each migration file returns a new instance, so we can run it multiple times
        $migration = require $path;
        
        if ($migration instanceof Migration) {
            $migration->$method();
        }
    }

    /**
     * Convert filename to class name
     */
    protected function getClassNameFromFile(string $file): string
    {
        // Remove date prefix (e.g., 2026_01_12_000001_)
        $name = preg_replace('/^\d{4}_\d{2}_\d{2}_\d{6}_/', '', $file);
        
        // Convert to PascalCase
        $name = str_replace('_', ' ', $name);
        $name = ucwords($name);
        $name = str_replace(' ', '', $name);
        
        return "App\\Migrations\\{$name}";
    }

    /**
     * Get all migration files sorted by name
     */
    protected function getMigrationFiles(): array
    {
        if (!is_dir($this->migrationsPath)) {
            mkdir($this->migrationsPath, 0755, true);
            return [];
        }

        $files = scandir($this->migrationsPath);
        $migrations = [];

        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                $migrations[] = pathinfo($file, PATHINFO_FILENAME);
            }
        }

        sort($migrations);
        return $migrations;
    }

    /**
     * Get migrations that have already run
     */
    protected function getRanMigrations(): array
    {
        $result = Database::select("SELECT migration FROM {$this->migrationsTable}");
        return array_column($result, 'migration');
    }

    /**
     * Get the next batch number
     */
    protected function getNextBatchNumber(): int
    {
        $result = Database::select("SELECT MAX(batch) as max_batch FROM {$this->migrationsTable}");
        return ($result[0]['max_batch'] ?? 0) + 1;
    }

    /**
     * Record a migration as ran
     */
    protected function recordMigration(string $migration, int $batch): void
    {
        Database::execute(
            "INSERT INTO {$this->migrationsTable} (migration, batch) VALUES (?, ?)",
            [$migration, $batch]
        );
    }

    /**
     * Remove a migration record
     */
    protected function removeMigrationRecord(string $migration): void
    {
        Database::execute(
            "DELETE FROM {$this->migrationsTable} WHERE migration = ?",
            [$migration]
        );
    }

    /**
     * Get migrations to rollback
     */
    protected function getMigrationsToRollback(int $steps): array
    {
        $batch = Database::select("SELECT MAX(batch) as max_batch FROM {$this->migrationsTable}");
        $maxBatch = $batch[0]['max_batch'] ?? 0;
        
        if ($maxBatch === 0) {
            return [];
        }

        $targetBatch = max(1, $maxBatch - $steps + 1);

        return Database::select(
            "SELECT migration, batch FROM {$this->migrationsTable} 
             WHERE batch >= ? ORDER BY batch DESC, id DESC",
            [$targetBatch]
        );
    }
}
