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
enum BrightColor: int
{

    use EnumTrait;

    case BLACK = 90;
    case RED = 91;
    case GREEN = 92;
    case YELLOW = 93;
    case BLUE = 94;
    case PURPLE = 95;
    case CYAN = 96;
    case GRAY = 97;

    public function getUnsetValue(): int
    {
        return 39;
    }

    public function getTag(): string
    {
        return sprintf('%s:bright', strtolower($this->getName()));
    }

    public function getTagAttribute(): string
    {
        return 'fg';
    }

}
