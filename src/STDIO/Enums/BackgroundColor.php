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
enum BackgroundColor: int
{

    use EnumTrait;

    case BLACK = 40;
    case RED = 41;
    case GREEN = 42;
    case YELLOW = 43;
    case BLUE = 44;
    case PURPLE = 45;
    case CYAN = 46;
    case GRAY = 47;

    public function getUnsetValue(): int
    {
        return 49;
    }

    public function getTag(): string
    {
        return sprintf('bg:%s', strtolower($this->getName()));
    }

}
