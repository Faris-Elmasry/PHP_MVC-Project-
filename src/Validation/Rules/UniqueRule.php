<?php

namespace Elmasry\Validation\Rules;

use Elmasry\Validation\Rules\Contract\Rule;
use Elmasry\Database\Database;

class UniqueRule implements Rule
{
    protected $table;
    protected $column;
    protected $ignoreId;

    public function __construct($table, $column, $ignoreId = null)
    {
        $this->table = $table;
        $this->column = $column;
        $this->ignoreId = $ignoreId;
    }

    public function apply($field, $value, $data = [])
    {
        $query = "SELECT COUNT(*) as count FROM {$this->table} WHERE {$this->column} = ?";
        $params = [$value];

        if ($this->ignoreId) {
            $query .= " AND id != ?";
            $params[] = $this->ignoreId;
        }

        $result = Database::select($query, $params);

        return ($result[0]['count'] ?? 0) == 0;
    }

    public function __toString()
    {
        return 'for user that our system have this %s';
    }
}