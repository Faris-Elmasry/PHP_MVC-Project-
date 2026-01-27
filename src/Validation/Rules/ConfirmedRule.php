<?php

namespace Elmasry\Validation\Rules;

use Elmasry\Validation\Rules\Contract\Rule;

class ConfirmedRule implements Rule
{
    public function apply($field, $value, $data = [])
    {
        return isset($data[$field . '_confirmation']) && ($data[$field] === $data[$field . '_confirmation']);
    }

    public function __toString()
    {
        return '%s does not match confirmation';
    }
}