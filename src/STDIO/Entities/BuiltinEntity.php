<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Entities;

abstract class BuiltinEntity extends Entity
{

    protected static function getTagName(): string
    {
        return strtolower(class_basename(static::class));
    }

    public static function getPriority(): int
    {
        return 10;
    }

    public static function matches(array $attributes): bool
    {
        return isset($attributes[self::getTagName()]);
    }

}
