<?php

namespace Elmasry\Database;

class Blueprint
{
    protected string $table;
    protected array $columns = [];
    protected array $foreignKeys = [];
    protected string $primaryKey = 'id';

    public function __construct(string $table)
    {
        $this->table = $table;
    }

    /**
     * Add auto-incrementing primary key
     */
    public function id(string $column = 'id'): self
    {
        $this->primaryKey = $column;
        $this->columns[] = "{$column} INT AUTO_INCREMENT PRIMARY KEY";
        return $this;
    }

    /**
     * Add a string/text column
     */
    public function string(string $column, int $length = 255): ColumnDefinition
    {
        return $this->addColumn($column, "VARCHAR({$length})");
    }

    /**
     * Add a text column
     */
    public function text(string $column): ColumnDefinition
    {
        return $this->addColumn($column, "TEXT");
    }

    /**
     * Add an integer column
     */
    public function integer(string $column): ColumnDefinition
    {
        return $this->addColumn($column, "INTEGER");
    }

    /**
     * Add a big integer column
     */
    public function bigInteger(string $column): ColumnDefinition
    {
        return $this->addColumn($column, "INTEGER");
    }

    /**
     * Add a decimal/float column
     */
    public function decimal(string $column, int $precision = 8, int $scale = 2): ColumnDefinition
    {
        return $this->addColumn($column, "REAL");
    }

    /**
     * Add a float column
     */
    public function float(string $column): ColumnDefinition
    {
        return $this->addColumn($column, "REAL");
    }

    /**
     * Add a boolean column
     */
    public function boolean(string $column): ColumnDefinition
    {
        return $this->addColumn($column, "INTEGER");
    }

    /**
     * Add a datetime column
     */
    public function datetime(string $column): ColumnDefinition
    {
        return $this->addColumn($column, "DATETIME");
    }

    /**
     * Add a timestamp column
     */
    public function timestamp(string $column): ColumnDefinition
    {
        return $this->addColumn($column, "DATETIME");
    }

    /**
     * Add created_at and updated_at timestamps
     */
    public function timestamps(): self
    {
        $this->columns[] = "created_at DATETIME DEFAULT CURRENT_TIMESTAMP";
        $this->columns[] = "updated_at DATETIME DEFAULT CURRENT_TIMESTAMP";
        return $this;
    }

    /**
     * Add a foreign key column (user_id references users.id)
     */
    public function foreignId(string $column): ForeignKeyDefinition
    {
        $this->columns[] = "{$column} INTEGER";
        return new ForeignKeyDefinition($this, $column);
    }

    /**
     * Add a foreign key constraint
     */
    public function addForeignKey(string $column, string $references, string $on, string $onDelete = 'CASCADE'): self
    {
        $this->foreignKeys[] = "FOREIGN KEY ({$column}) REFERENCES {$on}({$references}) ON DELETE {$onDelete}";
        return $this;
    }

    /**
     * Add a column with definition
     */
    protected function addColumn(string $column, string $type): ColumnDefinition
    {
        $definition = new ColumnDefinition($this, $column, $type);
        return $definition;
    }

    /**
     * Add raw column definition
     */
    public function addColumnDefinition(string $definition): void
    {
        $this->columns[] = $definition;
    }

    /**
     * Build the CREATE TABLE SQL
     */
    public function toSql(): string
    {
        $definitions = array_merge($this->columns, $this->foreignKeys);
        $sql = "CREATE TABLE IF NOT EXISTS {$this->table} (\n    ";
        $sql .= implode(",\n    ", $definitions);
        $sql .= "\n)";
        return $sql;
    }

    /**
     * Build the DROP TABLE SQL
     */
    public function toDropSql(): string
    {
        return "DROP TABLE IF EXISTS {$this->table}";
    }

    /**
     * Get the table name
     */
    public function getTable(): string
    {
        return $this->table;
    }
}

/**
 * Column definition helper for fluent API
 */
class ColumnDefinition
{
    protected Blueprint $blueprint;
    protected string $column;
    protected string $type;
    protected bool $nullable = false;
    protected bool $unique = false;
    protected ?string $default = null;

    public function __construct(Blueprint $blueprint, string $column, string $type)
    {
        $this->blueprint = $blueprint;
        $this->column = $column;
        $this->type = $type;
    }

    /**
     * Allow NULL values
     */
    public function nullable(): self
    {
        $this->nullable = true;
        return $this;
    }

    /**
     * Set column as unique
     */
    public function unique(): self
    {
        $this->unique = true;
        return $this;
    }

    /**
     * Set default value
     */
    public function default($value): self
    {
        if (is_bool($value)) {
            $value = $value ? 1 : 0;
        }
        $this->default = (string) $value;
        return $this;
    }

    /**
     * Finalize and add to blueprint
     */
    public function __destruct()
    {
        $definition = "{$this->column} {$this->type}";

        if (!$this->nullable) {
            $definition .= " NOT NULL";
        }

        if ($this->unique) {
            $definition .= " UNIQUE";
        }

        if ($this->default !== null) {
            if (is_string($this->default) && $this->default !== 'CURRENT_TIMESTAMP') {
                $definition .= " DEFAULT '{$this->default}'";
            } else {
                $definition .= " DEFAULT {$this->default}";
            }
        }

        $this->blueprint->addColumnDefinition($definition);
    }
}

/**
 * Foreign key definition helper for fluent API
 */
class ForeignKeyDefinition
{
    protected Blueprint $blueprint;
    protected string $column;
    protected string $onDelete = 'CASCADE';

    public function __construct(Blueprint $blueprint, string $column)
    {
        $this->blueprint = $blueprint;
        $this->column = $column;
    }

    /**
     * Set the referenced table and column (constrained to id by default)
     */
    public function constrained(?string $table = null, string $column = 'id'): self
    {
        if ($table === null) {
            // Auto-detect table from column name (user_id -> users)
            $table = str_replace('_id', 's', $this->column);
        }

        $this->blueprint->addForeignKey($this->column, $column, $table, $this->onDelete);
        return $this;
    }

    /**
     * Set ON DELETE action
     */
    public function onDelete(string $action): self
    {
        $this->onDelete = strtoupper($action);
        return $this;
    }

    /**
     * Alias for constrained with explicit references
     */
    public function references(string $column): ForeignKeyReferences
    {
        return new ForeignKeyReferences($this->blueprint, $this->column, $column);
    }
}

/**
 * Foreign key references helper
 */
class ForeignKeyReferences
{
    protected Blueprint $blueprint;
    protected string $column;
    protected string $references;
    protected string $onDelete = 'CASCADE';

    public function __construct(Blueprint $blueprint, string $column, string $references)
    {
        $this->blueprint = $blueprint;
        $this->column = $column;
        $this->references = $references;
    }

    /**
     * Set the referenced table
     */
    public function on(string $table): self
    {
        $this->blueprint->addForeignKey($this->column, $this->references, $table, $this->onDelete);
        return $this;
    }

    /**
     * Set ON DELETE action
     */
    public function onDelete(string $action): self
    {
        $this->onDelete = strtoupper($action);
        return $this;
    }
}
