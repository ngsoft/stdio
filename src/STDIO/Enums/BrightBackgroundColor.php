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
enum BrightBackgroundColor: int
{

    use EnumTrait;

    case BLACK = 100;
    case RED = 101;
    case GREEN = 102;
    case YELLOW = 103;
    case BLUE = 104;
    case PURPLE = 105;
    case CYAN = 106;
    case GRAY = 107;

    public function getUnsetValue(): int
    {
        return 49;
    }

    public function getTag(): string
    {
        return sprintf('bg:%s:bright', strtolower($this->getName()));
    }

    public function getTagAttribute(): string
    {
        return 'bg';
    }

}
