<?php

/**
 * Database Migration & Seeding CLI
 * 
 * Usage:
 *   php database/migrate.php              - Run all pending migrations
 *   php database/migrate.php --seed       - Run migrations and seeders
 *   php database/migrate.php --rollback   - Rollback last batch of migrations
 *   php database/migrate.php --reset      - Rollback all migrations
 *   php database/migrate.php --refresh    - Reset and re-run all migrations
 *   php database/migrate.php --status     - Show migration status
 *   php database/migrate.php --seed-only  - Only run seeders (no migrations)
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Elmasry\Database\Database;
use Elmasry\Database\Migrator;
use App\Seeders\DatabaseSeeder;

// Parse command line arguments
$options = getopt('', ['seed', 'rollback', 'reset', 'refresh', 'status', 'seed-only', 'help']);

// Show help
if (isset($options['help']) || in_array('--help', $argv) || in_array('-h', $argv)) {
    showHelp();
    exit(0);
}

// Ensure database directory exists
$databaseDir = __DIR__;
if (!is_dir($databaseDir)) {
    mkdir($databaseDir, 0755, true);
}

// Initialize database connection
try {
    Database::connection();
    echo "✅ Database connection established\n\n";
} catch (\Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Initialize migrator
$migrator = new Migrator(__DIR__ . '/../App/Migrations');

// Handle commands
if (isset($options['status'])) {
    showStatus($migrator);
} elseif (isset($options['rollback'])) {
    runRollback($migrator);
} elseif (isset($options['reset'])) {
    runReset($migrator);
} elseif (isset($options['refresh'])) {
    runRefresh($migrator, isset($options['seed']));
} elseif (isset($options['seed-only'])) {
    runSeeders();
} else {
    runMigrations($migrator, isset($options['seed']));
}

/**
 * Run pending migrations
 */
function runMigrations(Migrator $migrator, bool $seed = false): void
{
    echo "🚀 Running migrations...\n";
    
    $result = $migrator->migrate();
    
    if ($result['status'] === 'info') {
        echo "ℹ️  {$result['message']}\n";
    } else {
        echo "✅ {$result['message']}\n";
        foreach ($result['migrated'] ?? [] as $migration) {
            echo "   ✓ {$migration}\n";
        }
    }

    if ($seed) {
        echo "\n";
        runSeeders();
    }
}

/**
 * Run rollback
 */
function runRollback(Migrator $migrator): void
{
    echo "⏪ Rolling back migrations...\n";
    
    $result = $migrator->rollback();
    
    if ($result['status'] === 'info') {
        echo "ℹ️  {$result['message']}\n";
    } else {
        echo "✅ {$result['message']}\n";
        foreach ($result['rolled_back'] ?? [] as $migration) {
            echo "   ✓ {$migration}\n";
        }
    }
}

/**
 * Run reset (rollback all)
 */
function runReset(Migrator $migrator): void
{
    echo "🔄 Resetting all migrations...\n";
    
    $result = $migrator->reset();
    
    if ($result['status'] === 'info') {
        echo "ℹ️  {$result['message']}\n";
    } else {
        echo "✅ {$result['message']}\n";
        foreach ($result['reset'] ?? [] as $migration) {
            echo "   ✓ {$migration}\n";
        }
    }
}

/**
 * Run refresh (reset + migrate)
 */
function runRefresh(Migrator $migrator, bool $seed = false): void
{
    echo "🔄 Refreshing database...\n\n";
    
    runReset($migrator);
    echo "\n";
    runMigrations($migrator, $seed);
}

/**
 * Run database seeders
 */
function runSeeders(): void
{
    echo "🌱 Running seeders...\n";
    
    try {
        $seeder = new DatabaseSeeder();
        $seeder->run();
        echo "✅ Database seeding completed!\n";
    } catch (\Exception $e) {
        echo "❌ Seeding failed: " . $e->getMessage() . "\n";
        exit(1);
    }
}

/**
 * Show migration status
 */
function showStatus(Migrator $migrator): void
{
    echo "📋 Migration Status\n";
    echo str_repeat('-', 60) . "\n";
    
    $status = $migrator->status();
    
    if (empty($status)) {
        echo "No migrations found.\n";
        return;
    }

    foreach ($status as $item) {
        $icon = $item['status'] === 'Ran' ? '✅' : '⏳';
        echo "{$icon} [{$item['status']}] {$item['migration']}\n";
    }
}

/**
 * Show help message
 */
function showHelp(): void
{
    echo <<<HELP
Database Migration & Seeding CLI

Usage:
  php database/migrate.php [options]

Options:
  (no option)    Run all pending migrations
  --seed         Run migrations then seed the database
  --seed-only    Only run seeders (skip migrations)
  --rollback     Rollback the last batch of migrations
  --reset        Rollback all migrations
  --refresh      Reset and re-run all migrations (add --seed to also seed)
  --status       Show the status of each migration
  --help, -h     Show this help message

Examples:
  php database/migrate.php              # Run pending migrations
  php database/migrate.php --seed       # Run migrations and seed
  php database/migrate.php --refresh --seed  # Fresh database with seed data
  php database/migrate.php --status     # Check migration status

HELP;
}
