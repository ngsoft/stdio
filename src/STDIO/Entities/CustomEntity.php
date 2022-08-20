<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Entities;

class CustomEntity extends BuiltinEntity
{

    public static function getPriority(): int
    {
        return 16;
    }

}
