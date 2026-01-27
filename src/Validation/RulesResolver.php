<?php

namespace Elmasry\Validation;

trait RulesResolver
{
    public static function make($rules)
    {
        if (is_string($rules)) {
            $rules = (array) (str_contains($rules, '|') ? explode('|', $rules) : $rules);
        }

        return array_map(function ($rule) {
            if (is_string($rule)) {
                return static::getRuleFromString($rule);
            }

            return $rule;
        }, $rules);
    }

    public static function getRuleFromString(string $rule)
    {
        $exploded = explode(':', $rule);
        $ruleName = $exploded[0];
        $options = isset($exploded[1]) ? explode(',', $exploded[1]) : [];

        return RulesMapper::resolve($ruleName, $options);
    }
}