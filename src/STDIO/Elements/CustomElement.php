<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Elements;

abstract class CustomElement extends Element
{

    abstract public static function getTagName(): string;

    public static function getPriority(): int
    {
        return 16;
    }

    public static function managesAttributes(array $attribute): bool
    {
        if (static::class === __CLASS__) {
            return false;
        }

        return isset($attribute[self::getTagName()]);
    }

}
