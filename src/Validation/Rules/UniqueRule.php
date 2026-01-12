<?php

namespace SecTheater\Validation\Rules;

use SecTheater\Validation\Rules\Contract\Rule;

class UniqueRule implements Rule
{
    protected $table;

    protected $column;

    public function __construct($table, $column)
    {
        $this->table = $table;
        $this->column = $column;
    }

    public function apply($field, $value, $data =[])
    {
      return  true;
    }

    public function __toString()
    {
        return 'This %s is already taken';
    }
}