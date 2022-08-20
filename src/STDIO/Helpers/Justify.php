<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Helpers;

enum Justify
{

    use HelperEnumTrait;

    case LEFT;
    case CENTER;
    case FULL;
    case RIGHT;

}
