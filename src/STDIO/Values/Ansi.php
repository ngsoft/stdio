<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Values;

class Ansi extends Value {

    public const ESCAPE = "\033[";
    public const STYLE_SUFFIX = "m";
    public const CLEAR_END_LINE = self::ESCAPE . 'K';
    public const CLEAR_START_LINE = self::ESCAPE . '1K';
    public const CLEAR_LINE = self::ESCAPE . '2K';
    public const CLEAR_DOWN = self::ESCAPE . 'J';
    public const CLEAR_UP = self::ESCAPE . '1J';
    public const CLEAR_SCREEN = self::ESCAPE . '2J';
    public const SCROLL_UP = self::ESCAPE . 'S';
    public const SCROLL_DOWN = self::ESCAPE . 'T';
    public const CURSOR_SUFFIX_TO = 'H';
    public const CURSOR_UP = self::ESCAPE . 'A';
    public const CURSOR_DOWN = self::ESCAPE . 'B';
    public const CURSOR_RIGHT = self::ESCAPE . 'C';
    public const CURSOR_LEFT = self::ESCAPE . 'D';
    public const CURSOR_NEXT_LINE = self::ESCAPE . 'E';
    public const CURSOR_PREV_LINE = self::ESCAPE . 'F';
    public const CURSOR_SAVE_POS = self::ESCAPE . 's';
    public const CURSOR_LOAD_POS = self::ESCAPE . 'u';

}
