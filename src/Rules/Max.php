<?php
namespace PhpValidator\Rules;

class Max
{
    public static function name(): string
    {
        return 'max';
    }

    public static function validate($value, array $params = []): bool
    {
        $maxLength = (int)$params[0];
        return strlen($value) <= $maxLength;
    }

    public static function message(): string
    {
        return 'Поле :field слишком длинное';
    }
}