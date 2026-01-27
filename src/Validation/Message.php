<?php

namespace Elmasry\Validation;

use Elmasry\Validation\Rules\Contract\Rule;

class Message
{
    public static function generate(Rule $rule, $field)
    {
        // Replace %s in the rule's message with the field name
        return sprintf((string) $rule, $field);
    }
}
