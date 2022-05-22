<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Values;

/**
 * @method static static BLACK()
 * @method static static RED()
 * @method static static YELLOW()
 * @method static static BLUE()
 * @method static static PURPLE()
 * @method static static CYAN()
 * @method static static WHITE()
 * @method static static UNSET()
 */
class Color extends Value {

    public const BLACK = 30;
    public const RED = 31;
    public const GREEN = 32;
    public const YELLOW = 33;
    public const BLUE = 34;
    public const PURPLE = 35;
    public const CYAN = 36;
    public const WHITE = 37;

    public function getUnsetValue(): int {
        return 39;
    }

}