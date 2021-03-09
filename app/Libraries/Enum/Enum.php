<?php

namespace App\Libraries\Enum;

use ReflectionClass;

abstract class Enum
{
    /**
     * Get defined constants.
     *
     * @return array $constants
     * @throws \ReflectionException
     */
    public static function all()
    {
        return (new ReflectionClass(static::class))->getConstants();
    }
}
