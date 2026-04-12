<?php

namespace PhpValidator\Rules;

class Required
{
    public static function name(): string
    {
        return 'required';
    }

    public static function validate($value): bool
    {
        return !empty($value);
    }
}