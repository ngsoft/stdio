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
 * @method static static GRAY()
 * @method static static DEFAULT()
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
    case GRAY = 37;
    case DEFAULT = 39;

    public function getUnsetValue(): int
    {
        return 39;
    }

    public function getTag(): string
    {
        return strtolower($this->getName());
    }

    public function getFormatName(): string
    {
        return strtolower($this->getName());
    }

    public function getTagAttribute(): string
    {
        return 'fg';
    }
    

}
