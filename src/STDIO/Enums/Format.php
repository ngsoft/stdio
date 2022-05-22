<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Enums;

use NGSOFT\Enums\Enum;

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
class Format extends Enum {

    public const RESET = 0;
    public const BOLD = 1;
    public const DIM = 2;
    public const ITALIC = 3;
    public const UNDERLINE = 4;
    public const INVERSE = 7;
    public const HIDDEN = 8;
    public const STRIKETROUGH = 9;

    public function getUnsetValue(): int {
        static $format_unset = [
            0 => 0, 1 => 22, 2 => 22, 3 => 23,
            4 => 24, 7 => 27, 8 => 28, 9 => 29
        ];

        return $format_unset[$this->value];
    }

}
