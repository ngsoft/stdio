<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Enums;

use NGSOFT\Enums\EnumTrait;

enum Align
{

    use EnumTrait;

    case RIGHT;
    case LEFT;
    case CENTER;

    public function toLowerCase(): string
    {
        return strtolower($this->getName());
    }

}
