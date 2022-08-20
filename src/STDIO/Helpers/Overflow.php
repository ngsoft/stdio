<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Helpers;

enum Overflow
{

    use HelperEnumTrait;

    case CROP;
    case FOLD;
    case ELLISIS;
    case NONE;

}
