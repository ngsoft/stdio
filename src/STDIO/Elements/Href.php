<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Elements;

class Href extends Element
{

    protected static function getTagName(): string
    {
        return strtolower(class_basename(static::class));
    }

    public static function managesAttributes(array $attributes): bool
    {
        return preg_test('#^https?:#', $attributes['href'] ?? '');
    }

}
