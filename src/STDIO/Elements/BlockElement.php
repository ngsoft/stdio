<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Elements;

class BlockElement extends Element
{

    public static function getPriority(): int
    {
        return 16;
    }

    public static function managesAttributes(array $attribute): bool
    {
        return false;
    }

}
