<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Enums;

class BackgroundColor extends Color {

    public const BLACK = 40;
    public const RED = 41;
    public const GREEN = 42;
    public const YELLOW = 43;
    public const BLUE = 44;
    public const PURPLE = 45;
    public const CYAN = 46;
    public const WHITE = 47;

    public function getUnsetValue(): int {
        return 49;
    }

}
