<?php
namespace PhpValidator\Rules;

use Illuminate\Database\Capsule\Manager as Capsule;

class Unique
{
    public static function name(): string
    {
        return 'unique';
    }

    public static function validate($value, array $params = []): bool
    {
        [$table, $column] = $params;

        return (bool)!Capsule::table($table)
            ->where($column, $value)->count();
    }

    public static function message(): string
    {
        return ':field must be unique';
    }
}