<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Values;

class BackgroundColor extends Color {

    public const BLACK = 40;
    public const RED = 41;
    public const GREEN = 42;
    public const YELLOW = 43;
    public const BLUE = 44;
    public const PURPLE = 45;
    public const CYAN = 46;
    public const WHITE = 47;
    public const UNSET = 49;

    protected string $tagModifier = ' background';

}
