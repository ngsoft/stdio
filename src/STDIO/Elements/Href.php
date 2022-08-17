<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Elements;

class Href extends Element
{

    public static function getPriority(): int
    {
        return 10;
    }

}
