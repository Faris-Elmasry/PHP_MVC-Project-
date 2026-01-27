<?php

namespace Elmasry\Validation;

use Elmasry\Validation\Rules\EmailRule;
use Elmasry\Validation\Rules\UniqueRule;
use Elmasry\Validation\Rules\BetweenRule;
use Elmasry\Validation\Rules\AlphaNumRule;
use Elmasry\Validation\Rules\RequiredRule;
use Elmasry\Validation\Rules\ConfirmedRule;

trait RulesMapper
{
    protected static array $map = [
        'required' => RequiredRule::class,
        'alnum' => AlphaNumRule::class,
        'between' => BetweenRule::class,
        'email' => EmailRule::class,
        'confirmed' => ConfirmedRule::class,
        'unique' => UniqueRule::class,
    ];

    public static function resolve(string $rule, $options)
    {
        return new static::$map[$rule](...$options);
    }
}