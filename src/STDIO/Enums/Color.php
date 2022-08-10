<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Enums;

use NGSOFT\Enums\EnumTrait;

/**
 * @method static static BLACK()
 * @method static static RED()
 * @method static static GREEN()
 * @method static static YELLOW()
 * @method static static BLUE()
 * @method static static PURPLE()
 * @method static static CYAN()
 * @method static static WHITE()
 */
enum Color: int
{

    use EnumTrait;

    case BLACK = 30;
    case RED = 31;
    case GREEN = 32;
    case YELLOW = 33;
    case BLUE = 34;
    case PURPLE = 35;
    case CYAN = 36;
    case WHITE = 37;

    public function getUnsetValue(): int
    {
        return 39;
    }

    public function getBackgroundColor(): int
    {
        return $this->value + 10;
    }

    public function getBackgroundUnsetValue(): int
    {
        return 49;
    }

}
