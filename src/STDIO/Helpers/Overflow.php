<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Helpers;

enum Overflow
{

    use HelperEnumTrait;

    case NONE;
    case CROP;
    case FOLD;
    case ELLISIS;

}
