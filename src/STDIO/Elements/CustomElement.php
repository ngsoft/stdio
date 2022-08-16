<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Elements;

use function class_basename;

abstract class CustomElement extends Element
{

    protected static function getTagName(): string
    {
        return strtolower(class_basename(static::class));
    }

    public static function getPriority(): int
    {
        return 16;
    }

    public static function managesAttributes(array $attributes): bool
    {
        if (static::class === __CLASS__) {
            return false;
        }

        return isset($attributes[static::getTagName()]);
    }

}
