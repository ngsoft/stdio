<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Enums;

use NGSOFT\Enums\EnumUtils;

/**
 * @method static static RESET()
 * @method static static BOLD()
 * @method static static DIM()
 * @method static static ITALIC()
 * @method static static UNDERLINE()
 * @method static static INVERSE()
 * @method static static HIDDEN()
 * @method static static STRIKETROUGH()
 */
enum Format: int
{

    use EnumUtils;

    case RESET = 0;
    case BOLD = 1;
    case DIM = 2;
    case ITALIC = 3;
    case UNDERLINE = 4;
    case INVERSE = 7;
    case HIDDEN = 8;
    case STRIKETROUGH = 9;

    public function getUnsetValue(): int
    {

        static $format_unset = [
            0 => 0, 1 => 22, 2 => 22, 3 => 23,
            4 => 24, 7 => 27, 8 => 28, 9 => 29
        ];

        return $format_unset[$this->value];
    }

}
