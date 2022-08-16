<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Elements;

abstract class CustomElement extends Element
{

    abstract protected static function getTagName(): string;

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
