<?php

namespace Elmasry\Validation;

use Elmasry\Validation\Rules\Contract\Rule;

class Validator
{
    protected array $data = [];
    protected array $aliases = [];
    protected array $rules = [];
    protected ErrorBag $errorBag;

    public function make($data)
    {
        $this->data = $data;
        $this->errorBag = new ErrorBag();
        $this->validate();
    }

    protected function validate()
    {
        foreach ($this->rules as $field => $rules) {
            $resolvedRules = RulesResolver::make($rules);
            foreach ($resolvedRules as $rule) {
                // If rule is a class string or something else, make sure it's an instance
                // But RulesResolver::make via RulesMapper should return instances
                if ($rule instanceof Rule) {
                    if (!$rule->apply($field, $this->getFieldValue($field), $this->data)) {
                        $this->errorBag->add($field, Message::generate($rule, $this->alias($field)));
                    }
                }
            }
        }
    }

    protected function getFieldValue($field)
    {
        return $this->data[$field] ?? null;
    }

    public function setRules($rules)
    {
        $this->rules = $rules;
    }

    public function passes()
    {
        return empty($this->errors());
    }

    public function errors($key = null)
    {
        return $key ? ($this->errorBag->errors[$key] ?? []) : $this->errorBag->errors;
    }

    public function alias($field)
    {
        return $this->aliases[$field] ?? $field;
    }

    public function setAliases(array $aliases)
    {
        $this->aliases = $aliases;
    }
}