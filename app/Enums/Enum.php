<?php

namespace App\Enums;

use ReflectionClass;

class Enum
{
    public static function constants()
    {
        $constatns = (new ReflectionClass(static::class))->getConstants();

        return array_values($constatns);
    }
}
