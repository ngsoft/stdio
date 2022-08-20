<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Entities;

abstract class BuiltinEntity extends Entity
{

    public static function getPriority(): int
    {
        return 10;
    }

    public static function matches(array $attributes): bool
    {
        return isset($attributes[strtolower(class_basename(static::class))]);
    }

}
